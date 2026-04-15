<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('production_phases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('animal_type_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->integer('typical_duration_days')->nullable();
            $table->integer('order')->default(0);
            $table->foreignId('default_recipe_id')->nullable()->constrained('recipes')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('production_phases');
    }
};