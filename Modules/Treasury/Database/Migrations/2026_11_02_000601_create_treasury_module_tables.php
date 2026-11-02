<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations for Module 6: Treasury, Banks, Vouchers & Expenses.
     */
    public function up(): void
    {
        // 1. Treasuries & Bank Accounts (الخزائن والحسابات البنكية والمحافظ الإلكترونية)
        Schema::create('treasuries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name', 150);
            $table->enum('type', ['cash', 'bank', 'wallet'])->default('cash');
            $table->string('account_number', 100)->nullable();
            $table->string('bank_name', 150)->nullable();
            $table->string('bank_branch', 150)->nullable();
            $table->string('iban', 100)->nullable();
            $table->string('swift', 50)->nullable();
            $table->decimal('opening_balance', 15, 2)->default(0.00);
            $table->decimal('current_balance', 15, 2)->default(0.00);
            $table->string('currency', 10)->default('EGP');
            $table->boolean('is_active')->default(true);
            $table->boolean('is_default')->default(false);
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['tenant_id', 'type', 'is_active'], 'treasuries_tenant_type_idx');
        });

        // 2. Expense & Revenue Categories (بنود وتصنيفات المصروفات والإيرادات الأخرى)
        Schema::create('expense_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('name', 150);
            $table->string('code', 50)->nullable();
            $table->enum('type', ['expense', 'revenue'])->default('expense');
            $table->foreignId('parent_id')->nullable()->constrained('expense_categories')->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'type', 'is_active'], 'expense_cat_tenant_type_idx');
        });

        // 3. Cash & Bank Vouchers (سندات القبض والصرف)
        Schema::create('treasury_vouchers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('treasury_id')->constrained('treasuries')->cascadeOnDelete();
            $table->string('voucher_number', 50);
            $table->enum('type', ['receipt', 'payment']); // receipt = قبض, payment = صرف
            $table->foreignId('category_id')->nullable()->constrained('expense_categories')->nullOnDelete();
            $table->foreignId('client_id')->nullable()->constrained('clients')->nullOnDelete();
            $table->foreignId('invoice_id')->nullable()->constrained('invoices')->nullOnDelete();
            $table->decimal('amount', 15, 2);
            $table->date('date');
            $table->enum('payment_method', ['cash', 'bank_transfer', 'check', 'online', 'card'])->default('cash');
            $table->string('reference_number', 100)->nullable(); // رقم الشيك أو الحوالة
            $table->string('received_from_or_paid_to', 200)->nullable();
            $table->text('notes')->nullable();
            $table->string('attachment', 255)->nullable();
            $table->enum('status', ['approved', 'draft', 'cancelled'])->default('approved');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['tenant_id', 'type', 'voucher_number'], 'vouchers_tenant_type_num_unique');
            $table->index(['tenant_id', 'treasury_id', 'type', 'date'], 'vouchers_tenant_search_idx');
        });

        // 4. Internal Treasury & Bank Transfers (التحويلات النقدية والبنكية بين الخزائن)
        Schema::create('treasury_transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('transfer_number', 50);
            $table->foreignId('from_treasury_id')->constrained('treasuries')->cascadeOnDelete();
            $table->foreignId('to_treasury_id')->constrained('treasuries')->cascadeOnDelete();
            $table->decimal('amount', 15, 2);
            $table->decimal('transfer_fee', 15, 2)->default(0.00);
            $table->date('transfer_date');
            $table->text('notes')->nullable();
            $table->enum('status', ['completed', 'pending', 'cancelled'])->default('completed');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['tenant_id', 'transfer_number'], 'transfers_tenant_num_unique');
            $table->index(['tenant_id', 'transfer_date'], 'transfers_tenant_date_idx');
        });

        // 5. Treasury Statements & Cash Flow Transactions (سجل التدفق وحركات الخزينة التفصيلية)
        Schema::create('treasury_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('treasury_id')->constrained('treasuries')->cascadeOnDelete();
            $table->enum('type', ['in', 'out']); // in = إيداع/قبض, out = صرف/سحب
            $table->decimal('amount', 15, 2);
            $table->decimal('balance_before', 15, 2);
            $table->decimal('balance_after', 15, 2);
            $table->enum('source_type', ['voucher', 'invoice', 'pos_shift', 'transfer', 'opening'])->default('voucher');
            $table->unsignedBigInteger('source_id')->nullable();
            $table->dateTime('transaction_date');
            $table->string('description', 255);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['tenant_id', 'treasury_id', 'transaction_date'], 'treasury_tx_tenant_date_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('treasury_transactions');
        Schema::dropIfExists('treasury_transfers');
        Schema::dropIfExists('treasury_vouchers');
        Schema::dropIfExists('expense_categories');
        Schema::dropIfExists('treasuries');
    }
};
