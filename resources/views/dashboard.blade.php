@extends('layouts.app')

@section('title', 'Tableau de bord')

@section('content')

    {{-- Titre --}}
    <div class="mb-8">
        <h2 class="text-2xl font-semibold text-gray-800">Tableau de bord</h2>
        <p class="text-gray-400 text-sm mt-1">{{ now()->isoFormat('dddd D MMMM YYYY') }}</p>
    </div>

    {{-- ================================================ --}}
    {{-- LIGNE 1 : Compteurs principaux --}}
    {{-- ================================================ --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-5 mb-8">

        {{-- Classes --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center text-2xl shrink-0">🏫</div>
            <div>
                <p class="text-xs text-gray-400 font-medium uppercase tracking-wide">Classes</p>
                <p class="text-3xl font-bold text-gray-800">{{ $totalClasses }}</p>
                @if ($classesSansTarif > 0)
                    <p class="text-xs text-amber-500 mt-0.5">{{ $classesSansTarif }} sans tarif</p>
                @else
                    <p class="text-xs text-green-500 mt-0.5">Toutes configurées ✓</p>
                @endif
            </div>
        </div>

        {{-- Filières --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-purple-100 flex items-center justify-center text-2xl shrink-0">📘</div>
            <div>
                <p class="text-xs text-gray-400 font-medium uppercase tracking-wide">Filières</p>
                <p class="text-3xl font-bold text-gray-800">{{ $totalFilieres }}</p>
                <p class="text-xs text-gray-400 mt-0.5">enregistrées</p>
            </div>
        </div>

        {{-- Niveaux --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-green-100 flex items-center justify-center text-2xl shrink-0">🎓</div>
            <div>
                <p class="text-xs text-gray-400 font-medium uppercase tracking-wide">Niveaux</p>
                <p class="text-3xl font-bold text-gray-800">{{ $totalNiveaux }}</p>
                <p class="text-xs text-gray-400 mt-0.5">enregistrés</p>
            </div>
        </div>

        {{-- Tarifs --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-yellow-100 flex items-center justify-center text-2xl shrink-0">💰</div>
            <div>
                <p class="text-xs text-gray-400 font-medium uppercase tracking-wide">Tarifs</p>
                <p class="text-3xl font-bold text-gray-800">{{ $totalTarifs }}</p>
                <p class="text-xs text-gray-400 mt-0.5">{{ $tarifsActifs }} actifs</p>
            </div>
        </div>

    </div>

    {{-- ================================================ --}}
    {{-- LIGNE 2 : Montants + Alerte classes sans tarif --}}
    {{-- ================================================ --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-8">

        {{-- Montant moyen --}}
        <div class="bg-gradient-to-br from-blue-600 to-blue-700 rounded-2xl shadow p-6 text-white">
            <p class="text-xs font-medium uppercase tracking-wide text-blue-200">Montant moyen / classe</p>
            <p class="text-3xl font-bold mt-2">
                {{ number_format($montantMoyen, 0, ',', ' ') }}
                <span class="text-lg font-normal text-blue-200">FCFA</span>
            </p>
            <div class="mt-4 flex gap-4 text-sm text-blue-100">
                <span>Min : {{ number_format($montantMin, 0, ',', ' ') }} FCFA</span>
                <span>·</span>
                <span>Max : {{ number_format($montantMax, 0, ',', ' ') }} FCFA</span>
            </div>
        </div>

        {{-- Alerte classes sans tarif --}}
        @if ($classesSansTarif > 0)
            <div class="bg-amber-50 border border-amber-200 rounded-2xl p-6 flex flex-col justify-between">
                <div>
                    <p class="text-sm font-semibold text-amber-700 mb-1">⚠ Action requise</p>
                    <p class="text-amber-600 text-sm">
                        <span class="font-bold text-amber-800">{{ $classesSansTarif }} classe(s)</span>
                        n'ont pas encore de tarif rattaché.
                    </p>
                </div>
                <a href="{{ route('classes.index') }}"
                    class="mt-4 inline-block text-sm font-medium text-amber-700 hover:text-amber-900 underline">
                    Gérer les classes →
                </a>
            </div>
        @else
            <div class="bg-green-50 border border-green-200 rounded-2xl p-6 flex flex-col justify-between">
                <div>
                    <p class="text-sm font-semibold text-green-700 mb-1">✅ Tout est configuré</p>
                    <p class="text-green-600 text-sm">
                        Toutes les classes ont un tarif actif.
                    </p>
                </div>
                <a href="{{ route('classes.index') }}"
                    class="mt-4 inline-block text-sm font-medium text-green-700 hover:text-green-900 underline">
                    Voir les classes →
                </a>
            </div>
        @endif

        {{-- Accès rapide --}}
        <div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-6">
            <p class="text-xs text-gray-400 font-medium uppercase tracking-wide mb-4">Accès rapide</p>
            <div class="space-y-2">
                <a href="{{ route('classes.create') }}"
                    class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-blue-50 text-sm text-gray-700 hover:text-blue-700 transition">
                    <span>🏫</span> Nouvelle classe
                </a>
                <a href="{{ route('filieres.create') }}"
                    class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-purple-50 text-sm text-gray-700 hover:text-purple-700 transition">
                    <span>📘</span> Nouvelle filière
                </a>
                <a href="{{ route('niveaux.create') }}"
                    class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-green-50 text-sm text-gray-700 hover:text-green-700 transition">
                    <span>🎓</span> Nouveau niveau
                </a>
                <a href="{{ route('tarifs.create') }}"
                    class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-yellow-50 text-sm text-gray-700 hover:text-yellow-700 transition">
                    <span>💰</span> Nouveau tarif
                </a>
            </div>
        </div>

    </div>

    {{-- ================================================ --}}
    {{-- LIGNE 3 : Répartition filières + Dernières classes --}}
    {{-- ================================================ --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-8">

        {{-- Répartition par filière --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-sm font-semibold text-gray-700 mb-5">Classes par filière</h3>

            @forelse ($classesByFiliere as $filiere)
                @php
                    $pct = $totalClasses > 0 ? round(($filiere->classes_count / $totalClasses) * 100) : 0;
                @endphp
                <div class="mb-4">
                    <div class="flex justify-between items-center mb-1">
                        <span class="text-sm text-gray-600">{{ $filiere->nom }}</span>
                        <span class="text-sm font-semibold text-gray-800">
                            {{ $filiere->classes_count }} classe(s)
                        </span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-2">
                        <div class="bg-blue-500 h-2 rounded-full transition-all duration-500"
                            style="width: {{ $pct }}%"></div>
                    </div>
                </div>
            @empty
                <p class="text-gray-400 text-sm text-center py-4">Aucune filière enregistrée.</p>
            @endforelse
        </div>

        {{-- Dernières classes créées --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex justify-between items-center mb-5">
                <h3 class="text-sm font-semibold text-gray-700">Dernières classes ajoutées</h3>
                <a href="{{ route('classes.index') }}" class="text-xs text-blue-500 hover:underline">Voir tout</a>
            </div>

            @forelse ($dernieresClasses as $classe)
                <div class="flex items-center justify-between py-2.5 border-b border-gray-50 last:border-0">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center text-sm font-bold text-blue-700">
                            {{ strtoupper(substr($classe->code, 0, 2)) }}
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-800">{{ $classe->nom }}</p>
                            <p class="text-xs text-gray-400">{{ $classe->filiere->nom ?? '—' }}</p>
                        </div>
                    </div>
                    <span class="text-xs text-gray-400">
                        {{ $classe->created_at->diffForHumans() }}
                    </span>
                </div>
            @empty
                <p class="text-gray-400 text-sm text-center py-4">Aucune classe enregistrée.</p>
            @endforelse
        </div>

    </div>

    {{-- ================================================ --}}
    {{-- LIGNE 4 : Derniers tarifs rattachés --}}
    {{-- ================================================ --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex justify-between items-center mb-5">
            <h3 class="text-sm font-semibold text-gray-700">Derniers tarifs rattachés</h3>
            <a href="{{ route('classes.index') }}" class="text-xs text-blue-500 hover:underline">Voir les classes</a>
        </div>

        @forelse ($derniersTarifs as $pivot)
            <div class="flex items-center justify-between py-3 border-b border-gray-50 last:border-0">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-green-100 flex items-center justify-center text-base">💰</div>
                    <div>
                        <p class="text-sm font-medium text-gray-800">
                            {{ $pivot->classe->nom ?? '—' }}
                            <span class="text-gray-400 font-normal">·</span>
                            <span class="text-gray-500 font-normal text-xs">{{ $pivot->classe->filiere->nom ?? '' }}</span>
                        </p>
                        <p class="text-xs text-gray-400">
                            Depuis le {{ \Carbon\Carbon::parse($pivot->date_debut)->format('d/m/Y') }}
                        </p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-sm font-bold text-gray-800">
                        {{ number_format($pivot->montant_total, 0, ',', ' ') }} FCFA
                    </p>
                    <p class="text-xs text-gray-400">
                        {{ number_format($pivot->montant_mensualite, 0, ',', ' ') }} × {{ $pivot->nb_mois }} mois
                    </p>
                </div>
            </div>
        @empty
            <p class="text-gray-400 text-sm text-center py-4">Aucun tarif rattaché pour le moment.</p>
        @endforelse
    </div>

@endsection
