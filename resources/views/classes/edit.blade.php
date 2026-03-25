@extends('layouts.app')

@section('title', 'Modifier Classe')

@section('content')

    <div class="max-w-3xl mx-auto">

        <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-8">

            <div class="mb-8">
                <h2 class="text-2xl font-semibold text-gray-800">Modifier la Classe</h2>
                <p class="text-gray-500 text-sm mt-1">
                    Modification des informations de <span class="font-medium text-gray-700">{{ $classe->nom }}</span>.
                    Pour changer le tarif, utilisez "Rattacher un tarif".
                </p>
            </div>

            {{-- Erreurs --}}
            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-600 p-4 rounded-lg mb-6">
                    <ul class="list-disc pl-5 space-y-1 text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Tarif actif (lecture seule) --}}
            @if ($tarifActif)
                <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 mb-6 flex items-center justify-between">
                    <div>
                        <p class="text-xs text-blue-500 font-medium uppercase tracking-wide mb-1">Tarif actuellement appliqué
                        </p>
                        <p class="text-gray-800 font-semibold">
                            {{ number_format($tarifActif->pivot->montant_total, 0, ',', ' ') }} FCFA
                            <span class="text-sm font-normal text-gray-500">
                                ({{ number_format($tarifActif->pivot->montant_inscription, 0, ',', ' ') }} inscription +
                                {{ number_format($tarifActif->pivot->montant_mensualite, 0, ',', ' ') }} ×
                                {{ $tarifActif->pivot->nb_mois }} mois)
                            </span>
                        </p>
                        <p class="text-xs text-blue-400 mt-1">
                            Depuis le {{ \Carbon\Carbon::parse($tarifActif->pivot->date_debut)->format('d/m/Y') }}
                        </p>
                    </div>
                    <a href="{{ route('classes.tarif.create', $classe) }}"
                        class="text-sm text-blue-600 hover:text-blue-800 font-medium whitespace-nowrap ml-4">
                        Changer le tarif →
                    </a>
                </div>
            @else
                <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-6 flex items-center justify-between">
                    <p class="text-amber-700 text-sm">⚠ Aucun tarif rattaché à cette classe.</p>
                    <a href="{{ route('classes.tarif.create', $classe) }}"
                        class="text-sm text-amber-700 hover:text-amber-900 font-semibold ml-4">
                        Rattacher un tarif →
                    </a>
                </div>
            @endif

            <form action="{{ route('classes.update', $classe) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                @include('classes.partials.form-info')

                <div class="flex justify-between items-center pt-4 border-t border-gray-100">
                    <a href="{{ route('classes.historique', $classe) }}"
                        class="text-sm text-gray-500 hover:text-gray-700 transition">
                        Voir l'historique des tarifs
                    </a>

                    <div class="flex gap-3">
                        <a href="{{ route('classes.index') }}"
                            class="px-5 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg transition">
                            Annuler
                        </a>
                        <button type="submit"
                            class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg shadow transition">
                            Mettre à jour
                        </button>
                    </div>
                </div>
            </form>

        </div>
    </div>

@endsection
