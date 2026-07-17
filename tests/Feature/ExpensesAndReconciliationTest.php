<?php

declare(strict_types=1);

use App\Models\Client;
use App\Models\Employee;
use App\Models\GlobalSetting;
use App\Models\LaborRole;
use App\Models\MaterialCategory;
use App\Models\Project;
use App\Models\ProjectDeposit;
use App\Models\ProjectLaborLog;
use App\Models\ProjectMaterialPurchase;
use App\Models\ProjectStatus;
use App\Models\Quote;
use App\Models\QuoteStatus;
use App\Models\User;
use App\Filament\Resources\ProjectResource\Widgets\ProjectOverviewStats;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('calculates actual subtotal automatically on save in ProjectLaborLog', function () {
    $user = User::create([
        'name' => 'Admin',
        'email' => 'admin@example.com',
        'password' => bcrypt('password'),
    ]);

    GlobalSetting::create([
        'standard_monthly_hours' => 160.00,
        'default_overhead_rate_applied' => 15.00,
        'default_profit_margin' => 30.00,
        'overtime_multiplier' => 1.5,
    ]);

    $projectStatus = ProjectStatus::create(['code' => 'in_progress', 'display_name' => 'En Ejecución']);
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

    $employee = Employee::create(['name' => 'Johan Lince']);

    $log = ProjectLaborLog::create([
        'project_id' => $project->id,
        'employee_id' => $employee->id,
        'labor_role_id' => $role->id,
        'actual_hours_regular' => 8,
        'actual_hours_extra' => 2,
        'hourly_rate_actual' => 20.00,
        'logged_at' => now()->toDateString(),
    ]);

    expect((float) $log->overtime_multiplier_applied)->toBe(1.5);
    // (8 * 20.00) + (2 * 20.00 * 1.5) = 160 + 60 = 220
    expect((float) $log->actual_subtotal)->toBe(220.00);
});

test('calculates actual subtotal automatically on save in ProjectMaterialPurchase', function () {
    $projectStatus = ProjectStatus::create(['code' => 'in_progress', 'display_name' => 'En Ejecución']);
    $client = Client::create(['name' => 'Constructora Delta', 'email' => 'client@example.com']);
    
    $project = Project::create([
        'title' => 'Remodelacion Oficinas',
        'client_id' => $client->id,
        'project_status_id' => $projectStatus->id,
    ]);

    $cat = MaterialCategory::create([
        'code' => 'budgeted',
        'display_name' => 'Presupuestado',
    ]);

    $purchase = ProjectMaterialPurchase::create([
        'project_id' => $project->id,
        'material_category_id' => $cat->id,
        'concept' => 'Sherwin Williams 5G',
        'store' => 'Sherwin Williams',
        'payment_method' => 'Cash',
        'buyer_name' => 'Carlos',
        'actual_quantity' => 2.5,
        'actual_unit_price' => 45.00,
        'purchased_at' => now()->toDateString(),
    ]);

    expect((float) $purchase->actual_subtotal)->toBe(112.50);
});

test('allows logical annulment of logs, purchases, and deposits', function () {
    $user = User::create([
        'name' => 'Admin',
        'email' => 'admin@example.com',
        'password' => bcrypt('password'),
    ]);

    $projectStatus = ProjectStatus::create(['code' => 'in_progress', 'display_name' => 'En Ejecución']);
    $client = Client::create(['name' => 'Constructora Delta', 'email' => 'client@example.com']);
    $project = Project::create([
        'title' => 'Remodelacion Oficinas',
        'client_id' => $client->id,
        'project_status_id' => $projectStatus->id,
    ]);

    $deposit = ProjectDeposit::create([
        'project_id' => $project->id,
        'amount' => 1000.00,
        'payment_method' => 'Zelle',
        'received_at' => now()->toDateString(),
    ]);

    $deposit->update([
        'is_annulled' => true,
        'annulled_at' => now(),
        'annulment_reason' => 'Duplicate payment',
        'annulled_by_user_id' => $user->id,
    ]);

    expect($deposit->is_annulled)->toBeTrue();
    expect($deposit->annulment_reason)->toBe('Duplicate payment');
    expect($deposit->annulled_by_user_id)->toBe($user->id);
});

test('calculates correct dashboard metrics using the Filament widget', function () {
    $user = User::create([
        'name' => 'Admin',
        'email' => 'admin@example.com',
        'password' => bcrypt('password'),
    ]);

    $projectStatus = ProjectStatus::create(['code' => 'in_progress', 'display_name' => 'En Ejecución']);
    $approvedStatus = QuoteStatus::create(['code' => 'approved', 'display_name' => 'Aprobada']);

    $client = Client::create(['name' => 'Constructora Delta', 'email' => 'client@example.com']);
    $project = Project::create([
        'title' => 'Remodelacion Oficinas',
        'client_id' => $client->id,
        'project_status_id' => $projectStatus->id,
    ]);

    // Active Quote (Línea Base)
    $quote = Quote::create([
        'project_id' => $project->id,
        'status_id' => $approvedStatus->id,
        'title' => 'Cotizacion Pintura',
        'start_date' => now()->toDateString(),
        'end_date' => now()->addDays(5)->toDateString(),
        'direct_cost' => 500.00,
        'overhead_rate_applied' => 15.00,
        'overhead_cost' => 150.00, // 10 hours * 15.00
        'equilibrium_cost' => 650.00,
        'margin_applied' => 30.00,
        'total_price' => 928.57, // 650 / 0.7
    ]);

    // Real Expenses
    $role = LaborRole::create(['name' => 'Pintor', 'base_salary' => 10, 'social_load_pct' => 0]);
    $employee = Employee::create(['name' => 'Alex']);

    // Log 8 regular hours, 2 extra hours. Overtime multiplier is 1.5 (default in Settings)
    GlobalSetting::create([
        'standard_monthly_hours' => 160.00,
        'default_overhead_rate_applied' => 15.00,
        'default_profit_margin' => 30.00,
        'overtime_multiplier' => 1.5,
    ]);

    ProjectLaborLog::create([
        'project_id' => $project->id,
        'employee_id' => $employee->id,
        'labor_role_id' => $role->id,
        'actual_hours_regular' => 8,
        'actual_hours_extra' => 2,
        'hourly_rate_actual' => 15.00,
        'logged_at' => now()->toDateString(),
    ]);

    $cat = MaterialCategory::create(['code' => 'budgeted', 'display_name' => 'Presupuestado']);
    ProjectMaterialPurchase::create([
        'project_id' => $project->id,
        'material_category_id' => $cat->id,
        'concept' => 'Materials',
        'store' => 'Sherwin Williams',
        'payment_method' => 'Cash',
        'buyer_name' => 'Carlos',
        'actual_quantity' => 1,
        'actual_unit_price' => 150.00,
        'purchased_at' => now()->toDateString(),
    ]);

    // Log another log that is annulled (should NOT be counted in totals)
    $annulledLog = ProjectLaborLog::create([
        'project_id' => $project->id,
        'employee_id' => $employee->id,
        'labor_role_id' => $role->id,
        'actual_hours_regular' => 10,
        'actual_hours_extra' => 0,
        'hourly_rate_actual' => 15.00,
        'logged_at' => now()->toDateString(),
        'is_annulled' => true,
        'annulment_reason' => 'Error',
    ]);

    // Deposits (1 active, 1 annulled)
    ProjectDeposit::create([
        'project_id' => $project->id,
        'amount' => 600.00,
        'payment_method' => 'Zelle',
        'received_at' => now()->toDateString(),
    ]);

    ProjectDeposit::create([
        'project_id' => $project->id,
        'amount' => 400.00,
        'payment_method' => 'Cash',
        'received_at' => now()->toDateString(),
        'is_annulled' => true,
        'annulment_reason' => 'Canceled',
    ]);

    // Run Widget Stats
    $widget = new ProjectOverviewStats();
    $widget->record = $project;

    $reflector = new ReflectionClass(ProjectOverviewStats::class);
    $method = $reflector->getMethod('getStats');
    $method->setAccessible(true);
    $stats = $method->invoke($widget);

    // Verify stats array elements
    expect($stats)->toBeArray()->toHaveCount(4);

    // Balance de Caja:
    // Deposits = 600.00
    // Labor: regular = 8 * 15 = 120. Extra = 2 * 15 * 1.5 = 45. Subtotal = 165.00.
    // Purchases = 150.00
    // Cash Balance = 600 - (165 + 150) = 285.00
    expect($stats[0]->getLabel())->toBe('Balance de Caja');
    expect($stats[0]->getValue())->toBe('$285.00');

    // Desviación Costo Directo:
    // Budgeted direct cost = 500.00
    // Real direct cost = 165 (Labor) + 150 (Purchases) = 315.00
    expect($stats[1]->getLabel())->toBe('Desviación Costo Directo');
    expect($stats[1]->getValue())->toBe('$315.00 / $500.00');

    // Desviación de Overhead:
    // Real hours = 8 + 2 = 10 hours
    // Real overhead = 10 * 15.00 = 150.00
    // Budgeted overhead = 150.00
    expect($stats[2]->getLabel())->toBe('Desviación de Overhead');
    expect($stats[2]->getValue())->toBe('$150.00 / $150.00');

    // Utilidad Neta Real:
    // Total price = 928.57
    // Real direct cost = 315.00
    // Real overhead = 150.00
    // Real Net Profit = 928.57 - (315 + 150) = 463.57
    expect($stats[3]->getLabel())->toBe('Utilidad Neta Real');
    expect($stats[3]->getValue())->toBe('$463.57');
});
