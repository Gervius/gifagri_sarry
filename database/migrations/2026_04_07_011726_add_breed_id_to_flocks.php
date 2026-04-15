<?php

// Fichier: database/migrations/2026_04_07_000002_add_breed_id_to_flocks_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('flocks', function (Blueprint $table) {
            $table->foreignId('breed_id')
                  ->nullable()
                  ->after('animal_type_id')
                  ->constrained('breeds')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('flocks', function (Blueprint $table) {
            $table->dropForeign(['breed_id']);
            $table->dropColumn('breed_id');
        });
    }
};