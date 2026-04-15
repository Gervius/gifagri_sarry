<?php

// Fichier: database/migrations/2026_04_07_000000_add_missing_attributes_to_erp_tables.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Table partners
        Schema::table('partners', function (Blueprint $table) {
            $table->string('ifu')->nullable()->after('email'); // Identifiant Fiscal Unique
            $table->string('rccm')->nullable()->after('ifu');   // Registre de Commerce
            $table->string('company_type')->nullable()->after('rccm'); // SARL, SA, etc.
            $table->string('contact_person')->nullable()->after('company_type');
            $table->string('city')->nullable()->after('address');
            $table->string('country')->nullable()->after('city');
        });

        // Table buildings
        Schema::table('buildings', function (Blueprint $table) {
            $table->decimal('area_sqm', 10, 2)->nullable()->after('capacity');
        });

        // Table invoices
        Schema::table('invoices', function (Blueprint $table) {
            $table->string('payment_terms')->nullable()->after('due_date');
        });
    }

    public function down(): void
    {
        Schema::table('partners', function (Blueprint $table) {
            $table->dropColumn(['ifu', 'rccm', 'company_type', 'contact_person', 'city', 'country']);
        });

        Schema::table('buildings', function (Blueprint $table) {
            $table->dropColumn('area_sqm');
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn('payment_terms');
        });
    }
};