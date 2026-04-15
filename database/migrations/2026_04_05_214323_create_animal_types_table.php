<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('animal_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('code', 20)->unique();
            $table->boolean('can_lay_eggs')->default(false);
            $table->boolean('has_growth_tracking')->default(false);
            $table->boolean('has_breeding_cycle')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('animal_types');
    }
};