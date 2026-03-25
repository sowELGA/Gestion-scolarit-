{{-- Partial partagé : create.blade.php & edit.blade.php --}}

<div class="grid grid-cols-1 sm:grid-cols-2 gap-6">

    {{-- Code --}}
    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-2">
            Code <span class="text-red-500">*</span>
        </label>
        <input type="text" name="code" value="{{ old('code', $classe->code ?? '') }}" placeholder="Ex: 3IRT-A"
            class="w-full rounded-lg border border-gray-300 px-4 py-2
                      focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                      transition duration-200 outline-none uppercase
                      @error('code') border-red-500 focus:ring-red-400 @enderror">
        @error('code')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    {{-- Nom --}}
    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-2">
            Nom <span class="text-red-500">*</span>
        </label>
        <input type="text" name="nom" value="{{ old('nom', $classe->nom ?? '') }}"
            placeholder="Ex: 3ème Année Informatique A"
            class="w-full rounded-lg border border-gray-300 px-4 py-2
                      focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                      transition duration-200 outline-none
                      @error('nom') border-red-500 focus:ring-red-400 @enderror">
        @error('nom')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

</div>

<div class="grid grid-cols-1 sm:grid-cols-2 gap-6">

    {{-- Filière --}}
    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-2">
            Filière <span class="text-red-500">*</span>
        </label>
        <select name="filiere_id"
            class="w-full rounded-lg border border-gray-300 px-4 py-2 bg-white
                       focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                       transition duration-200 outline-none
                       @error('filiere_id') border-red-500 focus:ring-red-400 @enderror">
            <option value="">— Sélectionner une filière —</option>
            @foreach ($filieres as $filiere)
                <option value="{{ $filiere->id }}"
                    {{ old('filiere_id', $classe->filiere_id ?? '') == $filiere->id ? 'selected' : '' }}>
                    {{ $filiere->code }} — {{ $filiere->nom }}
                </option>
            @endforeach
        </select>
        @error('filiere_id')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    {{-- Sous-Niveau --}}
    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-2">
            Sous-Niveau <span class="text-red-500">*</span>
        </label>
        <select name="sous_niveau_id"
            class="w-full rounded-lg border border-gray-300 px-4 py-2 bg-white
                       focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                       transition duration-200 outline-none
                       @error('sous_niveau_id') border-red-500 focus:ring-red-400 @enderror">
            <option value="">— Sélectionner un sous-niveau —</option>
            @foreach ($sousNiveaux as $sn)
                <option value="{{ $sn->id }}"
                    {{ old('sous_niveau_id', $classe->sous_niveau_id ?? '') == $sn->id ? 'selected' : '' }}>
                    {{ $sn->niveau->nom ?? '' }} — {{ $sn->nom }}
                </option>
            @endforeach
        </select>
        @error('sous_niveau_id')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

</div>
