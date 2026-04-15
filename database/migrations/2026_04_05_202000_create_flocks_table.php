<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('flocks', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->enum('species', ['layer', 'broiler', 'pig']); // poule pondeuse, poulet chair, porcin
            $table->foreignId('building_id')->constrained();
            $table->date('arrival_date');
            $table->integer('initial_quantity');
            $table->integer('current_quantity')->nullable(); // mis à jour via daily records
            $table->decimal('purchase_cost', 15, 2)->nullable(); // coût total d’achat
            $table->enum('status', ['draft', 'active', 'completed', 'cancelled'])->default('draft');
            $table->date('ended_at')->nullable();
            $table->enum('end_reason', ['sale', 'mortality', 'disease', 'other'])->nullable();
            $table->decimal('standard_mortality_rate', 5, 2)->nullable()->default(1.0);
            $table->foreignId('supplier_id')->nullable()->constrained('partners')->nullOnDelete();
            $table->foreignId('invoice_id')->nullable(); // facture d’achat
            $table->foreignId('analytical_account_id')->nullable()->constrained('analytical_accounts');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamp('approved_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('flocks');
    }
};