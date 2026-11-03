<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations for Module 9: Human Resources, Employees & Payroll.
     */
    public function up(): void
    {
        // 1. Departments (الأقسام)
        Schema::create('hr_departments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->default(1)->index('hr_dept_tenant_idx');
            $table->string('name', 100);
            $table->string('code', 50)->nullable();
            $table->string('manager_name', 100)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. Designations (المسميات والوظائف)
        Schema::create('hr_designations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->default(1)->index('hr_desig_tenant_idx');
            $table->unsignedBigInteger('department_id')->nullable();
            $table->string('title', 100);
            $table->string('description', 255)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('department_id')->references('id')->on('hr_departments')->nullOnDelete();
        });

        // 3. Employees (سجلات وبيانات الموظفين)
        Schema::create('hr_employees', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->default(1)->index('hr_emp_tenant_idx');
            $table->unsignedBigInteger('branch_id')->nullable()->index('hr_emp_branch_idx');
            $table->unsignedBigInteger('department_id')->nullable();
            $table->unsignedBigInteger('designation_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('emp_code', 50);
            $table->string('name', 150);
            $table->string('national_id', 50)->nullable();
            $table->string('phone', 50)->nullable();
            $table->string('email', 100)->nullable();
            $table->date('hire_date');
            $table->date('birth_date')->nullable();
            $table->enum('contract_type', ['full_time', 'part_time', 'probation', 'contract'])->default('full_time');
            $table->decimal('basic_salary', 15, 2)->default(0.00);
            $table->decimal('housing_allowance', 15, 2)->default(0.00);
            $table->decimal('transport_allowance', 15, 2)->default(0.00);
            $table->decimal('other_allowances', 15, 2)->default(0.00);
            $table->string('bank_name', 100)->nullable();
            $table->string('bank_account', 100)->nullable();
            $table->string('iban', 100)->nullable();
            $table->enum('status', ['active', 'on_leave', 'suspended', 'terminated'])->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('branch_id')->references('id')->on('branches')->nullOnDelete();
            $table->foreign('department_id')->references('id')->on('hr_departments')->nullOnDelete();
            $table->foreign('designation_id')->references('id')->on('hr_designations')->nullOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();

            $table->unique(['tenant_id', 'emp_code'], 'hr_emp_tenant_code_unique');
        });

        // 4. Salary Clauses (بنود البدلات والخصومات)
        Schema::create('hr_salary_clauses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->default(1)->index('hr_clauses_tenant_idx');
            $table->string('name', 100);
            $table->enum('type', ['earning', 'deduction']);
            $table->boolean('is_fixed')->default(false);
            $table->decimal('default_amount', 15, 2)->default(0.00);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 5. Employee Loans / Advances (سلف الموظفين)
        Schema::create('hr_loans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->default(1)->index('hr_loans_tenant_idx');
            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('treasury_id')->nullable();
            $table->string('loan_number', 50);
            $table->date('date');
            $table->decimal('amount', 15, 2);
            $table->decimal('monthly_installment', 15, 2);
            $table->unsignedSmallInteger('total_installments')->default(1);
            $table->decimal('paid_amount', 15, 2)->default(0.00);
            $table->enum('status', ['pending', 'active', 'paid', 'cancelled'])->default('active');
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('employee_id')->references('id')->on('hr_employees')->cascadeOnDelete();
            $table->foreign('treasury_id')->references('id')->on('treasuries')->nullOnDelete();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
        });

        // 6. Leaves (إدارة الإجازات)
        Schema::create('hr_leaves', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->default(1)->index('hr_leaves_tenant_idx');
            $table->unsignedBigInteger('employee_id');
            $table->enum('leave_type', ['annual', 'sick', 'emergency', 'unpaid'])->default('annual');
            $table->date('start_date');
            $table->date('end_date');
            $table->unsignedSmallInteger('days_count')->default(1);
            $table->string('reason', 255)->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('approved');
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamps();

            $table->foreign('employee_id')->references('id')->on('hr_employees')->cascadeOnDelete();
            $table->foreign('approved_by')->references('id')->on('users')->nullOnDelete();
        });

        // 7. Attendances (الحضور والانصراف والدوام)
        Schema::create('hr_attendances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->default(1)->index('hr_att_tenant_idx');
            $table->unsignedBigInteger('employee_id');
            $table->date('date');
            $table->time('check_in')->nullable();
            $table->time('check_out')->nullable();
            $table->enum('status', ['present', 'absent', 'late', 'on_leave'])->default('present');
            $table->unsignedSmallInteger('late_minutes')->default(0);
            $table->decimal('overtime_hours', 5, 2)->default(0.00);
            $table->string('notes', 255)->nullable();
            $table->timestamps();

            $table->foreign('employee_id')->references('id')->on('hr_employees')->cascadeOnDelete();

            $table->unique(['tenant_id', 'employee_id', 'date'], 'hr_att_tenant_emp_date_unique');
        });

        // 8. Payroll Batches (مسيرات الرواتب الشهرية)
        Schema::create('hr_payrolls', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->default(1)->index('hr_pay_tenant_idx');
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->unsignedBigInteger('treasury_id')->nullable();
            $table->string('payroll_number', 50);
            $table->unsignedSmallInteger('year');
            $table->unsignedTinyInteger('month');
            $table->enum('status', ['draft', 'approved', 'paid'])->default('draft');
            $table->date('payment_date')->nullable();
            $table->decimal('total_basic', 15, 2)->default(0.00);
            $table->decimal('total_allowances', 15, 2)->default(0.00);
            $table->decimal('total_deductions', 15, 2)->default(0.00);
            $table->decimal('total_loans_deducted', 15, 2)->default(0.00);
            $table->decimal('total_net', 15, 2)->default(0.00);
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamps();

            $table->foreign('branch_id')->references('id')->on('branches')->nullOnDelete();
            $table->foreign('treasury_id')->references('id')->on('treasuries')->nullOnDelete();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('approved_by')->references('id')->on('users')->nullOnDelete();

            $table->unique(['tenant_id', 'branch_id', 'year', 'month'], 'hr_pay_tenant_branch_ym_unique');
        });

        // 9. Payroll Items (قسائم وبنود رواتب الموظفين في المسير)
        Schema::create('hr_payroll_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->default(1)->index('hr_pay_items_tenant_idx');
            $table->unsignedBigInteger('payroll_id');
            $table->unsignedBigInteger('employee_id');
            $table->decimal('basic_salary', 15, 2)->default(0.00);
            $table->decimal('allowances_amount', 15, 2)->default(0.00);
            $table->decimal('overtime_amount', 15, 2)->default(0.00);
            $table->decimal('deductions_amount', 15, 2)->default(0.00);
            $table->decimal('loan_deduction', 15, 2)->default(0.00);
            $table->decimal('net_salary', 15, 2)->default(0.00);
            $table->enum('status', ['pending', 'paid'])->default('pending');
            $table->enum('payment_method', ['cash', 'bank'])->default('cash');
            $table->string('notes', 255)->nullable();
            $table->timestamps();

            $table->foreign('payroll_id')->references('id')->on('hr_payrolls')->cascadeOnDelete();
            $table->foreign('employee_id')->references('id')->on('hr_employees')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hr_payroll_items');
        Schema::dropIfExists('hr_payrolls');
        Schema::dropIfExists('hr_attendances');
        Schema::dropIfExists('hr_leaves');
        Schema::dropIfExists('hr_loans');
        Schema::dropIfExists('hr_salary_clauses');
        Schema::dropIfExists('hr_employees');
        Schema::dropIfExists('hr_designations');
        Schema::dropIfExists('hr_departments');
    }
};

