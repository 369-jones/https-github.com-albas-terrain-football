<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->string('titre');
            $table->text('message');
            $table->enum('type', [
                'info',
                'success',
                'warning',
                'danger',
            ])->default('info');
            $table->enum('categorie', [
                'reservation',
                'paiement',
                'facture',
                'equipe',
                'systeme',
            ])->default('systeme');
            $table->string('lien')->nullable();       // URL de redirection
            $table->boolean('lue')->default(false);   // Lu ou non
            $table->timestamp('lue_at')->nullable();  // Quand lue
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
