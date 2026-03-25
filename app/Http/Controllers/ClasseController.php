<?php

namespace App\Http\Controllers;

use App\Models\Classe;
use App\Models\Filiere;
use App\Models\SousNiveau;
use App\Models\Tarif;
use App\Models\TarifClasse;
use App\Http\Requests\ClasseRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ClasseController extends Controller
{
    // ===============================
    // INDEX
    // ===============================

    public function index()
    {
        $classes = Classe::with([
            'filiere',
            'sousNiveau',
            'tarifs' => fn($q) => $q->wherePivot('actif', true),
        ])
            ->latest()
            ->paginate(10);

        return view('classes.index', compact('classes'));
    }

    // ===============================
    // CREATE (étape 1 — sans tarif)
    // ===============================

    public function create()
    {
        return view('classes.create', [
            'filieres'    => Filiere::orderBy('nom')->get(),
            'sousNiveaux' => SousNiveau::with('niveau')->orderBy('nom')->get(),
        ]);
    }

    // ===============================
    // STORE (étape 1 — sans tarif)
    // ===============================

    public function store(ClasseRequest $request)
    {
        $classe = Classe::create(
            $request->only(['code', 'nom', 'filiere_id', 'sous_niveau_id'])
        );

        return redirect()
            ->route('classes.tarif.create', $classe)
            ->with('success', 'Classe créée. Rattachez maintenant un tarif.');
    }

    // ===============================
    // EDIT
    // ===============================

    public function edit(Classe $classe)
    {
        $classe->load(['filiere', 'sousNiveau', 'tarifs']);

        return view('classes.edit', [
            'classe'      => $classe,
            'filieres'    => Filiere::orderBy('nom')->get(),
            'sousNiveaux' => SousNiveau::with('niveau')->orderBy('nom')->get(),
            'tarifActif'  => $classe->tarifActif(),
        ]);
    }

    // ===============================
    // UPDATE (informations uniquement)
    // ===============================

    public function update(ClasseRequest $request, Classe $classe)
    {
        $classe->update(
            $request->only(['code', 'nom', 'filiere_id', 'sous_niveau_id'])
        );

        return redirect()->route('classes.index')
            ->with('success', 'Classe mise à jour.');
    }

    // ===============================
    // DESTROY
    // ===============================

    public function destroy(Classe $classe)
    {
        try {
            $classe->delete();
            return back()->with('success', 'Classe supprimée.');
        } catch (\Exception $e) {
            return back()->withErrors($e->getMessage());
        }
    }

    // ===============================
    // TARIF — Formulaire rattachement
    // ===============================

    public function tarifCreate(Classe $classe)
    {
        return view('classes.tarif-create', [
            'classe'     => $classe->load(['filiere', 'sousNiveau']),
            'tarifs'     => Tarif::orderBy('inscription')->get(),
            'tarifActif' => $classe->tarifActif(),
        ]);
    }

    // ===============================
    // TARIF — Enregistrement
    // ===============================

    public function tarifStore(ClasseRequest $request, Classe $classe)
    {
        DB::transaction(function () use ($request, $classe) {

            $tarifActif = $classe->tarifActif();

            // Fermer l'ancien tarif si existant
            if ($tarifActif) {
                TarifClasse::where('classe_id', $classe->id)
                    ->where('actif', true)
                    ->update([
                        'actif'    => false,
                        'date_fin' => now()->toDateString(),
                    ]);
            }

            // Snapshot + création de la nouvelle ligne pivot
            $tarif = Tarif::findOrFail($request->tarif_id);

            TarifClasse::create([
                'classe_id'           => $classe->id,
                'tarif_id'            => $tarif->id,
                'actif'               => true,
                'montant_inscription' => $tarif->inscription,
                'montant_mensualite'  => $tarif->mensualite,
                'montant_autre_frais' => $tarif->autre_frais,
                'nb_mois'             => $request->integer('nb_mois', 10),
                'date_debut'          => $request->input('date_debut', now()->toDateString()),
                'date_fin'            => null,
                'created_by'          => Auth::id(),
                // montant_total calculé automatiquement par TarifClasse::booted()
            ]);

            // Marquer le tarif parent comme actif
            $tarif->update(['actif' => true]);
        });

        return redirect()->route('classes.index')
            ->with('success', 'Tarif rattaché avec succès.');
    }

    // ===============================
    // HISTORIQUE des tarifs
    // ===============================

    public function historique(Classe $classe)
    {
        $classe->load(['filiere', 'sousNiveau']);

        $historique = TarifClasse::with(['tarif', 'createdBy'])
            ->where('classe_id', $classe->id)
            ->orderByDesc('date_debut')
            ->get();

        return view('classes.historique', compact('classe', 'historique'));
    }
}
