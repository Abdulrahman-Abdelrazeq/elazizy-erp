<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations for Module 11: Multi-Branch & Distribution Management.
     */
    public function up(): void
    {
        // 1. Add enterprise fields to existing branches table if not present
        Schema::table('branches', function (Blueprint $table) {
            if (!Schema::hasColumn('branches', 'branch_type')) {
                $table->enum('branch_type', ['hq', 'retail', 'wholesale', 'distribution_center', 'warehouse_only'])->default('retail')->after('name');
            }
            if (!Schema::hasColumn('branches', 'city')) {
                $table->string('city', 100)->nullable()->after('address');
            }
            if (!Schema::hasColumn('branches', 'district')) {
                $table->string('district', 100)->nullable()->after('city');
            }
            if (!Schema::hasColumn('branches', 'manager_name')) {
                $table->string('manager_name', 150)->nullable()->after('email');
            }
            if (!Schema::hasColumn('branches', 'commercial_registration')) {
                $table->string('commercial_registration', 100)->nullable()->after('manager_name');
            }
            if (!Schema::hasColumn('branches', 'tax_number')) {
                $table->string('tax_number', 100)->nullable()->after('commercial_registration');
            }
            if (!Schema::hasColumn('branches', 'monthly_budget')) {
                $table->decimal('monthly_budget', 15, 2)->default(0.00)->after('tax_number');
            }
            if (!Schema::hasColumn('branches', 'parent_id')) {
                $table->unsignedBigInteger('parent_id')->nullable()->after('tenant_id');
                $table->foreign('parent_id')->references('id')->on('branches')->nullOnDelete();
            }
        });

        // 2. Branch Users Mapping & Local Roles (صلاحيات المستخدمين وتعيين الفروع)
        Schema::create('branch_users', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->default(1)->index('bu_tenant_idx');
            $table->unsignedBigInteger('branch_id');
            $table->unsignedBigInteger('user_id');
            $table->string('role', 50)->default('staff'); // manager, accountant, cashier, storekeeper, staff
            $table->boolean('is_default')->default(false);
            $table->boolean('can_switch')->default(true);
            $table->timestamps();

            $table->foreign('branch_id')->references('id')->on('branches')->cascadeOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->unique(['branch_id', 'user_id'], 'bu_branch_user_unique');
        });

        // 3. Inter-Branch Logistics Transfers (التحويلات اللوجستية بين الفروع)
        Schema::create('branch_transfers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->default(1)->index('bt_tenant_idx');
            $table->string('transfer_number', 50);
            $table->unsignedBigInteger('from_branch_id');
            $table->unsignedBigInteger('to_branch_id');
            $table->unsignedBigInteger('from_store_id');
            $table->unsignedBigInteger('to_store_id');
            $table->enum('status', ['draft', 'dispatched', 'received', 'cancelled'])->default('draft');
            $table->date('transfer_date');
            $table->dateTime('dispatch_date')->nullable();
            $table->dateTime('received_date')->nullable();
            $table->unsignedInteger('total_items_count')->default(0);
            $table->decimal('total_cost', 15, 2)->default(0.00);
            $table->string('shipping_company', 150)->nullable();
            $table->string('driver_name', 150)->nullable();
            $table->string('vehicle_plate', 50)->nullable();
            $table->string('tracking_number', 100)->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('dispatched_by')->nullable();
            $table->unsignedBigInteger('received_by')->nullable();
            $table->timestamps();

            $table->foreign('from_branch_id')->references('id')->on('branches')->cascadeOnDelete();
            $table->foreign('to_branch_id')->references('id')->on('branches')->cascadeOnDelete();
            $table->foreign('from_store_id')->references('id')->on('stores')->cascadeOnDelete();
            $table->foreign('to_store_id')->references('id')->on('stores')->cascadeOnDelete();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('dispatched_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('received_by')->references('id')->on('users')->nullOnDelete();
            $table->unique(['tenant_id', 'transfer_number'], 'bt_number_unique');
        });

        // 4. Inter-Branch Transfer Items (بنود الشحنة والكميات)
        Schema::create('branch_transfer_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->default(1)->index('bti_tenant_idx');
            $table->unsignedBigInteger('transfer_id');
            $table->unsignedBigInteger('item_id');
            $table->decimal('sent_quantity', 15, 3);
            $table->decimal('received_quantity', 15, 3)->default(0.000);
            $table->decimal('unit_cost', 15, 2)->default(0.00);
            $table->decimal('total_cost', 15, 2)->default(0.00);
            $table->string('notes', 255)->nullable();
            $table->timestamps();

            $table->foreign('transfer_id')->references('id')->on('branch_transfers')->cascadeOnDelete();
            $table->foreign('item_id')->references('id')->on('items')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('branch_transfer_items');
        Schema::dropIfExists('branch_transfers');
        Schema::dropIfExists('branch_users');

        Schema::table('branches', function (Blueprint $table) {
            $cols = [
                'parent_id', 'monthly_budget', 'tax_number', 'commercial_registration',
                'manager_name', 'district', 'city', 'branch_type'
            ];
            foreach ($cols as $col) {
                if (Schema::hasColumn('branches', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
