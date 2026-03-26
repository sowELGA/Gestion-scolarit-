{{-- Partial partagé : create.blade.php & edit.blade.php --}}

<div class="grid grid-cols-1 sm:grid-cols-2 gap-6">

    {{-- Code --}}
    <div class="sm:col-span-2">
        <label class="block text-sm font-semibold text-gray-700 mb-2">
            Code de l'année <span class="text-red-500">*</span>
            <span class="text-xs font-normal text-gray-400 ml-1">Format : 2025-2026</span>
        </label>
        <input type="text" name="code" value="{{ old('code', $annee->code ?? '') }}" placeholder="2025-2026"
            class="w-full rounded-lg border border-gray-300 px-4 py-2
                      focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                      transition outline-none
                      @error('code') border-red-400 focus:ring-red-400 @enderror">
        @error('code')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

</div>

{{-- Séparateur --}}
<div class="border-t border-gray-100 pt-6">
    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-4">Dates de l'année scolaire</p>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">

        {{-- Date ouverture --}}
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">
                Date d'ouverture <span class="text-red-500">*</span>
            </label>
            <input type="date" name="date_ouverture"
                value="{{ old('date_ouverture', isset($annee) ? $annee->date_ouverture?->format('Y-m-d') : '') }}"
                class="w-full rounded-lg border border-gray-300 px-4 py-2
                          focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                          transition outline-none
                          @error('date_ouverture') border-red-400 @enderror">
            @error('date_ouverture')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Date fermeture --}}
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">
                Date de fermeture <span class="text-red-500">*</span>
            </label>
            <input type="date" name="date_fermeture"
                value="{{ old('date_fermeture', isset($annee) ? $annee->date_fermeture?->format('Y-m-d') : '') }}"
                class="w-full rounded-lg border border-gray-300 px-4 py-2
                          focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                          transition outline-none
                          @error('date_fermeture') border-red-400 @enderror">
            @error('date_fermeture')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

    </div>
</div>

{{-- Séparateur --}}
<div class="border-t border-gray-100 pt-6">
    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">Dates des inscriptions</p>
    <p class="text-xs text-gray-400 mb-4">
        Les inscriptions doivent ouvrir <strong>avant</strong> l'école et fermer <strong>après</strong> l'ouverture mais
        <strong>avant</strong> la fermeture.
    </p>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">

        {{-- Date début inscription --}}
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">
                Début des inscriptions <span class="text-red-500">*</span>
            </label>
            <input type="date" name="date_debut_inscription"
                value="{{ old('date_debut_inscription', isset($annee) ? $annee->date_debut_inscription?->format('Y-m-d') : '') }}"
                class="w-full rounded-lg border border-gray-300 px-4 py-2
                          focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                          transition outline-none
                          @error('date_debut_inscription') border-red-400 @enderror">
            @error('date_debut_inscription')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Date fin inscription --}}
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">
                Fin des inscriptions <span class="text-red-500">*</span>
            </label>
            <input type="date" name="date_fin_inscription"
                value="{{ old('date_fin_inscription', isset($annee) ? $annee->date_fin_inscription?->format('Y-m-d') : '') }}"
                class="w-full rounded-lg border border-gray-300 px-4 py-2
                          focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                          transition outline-none
                          @error('date_fin_inscription') border-red-400 @enderror">
            @error('date_fin_inscription')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

    </div>
</div>

{{-- Délai changement de classe --}}
<div class="border-t border-gray-100 pt-6">
    <label class="block text-sm font-semibold text-gray-700 mb-2">
        Délai de changement de classe (jours)
        <span class="text-xs font-normal text-gray-400 ml-1">après clôture des inscriptions — concerne les L1</span>
    </label>
    <input type="number" name="delai_changement_classe"
        value="{{ old('delai_changement_classe', $annee->delai_changement_classe ?? 15) }}" min="1"
        max="60"
        class="w-40 rounded-lg border border-gray-300 px-4 py-2
                  focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                  transition outline-none
                  @error('delai_changement_classe') border-red-400 @enderror">
    @error('delai_changement_classe')
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>
