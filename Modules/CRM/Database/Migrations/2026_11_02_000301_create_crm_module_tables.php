<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations for Module 3 (CRM: Customers, Suppliers, Groups, Debts)
     */
    public function up(): void
    {
        // 1. Client Groups & Pricing Tiers (مجموعات وشرائح تصنيف العملاء)
        Schema::create('client_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('name'); // عملاء قطاعي، عملاء جملة، موزعين، VIP...
            $table->enum('price_tier', ['retail', 'wholesale', 'half_wholesale'])->default('retail');
            $table->decimal('discount_percentage', 5, 2)->default(0.00); // نسبة خصم تلقائية
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. Clients & Suppliers (العملاء والموردين)
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('group_id')->nullable()->constrained('client_groups')->nullOnDelete();
            $table->enum('type', ['customer', 'supplier', 'both'])->default('customer');
            $table->string('name'); // اسم العميل أو المورد
            $table->string('code', 50)->nullable(); // كود الحساب
            $table->string('company_name')->nullable(); // اسم المؤسسة / الشركة
            $table->string('phone', 50)->nullable();
            $table->string('mobile', 50)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('tax_number', 100)->nullable(); // الرقم الضريبي VAT
            $table->string('commercial_register', 100)->nullable(); // السجل التجاري CR
            $table->string('national_id', 50)->nullable(); // الهوية الوطنية / الإقامة
            $table->foreignId('location_id')->nullable()->constrained('locations')->nullOnDelete();
            $table->string('address')->nullable();

            // Credit & Balances (الائتمان والرصيد)
            $table->decimal('credit_limit', 12, 2)->default(0.00); // الحد الائتماني المسموح به
            $table->unsignedInteger('credit_days')->default(30); // فترة السداد بالأيام
            $table->decimal('opening_balance', 12, 2)->default(0.00); // رصيد أول المدة
            $table->enum('opening_balance_type', ['debit', 'credit'])->default('debit'); // مدين (عليه) أو دائن (له)
            $table->decimal('current_balance', 14, 2)->default(0.00); // الرصيد الحالي الصافي
            $table->unsignedInteger('loyalty_points')->default(0); // نقاط الولاء

            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['tenant_id', 'type']);
            $table->index(['tenant_id', 'phone']);
            $table->index(['tenant_id', 'tax_number']);
        });

        // 3. Client Interaction Notes (ملاحظات وسجل اتصالات العملاء والموردين)
        Schema::create('client_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
            $table->enum('type', ['call', 'visit', 'general', 'complaint'])->default('general');
            $table->text('note');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // 4. Debt Schedules & Installments (جدولة الديون والأقساط)
        Schema::create('debt_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
            $table->decimal('amount', 12, 2);
            $table->decimal('paid_amount', 12, 2)->default(0.00);
            $table->date('due_date');
            $table->date('paid_at')->nullable();
            $table->enum('status', ['pending', 'paid', 'overdue'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 5. Balance Adjustments & Write-Offs (شطب وتسويات الديون والأرصدة)
        Schema::create('balance_adjustments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
            $table->enum('type', ['write_off', 'discount', 'opening_diff'])->default('discount');
            $table->decimal('amount', 12, 2);
            $table->date('date');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('balance_adjustments');
        Schema::dropIfExists('debt_schedules');
        Schema::dropIfExists('client_notes');
        Schema::dropIfExists('clients');
        Schema::dropIfExists('client_groups');
    }
};
