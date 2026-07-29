<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // MODIFY COLUMN is MySQL-specific syntax (SQLite has no real ENUM type — the test
        // suite's :memory: DB doesn't need this rename since nothing there touches
        // reservations.type_match, only the marketplace tables).
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        // Widen the enum first so existing rows using the old labels stay valid while we rename them.
        DB::statement("ALTER TABLE reservations MODIFY type_match ENUM('Match amical','Championnat universitaire','Coupe interfacultés','Tournoi','Championnat','Coupe') NOT NULL DEFAULT 'Match amical'");

        DB::table('reservations')->where('type_match', 'Championnat universitaire')->update(['type_match' => 'Championnat']);
        DB::table('reservations')->where('type_match', 'Coupe interfacultés')->update(['type_match' => 'Coupe']);

        DB::statement("ALTER TABLE reservations MODIFY type_match ENUM('Match amical','Championnat','Coupe','Tournoi') NOT NULL DEFAULT 'Match amical'");
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        DB::statement("ALTER TABLE reservations MODIFY type_match ENUM('Match amical','Championnat universitaire','Coupe interfacultés','Tournoi','Championnat','Coupe') NOT NULL DEFAULT 'Match amical'");

        DB::table('reservations')->where('type_match', 'Championnat')->update(['type_match' => 'Championnat universitaire']);
        DB::table('reservations')->where('type_match', 'Coupe')->update(['type_match' => 'Coupe interfacultés']);

        DB::statement("ALTER TABLE reservations MODIFY type_match ENUM('Match amical','Championnat universitaire','Coupe interfacultés','Tournoi') NOT NULL DEFAULT 'Match amical'");
    }
};
