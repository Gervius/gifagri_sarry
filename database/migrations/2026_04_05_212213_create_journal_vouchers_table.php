<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('journal_vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('voucher_number')->nullable()->unique();
            $table->date('date');
            $table->text('description')->nullable();
            $table->morphs('source'); // invoice, payment, treatment, etc.
            $table->enum('status', ['draft', 'posted', 'cancelled'])->default('draft');
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('journal_vouchers');
    }
};