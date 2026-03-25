@extends('layouts.app')

@section('title', 'Gestion des Tarifs')

@section('content')

    <div class="max-w-xl mx-auto bg-white shadow-xl rounded-xl p-8">

        <h2 class="text-2xl font-semibold mb-6">
            Modifier le Tarif
        </h2>

        <form action="{{ route('tarifs.update', $tarif) }}" method="POST">
            @csrf
            @method('PUT')

            @include('tarifs.partials.form')

            <div class="mt-6 flex justify-end gap-3">
                <a href="{{ route('tarifs.index') }}"
                    class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition">
                    Annuler
                </a>

                <button type="submit"
                    class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition shadow">
                    Mettre à jour
                </button>
            </div>

        </form>

    </div>

@endsection
