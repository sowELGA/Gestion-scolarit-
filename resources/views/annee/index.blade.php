@extends('layouts.app')

@section('title', 'Années Académiques')

@section('content')

    <div class="max-w-6xl mx-auto">

        {{-- En-tête --}}
        <div class="flex justify-between items-center mb-8">
            <div>
                <h2 class="text-2xl font-semibold text-gray-800">Années Académiques</h2>
                <p class="text-gray-500 text-sm mt-1">Gestion du cycle de vie des années scolaires</p>
            </div>
            <a href="{{ route('annees.create') }}"
                class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg shadow transition">
                + Nouvelle Année
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
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 uppercase text-xs tracking-wider">
                    <tr>
                        <th class="px-6 py-4 text-left">Année</th>
                        <th class="px-6 py-4 text-left">Ouverture école</th>
                        <th class="px-6 py-4 text-left">Inscriptions</th>
                        <th class="px-6 py-4 text-left">Statut</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">
                    @forelse ($annees as $annee)
                        <tr class="hover:bg-gray-50 transition">

                            {{-- Code --}}
                            <td class="px-6 py-4">
                                <p class="font-semibold text-gray-800">{{ $annee->code }}</p>
                            </td>

                            {{-- Dates école --}}
                            <td class="px-6 py-4 text-gray-600">
                                <p>{{ $annee->date_ouverture->format('d/m/Y') }}</p>
                                <p class="text-xs text-gray-400">
                                    → {{ $annee->date_fermeture->format('d/m/Y') }}
                                </p>
                            </td>

                            {{-- Dates inscriptions --}}
                            <td class="px-6 py-4 text-gray-600">
                                <p>{{ $annee->date_debut_inscription->format('d/m/Y') }}</p>
                                <p class="text-xs text-gray-400">
                                    → {{ $annee->date_fin_inscription->format('d/m/Y') }}
                                </p>
                            </td>

                            {{-- Statut badge --}}
                            <td class="px-6 py-4">
                                <span
                                    class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium border
                                             {{ $annee->couleurStatut() }}">
                                    {{ $annee->libelleStatut() }}
                                </span>
                            </td>

                            {{-- Actions --}}
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end items-center gap-3">

                                    {{-- Bouton transition --}}
                                    @if ($annee->prochainStatut())
                                        <form action="{{ route('annees.avancer', $annee) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                onclick="return confirm('{{ $annee->libelleAction() }} ?')"
                                                class="text-xs px-3 py-1.5 rounded-lg font-medium transition
                                                           bg-blue-50 text-blue-700 hover:bg-blue-100 border border-blue-200">
                                                {{ $annee->libelleAction() }}
                                            </button>
                                        </form>
                                    @endif

                                    <a href="{{ route('annees.show', $annee) }}"
                                        class="text-gray-500 hover:text-gray-700 font-medium transition text-xs">
                                        Détails
                                    </a>

                                    @if ($annee->estModifiable())
                                        <a href="{{ route('annees.edit', $annee) }}"
                                            class="text-blue-600 hover:text-blue-800 font-medium transition">
                                            Modifier
                                        </a>

                                        <form action="{{ route('annees.destroy', $annee) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                onclick="return confirm('Supprimer l\'année {{ $annee->code }} ?')"
                                                class="text-red-600 hover:text-red-800 font-medium transition">
                                                Supprimer
                                            </button>
                                        </form>
                                    @endif

                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-gray-400">
                                Aucune année académique enregistrée.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">{{ $annees->links() }}</div>

    </div>

@endsection
