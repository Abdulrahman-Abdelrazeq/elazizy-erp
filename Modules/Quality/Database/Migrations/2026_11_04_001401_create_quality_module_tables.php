<?php

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
        // Safe drop in reverse order if partially created
        Schema::dropIfExists('quality_capas');
        Schema::dropIfExists('quality_ncrs');
        Schema::dropIfExists('quality_inspection_results');
        Schema::dropIfExists('quality_inspections');
        Schema::dropIfExists('quality_template_items');
        Schema::dropIfExists('quality_templates');

        // 1. Quality Inspection Templates & Checklists
        Schema::create('quality_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('code', 50)->index();
            $table->string('name', 190);
            $table->enum('type', ['incoming', 'in_process', 'final_product', 'project_site'])->default('incoming');
            $table->foreignId('item_id')->nullable()->constrained('items')->nullOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // 2. Quality Template Items (Criteria & Checkpoints)
        Schema::create('quality_template_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('template_id')->constrained('quality_templates')->cascadeOnDelete();
            $table->string('checkpoint_name', 190);
            $table->enum('standard_type', ['boolean', 'numeric_range', 'text'])->default('boolean');
            $table->boolean('expected_boolean')->nullable()->default(true);
            $table->decimal('min_value', 12, 4)->nullable();
            $table->decimal('max_value', 12, 4)->nullable();
            $table->decimal('target_value', 12, 4)->nullable();
            $table->string('unit_of_measure', 50)->nullable();
            $table->string('inspection_method', 190)->nullable();
            $table->boolean('is_mandatory')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // 3. Quality Inspections Registry
        Schema::create('quality_inspections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
            $table->string('inspection_number', 60)->index();
            $table->enum('type', ['incoming', 'in_process', 'final_product', 'project_site'])->default('incoming');
            $table->foreignId('template_id')->nullable()->constrained('quality_templates')->nullOnDelete();
            $table->foreignId('item_id')->nullable()->constrained('items')->nullOnDelete();
            $table->foreignId('supplier_id')->nullable()->constrained('clients')->nullOnDelete();
            $table->foreignId('manufacturing_order_id')->nullable()->constrained('mfg_orders')->nullOnDelete();
            $table->foreignId('project_id')->nullable()->constrained('projects')->nullOnDelete();
            $table->string('batch_number', 100)->nullable();
            $table->decimal('total_quantity', 14, 3)->default(0.000);
            $table->decimal('sample_size', 14, 3)->default(0.000);
            $table->decimal('passed_quantity', 14, 3)->default(0.000);
            $table->decimal('failed_quantity', 14, 3)->default(0.000);
            $table->enum('status', ['pending', 'passed', 'rejected', 'conditionally_passed'])->default('pending');
            $table->foreignId('inspector_id')->nullable()->constrained('hr_employees')->nullOnDelete();
            $table->date('inspection_date');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // 4. Quality Inspection Results (Per-Checkpoint Observations)
        Schema::create('quality_inspection_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('inspection_id')->constrained('quality_inspections')->cascadeOnDelete();
            $table->foreignId('template_item_id')->nullable()->constrained('quality_template_items')->nullOnDelete();
            $table->string('checkpoint_name', 190);
            $table->text('standard_criteria')->nullable();
            $table->string('observed_value', 255)->nullable();
            $table->boolean('is_passed')->default(true);
            $table->text('defect_notes')->nullable();
            $table->timestamps();
        });

        // 5. Non-Conformance Reports (NCR)
        Schema::create('quality_ncrs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
            $table->string('ncr_number', 60)->index();
            $table->foreignId('inspection_id')->nullable()->constrained('quality_inspections')->nullOnDelete();
            $table->foreignId('item_id')->nullable()->constrained('items')->nullOnDelete();
            $table->foreignId('supplier_id')->nullable()->constrained('clients')->nullOnDelete();
            $table->foreignId('manufacturing_order_id')->nullable()->constrained('mfg_orders')->nullOnDelete();
            $table->foreignId('project_id')->nullable()->constrained('projects')->nullOnDelete();
            $table->string('title', 190);
            $table->text('description');
            $table->enum('severity', ['minor', 'major', 'critical'])->default('minor');
            $table->enum('root_cause_category', ['raw_material', 'machinery', 'workmanship', 'environment', 'design', 'other'])->default('raw_material');
            $table->text('root_cause_analysis')->nullable();
            $table->decimal('defect_quantity', 14, 3)->default(0.000);
            $table->decimal('estimated_loss_amount', 14, 2)->default(0.00);
            $table->enum('disposition', ['pending', 'scrap', 'rework', 'return_to_vendor', 'use_as_is'])->default('pending');
            $table->text('disposition_notes')->nullable();
            $table->enum('status', ['open', 'under_investigation', 'action_taken', 'closed'])->default('open');
            $table->foreignId('reported_by')->nullable()->constrained('hr_employees')->nullOnDelete();
            $table->dateTime('closed_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // 6. Corrective & Preventive Actions (CAPA)
        Schema::create('quality_capas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('capa_number', 60)->index();
            $table->foreignId('ncr_id')->constrained('quality_ncrs')->cascadeOnDelete();
            $table->enum('type', ['corrective', 'preventive'])->default('corrective');
            $table->string('title', 190);
            $table->text('action_plan');
            $table->foreignId('assigned_to')->nullable()->constrained('hr_employees')->nullOnDelete();
            $table->date('due_date');
            $table->date('completion_date')->nullable();
            $table->text('effectiveness_criteria')->nullable();
            $table->enum('effectiveness_status', ['pending_evaluation', 'effective', 'ineffective'])->default('pending_evaluation');
            $table->enum('status', ['pending', 'in_progress', 'completed', 'verified_closed'])->default('pending');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quality_capas');
        Schema::dropIfExists('quality_ncrs');
        Schema::dropIfExists('quality_inspection_results');
        Schema::dropIfExists('quality_inspections');
        Schema::dropIfExists('quality_template_items');
        Schema::dropIfExists('quality_templates');
    }
};
