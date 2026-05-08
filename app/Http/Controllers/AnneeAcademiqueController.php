<?php

namespace App\Http\Controllers;

use App\Models\AnneeAcademique;
use App\Http\Requests\AnneeAcademiqueRequest;
use App\Services\AnneeAcademiqueService;
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

    public function store(AnneeAcademiqueRequest $request, AnneeAcademiqueService $service)
    {
        try {

            $service->creer($request->validated());

            return redirect()
                ->route('annees.index')
                ->with('success', 'Année académique créée en brouillon.');
        } catch (\Exception $e) {
            return back()
                ->withErrors($e->getMessage())
                ->withInput();
        }
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

    public function update(AnneeAcademique $annee, AnneeAcademiqueRequest $request, AnneeAcademiqueService $service)
    {
        try {
            $service->modifier($annee, $request->validated());

            return redirect()->route('annees.index')
                ->with('success', 'Année académique mise à jour.');
        } catch (\Exception $e) {
            return back()->withErrors($e->getMessage());
        }
    }

    // ===============================
    // DESTROY
    // ===============================

    public function destroy(AnneeAcademique $annee, AnneeAcademiqueService $service)
    {
        try {
            $service->supprimer($annee);

            return back()->with('success', 'Année académique supprimée.');
        } catch (\Exception $e) {
            return back()->withErrors($e->getMessage());
        }
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

    public function avancer(AnneeAcademique $annee, AnneeAcademiqueService $service)
    {
        try {
            $service->avancerStatus($annee);

            return back()->with('success', 'Statut mis à jour : ' . $annee->libelleStatut());
        } catch (\Exception $e) {
            return back()->withErrors($e->getMessage());
        }
    }
}
