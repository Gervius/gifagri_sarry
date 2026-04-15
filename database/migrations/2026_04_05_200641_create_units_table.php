<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->string('name');                 // Kilogramme, Litre, Pièce, etc.
            $table->string('symbol', 10);           // kg, L, unité
            $table->string('type');                 // mass, volume, unit, length
            $table->decimal('conversion_factor', 15, 6)->nullable(); // vers l’unité de base
            $table->foreignId('base_unit_id')->nullable()->constrained('units');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('units');
    }
};