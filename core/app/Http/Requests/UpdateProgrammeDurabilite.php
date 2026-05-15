<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProgrammeDurabilite extends FormRequest
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
            'libelle'=> 'required',
        ];
    }
    public function messages()
    {
        return [
            'libelle.required' => 'Le Nom programme de durabilité est obligatoire',
            
        ];
    }
    public function attributes()
    {
        return [
            'libelle' => 'Nom du programme de durabilité',
        ];
    }
}
