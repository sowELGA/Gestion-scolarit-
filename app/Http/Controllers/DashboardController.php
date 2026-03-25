<?php

namespace App\Http\Controllers;

use App\Models\Classe;
use App\Models\Filiere;
use App\Models\Niveau;
use App\Models\Tarif;
use App\Models\TarifClasse;

class DashboardController extends Controller
{
    public function index()
    {
        // -----------------------------------------------
        // Compteurs généraux
        // -----------------------------------------------
        $totalClasses  = Classe::count();
        $totalFilieres = Filiere::count();
        $totalNiveaux  = Niveau::count();
        $totalTarifs   = Tarif::count();

        // -----------------------------------------------
        // Classes sans tarif rattaché
        // -----------------------------------------------
        $classesSansTarif = Classe::whereDoesntHave('tarifs', function ($q) {
            $q->where('tarif_classe.actif', true);
        })->count();

        // -----------------------------------------------
        // Tarifs actifs (rattachés à au moins une classe)
        // -----------------------------------------------
        $tarifsActifs = Tarif::where('actif', true)->count();

        // -----------------------------------------------
        // Répartition des classes par filière
        // -----------------------------------------------
        $classesByFiliere = Filiere::withCount('classes')
            ->orderByDesc('classes_count')
            ->get();

        // -----------------------------------------------
        // 5 dernières classes créées
        // -----------------------------------------------
        $dernieresClasses = Classe::with(['filiere', 'sousNiveau'])
            ->latest()
            ->take(5)
            ->get();

        // -----------------------------------------------
        // Tarifs récemment rattachés (5 derniers)
        // -----------------------------------------------
        $derniersTarifs = TarifClasse::with(['classe.filiere', 'tarif'])
            ->where('actif', true)
            ->latest()
            ->take(5)
            ->get();

        // -----------------------------------------------
        // Montant moyen des tarifs actifs
        // -----------------------------------------------
        $montantMoyen = TarifClasse::where('actif', true)->avg('montant_total') ?? 0;
        $montantMax   = TarifClasse::where('actif', true)->max('montant_total') ?? 0;
        $montantMin   = TarifClasse::where('actif', true)->min('montant_total') ?? 0;

        return view('dashboard', compact(
            'totalClasses',
            'totalFilieres',
            'totalNiveaux',
            'totalTarifs',
            'classesSansTarif',
            'tarifsActifs',
            'classesByFiliere',
            'dernieresClasses',
            'derniersTarifs',
            'montantMoyen',
            'montantMax',
            'montantMin',
        ));
    }
}