@extends('layouts.app')

@section('title', 'Gestion des Tarifs')

@section('content')

    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-semibold">Liste des Tarifs</h2>

        <a href="{{ route('tarifs.create') }}"
            class="bg-blue-600 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-700 transition">
            + Nouveau Tarif
        </a>
    </div>

    @if (session('success'))
        <div class="bg-green-100 text-green-700 p-3 rounded-lg mb-4 shadow">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="bg-red-100 text-red-700 p-3 rounded-lg mb-4 shadow">
            {{ $errors->first() }}
        </div>
    @endif

    <div class="bg-white shadow-lg rounded-xl overflow-hidden">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-100 text-gray-700 uppercase text-xs">
                <tr>
                    <th class="px-6 py-3 text-left">Inscription</th>
                    <th class="px-6 py-3 text-left">Mensualité</th>
                    <th class="px-6 py-3 text-left">Autres frais</th>
                    <th class="px-6 py-3 text-center">Statut</th>
                    <th class="px-6 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tarifs as $tarif)
                    <tr class="border-t hover:bg-gray-50 transition">
                        <td class="px-6 py-4">{{ number_format($tarif->inscription, 0, ',', ' ') }} FCFA</td>
                        <td class="px-6 py-4">{{ number_format($tarif->mensualite, 0, ',', ' ') }} FCFA</td>
                        <td class="px-6 py-4">{{ number_format($tarif->autre_frais, 0, ',', ' ') }} FCFA</td>
                        <td class="px-6 py-4 text-center">
                            @if (!$tarif->actif)
                                <span class="bg-gray-200 text-gray-600 px-3 py-1 rounded-full text-xs">
                                    Inactif
                                </span>
                            @else
                                <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs">
                                    Actif
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right space-x-3">

                            <a href="{{ route('tarifs.edit', $tarif) }}" class="text-blue-600 hover:underline">
                                Modifier
                            </a>

                            <form action="{{ route('tarifs.destroy', $tarif) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')

                                <button type="submit" onclick="return confirm('Confirmer suppression ?')"
                                    class="text-red-600 hover:underline">
                                    Supprimer
                                </button>
                            </form>

                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-6 text-center text-gray-500">
                            Aucun tarif enregistré.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

@endsection
