<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pig_breeding_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('flock_id')->constrained()->cascadeOnDelete();
            $table->enum('event_type', ['heat', 'mating', 'pregnancy_check', 'farrowing', 'weaning']);
            $table->date('event_date');
            $table->integer('piglets_born_alive')->nullable();
            $table->integer('piglets_stillborn')->nullable();
            $table->integer('piglets_weaned')->nullable();
            $table->foreignId('boar_flock_id')->nullable()->constrained('flocks')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pig_breeding_events');
    }
};