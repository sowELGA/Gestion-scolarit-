<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Classe extends Model
{
    protected $fillable = [
        'code',
        'nom',
        'filiere_id',
        'sous_niveau_id',
    ];

    // ===============================
    // Relations
    // ===============================

    public function filiere(): BelongsTo
    {
        return $this->belongsTo(Filiere::class);
    }

    public function sousNiveau(): BelongsTo
    {
        return $this->belongsTo(SousNiveau::class);
    }

    /**
     * Tous les tarifs (historique complet)
     */
    public function tarifs(): BelongsToMany
    {
        return $this->belongsToMany(Tarif::class, 'tarif_classe')
            ->using(TarifClasse::class)
            ->withPivot([
                'id',
                'actif',
                'montant_inscription',
                'montant_mensualite',
                'montant_autre_frais',
                'montant_total',
                'nb_mois',
                'date_debut',
                'date_fin',
                'created_by',
            ])
            ->withTimestamps()
            ->orderByPivot('date_debut', 'desc');
    }

    /**
     * Uniquement le tarif actuellement actif
     */
    public function tarifActif(): ?Tarif
    {
        return $this->tarifs()->wherePivot('actif', true)->first();
    }

    /**
     * Ligne pivot du tarif actif (pour accéder aux montants snapshot)
     */
    public function pivotActif(): ?TarifClasse
    {
        return TarifClasse::where('classe_id', $this->id)
            ->where('actif', true)
            ->latest('date_debut')
            ->first();
    }

    /**
     * Historique complet des tarifs appliqués à cette classe
     */
    public function historiqueTarifs(): BelongsToMany
    {
        return $this->tarifs()->wherePivot('actif', false);
    }

    /*public function inscriptions(): HasMany
    {
        return $this->hasMany(Inscription::class);
    }*/

    // ===============================
    // Hooks
    // ===============================

   /* protected static function booted()
    {
        static::deleting(function ($classe) {
            if ($classe->inscriptions()->exists()) {
                throw new \Exception(
                    "Impossible de supprimer : cette classe contient des inscrits."
                );
            }
        });
    }*/
}