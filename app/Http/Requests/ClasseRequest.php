<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ClasseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        // Normalisation uniquement à l'étape 1 (code/nom présents)
        if ($this->has('code')) {
            $this->merge([
                'code' => strtoupper(trim($this->code ?? '')),
                'nom'  => ucwords(strtolower(trim($this->nom ?? ''))),
            ]);
        }
    }

    public function rules(): array
    {
        $classeId = $this->route('classe')?->id;

        // Étape 2 : rattachement du tarif
        if ($this->routeIs('classes.tarif.store')) {
            return [
                'tarif_id'   => ['required', 'exists:tarifs,id'],
                'nb_mois'    => ['nullable', 'integer', 'min:1', 'max:12'],
                'date_debut' => ['nullable', 'date'],
            ];
        }

        // Étape 1 : création / modification de la classe (sans tarif)
        return [
            'code' => [
                'required',
                'string',
                'max:20',
                Rule::unique('classes', 'code')->ignore($classeId),
            ],
            'nom' => [
                'required',
                'string',
                'max:100',
                Rule::unique('classes', 'nom')->ignore($classeId),
            ],
            'filiere_id'     => ['required', 'exists:filieres,id'],
            'sous_niveau_id' => ['required', 'exists:sous_niveaux,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'code.required'           => 'Le code est obligatoire.',
            'code.unique'             => 'Ce code existe déjà.',
            'nom.required'            => 'Le nom est obligatoire.',
            'nom.unique'              => 'Ce nom existe déjà.',
            'filiere_id.required'     => 'La filière est obligatoire.',
            'filiere_id.exists'       => 'La filière sélectionnée est invalide.',
            'sous_niveau_id.required' => 'Le sous-niveau est obligatoire.',
            'sous_niveau_id.exists'   => 'Le sous-niveau sélectionné est invalide.',
            'tarif_id.required'       => 'Veuillez sélectionner un tarif.',
            'tarif_id.exists'         => 'Le tarif sélectionné est invalide.',
            'nb_mois.min'             => 'Le nombre de mois doit être au moins 1.',
            'nb_mois.max'             => 'Le nombre de mois ne peut pas dépasser 12.',
            'date_debut.date'         => 'La date de début est invalide.',
        ];
    }
}