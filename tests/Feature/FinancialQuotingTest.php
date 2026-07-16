<?php

declare(strict_types=1);

use App\Models\Client;
use App\Models\FixedExpense;
use App\Models\GlobalSetting;
use App\Models\LaborRole;
use App\Models\Project;
use App\Models\ProjectStatus;
use App\Models\Quote;
use App\Models\QuoteLaborAssignment;
use App\Models\QuoteMaterialItem;
use App\Models\QuoteStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('calcula el costo cargado por hora de un rol laboral', function () {
    $role = LaborRole::create([
        'name' => 'Pintor Principal',
        'base_salary' => 20.00,
        'social_load_pct' => 25.00,
    ]);

    expect((float) $role->hourly_cost)->toBe(25.00);
});

test('calcula la tasa de overhead por hora automatica basada en Yasumi', function () {
    $expense = FixedExpense::create([
        'concept' => 'Renta Oficina',
        'amount' => 2400.00,
        'is_active' => true,
    ]);

    $settings = GlobalSetting::first();
    expect($settings)->not->toBeNull();
    expect((float) $settings->standard_monthly_hours)->toBeGreaterThan(0);
    
    $expectedRate = 2400.00 / (float) $settings->standard_monthly_hours;
    expect((float) $settings->default_overhead_rate_applied)->toBe(round($expectedRate, 4));
});

test('congela las tarifas y recalcula los totales al aprobar una cotizacion', function () {
    $projectStatus = ProjectStatus::create(['code' => 'draft', 'display_name' => 'Borrador']);
    $draftStatus = QuoteStatus::create(['code' => 'draft', 'display_name' => 'Borrador']);
    $approvedStatus = QuoteStatus::create(['code' => 'approved', 'display_name' => 'Aprobada']);

    $client = Client::create(['name' => 'Constructora Delta', 'email' => 'client@example.com']);
    $project = Project::create([
        'title' => 'Remodelacion Oficinas',
        'client_id' => $client->id,
        'project_status_id' => $projectStatus->id,
    ]);

    $role = LaborRole::create([
        'name' => 'Pintor Principal',
        'base_salary' => 20.00,
        'social_load_pct' => 25.00,
    ]);

    $settings = GlobalSetting::create([
        'standard_monthly_hours' => 160.00,
        'default_overhead_rate_applied' => 15.00,
        'default_profit_margin' => 30.00,
        'overtime_multiplier' => 1.5,
    ]);

    $quote = Quote::create([
        'project_id' => $project->id,
        'status_id' => $draftStatus->id,
        'title' => 'Cotizacion Pintura',
        'start_date' => now()->toDateString(),
        'end_date' => now()->addDays(5)->toDateString(),
        'margin_applied' => 30.00,
    ]);

    $assignment = QuoteLaborAssignment::create([
        'quote_id' => $quote->id,
        'labor_role_id' => $role->id,
        'estimated_hours_regular' => 10,
        'estimated_hours_extra' => 2,
        'hourly_rate_at_estimation' => 25.00,
        'estimated_subtotal' => 325.00,
    ]);

    $material = QuoteMaterialItem::create([
        'quote_id' => $quote->id,
        'concept' => 'Pintura Benjamin Moore',
        'estimated_quantity' => 2,
        'estimated_unit_price' => 50.00,
        'subtotal' => 100.00,
    ]);

    $quote->status_id = $approvedStatus->id;
    $quote->save();

    $quote->refresh();
    expect((float) $quote->overhead_rate_applied)->toBe(15.00);
    expect((float) $quote->overtime_multiplier_applied)->toBe(1.5);
    
    $assignment->refresh();
    expect((float) $assignment->hourly_rate_at_estimation)->toBe(25.00);
    expect((float) $assignment->estimated_subtotal)->toBe(325.00);

    expect((float) $quote->direct_labor_cost)->toBe(325.00);
    expect((float) $quote->direct_materials_cost)->toBe(100.00);
    expect((float) $quote->direct_cost)->toBe(425.00);
    expect((float) $quote->overhead_cost)->toBe(180.00);
    expect((float) $quote->equilibrium_cost)->toBe(605.00);
    expect((float) $quote->total_price)->toBe(round(605.00 / 0.7, 4));
});
