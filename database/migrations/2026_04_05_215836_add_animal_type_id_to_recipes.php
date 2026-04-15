<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('recipes', function (Blueprint $table) {
            $table->foreignId('animal_type_id')->nullable()->after('name')->constrained('animal_types')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('recipes', function (Blueprint $table) {
            $table->dropForeign(['animal_type_id']);
            $table->dropColumn('animal_type_id');
        });
    }
};