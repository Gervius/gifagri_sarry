<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('delivery_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('delivery_id')->constrained()->cascadeOnDelete();
            $table->string('description');
            $table->string('itemable_type');
            $table->unsignedBigInteger('itemable_id');
            $table->decimal('expected_quantity', 15, 2);
            $table->decimal('delivered_quantity', 15, 2)->nullable();
            $table->decimal('unit_price', 15, 2);
            $table->decimal('total', 15, 2);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['itemable_type', 'itemable_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_items');
    }
};
