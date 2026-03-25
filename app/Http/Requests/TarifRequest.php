<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TarifRequest extends FormRequest
{
    // FIX : méthode manquante — sans elle Laravel refuse toutes les requêtes (403)
    public function authorize(): bool
    {
        return true;
    }

    public function prepareForValidation()
    {
        $this->merge([
            'inscription' => $this->inscription !== null ? trim($this->inscription) : 0,
            'mensualite'  => $this->mensualite !== null ? trim($this->mensualite) : 0,
            'autre_frais' => $this->autre_frais !== null && $this->autre_frais !== ''
                ? trim($this->autre_frais)
                : 0,
        ]);
    }

    public function rules(): array
    {
        $id = $this->route('tarif')?->id;

        return [
            'inscription' => [
                'required',
                'numeric',
                'min:0',
                Rule::unique('tarifs')
                    ->where(
                        fn($q) => $q
                            ->where('inscription', $this->inscription)
                            ->where('mensualite', $this->mensualite)
                    )
                    ->ignore($id),
            ],

            'mensualite' => [
                'required',
                'numeric',
                'min:0',
            ],

            'autre_frais' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            // FIX : 'actif' n'est pas validé ici — il est géré automatiquement par le modèle
        ];
    }

    public function messages(): array
    {
        return [
            'inscription.min'    => 'Le montant inscription ne peut pas être négatif.',
            'mensualite.min'     => 'La mensualité ne peut pas être négative.',
            'autre_frais.min'    => 'Les autres frais ne peuvent pas être négatifs.',
            'inscription.unique' => 'Un tarif avec la même inscription et mensualité existe déjà.',
        ];
    }
}