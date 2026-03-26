<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('annees_academiques', function (Blueprint $table) {
            $table->id();

            // Ex : "2025-2026"
            $table->string('code', 20)->unique();

            // Dates de l'année scolaire
            $table->date('date_ouverture');
            $table->date('date_fermeture');

            // Dates des inscriptions
            $table->date('date_debut_inscription');
            $table->date('date_fin_inscription');

            // Délai (en jours) pour changer de classe après clôture des inscriptions
            // Paramétrable, défaut 15 jours (concerne les L1 selon spec)
            $table->unsignedTinyInteger('delai_changement_classe')->default(15);

            // Statut : brouillon | publie | inscription_ouverte | inscription_fermee | cloturee
            $table->string('statut', 30)->default('brouillon');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('annees_academiques');
    }
};
