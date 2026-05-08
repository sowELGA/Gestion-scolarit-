@extends('layouts.app')

@section('title', 'Année Académique')

@section('content')

    <div class="max-w-4xl mx-auto">

        {{-- En-tête --}}
        <div class="flex justify-between items-start mb-8">
            <div>
                <h2 class="text-2xl font-semibold text-gray-800">{{ $annee->code }}</h2>
                <p class="text-gray-500 text-sm mt-1">Détail de l'année académique</p>
            </div>
            <div class="flex gap-3 items-center">

                {{-- Bouton transition --}}
                @if ($annee->prochainStatut())
                    <form action="{{ route('annees.avancer', $annee) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit" onclick="return confirm('{{ $annee->libelleAction() }} — confirmer ?')"
                            class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg shadow transition text-sm font-medium">
                            {{ $annee->libelleAction() }} →
                        </button>
                    </form>
                @endif

                @if ($annee->estModifiable())
                    <a href="{{ route('annees.edit', $annee) }}"
                        class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg text-sm transition">
                        Modifier
                    </a>
                @endif

                <a href="{{ route('annees.index') }}"
                    class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-lg text-sm transition">
                    ← Retour
                </a>
            </div>
        </div>

        {{-- Notifications --}}
        @if (session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 p-4 rounded-lg mb-6">
                {{ session('success') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 p-4 rounded-lg mb-6">
                {{ $errors->first() }}
            </div>
        @endif

        {{-- Statut + cycle de vie --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-4">Cycle de vie</p>

            <div class="flex items-center gap-2 flex-wrap">
                @php
                    $etapes = [
                        \App\Models\AnneeAcademique::STATUT_BROUILLON,
                        \App\Models\AnneeAcademique::STATUT_PUBLIE,
                        \App\Models\AnneeAcademique::STATUT_INSCRIPTION_OUVERTE,
                        \App\Models\AnneeAcademique::STATUT_INSCRIPTION_FERMEE,
                        \App\Models\AnneeAcademique::STATUT_CLOTUREE,
                    ];
                    $statutsOrdre = array_flip($etapes);
                    $statutActuel = $statutsOrdre[$annee->statut] ?? 0;
                @endphp

                @foreach ($etapes as $i => $etape)
                    <div class="flex items-center gap-2">
                        <div
                            class="flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-medium border
                            {{ $i < $statutActuel ? 'bg-green-100 text-green-600 border-green-200' : '' }}
                            {{ $i === $statutActuel ? $annee->couleurStatut() : '' }}
                            {{ $i > $statutActuel ? 'bg-gray-100 text-gray-400 border-gray-200' : '' }}">
                            @if ($i < $statutActuel)
                                ✓
                            @endif
                            {{ $annee->libelleStatut() === $annee->libelleStatut() && $i === $statutActuel
                                ? $annee->libelleStatut()
                                : match ($etape) {
                                    'brouillon' => 'Brouillon',
                                    'publie' => 'Publié',
                                    'inscription_ouverte' => 'Inscriptions ouvertes',
                                    'inscription_fermee' => 'Inscriptions fermées',
                                    'cloturee' => 'Clôturée',
                                } }}
                        </div>
                        @if (!$loop->last)
                            <span class="text-gray-300 text-xs">→</span>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Informations --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

            {{-- Dates école --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-4">Année scolaire</p>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500">Ouverture</span>
                        <span class="text-sm font-medium text-gray-800">
                            {{ $annee->date_ouverture?->format('d/m/Y') ?? '—' }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500">Fermeture</span>
                        <span class="text-sm font-medium text-gray-800">
                            {{ $annee->date_fermeture?->format('d/m/Y') ?? '—' }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Dates inscriptions --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-4">Inscriptions</p>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500">Début</span>
                        <span class="text-sm font-medium text-gray-800">
                            {{ $annee->date_debut_inscription?->format('d/m/Y') ?? '—' }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500">Fin</span>
                        <span class="text-sm font-medium text-gray-800">
                            {{ $annee->date_fin_inscription?->format('d/m/Y') ?? '—' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

    </div>

@endsection
