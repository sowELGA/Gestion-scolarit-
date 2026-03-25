<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tarif extends Model
{
    protected $fillable = [
        'inscription',
        'mensualite',
        'autre_frais',
        // FIX : 'actif' retiré du fillable — il ne doit jamais être assigné manuellement
        // Il passe à true automatiquement quand le tarif est rattaché à une classe
    ];

    protected $casts = [
        'inscription' => 'decimal:2',
        'mensualite'  => 'decimal:2',
        'autre_frais' => 'decimal:2',
        'actif'       => 'boolean',
    ];

    // ===============================
    // Relations
    // ===============================

    public function classes()
    {
        return $this->belongsToMany(Classe::class, 'tarif_classe')
            ->withPivot(['actif'])
            ->withTimestamps();
    }

    // ===============================
    // Scopes
    // ===============================

    public function scopeActif($query)
    {
        return $query->where('actif', true);
    }

    public function scopeInactif($query)
    {
        return $query->where('actif', false);
    }

    // ===============================
    // Hooks
    // ===============================

    protected static function booted()
    {
        // FIX : à la création, actif est toujours false — il devient true via ClasseController
        static::creating(function ($tarif) {
            $tarif->actif = false;
        });

        static::deleting(function ($tarif) {
            if ($tarif->classes()->exists()) {
                throw new \Exception(
                    'Impossible de supprimer : ce tarif est associé à une classe.'
                );
            }
        });
    }
}