<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feed_stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('feed_stock_id')->constrained();
            $table->enum('type', ['in', 'out', 'adjust']);
            $table->decimal('quantity', 15, 2);
            $table->decimal('unit_price', 15, 2)->nullable();
            $table->nullableMorphs('source'); // feed_production, daily_record, etc.
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feed_stock_movements');
    }
};