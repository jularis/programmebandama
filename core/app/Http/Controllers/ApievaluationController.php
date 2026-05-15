<?php

namespace App\Http\Controllers;

use App\Models\Campagne;
use App\Models\Localite;
use App\Constants\Status;
use App\Models\Evaluation;
use App\Models\Inspection;
use App\Models\DebugMobile;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\InspectionQuestionnaire;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class ApievaluationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
   
    $debug = new DebugMobile();
    $debug->content = json_encode($request->all());
    $debug->save();

        if ($request->id) {
            $inspection = Inspection::find($request->id); 
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
        if ($inspection != null) {
            $id = $inspection->id;
            $datas = [];

            if (count($request->reponse)) {
                $commentaire = $request->commentaire;
                InspectionQuestionnaire::where('inspection_id', $id)->delete();
                $i = 0;
                foreach ($request->reponse as $key => $value) {

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

            $inspectionQuestionnaireNonConformes = InspectionQuestionnaire::where('inspection_id', $inspection->id)
                ->where('notation', "Pas Conforme")->select('id', 'questionnaire_id')
                ->get();
            $inspectionQuestionnaireNonApplicables = InspectionQuestionnaire::where('inspection_id', $inspection->id)->where('notation', "Non Applicable")->select('id', 'questionnaire_id')->get();
        }
        return response()->json([
            'parcelle_id' => $inspection->parcelle_id,
            'producteur_id' => $inspection->producteur_id,
            'campagne_id' => $inspection->campagne_id,
            'formateur_id' => $inspection->formateur_id,
            'certificat' => $inspection->certificat,
            'note' => $inspection->note,
            'total_question' => $inspection->total_question,
            'total_question_conforme' => $inspection->total_question_conforme,
            'total_question_non_conforme' => $inspection->total_question_non_conforme,
            'total_question_non_applicable' => $inspection->total_question_non_applicable,
            'production' => $inspection->production,
            'date_evaluation' => $inspection->date_evaluation,
            'updated_at' => $inspection->updated_at,
            'created_at' => $inspection->created_at,
            'id' => $inspection->id,
            'reponse_non_conforme' => $inspectionQuestionnaireNonConformes,
            'reponse_non_applicale' => $inspectionQuestionnaireNonApplicables
        ], 201);
    }

    public function getInspectionsNonApplicableEtNonConforme(Request $request)
    {
        $inspections = Inspection::joinRelationship('producteur.localite.section')->where('cooperative_id', $request->cooperativeid)->whereHas('reponsesInspection', function ($query) {
            $query->whereIn('notation', ['Pas Conforme', 'Non Applicable']);
        })->get();

        $inspections->each(function ($inspection) {
            $inspection->reponse_non_conforme = InspectionQuestionnaire::where('inspection_id', $inspection->id)
                ->where('notation', 'Pas Conforme')
                ->where('statuts', 'Non Débuté')
                ->select('id', 'questionnaire_id','commentaire','recommandations','delai','date_verification')
                ->get();

            $inspection->reponse_non_applicale = InspectionQuestionnaire::where('inspection_id', $inspection->id)
                ->where('notation', 'Non Applicable')
                ->where('statuts', 'Non Débuté')
                ->select('id', 'questionnaire_id','commentaire','recommandations','delai','date_verification')
                ->get();
        });

        return response()->json($inspections);
    }
    // public function updateInspection(Request $request)
    // {
    //     $recommandations = $request->recommandations;
    //     $delai = $request->delai;
    //     $date_verification = $request->date_verification;
    //     $statuts = $request->statuts;

    //     foreach ($recommandations as $key => $recomm) {
    //         if ($recomm == null) {
    //             continue;
    //         }
    //         $suivi = InspectionQuestionnaire::where('id', $key)->first();
    //         $suivi->recommandations = $recomm;
    //         $suivi->delai = isset($delai[$key]) ? $delai[$key] : null;
    //         $suivi->date_verification = isset($date_verification[$key]) ? $date_verification[$key] : null;
    //         $suivi->statuts = $statuts[$key];
    //         $suivi->save();
    //     }

    //     return Response::json([
    //         'data' => $suivi
    //     ], 200);
    // }
    public function updateInspection(Request $request)
    {
        $reponse_non_conforme = $request->input('reponse_non_conforme', []);
        $reponse_non_conformeObj = (object)$reponse_non_conforme;
        $recommandations = $reponse_non_conformeObj->recommandations;
        $delais = $reponse_non_conformeObj->delai;
        $date_verifications = $reponse_non_conformeObj->date_verification;
        $statuts = $reponse_non_conformeObj->statuts;
        // $approbation = $request->input('approbation');
        //dd(json_encode($request->all()));
        
        foreach ($recommandations as $key => $recomm) {
            if ($recomm == null) {
                continue;
            }
            $suivi = InspectionQuestionnaire::where('id', $key)->first();
            $suivi->recommandations = $recomm;
            $suivi->delai = isset($delais[$key]) ? $delais[$key] : null;
            $suivi->date_verification = isset($date_verifications[$key]) ? $date_verifications[$key] : null;
            $suivi->statuts = $statuts[$key];
            $suivi->save();
        }

        //Inspection::changeApprobation($request->input('id'), $approbation);

        $inspection = Inspection::where("id", $request->input('id'))->whereHas('reponsesInspection', function ($query) {
            $query->whereIn('notation', ['Pas Conforme', 'Non Applicable']);
        })->get()->first();

        $inspection->reponse_non_conforme = InspectionQuestionnaire::where('inspection_id', $inspection->id)
            ->where('notation', 'Pas Conforme')
            ->where('statuts', 'Non Débuté')
            ->select('id', 'questionnaire_id')
            ->get();

        $inspection->reponse_non_applicale = InspectionQuestionnaire::where('inspection_id', $inspection->id)
            ->where('notation', 'Non Applicable')
            ->where('statuts', 'Non Débuté')
            ->select('id', 'questionnaire_id')
            ->get();

        return response()->json($inspection);
    }

    public function getQuestionnaire()
    {
        $categoriequestionnaire = DB::table('categorie_questionnaires')->get();
        $donnees = DB::table('questionnaires')->get();
        $questionnaires = array();
        $gestlist = array();
        foreach ($categoriequestionnaire as $categquest) {

            foreach ($donnees as $data) {
                if ($data->categorie_questionnaire_id == $categquest->id) {
                    $gestlist[] = array('id' => $data->id, 'libelle' => $data->nom, 'certificat' => $data->certificat);
                }
            }
            $questionnaires[] = array('titre' => $categquest->titre, "questionnaires" => $gestlist);

            $gestlist = array();
        }

        return response()->json($questionnaires, 201);
    }
    public function getNotation()
    {
        $donnees = DB::table('notations')->get();
        return response()->json($donnees, 201);
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        //
    }
}
