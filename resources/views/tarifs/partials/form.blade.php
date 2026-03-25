<div class="space-y-5">

    <div>
        <label class="block text-gray-700 font-medium mb-2">
            Frais d'inscription
        </label>

        <input type="number" name="inscription" min="0" step="0.01"
            value="{{ old('inscription', $tarif->inscription ?? '') }}"
            class="w-full border rounded-lg px-4 py-2 focus:ring focus:border-blue-400">

        @error('inscription')
            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block text-gray-700 font-medium mb-2">
            Mensualité
        </label>

        <input type="number" name="mensualite" min="0" step="0.01"
            value="{{ old('mensualite', $tarif->mensualite ?? '') }}"
            class="w-full border rounded-lg px-4 py-2 focus:ring focus:border-blue-400">

        @error('mensualite')
            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block text-gray-700 font-medium mb-2">
            Autres frais
        </label>

        <input type="number" step="0.01" min="0" name="autre_frais" value="{{ old('autre_frais', 0) }}"
            required
        class="w-full border rounded-lg px-4 py-2 focus:ring focus:border-blue-400">

        @error('autre_frais')
            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

</div>
