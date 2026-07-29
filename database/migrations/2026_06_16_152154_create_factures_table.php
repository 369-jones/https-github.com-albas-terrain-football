<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('factures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paiement_id')->constrained('paiements')->onDelete('cascade');
            $table->foreignId('reservation_id')->constrained('reservations')->onDelete('cascade');
            $table->string('numero_facture')->unique();   // Ex: FACT-0001
            $table->decimal('montant', 10, 2);            // Montant de la facture
            $table->enum('statut', [
                'emise',
                'payee',
                'annulee',
            ])->default('emise');
            $table->date('date_emission');                // Date de génération
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('factures');
    }
};
