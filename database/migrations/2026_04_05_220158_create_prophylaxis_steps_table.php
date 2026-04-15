<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prophylaxis_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prophylaxis_plan_id')->constrained()->cascadeOnDelete();
            $table->integer('day_of_age'); // jour d'âge où l'action doit être faite
            $table->string('treatment_type'); // vaccine, antibiotic, etc.
            $table->string('administration_method'); // injection, oral, spray
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prophylaxis_steps');
    }
};