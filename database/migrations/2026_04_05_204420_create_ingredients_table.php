<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ingredients', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('reference')->nullable();
            $table->foreignId('default_unit_id')->constrained('units');
            $table->decimal('current_stock', 15, 2)->default(0);
            $table->decimal('current_stock_base', 15, 2)->default(0); // stock en unité de base
            $table->decimal('min_stock', 15, 2)->nullable();
            $table->decimal('max_stock', 15, 2)->nullable();
            $table->decimal('pmp', 15, 2)->default(0); // coût moyen pondéré
            $table->text('description')->nullable();
            $table->foreignId('partner_id')->nullable()->constrained('partners')->nullOnDelete(); // fournisseur par défaut
            $table->boolean('is_active')->default(true);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ingredients');
    }
};