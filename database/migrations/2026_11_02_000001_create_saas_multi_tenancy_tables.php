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
        // 1. Subscription Plans (خطط وباقات الاشتراك)
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->decimal('price_monthly', 12, 2)->default(0);
            $table->decimal('price_yearly', 12, 2)->default(0);
            $table->unsignedInteger('trial_days')->default(14);
            $table->json('features')->nullable();
            $table->unsignedInteger('max_users')->default(5);
            $table->unsignedInteger('max_branches')->default(1);
            $table->unsignedInteger('max_stores')->default(2);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. Tenants / Companies (المستأجرين والشركات المشتركة)
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->string('company_name');
            $table->string('slug')->unique();
            $table->string('domain')->nullable()->unique();
            $table->string('logo')->nullable();
            $table->string('commercial_register', 100)->nullable();
            $table->string('tax_number', 100)->nullable();
            $table->string('phone', 50)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('address')->nullable();
            $table->string('currency_code', 10)->default('SAR');
            $table->foreignId('plan_id')->nullable()->constrained('plans')->nullOnDelete();
            $table->string('subscription_status', 30)->default('trial'); // trial, active, past_due, suspended
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('subscription_starts_at')->nullable();
            $table->timestamp('subscription_ends_at')->nullable();
            $table->json('theme_settings')->nullable(); // custom primary colors, accent colors, logo
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 3. Branches (فروع كل شركة)
        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('name');
            $table->string('code', 50)->nullable();
            $table->string('phone', 50)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('address')->nullable();
            $table->boolean('is_main')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 4. Subscriptions log & renewals (سجل الاشتراكات والتجديد)
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('plan_id')->constrained('plans')->cascadeOnDelete();
            $table->enum('billing_cycle', ['monthly', 'yearly'])->default('yearly');
            $table->decimal('price', 12, 2)->default(0);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->string('status', 30)->default('active'); // active, expired, cancelled
            $table->string('payment_method', 50)->default('free_trial'); // cash, bank_transfer, credit_card, etc.
            $table->string('payment_reference')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 5. Update Users Table for SaaS & Branches
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('tenant_id')->nullable()->after('id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->after('tenant_id')->constrained('branches')->nullOnDelete();
            $table->string('role', 50)->default('tenant_admin')->after('branch_id'); // superadmin, tenant_admin, branch_manager, cashier, accountant, employee
            $table->string('phone', 50)->nullable()->after('email');
            $table->string('avatar')->nullable()->after('password');
            $table->string('theme_palette', 50)->default('emerald')->after('avatar');
            $table->boolean('is_active')->default(true)->after('theme_palette');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['tenant_id']);
            $table->dropForeign(['branch_id']);
            $table->dropColumn(['tenant_id', 'branch_id', 'role', 'phone', 'avatar', 'theme_palette', 'is_active']);
        });

        Schema::dropIfExists('subscriptions');
        Schema::dropIfExists('branches');
        Schema::dropIfExists('tenants');
        Schema::dropIfExists('plans');
    }
};
