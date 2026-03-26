<?php

namespace App\Http\Controllers;

use App\Models\AnneeAcademique;
use App\Http\Requests\AnneeAcademiqueRequest;
use Illuminate\Support\Facades\Auth;

class AnneeAcademiqueController extends Controller
{
    // ===============================
    // INDEX
    // ===============================

    public function index()
    {
        $annees = AnneeAcademique::latest()->paginate(10);

        return view('annee.index', compact('annees'));
    }

    // ===============================
    // CREATE
    // ===============================

    public function create()
    {
        return view('annee.create');
    }

    // ===============================
    // STORE
    // ===============================

    public function store(AnneeAcademiqueRequest $request)
    {
        AnneeAcademique::create([
            ...$request->validated(),
            // statut forcé à 'brouillon' par le hook du modèle
        ]);

        return redirect()->route('annees.index')
            ->with('success', 'Année académique créée en brouillon.');
    }

    // ===============================
    // EDIT
    // ===============================

    public function edit(AnneeAcademique $annee)
    {
       

        return view('annee.edit', compact('annee'));
    }

    // ===============================
    // UPDATE
    // ===============================

    public function update(AnneeAcademiqueRequest $request, AnneeAcademique $annee)
    {
        if (!$annee->estModifiable()) {
            return redirect()->route('annee.index')
                ->withErrors('Cette année académique ne peut plus être modifiée.');
        }

        $annee->update($request->validated());

        return redirect()->route('annees.index')
            ->with('success', 'Année académique mise à jour.');
    }

    // ===============================
    // DESTROY
    // ===============================

    public function destroy(AnneeAcademique $annee)
    {
        if (!$annee->estBrouillon()) {
            return back()->withErrors('Seule une année en brouillon peut être supprimée.');
        }

        $annee->delete();

        return back()->with('success', 'Année académique supprimée.');
    }

    // ===============================
    // SHOW
    // ===============================

    public function show(AnneeAcademique $annee)
    {
        return view('annee.show', compact('annee'));
    }

    // ===============================
    // TRANSITION DE STATUT
    // ===============================

    public function avancer(AnneeAcademique $annee)
    {
        $prochain = $annee->prochainStatut();

        if (!$prochain) {
            return back()->withErrors('Aucune transition possible depuis ce statut.');
        }

        // Validation métier spécifique à chaque transition
        $erreur = $this->validerTransition($annee, $prochain);
        if ($erreur) {
            return back()->withErrors($erreur);
        }

        $annee->update(['statut' => $prochain]);

        return back()->with('success', 'Statut mis à jour : ' . $annee->fresh()->libelleStatut());
    }

    // -----------------------------------------------
    // HELPER PRIVÉ — Validation des transitions
    // -----------------------------------------------

    private function validerTransition(AnneeAcademique $annee, string $prochain): ?string
    {
        return match ($prochain) {

            // Brouillon → Publié : vérifier cohérence des dates
            AnneeAcademique::STATUT_PUBLIE => $this->validerDates($annee),

            // Publié → Inscriptions ouvertes : la date de début d'inscription doit être atteinte
            AnneeAcademique::STATUT_INSCRIPTION_OUVERTE => (
                now()->lt($annee->date_debut_inscription)
                ? 'Impossible d\'ouvrir les inscriptions avant la date prévue ('
                . $annee->date_debut_inscription->format('d/m/Y') . ').'
                : null
            ),

            // Inscriptions ouvertes → Fermées : les inscriptions doivent être ouvertes (déjà garanti par le statut)
            AnneeAcademique::STATUT_INSCRIPTION_FERMEE => null,

            // Inscriptions fermées → Clôturée : la date de fermeture doit être atteinte
            AnneeAcademique::STATUT_CLOTUREE => (
                now()->lt($annee->date_fermeture)
                ? 'Impossible de clôturer avant la date de fermeture prévue ('
                . $annee->date_fermeture->format('d/m/Y') . ').'
                : null
            ),

            default => null,
        };
    }

    private function validerDates(AnneeAcademique $annee): ?string
    {
        if ($annee->date_fermeture->lte($annee->date_ouverture)) {
            return 'La date de fermeture doit être après la date d\'ouverture.';
        }
        if ($annee->date_debut_inscription->gte($annee->date_ouverture)) {
            return 'Les inscriptions doivent ouvrir avant l\'ouverture de l\'école.';
        }
        if ($annee->date_fin_inscription->lte($annee->date_debut_inscription)) {
            return 'La date de fin des inscriptions doit être après la date de début.';
        }
        if ($annee->date_fin_inscription->lte($annee->date_ouverture)) {
            return 'La clôture des inscriptions doit être après l\'ouverture de l\'école.';
        }
        if ($annee->date_fin_inscription->gte($annee->date_fermeture)) {
            return 'La clôture des inscriptions doit être avant la fermeture de l\'école.';
        }

        return null;
    }
}
