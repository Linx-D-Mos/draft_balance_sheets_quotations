<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use App\Models\Client;
use App\Models\Project;
use App\Models\ProjectStatus;
use App\Models\QuoteStatus;
use App\Models\Employee;
use App\Models\LaborRole;
use App\Models\FixedExpense;
use App\Models\GlobalSetting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create Default Admin User
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
            ]
        );

        // 2. Create Project Statuses
        $statuses = [
            ['code' => 'draft', 'display_name' => 'Borrador'],
            ['code' => 'in_progress', 'display_name' => 'En Ejecución'],
            ['code' => 'completed', 'display_name' => 'Finalizado'],
            ['code' => 'canceled', 'display_name' => 'Cancelado'],
        ];
        foreach ($statuses as $status) {
            ProjectStatus::updateOrCreate(['code' => $status['code']], $status);
        }

        // 3. Create Quote Statuses
        $qStatuses = [
            ['code' => 'draft', 'display_name' => 'Borrador'],
            ['code' => 'sent', 'display_name' => 'Enviada'],
            ['code' => 'approved', 'display_name' => 'Aprobada'],
            ['code' => 'closed_by_amendment', 'display_name' => 'Cerrada por Enmienda'],
            ['code' => 'canceled', 'display_name' => 'Cancelada'],
        ];
        foreach ($qStatuses as $qs) {
            QuoteStatus::updateOrCreate(['code' => $qs['code']], $qs);
        }

        // 4. Create Fixed Expenses
        FixedExpense::updateOrCreate(
            ['concept' => 'Renta de Oficina y Seguros'],
            [
                'amount' => 2400.00,
                'is_active' => true,
            ]
        );

        // 5. Create Global Settings (Forcing exact requirements from Paso 5)
        GlobalSetting::updateOrCreate(
            ['id' => 1],
            [
                'standard_monthly_hours' => 160.00,
                'default_overhead_rate_applied' => 15.00,
                'default_profit_margin' => 30.00,
                'overtime_multiplier' => 1.5,
            ]
        );

        // 6. Create Labor Roles
        $roles = [
            [
                'name' => 'Pintor Principal',
                'base_salary' => 20.00,
                'social_load_pct' => 25.00,
            ],
            [
                'name' => 'Preparador',
                'base_salary' => 15.00,
                'social_load_pct' => 20.00,
            ],
            [
                'name' => 'Carpintero',
                'base_salary' => 22.00,
                'social_load_pct' => 30.00,
            ],
        ];
        foreach ($roles as $roleData) {
            LaborRole::updateOrCreate(['name' => $roleData['name']], $roleData);
        }

        // 7. Create Employees
        $employees = [
            ['name' => 'Johan Lince', 'is_active' => true],
            ['name' => 'Alex', 'is_active' => true],
        ];
        foreach ($employees as $employeeData) {
            Employee::updateOrCreate(['name' => $employeeData['name']], $employeeData);
        }

        // 7.5 Create Material Categories
        $materialCategories = [
            ['code' => 'budgeted', 'display_name' => 'Presupuestado'],
            ['code' => 'unbudgeted', 'display_name' => 'No Presupuestado'],
        ];
        foreach ($materialCategories as $cat) {
            \App\Models\MaterialCategory::updateOrCreate(['code' => $cat['code']], $cat);
        }

        // 8. Create Client
        $client = Client::updateOrCreate(
            ['email' => 'client@example.com'],
            [
                'name' => 'Constructora Delta',
                'phone' => '+1 (555) 019-2834',
            ]
        );

        // 9. Create Project
        Project::updateOrCreate(
            ['title' => 'Remodelación Oficinas Corporativas'],
            [
                'client_id' => $client->id,
                'project_status_id' => ProjectStatus::where('code', 'draft')->first()->id,
            ]
        );
    }
}
