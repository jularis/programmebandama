<?php

namespace App\Http\Requests;

use App\Rules\ValidTravailleurs;
use Illuminate\Foundation\Http\FormRequest;

class StoreInfoRequest extends FormRequest
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
            'foretsjachere' => 'required',
            'autresCultures'  => 'required|max:255',
            'autreActivite' => 'required|max:255',
            'travailleurs'  => 'required|max:255',
            'travailleurspermanents'  => ['required', 'integer'],
            'travailleurstemporaires'  => ['required', 'integer'],
            'mobileMoney'  => 'required|max:255',
            'mainOeuvreFamilial'  => 'required|max:255',
        ];
    }
    public function messages()
    {
        return [
            'foretsjachere.required' => 'Le champ foretsjachere est obligatoire',
            'autresCultures.required' => 'Le champ autresCultures est obligatoire',
            'autreActivite.required' => 'Le champ autreActivite est obligatoire',
            'travailleurs.required' => 'Le champ travailleurs est obligatoire',
            'travailleurspermanents.required' => 'Le champ travailleurspermanents est obligatoire',
            'travailleurstemporaires.required' => 'Le champ travailleurstemporaires est obligatoire',
            'mobileMoney.required' => 'Le champ mobileMoney est obligatoire',
            'compteBanque.required' => 'Le champ compteBanque est obligatoire',
            'mainOeuvreFamilial.required' => 'Le champ mainOeuvreFamilial est obligatoire',
        ];
    }
    public function attributes()
    {
        return [
            'foretsjachere' => 'Forets jachere',
            'autresCultures' => 'Autres cultures',
            'autreActivite' => 'Autre activite',
            'travailleurs' => 'Travailleurs',
            'travailleurspermanents' => 'Travailleurs permanents',
            'travailleurstemporaires' => 'Travailleurs temporaires',
            'mobileMoney' => 'Mobile money',
            'compteBanque' => 'Compte banque',
            'mainOeuvreFamilial' => 'Main oeuvre familiale',
        ];
    }
}
