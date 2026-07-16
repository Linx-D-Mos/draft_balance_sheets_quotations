<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique()->nullable();
            $table->string('phone')->nullable();
            $table->timestamps();
        });

        Schema::create('project_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('display_name');
            $table->string('code')->unique();
            $table->timestamps();
        });

        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
            $table->foreignId('project_status_id')->constrained('project_statuses');
            $table->string('title');
            $table->timestamps();
        });

        Schema::create('quote_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('display_name');
            $table->string('code')->unique();
            $table->timestamps();
        });

        Schema::create('quotes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->foreignId('status_id')->constrained('quote_statuses');
            $table->foreignId('parent_quote_id')->nullable()->constrained('quotes')->nullOnDelete();
            $table->string('title');
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('work_weekends')->default(false);
            $table->integer('amendment_level')->default(0);
            $table->integer('total_hours')->default(0);
            $table->decimal('direct_labor_cost', 12, 4)->default(0);
            $table->decimal('direct_materials_cost', 12, 4)->default(0);
            $table->decimal('direct_cost', 12, 4)->default(0);
            $table->decimal('overhead_rate_applied', 12, 4)->default(0);
            $table->decimal('overtime_multiplier_applied', 12, 4)->default(0);
            $table->decimal('overhead_cost', 12, 4)->default(0);
            $table->decimal('equilibrium_cost', 12, 4)->default(0);
            $table->decimal('margin_applied', 12, 4)->default(0);
            $table->decimal('total_price', 12, 4)->default(0);
            $table->timestamps();
        });

        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('labor_roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->decimal('base_salary', 12, 4);
            $table->decimal('social_load_pct', 12, 4);
            $table->decimal('hourly_cost', 12, 4); // C_ch calculada y persistida
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('quote_labor_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quote_id')->constrained('quotes')->cascadeOnDelete();
            $table->foreignId('labor_role_id')->constrained('labor_roles');
            $table->foreignId('employee_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->string('worker_name_placeholder')->nullable();
            $table->integer('estimated_hours_regular');
            $table->integer('estimated_hours_extra');
            $table->decimal('hourly_rate_at_estimation', 12, 4); // Snapshot de C_ch
            $table->decimal('estimated_subtotal', 12, 4);
            $table->timestamps();
        });

        Schema::create('quote_material_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quote_id')->constrained('quotes')->cascadeOnDelete();
            $table->string('concept');
            $table->decimal('estimated_quantity', 12, 4);
            $table->decimal('estimated_unit_price', 12, 4);
            $table->decimal('subtotal', 12, 4); // quantity * unit_price
            $table->timestamps();
        });

        Schema::create('fixed_expenses', function (Blueprint $table) {
            $table->id();
            $table->string('concept');
            $table->decimal('amount', 12, 4);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('global_settings', function (Blueprint $table) {
            $table->id();
            $table->decimal('standard_monthly_hours', 12, 4);
            $table->decimal('default_overhead_rate_applied', 12, 4); // T_oh global calculada
            $table->decimal('default_profit_margin', 12, 4);
            $table->decimal('overtime_multiplier', 12, 4); // ej: 1.5
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('global_settings');
        Schema::dropIfExists('fixed_expenses');
        Schema::dropIfExists('quote_material_items');
        Schema::dropIfExists('quote_labor_assignments');
        Schema::dropIfExists('labor_roles');
        Schema::dropIfExists('employees');
        Schema::dropIfExists('quotes');
        Schema::dropIfExists('quote_statuses');
        Schema::dropIfExists('projects');
        Schema::dropIfExists('project_statuses');
        Schema::dropIfExists('clients');
    }
};
