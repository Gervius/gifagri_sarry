<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feed_stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recipe_id')->constrained();
            $table->decimal('quantity', 15, 2);
            $table->foreignId('unit_id')->constrained('units');
            $table->decimal('unit_cost', 15, 2)->nullable(); // coût de revient
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feed_stocks');
    }
};