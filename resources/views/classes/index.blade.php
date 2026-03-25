@extends('layouts.app')

@section('title', 'Gestion des Classes')

@section('content')

    <div class="max-w-6xl mx-auto">

        {{-- En-tête --}}
        <div class="flex justify-between items-center mb-8">
            <div>
                <h2 class="text-2xl font-semibold text-gray-800">Gestion des Classes</h2>
                <p class="text-gray-500 text-sm">Liste complète des classes enregistrées</p>
            </div>

            <a href="{{ route('classes.create') }}"
                class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg shadow transition">
                + Nouvelle Classe
            </a>
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

        {{-- Tableau --}}
        <div class="bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-gray-600 uppercase text-xs tracking-wider">
                    <tr>
                        <th class="px-6 py-4 text-left">Code</th>
                        <th class="px-6 py-4 text-left">Nom</th>
                        <th class="px-6 py-4 text-left">Filière</th>
                        <th class="px-6 py-4 text-left">Sous-Niveau</th>
                        <th class="px-6 py-4 text-left">Tarif actif</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">
                    @forelse($classes as $classe)
                        @php $tarifActif = $classe->tarifs->first() @endphp
                        <tr class="hover:bg-gray-50 transition">

                            <td class="px-6 py-4 font-medium text-gray-700">
                                {{ $classe->code }}
                            </td>

                            <td class="px-6 py-4 text-gray-800 font-medium">
                                {{ $classe->nom }}
                            </td>

                            <td class="px-6 py-4 text-gray-600">
                                {{ $classe->filiere->nom ?? '—' }}
                            </td>

                            <td class="px-6 py-4 text-gray-600">
                                {{ $classe->sousNiveau->nom ?? '—' }}
                            </td>

                            <td class="px-6 py-4">
                                @if ($tarifActif)
                                    <div class="text-gray-700">
                                        <span class="font-medium">
                                            {{ number_format($tarifActif->pivot->montant_total, 0, ',', ' ') }} FCFA
                                        </span>
                                        <p class="text-xs text-gray-400 mt-0.5">
                                            Depuis le
                                            {{ \Carbon\Carbon::parse($tarifActif->pivot->date_debut)->format('d/m/Y') }}
                                        </p>
                                    </div>
                                @else
                                    <span
                                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-amber-50 text-amber-700 border border-amber-200">
                                        ⚠ Aucun tarif
                                    </span>
                                @endif
                            </td>

                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end items-center gap-3">

                                    {{-- Rattacher un tarif si aucun actif --}}
                                    @if (!$tarifActif)
                                        <a href="{{ route('classes.tarif.create', $classe) }}"
                                            class="text-amber-600 hover:text-amber-800 font-medium transition text-xs">
                                            Rattacher tarif
                                        </a>
                                    @else
                                        <a href="{{ route('classes.historique', $classe) }}"
                                            class="text-gray-500 hover:text-gray-700 font-medium transition text-xs">
                                            Historique
                                        </a>
                                    @endif

                                    <a href="{{ route('classes.edit', $classe) }}"
                                        class="text-blue-600 hover:text-blue-800 font-medium transition">
                                        Modifier
                                    </a>

                                    <form action="{{ route('classes.destroy', $classe) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            onclick="return confirm('Supprimer la classe {{ $classe->nom }} ?')"
                                            class="text-red-600 hover:text-red-800 font-medium transition">
                                            Supprimer
                                        </button>
                                    </form>

                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-gray-400">
                                Aucune classe enregistrée.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $classes->links() }}
        </div>

    </div>

@endsection
