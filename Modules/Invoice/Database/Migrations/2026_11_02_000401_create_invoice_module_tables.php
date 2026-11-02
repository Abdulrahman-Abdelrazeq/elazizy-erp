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
        // 1. Invoices (الفواتير، عروض الأسعار، وأوامر الشراء)
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->foreignId('store_id')->nullable()->constrained('stores')->nullOnDelete();
            $table->foreignId('client_id')->nullable()->constrained('clients')->nullOnDelete();
            $table->string('invoice_number', 50)->index();
            $table->enum('type', [
                'sale',             // فاتورة مبيعات
                'purchase',         // فاتورة مشتريات
                'sale_return',      // مردود مبيعات
                'purchase_return',  // مردود مشتريات
                'quotation',        // عرض سعر
                'purchase_order',   // أمر شراء
            ])->default('sale')->index();
            $table->date('date')->index();
            $table->date('due_date')->nullable();
            $table->enum('status', ['draft', 'approved', 'closed', 'cancelled'])->default('approved');
            $table->enum('payment_type', ['cash', 'credit', 'partial', 'card', 'bank_transfer'])->default('cash');
            
            // Financial Calculations
            $table->decimal('subtotal', 12, 2)->default(0.00);
            $table->enum('discount_type', ['fixed', 'percentage'])->default('fixed');
            $table->decimal('discount_value', 10, 2)->default(0.00);
            $table->decimal('discount_amount', 12, 2)->default(0.00);
            
            $table->foreignId('tax_id')->nullable()->constrained('taxes')->nullOnDelete();
            $table->decimal('tax_rate', 6, 2)->default(0.00);
            $table->decimal('tax_amount', 12, 2)->default(0.00);
            
            $table->decimal('shipping_cost', 10, 2)->default(0.00);
            $table->decimal('grand_total', 12, 2)->default(0.00);
            $table->decimal('paid_amount', 12, 2)->default(0.00);
            $table->decimal('remaining_amount', 12, 2)->default(0.00);

            $table->text('notes')->nullable();
            $table->text('terms_conditions')->nullable();
            
            // Parent invoice (e.g. for returns or quotes converted to sales)
            $table->foreignId('parent_id')->nullable()->constrained('invoices')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
            $table->unique(['tenant_id', 'type', 'invoice_number']);
        });

        // 2. Invoice Items (بنود وأصناف الفاتورة)
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('invoice_id')->constrained('invoices')->cascadeOnDelete();
            $table->foreignId('item_id')->constrained('items')->cascadeOnDelete();
            $table->foreignId('unit_id')->nullable()->constrained('units')->nullOnDelete();
            $table->decimal('unit_multiplier', 8, 2)->default(1.00);
            $table->decimal('quantity', 10, 2)->default(1.00);
            $table->decimal('unit_price', 12, 2)->default(0.00);
            $table->decimal('cost_price', 12, 2)->default(0.00); // for profit margin calculation
            
            $table->decimal('discount_percent', 5, 2)->default(0.00);
            $table->decimal('discount_amount', 10, 2)->default(0.00);
            $table->decimal('subtotal', 12, 2)->default(0.00);
            
            $table->decimal('tax_percent', 5, 2)->default(0.00);
            $table->decimal('tax_amount', 10, 2)->default(0.00);
            $table->decimal('total', 12, 2)->default(0.00);
            
            $table->string('batch_number', 50)->nullable();
            $table->date('expiry_date')->nullable();
            $table->string('notes')->nullable();
            $table->timestamps();
        });

        // 3. Invoice Payments (دفعات وسدادات الفاتورة)
        Schema::create('invoice_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('invoice_id')->constrained('invoices')->cascadeOnDelete();
            $table->enum('payment_method', ['cash', 'card', 'bank_transfer', 'cheque'])->default('cash');
            $table->decimal('amount', 12, 2);
            $table->date('payment_date');
            $table->string('reference_number', 100)->nullable();
            $table->string('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_payments');
        Schema::dropIfExists('invoice_items');
        Schema::dropIfExists('invoices');
    }
};

