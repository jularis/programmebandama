<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProducteurRequest extends FormRequest
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
            'programme_id'=>['required','exists:programmes,id'],
            'proprietaires' => 'required',
            'certificats' => 'required',
            'habitationProducteur' => 'required',
            'statut' => 'required',
            'statutMatrimonial' => 'required',
            'localite_id'    => 'required|exists:localites,id',
            'nom' => 'required|max:255',
            'prenoms'  => 'required|max:255',
            'sexe'  => 'required|max:255',
            'nationalite'  => 'required|max:255',
            'dateNaiss'  => 'required|max:255',
            'niveau_etude'  => 'required|max:255',
            'type_piece'  => 'required|max:255',
            'num_ccc' => ['nullable', 'regex:/^\d{11}$/', 'unique:producteurs,num_ccc'], // Champ "num_ccc" peut être vide
            'anneeDemarrage' =>'required_if:proprietaires,==,Garantie',
            'anneeFin' =>'required_if:proprietaires,==,Garantie',
            'plantePartage'=>'required_if:proprietaires,==,Planté-partager',
            'numeroAssocie'=>'required_if:proprietaires,==,Planté-partager',
            'numeroAssocie' => Rule::when($this->proprietaires == 'Planté-partager', function () {
                return ['required', 'regex:/^\d{10}$/'];
            }),
            'typeCarteSecuriteSociale'=>'required',
            'autreCertificats'=>'required_if:certificats,==,Autre',
            'codeProd'=>'required_if:statut,==,Certifie',
            'certificat'=>'required_if:statut,==,Certifie',
            'autrePhone'=>'required_if:autreMembre,==,oui',
            'phone2' => Rule::when($this->autreMembre == 'oui', function () {
                return ['regex:/^\d{10}$/'];
            }),
            // 'phone2' => 'required_if:autreMembre,oui|min:10|max:10'
            // 'phone2' => 'required_if:autreMembre,oui|regex:/^\d{10}$/|unique:producteurs,phone2'

        ];
    }
    public function messages()
    {
        return [
            'programme_id.required' => 'Le programme est obligatoire',
            'proprietaires.required' => 'Le type de propriétaire est obligatoire',
            'certificats.required' => 'Le type de certificat est obligatoire',
            'habitationProducteur.required' => 'Le type d\'habitation est obligatoire',
            'statut.required' => 'Le statut est obligatoire',
            'statutMatrimonial.required' => 'Le statut matrimonial est obligatoire',
            'localite_id.required' => 'La localité est obligatoire',
            'nom.required' => 'Le nom est obligatoire',
            'prenoms.required' => 'Le prénom est obligatoire',
            'sexe.required' => 'Le sexe est obligatoire',
            'nationalite.required' => 'La nationalité est obligatoire',
            'dateNaiss.required' => 'La date de naissance est obligatoire',
            'phone1.required' => 'Le numéro de téléphone est obligatoire',
            'phone1.regex' => 'Le numéro de téléphone doit contenir exactement 10 chiffres.',
            'phone1.unique' => 'Ce numéro de téléphone est déjà utilisé.',
            'niveau_etude.required' => 'Le niveau d\'étude est obligatoire',
            'type_piece.required' => 'Le type de pièce est obligatoire',
            'numPiece.required' => 'Le numéro de pièce est obligatoire',
            'anneeDemarrage.required_if' => 'L\'année de démarrage est obligatoire',
            'anneeFin.required_if' => 'L\'année de fin est obligatoire',
            'plantePartage.required_if' => 'Le type de plante est obligatoire',
            'typeCarteSecuriteSociale.required' => 'Le type de carte de sécurité sociale est obligatoire',
            'autreCertificats.required_if' => 'Le type de certificat est obligatoire',
            'codeProdapp.required_if' => 'Le code Prodapp est obligatoire',
            'certificat.required_if' => 'Le certificat est obligatoire',
            'phone2.required_if' => 'Le numéro de téléphone est obligatoire',
            'phone2.regex' => 'Le numéro de téléphone doit contenir exactement 10 chiffres.',
            'phone2.unique' => 'Ce numéro de téléphone est déjà utilisé.',
            'autrePhone.required_if' => 'Le champ membre de famille est obligatoire',
            'num_ccc.regex'=>'numéro du conseil café cacao doit contenir 11 chiffres',
        ];
    }
    public function attributes()
    {
        return [
            'programme_id' => 'programme',
            'proprietaires' => 'propriétaire',
            'certificats' => 'certificat',
            'habitationProducteur' => 'habitation',
            'statut' => 'statut',
            'statutMatrimonial' => 'statut matrimonial',
            'localite_id'    => 'localité',
            'nom' => 'nom',
            'prenoms'  => 'prénom',
            'sexe'  => 'sexe',
            'nationalite'  => 'nationalité',
            'dateNaiss'  => 'date de naissance',
            'phone1'  => 'numéro de téléphone',
            'niveau_etude'  => 'niveau d\'étude',
            'type_piece'  => 'type de pièce',
            'numPiece'  => 'numéro de pièce',
            'anneeDemarrage' =>'année de démarrage',
            'anneeFin' =>'année de fin',
            'plantePartage'=>'Planté-partager',
            'typeCarteSecuriteSociale'=>'type de carte de sécurité sociale',
            'autreCertificats'=>'type de certificat',
            'codeProdapp'=>'code Prodapp',
            'certificat'=>'certificat',
            'phone2'=>'numéro de téléphone',
            'autrePhone'=>'membre de famille',
            'numCMU'=>'numéro de CMU',
            'num_ccc'=>'numéro du conseil café cacao',
        ];
    }
}
