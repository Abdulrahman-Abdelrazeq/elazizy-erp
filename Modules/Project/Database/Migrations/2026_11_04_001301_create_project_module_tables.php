<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations for Module 13: Project Management, Contracting & Progressive Billing.
     */
    public function up(): void
    {
        // 1. Projects & Contracting Registry (سجل المشاريع وعقود المقاولات)
        if (!Schema::hasTable('projects')) {
            Schema::create('projects', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
                $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
                $table->foreignId('cost_center_id')->nullable()->constrained('cost_centers')->nullOnDelete();
                $table->foreignId('project_manager_id')->nullable()->constrained('hr_employees')->nullOnDelete();

                $table->string('code', 50); // e.g. PRJ-2026-0001
                $table->string('name', 190);
                $table->string('contract_number', 100)->nullable();
                $table->date('contract_date')->nullable();
                $table->date('start_date');
                $table->date('expected_end_date');
                $table->date('actual_end_date')->nullable();

                $table->decimal('contract_value', 15, 2); // Initial Contract Sum
                $table->decimal('revised_value', 15, 2);  // Contract Sum after Change Orders
                $table->decimal('advance_payment_percentage', 5, 2)->default(10.00);
                $table->decimal('advance_payment_amount', 15, 2)->default(0.00);
                $table->decimal('retention_percentage', 5, 2)->default(5.00);
                $table->decimal('retention_amount', 15, 2)->default(0.00);
                $table->decimal('completion_percentage', 5, 2)->default(0.00); // 0.00 to 100.00%

                $table->enum('status', ['planning', 'in_progress', 'suspended', 'delivered_primary', 'completed', 'cancelled'])->default('planning');
                $table->string('location', 190)->nullable();
                $table->text('description')->nullable();
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();

                $table->unique(['tenant_id', 'code']);
                $table->index(['tenant_id', 'branch_id', 'status']);
                $table->index(['tenant_id', 'client_id']);
            });
        }

        // 2. Project Milestones & Phases (المراحل الزمنية ومحطات الإنجاز)
        if (!Schema::hasTable('project_milestones')) {
            Schema::create('project_milestones', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();

                $table->string('title', 150);
                $table->text('description')->nullable();
                $table->date('start_date');
                $table->date('end_date');
                $table->decimal('weight_percentage', 5, 2)->default(0.00); // Weight % of project total
                $table->decimal('completion_percentage', 5, 2)->default(0.00);
                $table->enum('status', ['pending', 'in_progress', 'completed', 'delayed'])->default('pending');
                $table->text('deliverables')->nullable();
                $table->timestamps();

                $table->index(['tenant_id', 'project_id', 'status']);
            });
        }

        // 3. Bill of Quantities / BOQ Items (جدول الكميات وبنود المقايسة المسعرة)
        if (!Schema::hasTable('project_boq_items')) {
            Schema::create('project_boq_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();

                $table->string('item_code', 50)->nullable();
                $table->text('description');
                $table->string('unit', 50); // م3، م2، م.ط، نقطة، طن، مقطوعية
                $table->decimal('contract_quantity', 12, 3);
                $table->decimal('revised_quantity', 12, 3);
                $table->decimal('unit_price', 15, 2);
                $table->decimal('total_price', 15, 2);
                $table->decimal('executed_quantity', 12, 3)->default(0.000);
                $table->decimal('completion_percentage', 5, 2)->default(0.00);
                $table->timestamps();

                $table->index(['tenant_id', 'project_id']);
            });
        }

        // 4. Change Orders (أوامر التغيير والملحقات التعاقدية)
        if (!Schema::hasTable('project_change_orders')) {
            Schema::create('project_change_orders', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();

                $table->string('order_number', 50); // e.g. CO-001
                $table->string('title', 150);
                $table->date('request_date');
                $table->date('approval_date')->nullable();
                $table->decimal('amount_change', 15, 2); // can be positive or negative
                $table->integer('time_extension_days')->default(0);
                $table->enum('status', ['draft', 'approved', 'rejected'])->default('draft');
                $table->text('justification')->nullable();
                $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();

                $table->index(['tenant_id', 'project_id', 'status']);
            });
        }

        // 5. Progressive Billing / Contractor Extracts (المستخلصات الجارية والختامية)
        if (!Schema::hasTable('project_extracts')) {
            Schema::create('project_extracts', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();

                $table->string('extract_number', 50); // e.g. EXT-2026-001
                $table->enum('extract_type', ['advance', 'progress', 'final'])->default('progress');
                $table->date('period_from');
                $table->date('period_to');
                $table->date('issue_date');

                $table->decimal('gross_amount', 15, 2)->default(0.00);          // Cumulative gross work to date
                $table->decimal('previous_gross_amount', 15, 2)->default(0.00); // Gross work in previous extracts
                $table->decimal('current_gross_amount', 15, 2)->default(0.00);  // Gross work in this extract

                $table->decimal('advance_payment_deduction', 15, 2)->default(0.00); // استقطاع رد الدفعة المقدمة
                $table->decimal('retention_deduction', 15, 2)->default(0.00);       // استقطاع ضمان الأعمال
                $table->decimal('other_deductions', 15, 2)->default(0.00);          // غرامات / خصومات أخرى
                $table->decimal('net_subtotal', 15, 2)->default(0.00);              // الصافي قبل الضريبة

                $table->decimal('tax_rate', 5, 2)->default(15.00);
                $table->decimal('tax_amount', 15, 2)->default(0.00);
                $table->decimal('net_amount', 15, 2)->default(0.00);                // الصافي النهائي المستحق للصرف
                $table->decimal('paid_amount', 15, 2)->default(0.00);

                $table->enum('status', ['draft', 'submitted', 'approved', 'paid', 'rejected'])->default('draft');
                $table->string('consultant_engineer_name', 150)->nullable();
                $table->text('notes')->nullable();
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();

                $table->unique(['tenant_id', 'extract_number']);
                $table->index(['tenant_id', 'project_id', 'status']);
            });
        }

        // 6. Extract Line Items (بنود وجدول كميات المستخلص)
        if (!Schema::hasTable('project_extract_items')) {
            Schema::create('project_extract_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->foreignId('extract_id')->constrained('project_extracts')->cascadeOnDelete();
                $table->foreignId('boq_item_id')->nullable()->constrained('project_boq_items')->nullOnDelete();

                $table->text('item_description');
                $table->string('unit', 50);
                $table->decimal('unit_price', 15, 2);
                $table->decimal('previous_quantity', 12, 3)->default(0.000);
                $table->decimal('current_quantity', 12, 3)->default(0.000);
                $table->decimal('total_quantity', 12, 3)->default(0.000); // previous + current
                $table->decimal('current_amount', 15, 2)->default(0.00);
                $table->decimal('total_amount', 15, 2)->default(0.00);
                $table->decimal('completion_percentage', 5, 2)->default(0.00);
                $table->timestamps();

                $table->index(['tenant_id', 'extract_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_extract_items');
        Schema::dropIfExists('project_extracts');
        Schema::dropIfExists('project_change_orders');
        Schema::dropIfExists('project_boq_items');
        Schema::dropIfExists('project_milestones');
        Schema::dropIfExists('projects');
    }
};
