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
        // 1. material_categories
        Schema::create('material_categories', function (Blueprint $table) {
            $table->id();
            $table->string('display_name');
            $table->string('code')->unique();
            $table->timestamps();
        });

        // 2. project_labor_logs
        Schema::create('project_labor_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->foreignId('labor_role_id')->constrained('labor_roles')->cascadeOnDelete();
            $table->foreignId('annulled_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->integer('actual_hours_regular');
            $table->integer('actual_hours_extra');
            $table->decimal('hourly_rate_actual', 12, 4);
            $table->decimal('overtime_multiplier_applied', 12, 4);
            $table->decimal('actual_subtotal', 12, 4);
            $table->date('logged_at');
            $table->boolean('is_annulled')->default(false);
            $table->timestamp('annulled_at')->nullable();
            $table->string('annulment_reason')->nullable();
            $table->timestamps();

            // Composite index for summing logs optimized by project and date
            $table->index(['project_id', 'logged_at']);
        });

        // 3. project_material_purchases
        Schema::create('project_material_purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->foreignId('material_category_id')->constrained('material_categories')->cascadeOnDelete();
            $table->foreignId('quote_material_item_id')->nullable()->constrained('quote_material_items')->nullOnDelete();
            $table->foreignId('annulled_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('concept');
            $table->string('store');
            $table->string('payment_method');
            $table->string('buyer_name');
            $table->decimal('actual_quantity', 12, 4);
            $table->decimal('actual_unit_price', 12, 4);
            $table->decimal('actual_subtotal', 12, 4);
            $table->date('purchased_at');
            $table->boolean('is_annulled')->default(false);
            $table->timestamp('annulled_at')->nullable();
            $table->string('annulment_reason')->nullable();
            $table->timestamps();

            // Composite index for summing purchases optimized by project and date
            $table->index(['project_id', 'purchased_at']);
        });

        // 4. project_deposits
        Schema::create('project_deposits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->foreignId('annulled_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->decimal('amount', 12, 4);
            $table->string('payment_method');
            $table->date('received_at');
            $table->string('reference_number')->nullable();
            $table->boolean('is_annulled')->default(false);
            $table->timestamp('annulled_at')->nullable();
            $table->string('annulment_reason')->nullable();
            $table->timestamps();

            // Composite index for summing deposits optimized by project and date
            $table->index(['project_id', 'received_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_deposits');
        Schema::dropIfExists('project_material_purchases');
        Schema::dropIfExists('project_labor_logs');
        Schema::dropIfExists('material_categories');
    }
};
