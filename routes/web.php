<?php

use App\Http\Controllers\ClasseController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FiliereController;
use App\Http\Controllers\NiveauController;
use App\Http\Controllers\TarifController;
use Illuminate\Support\Facades\Route;

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

Route::resource('filieres', FiliereController::class);

Route::resource('niveaux', NiveauController::class);
Route::post('niveaux/{niveau}/sous-niveaux', [NiveauController::class, 'storeSousNiveau'])
    ->name('niveaux.sousNiveaux.store');

Route::put('sous-niveaux/{sousNiveau}', [NiveauController::class, 'updateSousNiveau'])
    ->name('sousNiveaux.update');

Route::delete('sous-niveaux/{sousNiveau}', [NiveauController::class, 'destroySousNiveau'])
    ->name('sousNiveaux.destroy');

Route::get('niveaux/{niveau}/sous-niveaux/create', [NiveauController::class, 'createSousNiveau'])
    ->name('niveaux.sousNiveaux.create');

Route::get('sous-niveaux/{sousNiveau}/edit',[NiveauController::class, 'editSousNiveau'])
    ->name('sousNiveaux.edit');

Route::resource('tarifs', TarifController::class);

// CRUD standard des classes
Route::resource('classes', ClasseController::class)
    ->parameters(['classes' => 'classe']);

Route::prefix('classes/{classe}')->name('classes.')->group(function () {
    Route::get('tarif/create',  [ClasseController::class, 'tarifCreate'])->name('tarif.create');
    Route::post('tarif',        [ClasseController::class, 'tarifStore'])->name('tarif.store');
    Route::get('historique',    [ClasseController::class, 'historique'])->name('historique');
});