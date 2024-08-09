<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfilRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];

        if ($this->has('nom')) {
            $rules['nom'] = 'string|max:255';
        }

        if ($this->has('prenom')) {
            $rules['prenom'] = 'string|max:255';
        }

        if ($this->has('statut')) {
            $rules['statut'] = 'in:inactif,en attente,actif';
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'image.image' => 'The image must be a valid image file.',
            'image.mimes' => 'The image must be a file of type: jpeg, png, jpg, gif.',
            'image.max' => 'The image size must not exceed 2048 kilobytes.',
            'nom.string' => 'The name must be a string.',
            'nom.max' => 'The name must not exceed 255 characters.',
            'prenom.string' => 'The prenom must be a string.',
            'prenom.max' => 'The prenom must not exceed 255 characters.',
            'statut.in' => 'The statut must be one of the following types: inactif, en attente, actif.',
        ];
    }
}
