<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feed_productions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recipe_id')->constrained();
            $table->decimal('quantity_produced', 12, 2);
            $table->foreignId('unit_id')->constrained('units');
            $table->date('production_date');
            $table->text('notes')->nullable();
            $table->enum('status', ['draft', 'approved', 'rejected'])->default('draft');
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamp('approved_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feed_productions');
    }
};