<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations for Module 8 (Financial & Analytical Reports).
     */
    public function up(): void
    {
        Schema::create('financial_budgets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->default(1)->index('fin_budgets_tenant_idx');
            $table->unsignedBigInteger('branch_id')->nullable()->index('fin_budgets_branch_idx');
            $table->unsignedSmallInteger('fiscal_year')->default(2026)->index('fin_budgets_year_idx');
            $table->unsignedBigInteger('account_id')->index('fin_budgets_account_idx');
            $table->enum('period_type', ['annual', 'q1', 'q2', 'q3', 'q4', 'monthly'])->default('annual');
            $table->decimal('target_amount', 15, 2)->default(0.00);
            $table->string('notes', 255)->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('account_id')->references('id')->on('chart_of_accounts')->cascadeOnDelete();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();

            $table->unique(['tenant_id', 'fiscal_year', 'account_id', 'period_type'], 'fin_budgets_unique_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('financial_budgets');
    }
};

