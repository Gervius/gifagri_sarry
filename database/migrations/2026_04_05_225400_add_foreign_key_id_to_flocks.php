<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('flocks', function (Blueprint $table) {
            // Vérifier si la colonne existe et n'a pas déjà de contrainte
            if (Schema::hasColumn('flocks', 'invoice_id')) {
                $table->foreign('invoice_id')
                      ->references('id')
                      ->on('invoices')
                      ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('flocks', function (Blueprint $table) {
            $table->dropForeign(['invoice_id']);
        });
    }
};