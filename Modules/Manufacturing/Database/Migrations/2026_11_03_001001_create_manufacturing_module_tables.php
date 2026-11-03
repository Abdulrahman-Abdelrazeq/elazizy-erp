<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations for Module 10: Manufacturing & BOM.
     */
    public function up(): void
    {
        // 1. Workstations / Production Lines (محطات العمل وخطوط الإنتاج)
        Schema::create('mfg_workstations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->default(1)->index('mfg_ws_tenant_idx');
            $table->unsignedBigInteger('branch_id')->nullable()->index('mfg_ws_branch_idx');
            $table->string('name', 100);
            $table->string('code', 50)->nullable();
            $table->decimal('hourly_cost', 15, 2)->default(0.00);
            $table->decimal('capacity_per_day', 10, 2)->default(8.00);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('branch_id')->references('id')->on('branches')->nullOnDelete();
            $table->unique(['tenant_id', 'code'], 'mfg_ws_code_unique');
        });

        // 2. Bills of Materials (BOM) - قوائم وفواتير المواد والتراكيب
        Schema::create('mfg_boms', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->default(1)->index('mfg_bom_tenant_idx');
            $table->string('bom_number', 50);
            $table->string('name', 150);
            $table->unsignedBigInteger('product_id')->index('mfg_bom_prod_idx');
            $table->decimal('output_quantity', 15, 3)->default(1.000);
            $table->unsignedBigInteger('unit_id')->nullable();
            $table->decimal('raw_material_cost', 15, 2)->default(0.00);
            $table->decimal('labor_cost', 15, 2)->default(0.00);
            $table->decimal('overhead_cost', 15, 2)->default(0.00);
            $table->decimal('total_cost', 15, 2)->default(0.00);
            $table->decimal('cost_per_unit', 15, 2)->default(0.00);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_default')->default(true);
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('items')->cascadeOnDelete();
            $table->foreign('unit_id')->references('id')->on('units')->nullOnDelete();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            $table->unique(['tenant_id', 'bom_number'], 'mfg_bom_number_unique');
        });

        // 3. BOM Items / Raw Materials (المواد الخام في التركيبة)
        Schema::create('mfg_bom_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->default(1)->index('mfg_bitems_tenant_idx');
            $table->unsignedBigInteger('bom_id');
            $table->unsignedBigInteger('item_id');
            $table->decimal('quantity', 15, 3);
            $table->decimal('unit_cost', 15, 2)->default(0.00);
            $table->decimal('scrap_percentage', 5, 2)->default(0.00);
            $table->decimal('total_cost', 15, 2)->default(0.00);
            $table->string('notes', 255)->nullable();
            $table->timestamps();

            $table->foreign('bom_id')->references('id')->on('mfg_boms')->cascadeOnDelete();
            $table->foreign('item_id')->references('id')->on('items')->cascadeOnDelete();
        });

        // 4. BOM Operations / Routing (مراحل وعمليات التشغيل)
        Schema::create('mfg_bom_operations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->default(1)->index('mfg_bops_tenant_idx');
            $table->unsignedBigInteger('bom_id');
            $table->unsignedBigInteger('workstation_id')->nullable();
            $table->string('operation_name', 150);
            $table->unsignedSmallInteger('sequence_order')->default(1);
            $table->decimal('standard_time_minutes', 10, 2)->default(0.00);
            $table->decimal('operation_cost', 15, 2)->default(0.00);
            $table->string('notes', 255)->nullable();
            $table->timestamps();

            $table->foreign('bom_id')->references('id')->on('mfg_boms')->cascadeOnDelete();
            $table->foreign('workstation_id')->references('id')->on('mfg_workstations')->nullOnDelete();
        });

        // 5. Manufacturing Orders (أوامر التشغيل والتصنيع)
        Schema::create('mfg_orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->default(1)->index('mfg_ord_tenant_idx');
            $table->unsignedBigInteger('branch_id')->nullable()->index('mfg_ord_branch_idx');
            $table->string('order_number', 50);
            $table->unsignedBigInteger('bom_id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('from_store_id');
            $table->unsignedBigInteger('to_store_id');
            $table->decimal('planned_quantity', 15, 3);
            $table->decimal('produced_quantity', 15, 3)->default(0.000);
            $table->decimal('rejected_quantity', 15, 3)->default(0.000);
            $table->enum('status', ['draft', 'confirmed', 'in_progress', 'completed', 'cancelled'])->default('draft');
            $table->date('order_date');
            $table->date('expected_completion_date')->nullable();
            $table->date('actual_completion_date')->nullable();
            $table->decimal('estimated_material_cost', 15, 2)->default(0.00);
            $table->decimal('estimated_labor_cost', 15, 2)->default(0.00);
            $table->decimal('estimated_overhead_cost', 15, 2)->default(0.00);
            $table->decimal('estimated_total_cost', 15, 2)->default(0.00);
            $table->decimal('actual_material_cost', 15, 2)->default(0.00);
            $table->decimal('actual_labor_cost', 15, 2)->default(0.00);
            $table->decimal('actual_overhead_cost', 15, 2)->default(0.00);
            $table->decimal('actual_total_cost', 15, 2)->default(0.00);
            $table->decimal('unit_cost', 15, 2)->default(0.00);
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('completed_by')->nullable();
            $table->timestamps();

            $table->foreign('branch_id')->references('id')->on('branches')->nullOnDelete();
            $table->foreign('bom_id')->references('id')->on('mfg_boms')->cascadeOnDelete();
            $table->foreign('product_id')->references('id')->on('items')->cascadeOnDelete();
            $table->foreign('from_store_id')->references('id')->on('stores')->cascadeOnDelete();
            $table->foreign('to_store_id')->references('id')->on('stores')->cascadeOnDelete();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('completed_by')->references('id')->on('users')->nullOnDelete();
            $table->unique(['tenant_id', 'order_number'], 'mfg_ord_number_unique');
        });

        // 6. Manufacturing Order Items (المواد الخام المصروفة فعلياً في أمر التصنيع)
        Schema::create('mfg_order_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->default(1)->index('mfg_orditems_tenant_idx');
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('item_id');
            $table->decimal('planned_quantity', 15, 3);
            $table->decimal('actual_quantity', 15, 3)->default(0.000);
            $table->decimal('unit_cost', 15, 2)->default(0.00);
            $table->decimal('total_cost', 15, 2)->default(0.00);
            $table->string('notes', 255)->nullable();
            $table->timestamps();

            $table->foreign('order_id')->references('id')->on('mfg_orders')->cascadeOnDelete();
            $table->foreign('item_id')->references('id')->on('items')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mfg_order_items');
        Schema::dropIfExists('mfg_orders');
        Schema::dropIfExists('mfg_bom_operations');
        Schema::dropIfExists('mfg_bom_items');
        Schema::dropIfExists('mfg_boms');
        Schema::dropIfExists('mfg_workstations');
    }
};
