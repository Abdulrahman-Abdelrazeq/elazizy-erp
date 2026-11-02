<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations for Module 2 (Inventory, Units, Items, Stores)
     */
    public function up(): void
    {
        // 1. Units of Measurement (دليل وحدات القياس)
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('name'); // قطعة، كرتونة، كجم، لتر، متر...
            $table->string('code', 20)->nullable(); // PCS, CTN, KG...
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. Categories (أقسام وتصنيفات الأصناف)
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->string('name');
            $table->string('code', 50)->nullable();
            $table->string('image')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 3. Brands (الماركات والعلامات التجارية)
        Schema::create('brands', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('name');
            $table->string('logo')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 4. Warehouses / Stores (المستودعات والمخازن)
        Schema::create('stores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->string('name'); // مستودع الرياض الرئيسي، مستودع الخامات...
            $table->string('code', 50)->nullable();
            $table->string('keeper_name')->nullable(); // أمين المستودع
            $table->string('phone', 50)->nullable();
            $table->string('address')->nullable();
            $table->boolean('is_main')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 5. Items Catalog (دليل الأصناف والمنتجات)
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('name'); // اسم الصنف
            $table->string('sku', 100)->nullable(); // كود الصنف / SKU
            $table->string('barcode', 100)->nullable(); // الباركود
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->foreignId('brand_id')->nullable()->constrained('brands')->nullOnDelete();
            $table->foreignId('unit_id')->nullable()->constrained('units')->nullOnDelete(); // الوحدة الأساسية
            $table->foreignId('tax_id')->nullable()->constrained('taxes')->nullOnDelete();

            // Cost & Sale Prices (الأسعار والتكلفة)
            $table->decimal('buy_price', 12, 2)->default(0.00); // سعر الشراء الأساسي
            $table->decimal('avg_cost', 12, 2)->default(0.00); // متوسط التكلفة المرجح
            $table->decimal('sell_price', 12, 2)->default(0.00); // سعر البيع قطاعي (تجزئة)
            $table->decimal('wholesale_price', 12, 2)->default(0.00); // سعر البيع جملة
            $table->decimal('half_wholesale_price', 12, 2)->default(0.00); // سعر البيع نصف جملة
            $table->decimal('minimum_sell_price', 12, 2)->default(0.00); // أدنى سعر بيع مسموح به للكاشير

            // Inventory & Alerts (المخزون وحد الطلب)
            $table->decimal('min_quantity_alert', 10, 2)->default(5.00); // حد الطلب
            $table->boolean('is_service')->default(false); // صنف خدمي (لا يخصم من المخزون)
            $table->boolean('has_expiration')->default(false); // له تاريخ صلاحية
            $table->date('expire_date')->nullable(); // تاريخ الصلاحية
            $table->string('image')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['tenant_id', 'barcode']);
            $table->index(['tenant_id', 'sku']);
        });

        // 6. Item Additional Units (الوحدات الإضافية ومعاملات التحويل)
        Schema::create('item_units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('item_id')->constrained('items')->cascadeOnDelete();
            $table->foreignId('unit_id')->constrained('units')->cascadeOnDelete();
            $table->decimal('multiplier', 10, 3)->default(1.000); // معامل التحويل (مثلاً الكرتونة = 24 قطعة)
            $table->decimal('buy_price', 12, 2)->default(0.00);
            $table->decimal('sell_price', 12, 2)->default(0.00);
            $table->string('barcode', 100)->nullable();
            $table->timestamps();
        });

        // 7. Item Stocks per Store (أرصدة الأصناف في كل مستودع)
        Schema::create('item_stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('item_id')->constrained('items')->cascadeOnDelete();
            $table->foreignId('store_id')->constrained('stores')->cascadeOnDelete();
            $table->decimal('quantity', 12, 3)->default(0.000);
            $table->decimal('avg_cost', 12, 2)->default(0.00);
            $table->timestamps();

            $table->unique(['tenant_id', 'item_id', 'store_id']);
        });

        // 8. Stock Transfers (التحويلات بين المستودعات)
        Schema::create('stock_transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('transfer_number', 50); // TR-0001
            $table->foreignId('from_store_id')->constrained('stores')->cascadeOnDelete();
            $table->foreignId('to_store_id')->constrained('stores')->cascadeOnDelete();
            $table->date('date');
            $table->string('status', 30)->default('completed'); // pending, completed, cancelled
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // 9. Stock Transfer Items (أصناف التحويل)
        Schema::create('stock_transfer_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_transfer_id')->constrained('stock_transfers')->cascadeOnDelete();
            $table->foreignId('item_id')->constrained('items')->cascadeOnDelete();
            $table->foreignId('unit_id')->nullable()->constrained('units')->nullOnDelete();
            $table->decimal('quantity', 12, 3);
            $table->timestamps();
        });

        // 10. Stock Adjustments (تسويات الجرد والعجز والتالف)
        Schema::create('stock_adjustments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('adjustment_number', 50); // ADJ-0001
            $table->foreignId('store_id')->constrained('stores')->cascadeOnDelete();
            $table->enum('type', ['addition', 'deficit', 'damage'])->default('damage'); // زيادة جرد، عجز جرد، هالك
            $table->date('date');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // 11. Stock Adjustment Items (أصناف التسوية)
        Schema::create('stock_adjustment_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_adjustment_id')->constrained('stock_adjustments')->cascadeOnDelete();
            $table->foreignId('item_id')->constrained('items')->cascadeOnDelete();
            $table->decimal('quantity', 12, 3);
            $table->decimal('cost', 12, 2)->default(0.00);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_adjustment_items');
        Schema::dropIfExists('stock_adjustments');
        Schema::dropIfExists('stock_transfer_items');
        Schema::dropIfExists('stock_transfers');
        Schema::dropIfExists('item_stocks');
        Schema::dropIfExists('item_units');
        Schema::dropIfExists('items');
        Schema::dropIfExists('stores');
        Schema::dropIfExists('brands');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('units');
    }
};

