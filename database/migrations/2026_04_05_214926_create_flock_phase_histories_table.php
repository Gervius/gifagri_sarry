<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('flock_phase_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('flock_id')->constrained()->cascadeOnDelete();
            $table->foreignId('phase_id')->constrained('production_phases')->restrictOnDelete();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('flock_phase_histories');
    }
};