<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->string('devise', 10)->default('XAF')->after('montant');
        });

        Schema::table('paiements', function (Blueprint $table) {
            $table->string('devise', 10)->default('XAF')->after('montant_paye');
        });

        Schema::table('factures', function (Blueprint $table) {
            $table->string('devise', 10)->default('XAF')->after('montant');
        });
    }

    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn('devise');
        });
        Schema::table('paiements', function (Blueprint $table) {
            $table->dropColumn('devise');
        });
        Schema::table('factures', function (Blueprint $table) {
            $table->dropColumn('devise');
        });
    }
};
