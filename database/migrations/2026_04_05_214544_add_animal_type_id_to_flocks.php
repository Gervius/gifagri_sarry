<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('flocks', function (Blueprint $table) {
            // Supprimer l'ancienne colonne species (enum) après migration
            $table->dropColumn('species');
            $table->foreignId('animal_type_id')->after('name')->constrained('animal_types')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::table('flocks', function (Blueprint $table) {
            $table->dropForeign(['animal_type_id']);
            $table->dropColumn('animal_type_id');
            $table->enum('species', ['layer', 'broiler', 'pig'])->after('name');
        });
    }
};