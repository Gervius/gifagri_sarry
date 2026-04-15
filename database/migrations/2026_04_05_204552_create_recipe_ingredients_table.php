<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recipe_ingredients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recipe_id')->constrained()->cascadeOnDelete();
            $table->foreignId('ingredient_id')->constrained()->cascadeOnDelete();
            $table->decimal('quantity', 12, 2);
            $table->foreignId('unit_id')->constrained('units');
            $table->timestamps();
            $table->unique(['recipe_id', 'ingredient_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recipe_ingredients');
    }
};