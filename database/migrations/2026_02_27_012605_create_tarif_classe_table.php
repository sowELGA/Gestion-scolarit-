<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tarif_classe', function (Blueprint $table) {
            $table->id();

            $table->foreignId('classe_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('tarif_id')
                ->constrained()
                ->cascadeOnDelete();

            // -----------------------------------------------
            // Statut du tarif sur cette classe
            // -----------------------------------------------

            // true = tarif actuellement appliqué à la classe
            // false = tarif historique (remplacé par un plus récent)
            $table->boolean('actif')->default(true);

            // -----------------------------------------------
            // Snapshot des montants au moment de l'affectation
            // (immuable — permet les calculs historiques même
            //  si le Tarif parent est modifié ultérieurement)
            // -----------------------------------------------
            $table->decimal('montant_inscription', 10, 2);
            $table->decimal('montant_mensualite',  10, 2);
            $table->decimal('montant_autre_frais', 10, 2)->default(0);

            // Montant total = inscription + (mensualite * nb_mois) + autre_frais
            // Stocké pour éviter de recalculer à chaque requête
            $table->decimal('montant_total', 10, 2)->default(0);

            // Nombre de mois de scolarité couverts par ce tarif
            $table->unsignedTinyInteger('nb_mois')->default(10);

            // -----------------------------------------------
            // Période d'application (historique)
            // -----------------------------------------------
            $table->date('date_debut');
            $table->date('date_fin')->nullable(); // null = encore actif

            // -----------------------------------------------
            // Traçabilité
            // -----------------------------------------------
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            // Une seule entrée active par classe à la fois
            $table->unique(['classe_id', 'tarif_id', 'date_debut']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tarif_classe');
    }
};