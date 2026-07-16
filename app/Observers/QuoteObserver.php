<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Quote;
use App\Models\QuoteStatus;
use App\Models\GlobalSetting;

class QuoteObserver
{
    /**
     * Handle the Quote "updating" event.
     */
    public function updating(Quote $quote): void
    {
        if ($quote->isDirty('status_id')) {
            $status = QuoteStatus::find($quote->status_id);
            if ($status && $status->code === 'approved') {
                // Get the current global settings
                $globalSettings = GlobalSetting::first();
                $overheadRate = $globalSettings ? (float) $globalSettings->default_overhead_rate_applied : 0.0;
                $overtimeMultiplier = $globalSettings ? (float) $globalSettings->overtime_multiplier : 1.5;

                // Copy physically the rates of today in the snapshot columns
                $quote->overhead_rate_applied = $overheadRate;
                $quote->overtime_multiplier_applied = $overtimeMultiplier;

                // Recalculate labor assignments costs and snapshot rates
                foreach ($quote->laborAssignments as $assignment) {
                    $role = $assignment->laborRole;
                    if ($role) {
                        $assignment->hourly_rate_at_estimation = $role->hourly_cost;
                    }
                    
                    $regHours = (float) $assignment->estimated_hours_regular;
                    $extHours = (float) $assignment->estimated_hours_extra;
                    $rate = (float) $assignment->hourly_rate_at_estimation;
                    
                    $assignment->estimated_subtotal = ($regHours * $rate) + ($extHours * $rate * $overtimeMultiplier);
                    $assignment->saveQuietly();
                }

                // Recalculate Quote totals based on frozen assignments
                $directLaborCost = 0.0;
                $totalHours = 0;
                foreach ($quote->laborAssignments as $assignment) {
                    $directLaborCost += (float) $assignment->estimated_subtotal;
                    $totalHours += $assignment->estimated_hours_regular + $assignment->estimated_hours_extra;
                }

                $directMaterialsCost = 0.0;
                foreach ($quote->materialItems as $item) {
                    $qty = (float) $item->estimated_quantity;
                    $price = (float) $item->estimated_unit_price;
                    $item->subtotal = $qty * $price;
                    $item->saveQuietly();
                    
                    $directMaterialsCost += (float) $item->subtotal;
                }

                $directCost = $directLaborCost + $directMaterialsCost;
                $overheadCost = $totalHours * $overheadRate;
                $equilibriumCost = $directCost + $overheadCost;
                $margin = (float) $quote->margin_applied;
                $divider = 1.0 - ($margin / 100.0);
                $totalPrice = $divider > 0 ? ($equilibriumCost / $divider) : $equilibriumCost;

                $quote->total_hours = $totalHours;
                $quote->direct_labor_cost = $directLaborCost;
                $quote->direct_materials_cost = $directMaterialsCost;
                $quote->direct_cost = $directCost;
                $quote->overhead_cost = $overheadCost;
                $quote->equilibrium_cost = $equilibriumCost;
                $quote->total_price = $totalPrice;
            }
        }
    }
}
