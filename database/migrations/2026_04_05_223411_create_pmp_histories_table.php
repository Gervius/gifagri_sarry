<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pmp_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ingredient_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->decimal('old_pmp', 15, 2);
            $table->decimal('new_pmp', 15, 2);
            $table->morphs('source'); // receipt, stock_movement
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pmp_histories');
    }
};