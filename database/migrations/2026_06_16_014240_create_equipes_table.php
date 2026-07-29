<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('equipes', function (Blueprint $table) {
            $table->id();
            $table->string('nom');                        // Nom de l'équipe
            $table->string('responsable');                // Nom du responsable
            $table->string('contact');                    // Téléphone
            $table->string('faculte')->nullable();        // UFR / Faculté
            $table->string('email')->nullable();          // Email
            $table->enum('statut', ['actif', 'inactif'])->default('actif');
            $table->timestamps();                         // created_at, updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('equipes');
    }
};
