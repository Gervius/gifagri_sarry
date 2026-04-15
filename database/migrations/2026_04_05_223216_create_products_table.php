<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('stock_quantity', 15, 2)->default(0);
            $table->foreignId('unit_id')->constrained('units')->restrictOnDelete();
            $table->foreignId('accounting_account_id')->constrained('accounts')->restrictOnDelete(); // compte de vente
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};