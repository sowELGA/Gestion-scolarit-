<header class="bg-white shadow-sm px-8 py-5 flex justify-between items-center">

    <h1 class="text-2xl font-bold text-gray-700 tracking-wide">
        @yield('title', 'Tableau de bord')
    </h1>

    <div class="flex items-center gap-4">
        <span class="text-sm text-gray-500">
            {{ now()->format('d M Y') }}
        </span>

        <div class="w-9 h-9 bg-blue-600 text-white flex items-center justify-center rounded-full font-bold">
            E
        </div>
    </div>

</header>
