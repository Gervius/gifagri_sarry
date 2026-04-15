<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('weight_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('flock_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->decimal('average_weight', 10, 2); // kg ou g selon unité
            $table->integer('sample_size')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('weight_records');
    }
};