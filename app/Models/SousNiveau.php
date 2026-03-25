<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SousNiveau extends Model
{
    protected $fillable = ['code', 'nom', 'niveau_id'];

    // ===============================
    // Relations
    // ===============================

    public function niveau()
    {
        return $this->belongsTo(Niveau::class);
    }

    // FIX : relation nécessaire pour la protection à la suppression
    public function classes()
    {
        return $this->hasMany(Classe::class);
    }

    // ===============================
    // Hooks
    // ===============================

    protected static function booted()
    {
        static::saving(function ($sousNiveau) {

            $sousNiveau->code = trim($sousNiveau->code);
            $sousNiveau->nom  = trim($sousNiveau->nom);

            if (empty($sousNiveau->code) || empty($sousNiveau->nom)) {
                throw new \Exception("Code et nom ne peuvent pas être vides.");
            }

            // Insensible à la casse
            $exists = self::where(function ($q) use ($sousNiveau) {
                    $q->whereRaw('LOWER(code) = ?', [strtolower($sousNiveau->code)])
                      ->orWhereRaw('LOWER(nom) = ?', [strtolower($sousNiveau->nom)]);
                })
                ->when($sousNiveau->id, fn ($q) => $q->where('id', '!=', $sousNiveau->id))
                ->exists();

            if ($exists) {
                throw new \Exception("Code ou nom déjà existant.");
            }

            $sousNiveau->code = strtoupper($sousNiveau->code);
            $sousNiveau->nom  = ucfirst(strtolower($sousNiveau->nom));
        });

        // FIX : protection suppression — empêche de supprimer un sous-niveau lié à des classes
        static::deleting(function ($sousNiveau) {
            if ($sousNiveau->classes()->exists()) {
                throw new \Exception(
                    "Impossible de supprimer : ce sous-niveau est associé à des classes."
                );
            }
        });
    }
}