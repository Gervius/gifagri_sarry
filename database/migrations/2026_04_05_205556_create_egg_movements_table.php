<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('egg_movements', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->enum('type', ['in', 'out', 'loss', 'adjust']);
            $table->integer('quantity');
            $table->decimal('unit_cost', 15, 2)->nullable(); // coût de production unitaire
            $table->nullableMorphs('source'); // daily_record, invoice_item
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            $table->index(['date', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('egg_movements');
    }
};