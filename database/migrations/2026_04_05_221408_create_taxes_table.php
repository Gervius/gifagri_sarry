<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('taxes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('rate', 5, 2);
            $table->enum('type', ['vat', 'aib', 'other'])->default('vat');
            $table->foreignId('accounting_account_id')->constrained('accounts')->restrictOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('taxes');
    }
};