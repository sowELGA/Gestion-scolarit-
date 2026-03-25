@extends('layouts.app')

@section('title', 'Nouvelle Classe')

@section('content')

    <div class="max-w-3xl mx-auto">

        <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-8">

            {{-- En-tête --}}
            <div class="mb-8">
                <h2 class="text-2xl font-semibold text-gray-800">Créer une Classe</h2>
                <p class="text-gray-500 text-sm mt-1">
                    Étape 1 sur 2 — Informations de la classe. Le tarif sera rattaché dans un second temps.
                </p>

                {{-- Stepper --}}
                <div class="flex items-center gap-3 mt-5">
                    <div class="flex items-center gap-2">
                        <span
                            class="w-7 h-7 rounded-full bg-blue-600 text-white text-xs flex items-center justify-center font-bold">1</span>
                        <span class="text-sm font-medium text-blue-600">Informations</span>
                    </div>
                    <div class="flex-1 h-px bg-gray-200"></div>
                    <div class="flex items-center gap-2">
                        <span
                            class="w-7 h-7 rounded-full bg-gray-200 text-gray-400 text-xs flex items-center justify-center font-bold">2</span>
                        <span class="text-sm text-gray-400">Tarif</span>
                    </div>
                </div>
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

            <form action="{{ route('classes.store') }}" method="POST" class="space-y-6">
                @csrf

                @include('classes.partials.form-info')

                <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                    <a href="{{ route('classes.index') }}"
                        class="px-5 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg transition">
                        Annuler
                    </a>
                    <button type="submit"
                        class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg shadow transition">
                        Créer la Classe →
                    </button>
                </div>
            </form>

        </div>
    </div>

@endsection
