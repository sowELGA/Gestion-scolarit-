<?php

namespace App\Http\Controllers;

use App\Http\Requests\NiveauRequest;
use App\Http\Requests\SousNiveauRequest;
use App\Models\Niveau;
use App\Models\SousNiveau;

class NiveauController extends Controller
{
    public function index()
    {
        $niveaux = Niveau::with('sousNiveaux')
            ->orderBy('nom')
            ->get();

        return view('niveaux.index', compact('niveaux'));
    }

    // FORM CREATE
    public function create()
    {
        return view('niveaux.create');
    }

    // FORM EDIT
    public function edit(Niveau $niveau)
    {
        $niveau->load('sousNiveaux');

        return view('niveaux.edit', compact('niveau'));
    }

    // STORE
    public function store(NiveauRequest $request)
    {
        Niveau::create($request->validated());

        return redirect()->route('niveaux.index')
            ->with('success', 'Niveau ajouté.');
    }

    // UPDATE
    public function update(NiveauRequest $request, Niveau $niveau)
    {
        $niveau->update($request->validated());

        return redirect()->route('niveaux.index')
            ->with('success', 'Niveau modifié.');
    }

    // DELETE
    public function destroy(Niveau $niveau)
    {
        try {
            $niveau->delete();

            return back()->with('success', 'Niveau supprimé.');
        } catch (\Exception $e) {
            return back()->withErrors($e->getMessage());
        }
    }

    // ===============================
    // SOUS NIVEAU
    // ===============================

    public function createSousNiveau(Niveau $niveau)
    {
        return view('niveaux.create-sous-niveau', compact('niveau'));
    }

    public function storeSousNiveau(
        SousNiveauRequest $request,
        Niveau $niveau
    ) {
        $niveau->sousNiveaux()->create(
            $request->validated()
        );

        return redirect()->route('niveaux.index')->with('success', 'Sous-niveau ajouté.');
    }

    public function editSousNiveau(SousNiveau $sousNiveau)
    {
        $niveau = $sousNiveau->niveau;

        return view('niveaux.edit-sous-niveau', compact('sousNiveau', 'niveau'));
    }

    public function updateSousNiveau(
        SousNiveauRequest $request,
        SousNiveau $sousNiveau
    ) {
        $sousNiveau->update($request->validated());

        return redirect()->route('niveaux.index')->with('success', 'Sous-niveau modifié.');
    }

    public function destroySousNiveau(SousNiveau $sousNiveau)
    {
        try {
            $sousNiveau->delete();

            return back()->with('success', 'Sous niveau supprimé.');
        } catch (\Exception $e) {
            return back()->withErrors($e->getMessage());
        }
    }
}
