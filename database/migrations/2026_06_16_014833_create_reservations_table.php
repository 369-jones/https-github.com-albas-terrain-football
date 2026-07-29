<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipe_a_id')->constrained('equipes')->onDelete('cascade');
            $table->foreignId('equipe_b_id')->constrained('equipes')->onDelete('cascade');
            $table->date('date_match');                   // Date de la rencontre
            $table->enum('creneau', [
                '08h00-10h00',
                '10h00-12h00',
                '14h00-16h00',
                '16h00-18h00',
                '18h00-20h00'
            ]);                                           // Créneau horaire
            $table->enum('type_match', [
                'Match amical',
                'Championnat universitaire',
                'Coupe interfacultés',
                'Tournoi'
            ])->default('Match amical');
            $table->decimal('montant', 10, 2);            // Montant à payer
            $table->enum('statut', [
                'en_attente',
                'confirme',
                'annule'
            ])->default('en_attente');
            $table->text('notes')->nullable();            // Observations
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
