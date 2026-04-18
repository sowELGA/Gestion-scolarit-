<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Gestion Scolarité') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

{{-- FIX : overflow-hidden sur body pour bloquer le scroll global --}}

<body class="bg-gray-100 font-sans antialiased overflow-hidden h-screen">

    {{-- FIX : h-screen pour que le conteneur prenne exactement la hauteur de l'écran --}}
    <div class="flex h-screen">

        {{-- Sidebar --}}
        @include('layouts.partials.sidebar')

        {{-- Contenu principal scrollable --}}
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">

            {{-- Header --}}
            @include('layouts.partials.header')

            {{-- FIX : overflow-y-auto ici uniquement — seul le contenu scrolle --}}
            <main class="flex-1 overflow-y-auto p-8">
                <div class="max-w-7xl mx-auto">
                    @yield('content')
                </div>
            </main>

            {{-- Footer --}}
            @include('layouts.partials.footer')

        </div>
    </div>

    <script src="//unpkg.com/alpinejs" defer></script>
</body>

</html>
