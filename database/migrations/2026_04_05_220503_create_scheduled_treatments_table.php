<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('scheduled_treatments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('flock_id')->constrained()->cascadeOnDelete();
            $table->foreignId('prophylaxis_step_id')->constrained()->cascadeOnDelete();
            $table->date('scheduled_date');
            $table->integer('alert_days_before')->default(3);
            $table->enum('status', ['pending', 'completed', 'missed', 'cancelled'])->default('pending');
            $table->foreignId('actual_treatment_id')->nullable()->constrained('treatments')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scheduled_treatments');
    }
};