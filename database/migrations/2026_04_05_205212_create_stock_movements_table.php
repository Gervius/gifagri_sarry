<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ingredient_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['in', 'out', 'adjust']);
            $table->decimal('quantity', 15, 2);
            $table->foreignId('unit_id')->constrained('units');
            $table->decimal('base_quantity', 15, 2); // convertie en unité de base
            $table->foreignId('base_unit_id')->constrained('units');
            $table->decimal('unit_price', 15, 2)->nullable(); // prix unitaire à l’entrée
            $table->text('reason')->nullable();
            $table->foreignId('source_id')->nullable();      // polymorphic source
            $table->string('source_type')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamp('approved_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
            $table->index(['ingredient_id', 'status']);
            $table->index(['source_type', 'source_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};