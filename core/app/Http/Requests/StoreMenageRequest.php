<?php

namespace App\Http\Requests;

use App\Rules\VlidateEnfantTotal;
use Illuminate\Foundation\Http\FormRequest;

class StoreMenageRequest extends FormRequest
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
            'producteur'    => 'required|exists:producteurs,id',
            'quartier' => 'required|max:255',
            'ageEnfant0A5' => ['required','integer', new VlidateEnfantTotal],
            'ageEnfant6A17' => ['required','integer', new VlidateEnfantTotal],
            'enfantscolarises' => ['required','integer', new VlidateEnfantTotal],
            'enfantsPasExtrait' => ['required','integer', new VlidateEnfantTotal],
            'enfantsPasExtrait6A17' => ['required','integer'],
            'sources_energies'  => 'required|max:255',
            'ordures_menageres'  => 'required|max:255',
            'separationMenage'  => 'required|max:255',
            'eauxToillette'  => 'required|max:255',
            'eauxVaisselle'  => 'required|max:255',
            'wc'  => 'required|max:255',
            'sources_eaux'  => 'required|max:255',
            'equipements'  => 'required|max:255',
            'traitementChamps'  => 'required|max:255',
            'activiteFemme'  => 'required|max:255',
            'nomApplicateur'=>'required_if:traitementChamps,==,Oui',
            'numeroApplicateur'=>'required_if:traitementChamps,==,Oui',
           
            // 'nomApplicateur'  => 'required|max:255',
            // 'numeroApplicateur'  => 'required|max:255',
        ];
    }
    public function messages()
    {
        return [
            'producteur.required' => 'Le champ producteur est obligatoire',
            'quartier.required' => 'Le champ quartier est obligatoire',
            'ageEnfant0A5.required' => 'Le champ ageEnfant0A5 est obligatoire',
            'ageEnfant6A17.required' => 'Le champ ageEnfant6A17 est obligatoire',
            'enfantscolarises.required' => 'Le champ enfantscolarises est obligatoire',
            'enfantsPasExtrait.required' => 'Le champ enfantsPasExtrait est obligatoire',
            'sources_energies.required' => 'Le champ sources_energies est obligatoire',
            'ordures_menageres.required' => 'Le champ ordures_menageres est obligatoire',
            'separationMenage.required' => 'Le champ separationMenage est obligatoire',
            'eauxToillette.required' => 'Le champ eauxToillette est obligatoire',
            'eauxVaisselle.required' => 'Le champ eauxVaisselle est obligatoire',
            'wc.required' => 'Le champ wc est obligatoire',
            'sources_eaux.required' => 'Le champ sources_eaux est obligatoire',
            'garde_machines.required' => 'Le champ garde_machines est obligatoire',
            'equipements.required' => 'Le champ equipements est obligatoire',
            'traitementChamps.required' => 'Le champ traitementChamps est obligatoire',
            'activiteFemme.required' => 'Le champ activitFemme est obligatoire',
        ];
    }
    public function attributes()
    {
        return [
            'producteur' => 'Producteur',
            'quartier' => 'Quartier',
            'ageEnfant0A5' => 'Age enfant 0 à 5',
            'ageEnfant6A17' => 'Age enfant 6 à 17',
            'enfantscolarises' => 'Enfants scolarisés',
            'enfantsPasExtrait' => 'Enfants pas extrait',
            'sources_energies' => 'Sources energies',
            'ordures_menageres' => 'Ordures menageres',
            'separationMenage' => 'Separation menage',
            'eauxToillette' => 'Eaux toillette',
            'eauxVaisselle' => 'Eaux vaisselle',
            'wc' => 'Wc',
            'sources_eaux' => 'Sources eaux',
            'garde_machines' => 'Garde machines',
            'equipements' => 'Equipements',
            'traitementChamps' => 'Traitement champs',
            'activiteFemme' => 'Activite femme',
        ];
    }

}
