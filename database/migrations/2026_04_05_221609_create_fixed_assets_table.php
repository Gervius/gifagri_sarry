<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fixed_assets', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->date('purchase_date');
            $table->decimal('purchase_cost', 15, 2);
            $table->integer('lifespan_months');
            $table->decimal('salvage_value', 15, 2)->default(0);
            $table->foreignId('asset_account_id')->constrained('accounts')->restrictOnDelete();
            $table->foreignId('depreciation_account_id')->constrained('accounts')->restrictOnDelete();
            $table->foreignId('expense_account_id')->constrained('accounts')->restrictOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fixed_assets');
    }
};