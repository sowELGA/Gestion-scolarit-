@extends('layouts.app')

@section('title', 'Gestion des Tarifs')

@section('content')

    <div class="max-w-xl mx-auto bg-white shadow-xl rounded-xl p-8">

        <h2 class="text-2xl font-semibold mb-6">Nouveau Tarif</h2>

        <form action="{{ route('tarifs.store') }}" method="POST">
            @csrf

            @include('tarifs.partials.form')

            <div class="mt-6 flex justify-end gap-3">
                <a href="{{ route('tarifs.index') }}"
                    class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition">
                    Annuler
                </a>

                <button type="submit"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition shadow">
                    Enregistrer
                </button>
            </div>

        </form>

    </div>

@endsection
