<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pitches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->constrained('users')->cascadeOnDelete();
            $table->string('slug')->unique();

            // Translated content stored as JSON: {"en": "...", "fr": "...", "pt": "...", "sw": "..."}
            $table->json('name');
            $table->json('description')->nullable();

            $table->string('country', 2);      // ISO 3166-1 alpha-2
            $table->string('city');
            $table->string('address')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();

            $table->enum('surface_type', ['natural_grass', 'synthetic_turf', 'concrete', 'indoor'])->default('synthetic_turf');
            $table->unsignedTinyInteger('capacity')->default(10); // e.g. 5-a-side, 7-a-side, 11-a-side
            $table->json('amenities')->nullable(); // ["lighting","parking","showers","equipment_rental"]

            $table->decimal('price_per_hour', 12, 2);
            $table->string('currency', 3)->default('XOF'); // base currency the owner sets the price in

            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['country', 'city']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pitches');
    }
};
