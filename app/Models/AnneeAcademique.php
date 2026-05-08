<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnneeAcademique extends Model
{

    protected $table = 'annees_academiques';
    
    // -----------------------------------------------
    // Constantes de statut
    // -----------------------------------------------
    const STATUT_BROUILLON            = 'brouillon';
    const STATUT_PUBLIE               = 'publie';
    const STATUT_INSCRIPTION_OUVERTE  = 'inscription_ouverte';
    const STATUT_INSCRIPTION_FERMEE   = 'inscription_fermee';
    const STATUT_CLOTUREE             = 'cloturee';

    // Ordre des transitions autorisées
    const TRANSITIONS = [
        self::STATUT_BROUILLON           => self::STATUT_PUBLIE,
        self::STATUT_PUBLIE              => self::STATUT_INSCRIPTION_OUVERTE,
        self::STATUT_INSCRIPTION_OUVERTE => self::STATUT_INSCRIPTION_FERMEE,
        self::STATUT_INSCRIPTION_FERMEE  => self::STATUT_CLOTUREE,
    ];

    // -----------------------------------------------
    // Fillable
    // -----------------------------------------------
    protected $fillable = [
        'code',
        'date_ouverture',
        'date_fermeture',
        'date_debut_inscription',
        'date_fin_inscription',
        'statut',
    ];

    protected $casts = [
        'date_ouverture'          => 'date',
        'date_fermeture'          => 'date',
        'date_debut_inscription'  => 'date',
        'date_fin_inscription'    => 'date',
    ];

    // -----------------------------------------------
    // Helpers statut
    // -----------------------------------------------

    public function estBrouillon(): bool
    {
        return $this->statut === self::STATUT_BROUILLON;
    }

    public function estPublie(): bool
    {
        return $this->statut === self::STATUT_PUBLIE;
    }

    public function estInscriptionOuverte(): bool
    {
        return $this->statut === self::STATUT_INSCRIPTION_OUVERTE;
    }

    public function estInscriptionFermee(): bool
    {
        return $this->statut === self::STATUT_INSCRIPTION_FERMEE;
    }

    public function estCloturee(): bool
    {
        return $this->statut === self::STATUT_CLOTUREE;
    }

    public function estModifiable(): bool
    {
        return $this->statut === self::STATUT_BROUILLON;
    }

    public function estLectureSeule(): bool
    {
        return in_array($this->statut, [
            self::STATUT_CLOTUREE,
            self::STATUT_PUBLIE,
            self::STATUT_INSCRIPTION_OUVERTE,
            self::STATUT_INSCRIPTION_FERMEE,
        ]);
    }

    /**
     * Retourne le prochain statut possible ou null si fin de cycle
     */
    public function prochainStatut(): ?string
    {
        return self::TRANSITIONS[$this->statut] ?? null;
    }

    /**
     * Libellé lisible du statut
     */
    public function libelleStatut(): string
    {
        return match ($this->statut) {
            self::STATUT_BROUILLON           => 'Brouillon',
            self::STATUT_PUBLIE              => 'Publié',
            self::STATUT_INSCRIPTION_OUVERTE => 'Inscriptions ouvertes',
            self::STATUT_INSCRIPTION_FERMEE  => 'Inscriptions fermées',
            self::STATUT_CLOTUREE            => 'Clôturée',
            default                          => ucfirst($this->statut),
        };
    }

    /**
     * Couleur Tailwind badge selon statut
     */
    public function couleurStatut(): string
    {
        return match ($this->statut) {
            self::STATUT_BROUILLON           => 'bg-gray-100 text-gray-600 border-gray-200',
            self::STATUT_PUBLIE              => 'bg-blue-100 text-blue-700 border-blue-200',
            self::STATUT_INSCRIPTION_OUVERTE => 'bg-green-100 text-green-700 border-green-200',
            self::STATUT_INSCRIPTION_FERMEE  => 'bg-amber-100 text-amber-700 border-amber-200',
            self::STATUT_CLOTUREE            => 'bg-red-100 text-red-700 border-red-200',
            default                          => 'bg-gray-100 text-gray-600',
        };
    }

    /**
     * Libellé de l'action pour passer au prochain statut
     */
    public function libelleAction(): ?string
    {
        return match ($this->statut) {
            self::STATUT_BROUILLON           => 'Publier',
            self::STATUT_PUBLIE              => 'Ouvrir les inscriptions',
            self::STATUT_INSCRIPTION_OUVERTE => 'Fermer les inscriptions',
            self::STATUT_INSCRIPTION_FERMEE  => 'Clôturer l\'année',
            default                          => null,
        };
    }

    // -----------------------------------------------
    // Hooks
    // -----------------------------------------------

    protected static function booted(): void
    {
        // Statut par défaut à la création
        static::creating(function (AnneeAcademique $annee) {
            $annee->statut = self::STATUT_BROUILLON;
        });

        // Empêcher la modification si non brouillon
        static::updating(function (AnneeAcademique $annee) {
            if (!$annee->getOriginal('statut') === self::STATUT_BROUILLON) {
                // Seul le champ statut peut changer hors brouillon (transitions)
                $dirty = collect($annee->getDirty())->except('statut')->keys();
                if ($dirty->isNotEmpty()) {
                    throw new \Exception(
                        "Impossible de modifier : l'année académique n'est plus en brouillon."
                    );
                }
            }
        });
    }
}