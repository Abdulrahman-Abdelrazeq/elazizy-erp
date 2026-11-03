<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations for Module 7: Accounting & General Ledger.
     */
    public function up(): void
    {
        // 1. Chart of Accounts (شجرة الحسابات المالية)
        Schema::create('chart_of_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('chart_of_accounts')->nullOnDelete();
            $table->string('code', 30);
            $table->string('name', 200);
            $table->string('name_en', 200)->nullable();
            $table->enum('type', ['asset', 'liability', 'equity', 'revenue', 'expense']);
            $table->enum('nature', ['debit', 'credit']); // طبيعة الحساب: مدين أو دائن
            $table->unsignedTinyInteger('level')->default(1); // 1: رئيسي، 2: مساعد، 3: فرعي، إلخ
            $table->boolean('is_parent')->default(false); // حساب رئيسي تندرج تحته حسابات ولا يقبل قيود مباشرة
            $table->decimal('opening_balance', 18, 2)->default(0.00);
            $table->decimal('current_balance', 18, 2)->default(0.00);
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['tenant_id', 'code'], 'coa_tenant_code_unique');
            $table->index(['tenant_id', 'type', 'is_active'], 'coa_tenant_type_idx');
            $table->index(['tenant_id', 'parent_id'], 'coa_tenant_parent_idx');
        });

        // 2. Cost Centers (مراكز التكلفة)
        Schema::create('cost_centers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('cost_centers')->nullOnDelete();
            $table->string('code', 50);
            $table->string('name', 150);
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['tenant_id', 'code'], 'cost_center_tenant_code_unique');
        });

        // 3. Journal Entries - Headers (دفتر القيود اليومية المزدوجة)
        Schema::create('journal_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->string('entry_number', 50); // e.g. ENT-00001
            $table->date('date');
            $table->string('reference', 100)->nullable(); // رقم مرجع خارجي / شيك / فاتورة
            $table->text('description')->nullable(); // البيان العام للقيد
            $table->decimal('total_debit', 18, 2)->default(0.00);
            $table->decimal('total_credit', 18, 2)->default(0.00);
            $table->enum('status', ['posted', 'draft'])->default('posted'); // معتمد ومؤثر أو مسودة
            $table->enum('source_type', ['manual', 'invoice', 'voucher', 'pos_shift', 'opening'])->default('manual');
            $table->unsignedBigInteger('source_id')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('posted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('posted_at')->nullable();
            $table->timestamps();

            $table->unique(['tenant_id', 'entry_number'], 'journal_entries_num_unique');
            $table->index(['tenant_id', 'date', 'status'], 'journal_entries_date_idx');
        });

        // 4. Journal Entry Lines (أطراف وأسطر القيد المحاسبي)
        Schema::create('journal_entry_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('journal_entry_id')->constrained('journal_entries')->cascadeOnDelete();
            $table->foreignId('account_id')->constrained('chart_of_accounts')->cascadeOnDelete();
            $table->foreignId('cost_center_id')->nullable()->constrained('cost_centers')->nullOnDelete();
            $table->decimal('debit', 18, 2)->default(0.00);
            $table->decimal('credit', 18, 2)->default(0.00);
            $table->string('description', 255)->nullable(); // بيان السطر التفصيلي
            $table->timestamps();

            $table->index(['tenant_id', 'account_id'], 'entry_lines_account_idx');
            $table->index(['tenant_id', 'journal_entry_id'], 'entry_lines_entry_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('journal_entry_lines');
        Schema::dropIfExists('journal_entries');
        Schema::dropIfExists('cost_centers');
        Schema::dropIfExists('chart_of_accounts');
    }
};

