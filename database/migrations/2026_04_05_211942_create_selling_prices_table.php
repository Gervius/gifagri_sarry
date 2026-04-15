<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('selling_prices', function (Blueprint $table) {
            $table->id();
            $table->morphs('sellable'); // egg_category, product
            $table->decimal('price', 15, 2);
            $table->date('effective_date');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('selling_prices');
    }
};