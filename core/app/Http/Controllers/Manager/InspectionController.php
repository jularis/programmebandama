<?php

namespace App\Http\Controllers\Manager;

use App\Models\User;
use App\Models\Campagne;
use App\Models\Notation;
use App\Models\Parcelle;
use App\Constants\Status;
use App\Models\Localite;
use App\Models\Inspection;
use App\Models\Producteur;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Exports\ExportInspections;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\CategorieQuestionnaire;
use App\Models\InspectionQuestionnaire;
use App\Models\Producteur_certification;

class InspectionController extends Controller
{

    public function index()
    {
        $pageTitle      = "Gestion des inspections";
        $manager   = auth()->user();
        $staffs = User::whereHas('roles', function ($q) {
            $q->whereIn('name', ['Inspecteur','ADG','Coach']);
        })
            ->where('cooperative_id', $manager->cooperative_id)
            ->select('users.*')
            ->get();
        $producteurs  = Producteur::joinRelationship('localite.section')
        ->where([['cooperative_id', $manager->cooperative_id],['producteurs.status', 1]])->with('localite')->get();
        $localites = Localite::joinRelationship('section')->where([['cooperative_id',$manager->cooperative_id],['localites.status',1]])->get();
        $inspections = Inspection::dateFilter()->searchable([])->latest('id')->joinRelationship('producteur.localite.section')->where('sections.cooperative_id',$manager->cooperative_id)->where(function ($q) {
            if(request()->localite != null){
                $q->where('localite_id',request()->localite);
            }
            if(request()->producteur != null){
                $q->where('producteur_id',request()->producteur);
            }
            if(request()->staff != null){
                $q->where('formateur_id',request()->staff);
            }
        })->with('producteur','user')->paginate(getPaginate());

        return view('manager.inspection.index', compact('pageTitle', 'inspections','localites','staffs','producteurs'));
    }

    public function create()
    {
        $pageTitle = "Ajouter une inspection";
        $manager   = auth()->user();
        $producteurs  = Producteur::joinRelationship('localite.section')->where([['cooperative_id', $manager->cooperative_id],['producteurs.status',1]])->with('localite')->get();
        $localites = Localite::joinRelationship('section')->where([['cooperative_id',$manager->cooperative_id],['localites.status',1]])->get();
        $staffs = User::whereHas('roles', function ($q) {
            $q->whereIn('name', ['Inspecteur','ADG','Coach']);
        })
            ->where('cooperative_id', $manager->cooperative_id)
            ->select('users.*')
            ->get();
            $parcelles  = Parcelle::with('producteur')->get();
        return view('manager.inspection.create', compact('pageTitle', 'producteurs','localites','staffs','parcelles'));
    }

    public function store(Request $request)
    {
        $validationRule = [
            'parcelle'    => 'required|exists:parcelles,id',
            'producteur'    => 'required|exists:producteurs,id',
            'encadreur' => 'required|exists:users,id',
            'note'  => 'required|max:255',
            'date_evaluation'  => 'required|max:255',
        ];


        $request->validate($validationRule);

        $localite = Localite::where('id', $request->localite)->first();

        if ($localite->status == Status::NO) {
            $notify[] = ['error', 'Cette localité est désactivé'];
            return back()->withNotify($notify)->withInput();
        }

        if($request->id) {
            $inspection = Inspection::findOrFail($request->id);
            $message = "L'inspection a été mise à jour avec succès";

        } else {
            $inspection = new Inspection();
        }
        $campagne = Campagne::active()->first();

        $inspection->parcelle_id  = $request->parcelle;
        $inspection->producteur_id  = $request->producteur;
        $inspection->campagne_id  = $campagne->id;
        $inspection->formateur_id  = $request->encadreur;
        $inspection->certificat  = json_encode($request->certificat);
        $inspection->note  = $request->note;
        $inspection->total_question  = $request->total_question;
        $inspection->total_question_conforme  = $request->total_question_conforme;
        $inspection->total_question_non_conforme  = $request->total_question_non_conforme;
        $inspection->total_question_non_applicable  = $request->total_question_non_applicable;
        $inspection->production = $request->production;

        $inspection->date_evaluation     = $request->date_evaluation;

        $inspection->save();
        if($inspection !=null ){
            $id = $inspection->id;
            $datas = [];

            if(count($request->reponse)) {
                $commentaire = $request->commentaire;
                InspectionQuestionnaire::where('inspection_id',$id)->delete();
                $i=0;
                foreach($request->reponse as $key=>$value){

                        $datas[] = [
                        'inspection_id' => $id,
                        'questionnaire_id' => $key,
                        'notation' => $value,
                        'commentaire' => $commentaire[$key],
                        'statuts' => 'Non Débuté',
                    ];
                }
            }
            InspectionQuestionnaire::insert($datas);
        }

        $notify[] = ['success', isset($message) ? $message : 'L\'inspection a été crée avec succès.'];
       return back()->withNotify($notify);
    }

    public function edit($id)
    {
        $pageTitle = "Mise à jour de l'inspection";
        $manager   = auth()->user();
        $producteurs  = Producteur::joinRelationship('localite.section')->where([['cooperative_id', $manager->cooperative_id],['producteurs.status',1]])->with('localite')->get();
        $localites = Localite::joinRelationship('section')->where([['cooperative_id',$manager->cooperative_id],['localites.status',1]])->get();
        $staffs  = User::staff()->get();
        $categoriequestionnaire = CategorieQuestionnaire::with('questions')->get();
        $notations = Notation::get();
        $inspection   = Inspection::findOrFail($id);
        return view('manager.inspection.edit', compact('pageTitle', 'localites', 'inspection','producteurs','staffs','categoriequestionnaire','notations'));
    }
    public function show($id)
    {
        $pageTitle = "Détails de l'inspection";
        $manager   = auth()->user();
        $producteurs  = Producteur::joinRelationship('localite.section')
        ->where([['cooperative_id', $manager->cooperative_id],['producteurs.status', 1]])->with('localite')->get();
        $localites = Localite::joinRelationship('section')->where([['cooperative_id',$manager->cooperative_id],['localites.status',1]])->get();
        $staffs  = User::staff()->get();
        $categoriequestionnaire = CategorieQuestionnaire::with('questions')->get();
        $notations = Notation::get();
        $inspection   = Inspection::findOrFail($id);
        return view('manager.inspection.show', compact('pageTitle', 'localites', 'inspection','producteurs','staffs','categoriequestionnaire','notations'));
    }

    public function suiviStore(Request $request)
    {

        $recommandations = $request->recommandations;
        $delai = $request->delai;
        $date_verification = $request->date_verification;
        $statuts = $request->statuts;

        foreach($recommandations as $key => $recomm){
            if($recomm==null){
                continue;
            }
        $suivi = InspectionQuestionnaire::where('id', $key)->first();
        $suivi->recommandations = $recomm;
        $suivi->delai = isset($delai[$key]) ? $delai[$key] : null;
        $suivi->date_verification = isset($date_verification[$key]) ? $date_verification[$key] : null;
        $suivi->statuts = $statuts[$key];
        $suivi->save();
        }


        return $suivi;
    }

    public function getCertificat(Request $request){
        $content='<option value="">Selectionner un certificat</option>';
        $certificats = Producteur_certification::where('producteur_id', $request->producteur)->get();
        if($certificats->count()){
            foreach($certificats as $data){
                $content .= '<option value="' . $data->certification . '" >' . $data->certification . '</option>';
            }
        }else{
            $content .= '<option value="Rainforest" >Rainforest</option>';
        }
        return $content;
    }
    public function getQuestionnaire(Request $request){
        $contents='';

        $notations = Notation::get();
        $total=0;
        $datas = array();
        if($request->certificat !=null)
        {
        $categoriequestionnaire = CategorieQuestionnaire::joinRelationship('questions')->whereIn('certificat',$request->certificat)->with('questions')->groupBy('categorie_questionnaires.id')->get();
        if($categoriequestionnaire->count()){

            $note = 0;

            foreach($categoriequestionnaire as $catquest){
$contents .='<tr><td colspan="4"><strong>'. $catquest->titre.'</strong></td></tr>';

            foreach($catquest->questions as $q) {
                if(!in_array($q->certificat, $request->certificat)){
                    continue;
                }
                $total = $total + 1;
                $contents .= '<tr>
                 <td>'. $q->nom.'
            </td>
            <td>'. $q->certificat.'
            </td>
            <td><select class="form-control" class="notation" id="reponse-'. $q->id.'" name="reponse['. $q->id.']" required>
                    <option value=""> </option>';
                          $a = 1;
                        foreach($notations as $not)
                        {
                            $contents .= '<option value="'. $not->nom.'" class="colorSelect_'.$a.'">'. $not->nom.'</option>';
                           $a++;
                        }
                        $contents .='</select>
                 </td>
                 <td>
                 <textarea class="comment" name="commentaire['. $q->id.']" cols="10" style="display:none;"></textarea>
                 </td>
                 </tr>';

                }
            }
        }else{
            $contents ="<div class='text-center'><span style='color:red;text-align:center'>Aucun questionnaire n'est disponible pour ce certificat.</span></div>";
        }
    }
         $datas['total'] = $total;
         $datas['contents'] = $contents;
        return $datas;
    }
    public function status($id)
    {
        return Inspection::changeStatus($id);
    }
    public function approbation()
    {
        return Inspection::changeApprobation(request()->id,request()->statut);
    }
    public function exportExcel()
    {
        $filename = 'inspections-' . gmdate('dmYhms') . '.xlsx';
        return Excel::download(new ExportInspections, $filename);
    }

    public function delete($id)
    {
        Inspection::where('id', decrypt($id))->delete();
        $notify[] = ['success', 'Le contenu supprimé avec succès'];
        return back()->withNotify($notify);
    }
}
