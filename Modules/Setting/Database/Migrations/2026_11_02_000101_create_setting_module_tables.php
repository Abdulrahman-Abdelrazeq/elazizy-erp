<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations for Module 1 (Setting & Location)
     */
    public function up(): void
    {
        // 1. Company & System Operational Settings (مفتاح وقيمة لكل شركة)
        Schema::create('company_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('key', 100);
            $table->longText('value')->nullable();
            $table->string('group', 50)->default('general'); // company, general, invoice, sales, pos, inventory, hr
            $table->timestamps();

            $table->unique(['tenant_id', 'key']);
        });

        // 2. Taxes / VAT (الضرائب وضريبة القيمة المضافة)
        Schema::create('taxes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('name');
            $table->decimal('rate', 6, 2)->default(15.00);
            $table->string('code', 20)->nullable();
            $table->enum('type', ['percentage', 'fixed'])->default('percentage');
            $table->enum('applies_to', ['all', 'sales', 'purchases'])->default('all');
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 3. Currencies (دليل العملات وأسعار الصرف)
        Schema::create('currencies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('name');
            $table->string('code', 10); // SAR, EGP, USD, AED, etc.
            $table->string('symbol', 15); // ر.س, ج.م, $, etc.
            $table->decimal('exchange_rate', 12, 4)->default(1.0000);
            $table->string('subunit', 50)->nullable(); // هللة, قرش, سنت
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 4. Locations (شجرة المناطق الجغرافية: دول، مدن، أحياء/مناطق شحن)
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('locations')->nullOnDelete();
            $table->string('name');
            $table->enum('type', ['country', 'city', 'zone'])->default('city');
            $table->decimal('delivery_cost', 10, 2)->default(0.00);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 5. Print Templates (قوالب طباعة الفواتير والإيصالات)
        Schema::create('print_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('title');
            $table->string('invoice_type', 30)->default('sales'); // sales, purchases, pos, quotation
            $table->enum('paper_size', ['A4', 'A5', 'thermal_80mm', 'thermal_57mm'])->default('thermal_80mm');
            $table->boolean('show_logo')->default(true);
            $table->boolean('show_qr')->default(true);
            $table->boolean('show_tax_number')->default(true);
            $table->boolean('show_terms')->default(true);
            $table->text('terms_and_conditions')->nullable();
            $table->text('footer_text')->nullable();
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });

        // 6. Barcode Settings (إعدادات طباعة ملصقات الباركود)
        Schema::create('barcode_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('name')->default('الافتراضي (38x25 مم)');
            $table->string('barcode_type', 30)->default('code128'); // code128, ean13, qr
            $table->unsignedInteger('label_width')->default(38); // mm
            $table->unsignedInteger('label_height')->default(25); // mm
            $table->boolean('show_product_name')->default(true);
            $table->boolean('show_price')->default(true);
            $table->boolean('show_company_name')->default(true);
            $table->boolean('show_barcode_number')->default(true);
            $table->boolean('is_default')->default(true);
            $table->timestamps();
        });

        // 7. Guarantees (الضمانات وسياسات الصيانة)
        Schema::create('guarantees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('title');
            $table->unsignedInteger('duration_days')->default(365);
            $table->text('policy')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guarantees');
        Schema::dropIfExists('barcode_settings');
        Schema::dropIfExists('print_templates');
        Schema::dropIfExists('locations');
        Schema::dropIfExists('currencies');
        Schema::dropIfExists('taxes');
        Schema::dropIfExists('company_settings');
    }
};

