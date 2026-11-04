<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations for Module 12: Fixed Assets & Depreciation.
     */
    public function up(): void
    {
        // 1. Asset Categories (تصنيفات وفئات الأصول الثابتة)
        if (!Schema::hasTable('asset_categories')) {
            Schema::create('asset_categories', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->string('name', 150);
                $table->string('code', 50)->nullable();
                $table->enum('depreciation_method', ['straight_line', 'declining_balance', 'none'])->default('straight_line');
                $table->decimal('depreciation_rate', 5, 2)->default(20.00); // Annual %
                $table->decimal('useful_life_years', 5, 2)->default(5.00); // Years
                $table->foreignId('asset_account_id')->nullable()->constrained('chart_of_accounts')->nullOnDelete();
                $table->foreignId('depreciation_account_id')->nullable()->constrained('chart_of_accounts')->nullOnDelete();
                $table->foreignId('depreciation_expense_account_id')->nullable()->constrained('chart_of_accounts')->nullOnDelete();
                $table->text('description')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->index(['tenant_id', 'is_active']);
            });
        }

        // 2. Fixed Assets Registry (سجل وبطاقات الأصول والمعدات)
        if (!Schema::hasTable('assets')) {
            Schema::create('assets', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
                $table->foreignId('category_id')->constrained('asset_categories')->cascadeOnDelete();
                $table->foreignId('location_id')->nullable()->constrained('locations')->nullOnDelete();
                $table->foreignId('cost_center_id')->nullable()->constrained('cost_centers')->nullOnDelete();
                $table->foreignId('custodian_employee_id')->nullable()->constrained('hr_employees')->nullOnDelete();
                $table->foreignId('supplier_id')->nullable()->constrained('clients')->nullOnDelete();

                $table->string('asset_number', 50); // e.g. AST-2026-0001
                $table->string('name', 190);
                $table->string('barcode', 100)->nullable();
                $table->string('serial_number', 100)->nullable();
                $table->string('model_number', 100)->nullable();

                $table->date('purchase_date');
                $table->date('in_service_date');
                $table->decimal('purchase_cost', 15, 2);
                $table->decimal('salvage_value', 15, 2)->default(0.00); // Scrap/Salvage
                $table->decimal('depreciable_cost', 15, 2)->default(0.00); // Cost - Salvage

                $table->enum('depreciation_method', ['straight_line', 'declining_balance', 'none'])->default('straight_line');
                $table->decimal('depreciation_rate', 5, 2)->default(20.00);
                $table->decimal('useful_life_years', 5, 2)->default(5.00);

                $table->decimal('total_depreciated_amount', 15, 2)->default(0.00);
                $table->decimal('current_book_value', 15, 2); // Purchase Cost - Depreciated Amount

                $table->enum('status', ['active', 'in_maintenance', 'fully_depreciated', 'disposed', 'sold'])->default('active');
                $table->date('warranty_expiry')->nullable();
                $table->text('notes')->nullable();
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();

                $table->unique(['tenant_id', 'asset_number']);
                $table->index(['tenant_id', 'branch_id', 'status']);
                $table->index(['tenant_id', 'category_id']);
            });
        }

        // 3. Asset Depreciation Schedules & Periodic Postings (جدول وإدخالات الإهلاك الدورية)
        if (!Schema::hasTable('asset_depreciations')) {
            Schema::create('asset_depreciations', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->foreignId('asset_id')->constrained('assets')->cascadeOnDelete();
                $table->foreignId('journal_entry_id')->nullable()->constrained('journal_entries')->nullOnDelete();

                $table->date('depreciation_date');
                $table->smallInteger('fiscal_year');
                $table->string('fiscal_period', 20); // e.g. '2026-11' or '2026-Q4' or '2026-Annual'
                $table->decimal('depreciation_amount', 15, 2);
                $table->decimal('accumulated_depreciation_after', 15, 2);
                $table->decimal('book_value_after', 15, 2);
                $table->boolean('is_posted')->default(false);
                $table->text('notes')->nullable();
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();

                $table->index(['tenant_id', 'asset_id']);
                $table->index(['tenant_id', 'fiscal_year', 'fiscal_period']);
            });
        }

        // 4. Asset Disposals & Sales (استبعاد وتكهين وبيع الأصول الثابتة)
        if (!Schema::hasTable('asset_disposals')) {
            Schema::create('asset_disposals', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->foreignId('asset_id')->constrained('assets')->cascadeOnDelete();
                $table->foreignId('journal_entry_id')->nullable()->constrained('journal_entries')->nullOnDelete();

                $table->enum('disposal_type', ['sale', 'scrap', 'loss'])->default('scrap');
                $table->date('disposal_date');
                $table->decimal('sale_price', 15, 2)->default(0.00);
                $table->decimal('book_value_at_disposal', 15, 2);
                $table->decimal('gain_or_loss', 15, 2)->default(0.00); // sale_price - book_value (+ve = gain, -ve = loss)
                $table->foreignId('treasury_id')->nullable()->constrained('treasuries')->nullOnDelete();
                $table->string('buyer_name', 150)->nullable();
                $table->text('notes')->nullable();
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();

                $table->index(['tenant_id', 'asset_id']);
                $table->index(['tenant_id', 'disposal_type']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_disposals');
        Schema::dropIfExists('asset_depreciations');
        Schema::dropIfExists('assets');
        Schema::dropIfExists('asset_categories');
    }
};
