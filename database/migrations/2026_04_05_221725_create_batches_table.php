<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('batches', function (Blueprint $table) {
            $table->id();
            $table->string('batch_number')->unique();
            $table->date('manufacturing_date')->nullable();
            $table->date('expiration_date')->nullable();
            $table->decimal('initial_quantity', 15, 2);
            $table->decimal('current_quantity', 15, 2);
            $table->morphs('batchable'); // ingredient, feed_stock
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('batches');
    }
};