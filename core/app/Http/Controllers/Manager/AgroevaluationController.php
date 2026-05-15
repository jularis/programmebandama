<?php

namespace App\Http\Controllers\Manager;

use Maatwebsite\Excel\Facades\Excel;
use App\Models\Campagne;
use App\Constants\Status;
use App\Models\Localite;
use App\Models\Parcelle;
use App\Models\Producteur;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\Agroevaluation;
use App\Imports\ParcelleImport;
use App\Exports\ExportParcelles;
use App\Models\Agroespecesarbre;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\AgroevaluationEspece;
use Illuminate\Support\Facades\Hash;
use App\Exports\ExportAgroevaluations;
use Illuminate\Database\Eloquent\Builder;

class AgroevaluationController extends Controller
{

    public function index()
    {
        $pageTitle      = "Evaluation des besoins en arbres";
        $manager   = auth()->user();
        $localites = Localite::joinRelationship('section')->where([['cooperative_id', $manager->cooperative_id], ['localites.status', 1]])->get();
        $agroevaluations = Agroevaluation::dateFilter()->searchable([])->latest('id')->joinRelationship('producteur.localite.section')->where('sections.cooperative_id', $manager->cooperative_id)->where(function ($q) {
            if (request()->localite != null) {
                $q->where('localite_id', request()->localite);
            }
            if (request()->status != null) {
                $q->where('agroevaluations.status', request()->status);
            }
        })->with('producteur', 'producteur.localite')->paginate(getPaginate());

        return view('manager.agroevaluation.index', compact('pageTitle', 'agroevaluations', 'localites'));
    }

    public function create()
    {
        $pageTitle = "Ajouter une évaluation AgroForesterie";
        $manager   = auth()->user();
        // $producteurs  = Producteur::with('localite')->get();
        $localites = Localite::joinRelationship('section')->where([['cooperative_id', $manager->cooperative_id], ['localites.status', 1]])->orderBy('nom')->get();
        $campagnes = Campagne::active()->pluck('nom', 'id');
        //$especesarbres  = Agroespecesarbre::orderby('strate', 'asc')->orderby('nom', 'asc')->get();
        $especesarbres  = Agroespecesarbre::where('strate', '<>', 0)
            ->orderby('strate', 'asc')
            ->orderby('nom', 'asc')
            ->get();
        $listeprod = Agroevaluation::select('producteur_id')->get();
        $dataProd = array();
        if ($listeprod->count()) {
            foreach ($listeprod as $data) {
                $dataProd[] = $data->producteur_id;
            }
        }
        // $producteurs = Producteur::joinRelationship('localite.section')->where([['cooperative_id', $manager->cooperative_id],['producteurs.status',1]])->whereNotIn('producteurs.id', $dataProd)->get();
        $producteurs = Producteur::joinRelationship('localite.section')->where([['cooperative_id', $manager->cooperative_id],['producteurs.status',1]])->get();

        return view('manager.agroevaluation.create', compact('pageTitle', 'producteurs', 'localites', 'campagnes', 'especesarbres'));
    }

    public function store(Request $request)
    {
        $validationRule = [
            'localite'    => 'required|exists:localites,id',
            'producteur'    => 'required|exists:producteurs,id',
            'especesarbre'            => 'required|array',
            'quantite'            => 'required|array',
        ];


        $request->validate($validationRule);

        $localite = Localite::where('id', $request->localite)->first();

        if ($localite->status == Status::NO) {
            $notify[] = ['error', 'Cette localité est désactivée'];
            return back()->withNotify($notify)->withInput();
        }
        if ($request->id) {
            $agroevaluation = Agroevaluation::findOrFail($request->id);
            $message = "Le contenu a été mise à jour avec succès";
        } else {
            $agroevaluation = new Agroevaluation();
        }
       // $campagne = Campagne::active()->where('cooperative_id',auth()->user()->cooperative_id)->first();
        $campagne = Campagne::active()->first();
        $agroevaluation->campagne_id  = $campagne->id;
        $agroevaluation->producteur_id  = $request->producteur;
        $agroevaluation->quantite  = array_sum($request->quantite);
        $agroevaluation->save();
        $k = 0;
        $i = 0;
        $datas = [];
        if ($agroevaluation != null) {
            $id = $agroevaluation->id;
            if ($request->especesarbre) {
                AgroevaluationEspece::where('agroevaluation_id', $id)->delete();
                $quantite = $request->quantite;
                foreach ($request->especesarbre as $key => $data) {

                    $total = $quantite[$key];
                    if ($total != null) {
                        $datas[] = [
                            'agroevaluation_id' => $id,
                            'agroespecesarbre_id' => $data,
                            'total' => $total,
                        ];
                        $i++;
                    } else {
                        $k++;
                    }
                }
                AgroevaluationEspece::insert($datas);
            }
        }

        $notify[] = ['success', isset($message) ? $message : "$i arbres à ombrage ont été exprimés comme besoin de cet Producteur."];

        return back()->withNotify($notify);
    }



    public function edit($id)
    {
        $pageTitle = "Mise à jour de l'évaluation AgroForesterie";
        $manager = auth()->user();
        $localites = Localite::joinRelationship('section')->where([['cooperative_id', $manager->cooperative_id], ['localites.status', 1]])->orderBy('nom')->get();

        $evaluation   = Agroevaluation::findOrFail($id);
        $producteurs  = Producteur::joinRelationship('localite.section')
        ->where([['cooperative_id', $manager->cooperative_id],['producteurs.status', 1]])->where('producteurs.id', $evaluation->producteur_id)->first();
        $especesarbres  = Agroespecesarbre::orderby('strate', 'asc')->orderby('nom', 'asc')->get();
        $agroevaluationEspece  = AgroevaluationEspece::where('agroevaluation_id', $evaluation->id)->get();
        $dataEspece = $dataQuantite = array();
        if ($agroevaluationEspece->count()) {
            foreach ($agroevaluationEspece as $data) {
                $dataEspece[] = $data->agroespecesarbre_id;
                $dataQuantite[$data->agroespecesarbre_id] = $data->total;
            }
        }

        return view('manager.agroevaluation.edit', compact('pageTitle', 'evaluation', 'especesarbres', 'producteurs', 'dataEspece', 'dataQuantite'));
    }
    public function show($id){
        $pageTitle = "Détails de l'évaluation AgroForesterie";
        $manager = auth()->user();
        $localites = Localite::joinRelationship('section')->where([['cooperative_id', $manager->cooperative_id], ['localites.status', 1]])->orderBy('nom')->get();

        $evaluation   = Agroevaluation::findOrFail($id);
        $producteurs  = Producteur::joinRelationship('localite.section')
        ->where([['cooperative_id', $manager->cooperative_id],['producteurs.status', 1]])->where('producteurs.id', $evaluation->producteur_id)->first();
        $especesarbres  = Agroespecesarbre::orderby('strate', 'asc')->orderby('nom', 'asc')->get();
        $agroevaluationEspece  = AgroevaluationEspece::where('agroevaluation_id', $evaluation->id)->get();
        $dataEspece = $dataQuantite = array();
        if ($agroevaluationEspece->count()) {
            foreach ($agroevaluationEspece as $data) {
                $dataEspece[] = $data->agroespecesarbre_id;
                $dataQuantite[$data->agroespecesarbre_id] = $data->total;
            }
        }

        return view('manager.agroevaluation.show', compact('pageTitle', 'evaluation', 'especesarbres', 'producteurs', 'dataEspece', 'dataQuantite'));
    }

    public function status($id)
    {
        return Agroevaluation::changeStatus($id);
    }

    public function destroy($id)
    {
        Agroevaluation::find(decrypt($id))->delete();

        $notify[] = ['success', "L'évaluation de cette parcelle a été supprimer avec succès"];
        return back()->withNotify($notify);
    }

    public function exportExcel()
    {
        $filename = 'agroevaluation-' . gmdate('dmYhms') . '.xlsx';
        return Excel::download(new ExportAgroevaluations, $filename);
    }
    public function delete($id)
    {
        Agroevaluation::where('id', decrypt($id))->delete();
        $notify[] = ['success', 'Le contenu supprimé avec succès'];
        return back()->withNotify($notify);
    }
}
