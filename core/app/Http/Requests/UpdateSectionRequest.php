<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSectionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
           'cooperative_id' =>'required|exists:cooperatives,id',
            'libelle' => 'required|max:255',
            'sousPrefecture' => 'required|max:255',
        ];
    }

    public function messages()
    {
        return [
            'cooperative_id.required' => 'La coopérative est obligatoire',
            'cooperative_id.exists' => 'La coopérative n\'existe pas',
            'libelle.required' => 'Le nom de la section est obligatoire',
            'libelle.max' => 'Le nom de la section ne doit pas dépasser 255 caractères',
            'sousPrefecture.required' => 'La sous-préfecture est obligatoire',
            'sousPrefecture.max' => 'La sous-préfecture ne doit pas dépasser 255 caractères',
        ];
    }

    public function attributes()
    {
        return [
            'localite_id' => 'localité',
            'libelle' => 'Nom de la section',
            'sousPrefecture' => 'Sous-préfecture',
        ];
    }
}
