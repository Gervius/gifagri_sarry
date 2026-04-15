<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('flock_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->integer('losses')->default(0);           // mortalité / élimination
            $table->integer('eggs')->default(0);             // pour les pondeuses
            $table->decimal('feed_consumed', 12, 2)->default(0);
            $table->foreignId('feed_type_id')->nullable();
            $table->decimal('water_consumed', 12, 2)->default(0);
            $table->text('notes')->nullable();
            $table->enum('status', ['draft', 'approved', 'rejected'])->default('draft');
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamp('approved_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
            $table->unique(['flock_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_records');
    }
};