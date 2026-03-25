@extends('layouts.app')

@section('title', 'Historique des Tarifs')

@section('content')

    <div class="max-w-5xl mx-auto">

        {{-- En-tête --}}
        <div class="flex justify-between items-start mb-8">
            <div>
                <h2 class="text-2xl font-semibold text-gray-800">Historique des Tarifs</h2>
                <p class="text-gray-500 text-sm mt-1">
                    Classe : <span class="font-medium text-gray-700">{{ $classe->nom }}</span>
                    · {{ $classe->filiere->nom ?? '' }}
                    · {{ $classe->sousNiveau->nom ?? '' }}
                </p>
            </div>

            <div class="flex gap-3">
                <a href="{{ route('classes.tarif.create', $classe) }}"
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg shadow text-sm transition">
                    + Nouveau tarif
                </a>
                <a href="{{ route('classes.index') }}"
                    class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg text-sm transition">
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

        {{-- Timeline --}}
        @if ($historique->isEmpty())
            <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-10 text-center text-gray-400">
                Aucun tarif rattaché à cette classe.
            </div>
        @else
            <div class="space-y-4">
                @foreach ($historique as $pivot)
                    <div
                        class="bg-white rounded-2xl shadow-sm border
                                {{ $pivot->actif ? 'border-blue-200' : 'border-gray-100' }}
                                p-6">

                        <div class="flex items-start justify-between gap-4">

                            {{-- Statut --}}
                            <div class="flex items-center gap-3">
                                @if ($pivot->actif)
                                    <span
                                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full
                                                 text-xs font-semibold bg-green-100 text-green-700 border border-green-200">
                                        ● Actif
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full
                                                 text-xs font-medium bg-gray-100 text-gray-500 border border-gray-200">
                                        ○ Archivé
                                    </span>
                                @endif
                            </div>

                            {{-- Période --}}
                            <p class="text-xs text-gray-400">
                                Du {{ \Carbon\Carbon::parse($pivot->date_debut)->format('d/m/Y') }}
                                @if ($pivot->date_fin)
                                    au {{ \Carbon\Carbon::parse($pivot->date_fin)->format('d/m/Y') }}
                                @else
                                    · en cours
                                @endif
                            </p>
                        </div>

                        {{-- Montants --}}
                        <div class="mt-4 grid grid-cols-2 sm:grid-cols-4 gap-4">

                            <div class="bg-gray-50 rounded-lg p-3">
                                <p class="text-xs text-gray-400 mb-1">Inscription</p>
                                <p class="text-gray-800 font-semibold text-sm">
                                    {{ number_format($pivot->montant_inscription, 0, ',', ' ') }} FCFA
                                </p>
                            </div>

                            <div class="bg-gray-50 rounded-lg p-3">
                                <p class="text-xs text-gray-400 mb-1">Mensualité × {{ $pivot->nb_mois }} mois</p>
                                <p class="text-gray-800 font-semibold text-sm">
                                    {{ number_format($pivot->montant_mensualite, 0, ',', ' ') }} FCFA
                                </p>
                            </div>

                            @if ($pivot->montant_autre_frais > 0)
                                <div class="bg-gray-50 rounded-lg p-3">
                                    <p class="text-xs text-gray-400 mb-1">Autres frais</p>
                                    <p class="text-gray-800 font-semibold text-sm">
                                        {{ number_format($pivot->montant_autre_frais, 0, ',', ' ') }} FCFA
                                    </p>
                                </div>
                            @endif

                            <div class="{{ $pivot->actif ? 'bg-blue-50' : 'bg-gray-50' }} rounded-lg p-3">
                                <p class="text-xs {{ $pivot->actif ? 'text-blue-400' : 'text-gray-400' }} mb-1">Total
                                    annuel</p>
                                <p class="{{ $pivot->actif ? 'text-blue-700' : 'text-gray-700' }} font-bold text-sm">
                                    {{ number_format($pivot->montant_total, 0, ',', ' ') }} FCFA
                                </p>
                            </div>

                        </div>

                        {{-- Ajouté par --}}
                        @if ($pivot->createdBy)
                            <p class="text-xs text-gray-400 mt-3">
                                Rattaché par <span class="font-medium">{{ $pivot->createdBy->name }}</span>
                                le {{ $pivot->created_at->format('d/m/Y à H:i') }}
                            </p>
                        @endif

                    </div>
                @endforeach
            </div>
        @endif

    </div>

@endsection
