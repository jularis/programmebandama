<?php

namespace App\Http\Controllers\Manager;

use App\Constants\Status;
use App\Exports\ExportEstimations;
use App\Http\Controllers\Controller;
use App\Imports\EstimationImport;
use App\Models\Localite; 
use App\Models\Producteur; 
use App\Models\Estimation; 
use App\Models\Parcelle; 
use App\Models\Campagne;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Excel;

class EstimationController extends Controller
{

    public function index()
    {
        $pageTitle      = "Gestion des estimations";
        $manager   = auth()->user();
        $localites = Localite::joinRelationship('section')->where([['cooperative_id',$manager->cooperative_id],['localites.status',1]])->get();
        $estimations = Estimation::dateFilter()->searchable(["EA1","EA2","EA3","EB1","EB2","EB3","EC1","EC2","EC3","T1","T2","T3","V1","V2","V3","VM1","VM2","VM3","Q","RF","EsP","date_estimation","productionAnnuelle"])->latest('id')->joinRelationship('parcelle.producteur.localite.section')->where('sections.cooperative_id',$manager->cooperative_id)->where(function ($q) {
            if(request()->localite != null){
                $q->where('localite_id',request()->localite);
            }
            if(request()->status != null){
                $q->where('estimations.status',request()->status);
            }
        })->with('parcelle');

        $campagne = Campagne::active()->latest()->first();
   
        $estimationsFiltre = $estimations->get();
        $estimations = $estimations->paginate(getPaginate());
        $total_estimation_calculee = $estimationsFiltre->where('campagne_id',$campagne->id)->where('typeEstimation', 'Rendement calculé')->count();

        $total_estimation_estimee = $estimationsFiltre->where('campagne_id',$campagne->id)->where('typeEstimation', '!=','Rendement calculé')->count();

        return view('manager.estimation.index', compact('pageTitle', 'estimations','localites','total_estimation_calculee','total_estimation_estimee'));
    }
 
    public function create()
    {
        $pageTitle = "Ajouter une estimation";
        $manager   = auth()->user();
        $producteurs  = Producteur::joinRelationship('localite.section')
        ->where([['cooperative_id', $manager->cooperative_id],['producteurs.status', 1]])->with('localite')->get();
        $localites = Localite::joinRelationship('section')->where([['cooperative_id',$manager->cooperative_id],['localites.status',1]])->get();
        $campagnes = Campagne::active()->pluck('nom','id');
        $typeEstimation = array('Rendement calculé','Rendement estimé');
        $parcelles  = Parcelle::joinRelationship('producteur.localite.section')
        ->where([['cooperative_id', $manager->cooperative_id],['producteurs.status', 1]])->with('producteur')->get();
        return view('manager.estimation.create', compact('pageTitle', 'producteurs','localites','campagnes','parcelles','typeEstimation'));
    }

    public function store(Request $request)
    {
        $validationRule = [
            'parcelle'    => 'required|exists:parcelles,id',
            'campagne' => 'required|max:255', 
            'RF'  => 'required|max:255',
            'EsP'  => 'required|max:255', 
            'date_estimation'  => 'required|max:255', 
        ];

        $request->validate($validationRule);

        // $localite = Localite::where('id', $request->localite)->first();

        // if ($localite->status == Status::NO) {
        //     $notify[] = ['error', 'Cette localité est désactivée'];
        //     return back()->withNotify($notify)->withInput();
        // }
        
        if($request->id) {
            $estimation = Estimation::findOrFail($request->id);
            $message = "L'estimation a été mise à jour avec succès";
        } else {
            $estimation = Estimation::where([['campagne_id',$request->campagne],['parcelle_id',$request->parcelle]])->first();
            if($estimation ==null){
                $estimation = new Estimation(); 
            }
             
        } 
        
        $estimation->parcelle_id  = $request->parcelle;  
        $estimation->campagne_id  = $request->campagne;
        $estimation->EA1  = $request->EA1;
        $estimation->EA2  = $request->EA2;
        $estimation->EA3  = $request->EA3;
        $estimation->EB1  = $request->EB1;
        $estimation->EB2  = $request->EB2;
        $estimation->EB3  = $request->EB3;
        $estimation->EC1  = $request->EC1;
        $estimation->EC2  = $request->EC2;
        $estimation->EC3  = $request->EC3;
        $estimation->T1 = $request->T1; 
        $estimation->T2 = $request->T2;
        $estimation->T3 = $request->T3;
        $estimation->V1 = $request->V1;  
        $estimation->V2 = $request->V2; 
        $estimation->V3 = $request->V3; 
        $estimation->VM1    = $request->VM1;
        $estimation->VM2    = $request->VM2;
        $estimation->VM3    = $request->VM3;
        $estimation->Q    = $request->Q;
        $estimation->RT    = $request->RT;
        $estimation->RF    = $request->RF;
        $estimation->EsP    = $request->EsP;
        $estimation->ajustement    = $request->ajustement;
        $estimation->typeEstimation    = $request->typeEstimation;
        $estimation->date_estimation    = $request->date_estimation; 

        $estimation->save(); 

        $notify[] = ['success', isset($message) ? $message : "L'estimation a été crée avec succès."];
        return back()->withNotify($notify);
    }

     

    public function edit($id)
    {
        $pageTitle = "Mise à jour de la estimation";
        $manager = auth()->user();
        $localites = Localite::joinRelationship('section')->where([['cooperative_id',$manager->cooperative_id],['localites.status',1]])->get();
        $producteurs  = Producteur::joinRelationship('localite.section')
        ->where([['cooperative_id', $manager->cooperative_id],['producteurs.status', 1]])->with('localite')->get();
        $estimation   = Estimation::findOrFail($id);
        return view('manager.estimation.edit', compact('pageTitle', 'localites', 'estimation','producteurs'));
    } 
    public function show($id){
        $pageTitle = "Détails de la estimation";
        $manager = auth()->user();
        $estimation   = Estimation::findOrFail($id);
        $localites = Localite::joinRelationship('section')->where([['cooperative_id',$manager->cooperative_id],['localites.status',1]])->get();
        $producteurs  = Producteur::joinRelationship('localite.section')
        ->where([['cooperative_id', $manager->cooperative_id],['producteurs.status', 1]])->with('localite')->get();
        
        return view('manager.estimation.show', compact('pageTitle', 'localites', 'estimation','producteurs'));
    }

    public function status($id)
    {
        return Estimation::changeStatus($id);
    }

    public function exportExcel()
    {
        return (new ExportEstimations())->download('estimations.xlsx');
    }
    public function  uploadContent(Request $request)
    {
        Excel::import(new EstimationImport, $request->file('uploaded_file'));
        return back();
    }
}
