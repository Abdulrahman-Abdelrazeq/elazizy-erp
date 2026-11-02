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
        // 1. POS Terminals (أجهزة ونقاط البيع)
        Schema::create('pos_terminals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->foreignId('store_id')->nullable()->constrained('stores')->nullOnDelete();
            $table->string('name', 100);
            $table->string('code', 50)->nullable();
            $table->enum('printer_type', ['thermal_80mm', 'thermal_57mm', 'a4'])->default('thermal_80mm');
            $table->foreignId('default_client_id')->nullable()->constrained('clients')->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. Cashier Shifts (وردات وشيفتات الكاشير وإدارة الدرج)
        Schema::create('cashier_shifts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->foreignId('terminal_id')->constrained('pos_terminals')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('shift_number', 50)->index();
            $table->timestamp('started_at');
            $table->timestamp('ended_at')->nullable();
            $table->enum('status', ['open', 'closed'])->default('open')->index();
            
            // Drawer Cash Flow
            $table->decimal('opening_balance', 12, 2)->default(0.00); // العهدة الافتتاحية في الدرج
            $table->decimal('cash_sales', 12, 2)->default(0.00);
            $table->decimal('card_sales', 12, 2)->default(0.00);
            $table->decimal('credit_sales', 12, 2)->default(0.00);
            $table->decimal('total_returns', 12, 2)->default(0.00);
            
            $table->decimal('expected_cash', 12, 2)->default(0.00); // النقدية المتوقعة دفترياً
            $table->decimal('actual_cash', 12, 2)->nullable();       // النقدية الفعلية المحصاة
            $table->decimal('cash_difference', 12, 2)->nullable();   // الفرق (عجز بالسالب أو زيادة بالموجب)
            
            $table->text('start_notes')->nullable();
            $table->text('end_notes')->nullable();
            $table->foreignId('closed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // 3. Link shift_id to invoices if not present
        if (Schema::hasTable('invoices') && !Schema::hasColumn('invoices', 'shift_id')) {
            Schema::table('invoices', function (Blueprint $table) {
                $table->foreignId('shift_id')->nullable()->after('branch_id')->constrained('cashier_shifts')->nullOnDelete();
            });
        }

        // 4. POS Held Orders (الفواتير المعلقة / انتظار الزبائن)
        Schema::create('pos_held_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('shift_id')->nullable()->constrained('cashier_shifts')->cascadeOnDelete();
            $table->foreignId('terminal_id')->nullable()->constrained('pos_terminals')->nullOnDelete();
            $table->string('reference_code', 50);
            $table->foreignId('client_id')->nullable()->constrained('clients')->nullOnDelete();
            $table->longText('cart_data'); // JSON serialized cart lines
            $table->decimal('subtotal', 12, 2)->default(0.00);
            $table->text('notes')->nullable();
            $table->timestamp('held_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('invoices') && Schema::hasColumn('invoices', 'shift_id')) {
            Schema::table('invoices', function (Blueprint $table) {
                $table->dropForeign(['shift_id']);
                $table->dropColumn('shift_id');
            });
        }
        Schema::dropIfExists('pos_held_orders');
        Schema::dropIfExists('cashier_shifts');
        Schema::dropIfExists('pos_terminals');
    }
};
