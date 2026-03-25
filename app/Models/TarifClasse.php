<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class TarifClasse extends Pivot
{
    protected $table = 'tarif_classe';

    public $incrementing = true; // on utilise un id auto-increment

    protected $fillable = [
        'classe_id',
        'tarif_id',
        'actif',
        'montant_inscription',
        'montant_mensualite',
        'montant_autre_frais',
        'montant_total',
        'nb_mois',
        'date_debut',
        'date_fin',
        'created_by',
    ];

    protected $casts = [
        'actif'               => 'boolean',
        'montant_inscription' => 'decimal:2',
        'montant_mensualite'  => 'decimal:2',
        'montant_autre_frais' => 'decimal:2',
        'montant_total'       => 'decimal:2',
        'date_debut'          => 'date',
        'date_fin'            => 'date',
    ];

    // ===============================
    // Relations
    // ===============================

    public function classe()
    {
        return $this->belongsTo(Classe::class);
    }

    public function tarif()
    {
        return $this->belongsTo(Tarif::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ===============================
    // Helpers calcul
    // ===============================

    /**
     * Calcule et retourne le montant total
     * inscription + (mensualite * nb_mois) + autre_frais
     */
    public function calculerMontantTotal(): float
    {
        return (float) $this->montant_inscription
            + ((float) $this->montant_mensualite * $this->nb_mois)
            + (float) $this->montant_autre_frais;
    }

    // ===============================
    // Hooks
    // ===============================

    protected static function booted()
    {
        // Calcul automatique du montant_total avant chaque sauvegarde
        static::saving(function (TarifClasse $pivot) {
            $pivot->montant_total = $pivot->calculerMontantTotal();
        });
    }
}