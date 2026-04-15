<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('landed_cost_allocations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_item_id')->constrained('invoice_items')->cascadeOnDelete(); // la ligne de frais (transport, douane)
            $table->morphs('target_item'); // peut être un invoice_item de matière première ou un ingredient
            $table->decimal('allocated_amount', 15, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('landed_cost_allocations');
    }
};