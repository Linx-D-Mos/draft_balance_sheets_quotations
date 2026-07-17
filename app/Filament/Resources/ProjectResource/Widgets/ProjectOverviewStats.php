<?php

declare(strict_types=1);

namespace App\Filament\Resources\ProjectResource\Widgets;

use App\Models\Project;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Model;

class ProjectOverviewStats extends BaseWidget
{
    public ?Model $record = null;

    protected function getStats(): array
    {
        if (!$this->record instanceof Project) {
            return [];
        }

        // 1. Fetch the active (approved) quote
        $approvedQuote = $this->record->quotes()
            ->whereHas('status', fn ($query) => $query->where('code', 'approved'))
            ->first();

        // 2. Sum real expenses and deposits (excluding annulled ones)
        $depositsSum = (float) $this->record->deposits()
            ->where('is_annulled', false)
            ->sum('amount');

        $laborLogsSum = (float) $this->record->laborLogs()
            ->where('is_annulled', false)
            ->sum('actual_subtotal');

        $purchasesSum = (float) $this->record->materialPurchases()
            ->where('is_annulled', false)
            ->sum('actual_subtotal');

        // Cash Balance = Deposits - (Labor + Purchases)
        $cashBalance = $depositsSum - ($laborLogsSum + $purchasesSum);
        $cashColor = $cashBalance < 0 ? 'danger' : 'success';

        // 3. Direct Cost Deviation
        $budgetedDirectCost = $approvedQuote ? (float) $approvedQuote->direct_cost : 0.0;
        $realDirectCost = $laborLogsSum + $purchasesSum;
        $directCostDeviation = $realDirectCost - $budgetedDirectCost;
        $directCostColor = $directCostDeviation > 0 ? 'danger' : 'success';

        // 4. Overhead Deviation
        $realHoursTotal = (float) $this->record->laborLogs()
            ->where('is_annulled', false)
            ->get()
            ->sum(fn ($log) => $log->actual_hours_regular + $log->actual_hours_extra);

        $overheadRate = $approvedQuote ? (float) $approvedQuote->overhead_rate_applied : 0.0;
        $realOverhead = $realHoursTotal * $overheadRate;
        $budgetedOverhead = $approvedQuote ? (float) $approvedQuote->overhead_cost : 0.0;
        $overheadDeviation = $realOverhead - $budgetedOverhead;
        $overheadColor = $overheadDeviation > 0 ? 'danger' : 'success';

        // 5. Real Net Profit
        $totalPrice = $approvedQuote ? (float) $approvedQuote->total_price : 0.0;
        $realNetProfit = $totalPrice - ($realDirectCost + $realOverhead);
        $budgetedNetProfit = $approvedQuote ? (float) ($approvedQuote->total_price - $approvedQuote->equilibrium_cost) : 0.0;
        $netProfitColor = $realNetProfit < $budgetedNetProfit ? 'warning' : 'success';
        if ($realNetProfit < 0) {
            $netProfitColor = 'danger';
        }

        return [
            Stat::make('Balance de Caja', '$' . number_format($cashBalance, 2))
                ->description('Total anticipos menos costos reales de campo')
                ->descriptionIcon($cashBalance < 0 ? 'heroicon-m-arrow-trending-down' : 'heroicon-m-arrow-trending-up')
                ->color($cashColor),

            Stat::make('Desviación Costo Directo', '$' . number_format($realDirectCost, 2) . ' / $' . number_format($budgetedDirectCost, 2))
                ->description($directCostDeviation > 0 
                    ? 'Sobre el presupuesto: +$' . number_format($directCostDeviation, 2) 
                    : 'Bajo el presupuesto: -$' . number_format(abs($directCostDeviation), 2))
                ->descriptionIcon($directCostDeviation > 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($directCostColor),

            Stat::make('Desviación de Overhead', '$' . number_format($realOverhead, 2) . ' / $' . number_format($budgetedOverhead, 2))
                ->description($overheadDeviation > 0 
                    ? 'Sobre el overhead: +$' . number_format($overheadDeviation, 2)
                    : 'Bajo el overhead: -$' . number_format(abs($overheadDeviation), 2))
                ->descriptionIcon($overheadDeviation > 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($overheadColor),

            Stat::make('Utilidad Neta Real', '$' . number_format($realNetProfit, 2))
                ->description($approvedQuote 
                    ? 'Pactada: $' . number_format($budgetedNetProfit, 2) 
                    : 'Sin cotización aprobada')
                ->descriptionIcon($realNetProfit < $budgetedNetProfit ? 'heroicon-m-arrow-trending-down' : 'heroicon-m-arrow-trending-up')
                ->color($netProfitColor),
        ];
    }
}
