<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_rule_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('accounting_rule_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['debit', 'credit']);
            $table->enum('account_resolution_type', ['fixed', 'dynamic']);
            $table->foreignId('account_id')->nullable()->constrained('accounts');
            $table->string('dynamic_account_placeholder')->nullable(); // partner_account, product_account
            $table->string('amount_source'); // total_ht, tax_amount, total_ttc, quantity * unit_cost
            $table->decimal('percentage', 5, 2)->default(100);
            $table->string('description_template')->nullable();
            $table->string('analytical_target_source')->nullable(); // flock, building
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_rule_lines');
    }
};