<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pitches', function (Blueprint $table) {
            $table->string('sport', 20)->default('football')->after('owner_id');
            // Widen surface_type for sports where "pitch" isn't turf/grass — hardwood
            // courts (basketball) and sand (beach volleyball).
            $table->enum('surface_type', ['natural_grass', 'synthetic_turf', 'concrete', 'indoor', 'hardwood', 'sand'])
                ->default('synthetic_turf')
                ->change();
        });
    }

    public function down(): void
    {
        Schema::table('pitches', function (Blueprint $table) {
            $table->dropColumn('sport');
            $table->enum('surface_type', ['natural_grass', 'synthetic_turf', 'concrete', 'indoor'])
                ->default('synthetic_turf')
                ->change();
        });
    }
};
