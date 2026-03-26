<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AnneeAcademiqueRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('annee')?->id;

        return [
            'code' => [
                'required',
                'string',
                'max:20',
                'regex:/^\d{4}-\d{4}$/', // format : 2025-2026
                Rule::unique('annees_academiques', 'code')->ignore($id),
            ],

            'date_ouverture' => [
                'required',
                'date',
            ],

            'date_fermeture' => [
                'required',
                'date',
                // DD < DF
                'after:date_ouverture',
            ],

            'date_debut_inscription' => [
                'required',
                'date',
                // Ouverture inscription AVANT ouverture école
                'before:date_ouverture',
            ],

            'date_fin_inscription' => [
                'required',
                'date',
                // DDI < DFI
                'after:date_debut_inscription',
                // Fermeture inscription APRÈS ouverture école ET AVANT fermeture école
                'after:date_ouverture',
                'before:date_fermeture',
            ],

            'delai_changement_classe' => [
                'nullable',
                'integer',
                'min:1',
                'max:60',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'code.required'                    => 'Le code est obligatoire.',
            'code.unique'                      => 'Cette année académique existe déjà.',
            'code.regex'                       => 'Le format doit être : 2025-2026.',

            'date_ouverture.required'          => 'La date d\'ouverture est obligatoire.',
            'date_ouverture.date'              => 'La date d\'ouverture est invalide.',

            'date_fermeture.required'          => 'La date de fermeture est obligatoire.',
            'date_fermeture.after'             => 'La date de fermeture doit être après la date d\'ouverture.',

            'date_debut_inscription.required'  => 'La date de début des inscriptions est obligatoire.',
            'date_debut_inscription.before'    => 'Les inscriptions doivent ouvrir avant l\'ouverture de l\'école.',

            'date_fin_inscription.required'    => 'La date de fin des inscriptions est obligatoire.',
            'date_fin_inscription.after:date_debut_inscription'
            => 'La date de fin des inscriptions doit être après la date de début.',
            'date_fin_inscription.after:date_ouverture'
            => 'La clôture des inscriptions doit être après l\'ouverture de l\'école.',
            'date_fin_inscription.before'      => 'La clôture des inscriptions doit être avant la fermeture de l\'école.',

            'delai_changement_classe.min'      => 'Le délai doit être d\'au moins 1 jour.',
            'delai_changement_classe.max'      => 'Le délai ne peut pas dépasser 60 jours.',
        ];
    }
}
