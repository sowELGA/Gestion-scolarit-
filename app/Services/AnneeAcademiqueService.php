<?php

namespace App\Services;

use App\Models\AnneeAcademique;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use PhpParser\Node\Scalar\String_;

class AnneeAcademiqueService
{
    const NB_MOIS_REQUIS = 9;

    public function creer(array $donnees): AnneeAcademique
    {
        $this->validerDuree($donnees['date_ouverture'], $donnees['date_fermeture']);
        return AnneeAcademique::create([
            ...$donnees,
            // statut forcé à 'brouillon' par le hook du modèle
        ]);
    }

    public function modifier(AnneeAcademique $annee, array $donnees): AnneeAcademique
    {
        if (!$annee->estModifiable()) {
            throw new \Exception(
                "Impossible de modifier : l'année académique n'est plus en brouillon."
            );
        }

        $annee->update($donnees);
        return $annee->refresh();
    }

    public function supprimer(AnneeAcademique $annee)
    {
        if (!$annee->estBrouillon()) {
            throw new \Exception(
                "Seule une année en brouillon peut être supprimée."
            );
        }

        $annee->delete();
    }

        // ===============================
    // RÈGLES MÉTIER — DURÉE
    // ===============================

    /**
     * L'année scolaire doit durer exactement 9 mois
     */
    public function validerDuree(String|\DateTimeInterface $dateOuverture, String|\DateTimeInterface $dateFermeture): void
    {
        $debut = \Carbon\Carbon::parse($dateOuverture)->startOfDay();
        $fin = \Carbon\Carbon::parse($dateFermeture)->endOfDay();

        $nbMois = (int) $debut->diffInMonths($fin);

        if ($nbMois !== self::NB_MOIS_REQUIS) {
            throw new \Exception(
                "L'année scolaire doit durer exactement " . self::NB_MOIS_REQUIS . " mois. "
                    . "Durée actuelle : {$nbMois} mois."
            );
        }
    }

    // ===============================
    // TRANSITIONS DE STATUT
    // ===============================

    public function avancerStatus(AnneeAcademique $annee)
    {
        $prochain = $annee->prochainStatut();

        if (!$prochain) {
            throw new \Exception(
                "Aucune transition possible depuis le statut : {$annee->libelleStatut()}."
            );
        }

        $this->validerTransition($annee, $prochain);

        $annee->update(['statut' => $prochain]);

        return $annee->fresh();
    }

    // ===============================
    // RÈGLES MÉTIER DES TRANSITIONS
    // ===============================

    private function validerTransition(AnneeAcademique $annee, string $prochain): void
    {
        match ($prochain) {
            AnneeAcademique::STATUT_PUBLIE              => $this->validerPublication($annee),
            AnneeAcademique::STATUT_INSCRIPTION_OUVERTE => $this->validerOuvertureInscription($annee),
            AnneeAcademique::STATUT_INSCRIPTION_FERMEE  => $this->validerFermetureInscription($annee),
            AnneeAcademique::STATUT_CLOTUREE            => $this->validerCloture($annee),
            default => null,
        };
    }

    /**
     * Brouillon → Publié
     * Vérifie la cohérence de toutes les dates avant publication
     */
    private function validerPublication(AnneeAcademique $annee): void
    {
        // DD < DF
        if ($annee->date_fermeture->lte($annee->date_ouverture)) {
            throw new \Exception(
                "La date de fermeture doit être après la date d'ouverture."
            );
        }

        // DDI < DFI
        if ($annee->date_fin_inscription->lte($annee->date_debut_inscription)) {
            throw new \Exception(
                "La date de fin des inscriptions doit être après la date de début."
            );
        }

        // Ouverture inscription AVANT ouverture école
        if ($annee->date_debut_inscription->gte($annee->date_ouverture)) {
            throw new \Exception(
                "Les inscriptions doivent ouvrir avant l'ouverture de l'école."
            );
        }

        // Fermeture inscription APRÈS ouverture école
        if ($annee->date_fin_inscription->lte($annee->date_ouverture)) {
            throw new \Exception(
                "La clôture des inscriptions doit être après l'ouverture de l'école."
            );
        }

        // Fermeture inscription AVANT fermeture école
        if ($annee->date_fin_inscription->gte($annee->date_fermeture)) {
            throw new \Exception(
                "La clôture des inscriptions doit être avant la fermeture de l'école."
            );
        }
    }

    /**
     * Publié → Inscriptions ouvertes
     * La date de début d'inscription doit être atteinte
     */
    private function validerOuvertureInscription(AnneeAcademique $annee): void
    {
        if (now()->lt($annee->date_debut_inscription)) {
            throw new \Exception(
                "Impossible d'ouvrir les inscriptions avant la date prévue ("
                    . $annee->date_debut_inscription->format('d/m/Y') . ")."
            );
        }
    }

    /**
     * Inscriptions ouvertes → Inscriptions fermées
     * Les inscriptions doivent être ouvertes (garanti par le statut)
     * On peut fermer à tout moment une fois ouvertes
     */
    private function validerFermetureInscription(AnneeAcademique $annee): void
    {
        if (!$annee->estInscriptionOuverte()) {
            throw new \Exception(
                "Les inscriptions doivent être ouvertes pour pouvoir les fermer."
            );
        }
    }

    /**
     * Inscriptions fermées → Clôturée
     * La date de fermeture de l'école doit être atteinte
     */
    private function validerCloture(AnneeAcademique $annee): void
    {
        if (now()->lt($annee->date_fermeture)) {
            throw new \Exception(
                "Impossible de clôturer avant la date de fermeture prévue ("
                    . $annee->date_fermeture->format('d/m/Y') . ")."
            );
        }
    }
}
