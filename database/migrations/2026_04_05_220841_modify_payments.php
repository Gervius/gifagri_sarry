<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // Ajouter la clé étrangère bank_account_id
            $table->foreignId('bank_account_id')->nullable()->after('method')->constrained('bank_accounts')->nullOnDelete();
            // On laisse la colonne method pour compatibilité, mais on peut la rendre nullable
            $table->string('method')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['bank_account_id']);
            $table->dropColumn('bank_account_id');
            $table->string('method')->nullable(false)->change();
        });
    }
};