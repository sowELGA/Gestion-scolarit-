@extends('layouts.app')

@section('title', 'Rattacher un Tarif')

@section('content')

    <div class="max-w-3xl mx-auto">

        <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-8">

            {{-- En-tête + Stepper --}}
            <div class="mb-8">
                <h2 class="text-2xl font-semibold text-gray-800">Rattacher un Tarif</h2>
                <p class="text-gray-500 text-sm mt-1">
                    Classe : <span class="font-medium text-gray-700">{{ $classe->nom }}</span>
                    ({{ $classe->filiere->nom ?? '' }} — {{ $classe->sousNiveau->nom ?? '' }})
                </p>

                {{-- Stepper --}}
                <div class="flex items-center gap-3 mt-5">
                    <div class="flex items-center gap-2">
                        <span
                            class="w-7 h-7 rounded-full bg-green-500 text-white text-xs flex items-center justify-center font-bold">✓</span>
                        <span class="text-sm text-green-600 font-medium">Informations</span>
                    </div>
                    <div class="flex-1 h-px bg-blue-300"></div>
                    <div class="flex items-center gap-2">
                        <span
                            class="w-7 h-7 rounded-full bg-blue-600 text-white text-xs flex items-center justify-center font-bold">2</span>
                        <span class="text-sm font-medium text-blue-600">Tarif</span>
                    </div>
                </div>
            </div>

            {{-- Tarif actuel si existant --}}
            @if ($tarifActif)
                <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-6">
                    <p class="text-sm font-semibold text-amber-700 mb-1">⚠ Remplacement de tarif</p>
                    <p class="text-sm text-amber-600">
                        Le tarif actuel
                        (<span class="font-medium">{{ number_format($tarifActif->pivot->montant_total, 0, ',', ' ') }}
                            FCFA</span>,
                        depuis le {{ \Carbon\Carbon::parse($tarifActif->pivot->date_debut)->format('d/m/Y') }})
                        sera désactivé et conservé dans l'historique.
                    </p>
                </div>
            @endif

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

            <form action="{{ route('classes.tarif.store', $classe) }}" method="POST" class="space-y-6"
                x-data="tarifSelector()">

                @csrf

                {{-- Sélection du tarif --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Tarif à appliquer <span class="text-red-500">*</span>
                    </label>

                    <div class="space-y-3">
                        @forelse($tarifs as $tarif)
                            <label class="flex items-start gap-4 p-4 border rounded-xl cursor-pointer transition"
                                :class="selected == {{ $tarif->id }} ?
                                    'border-blue-500 bg-blue-50' :
                                    'border-gray-200 hover:border-gray-300'">

                                <input type="radio" name="tarif_id" value="{{ $tarif->id }}" x-model="selected"
                                    class="mt-1 accent-blue-600" {{ old('tarif_id') == $tarif->id ? 'checked' : '' }}>

                                <div class="flex-1">
                                    <div class="flex items-center justify-between">
                                        <span class="font-semibold text-gray-800 text-sm">
                                            Inscription : {{ number_format($tarif->inscription, 0, ',', ' ') }} FCFA
                                        </span>
                                        <span class="text-xs text-gray-400">#{{ $tarif->id }}</span>
                                    </div>
                                    <p class="text-sm text-gray-600 mt-0.5">
                                        Mensualité : {{ number_format($tarif->mensualite, 0, ',', ' ') }} FCFA
                                        @if ($tarif->autre_frais > 0)
                                            · Autres frais : {{ number_format($tarif->autre_frais, 0, ',', ' ') }} FCFA
                                        @endif
                                    </p>
                                </div>
                            </label>
                        @empty
                            <p class="text-gray-400 text-sm py-4 text-center">
                                Aucun tarif disponible.
                                <a href="{{ route('tarifs.create') }}" class="text-blue-600 hover:underline">Créer un
                                    tarif</a>
                            </p>
                        @endforelse
                    </div>

                    @error('tarif_id')
                        <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Paramètres du tarif --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 pt-2 border-t border-gray-100">

                    {{-- Nombre de mois --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Nombre de mois
                        </label>
                        <input type="number" name="nb_mois" value="{{ old('nb_mois', 10) }}" min="1" max="12"
                            class="w-full rounded-lg border border-gray-300 px-4 py-2
                                      focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                                      transition outline-none
                                      @error('nb_mois') border-red-500 @enderror">
                        <p class="text-xs text-gray-400 mt-1">Utilisé pour calculer le montant total.</p>
                        @error('nb_mois')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Date de début --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Date de début
                        </label>
                        <input type="date" name="date_debut" value="{{ old('date_debut', now()->toDateString()) }}"
                            class="w-full rounded-lg border border-gray-300 px-4 py-2
                                      focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                                      transition outline-none
                                      @error('date_debut') border-red-500 @enderror">
                        @error('date_debut')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                </div>

                {{-- Aperçu calcul --}}
                <div x-show="selected" x-cloak class="bg-gray-50 border border-gray-200 rounded-xl p-4">
                    <p class="text-xs text-gray-500 font-medium uppercase tracking-wide mb-2">Aperçu du montant total</p>
                    <p class="text-gray-700 text-sm">
                        Inscription + (Mensualité × nb_mois) + Autres frais
                    </p>
                    <p class="text-sm text-gray-400 mt-1">Le montant exact sera calculé et enregistré automatiquement.</p>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                    <a href="{{ route('classes.index') }}"
                        class="px-5 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg transition">
                        Annuler
                    </a>
                    <button type="submit"
                        class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg shadow transition">
                        Rattacher le tarif
                    </button>
                </div>

            </form>
        </div>
    </div>

    <script>
        function tarifSelector() {
            return {
                selected: {{ old('tarif_id', 'null') }},
            }
        }
    </script>

@endsection
