<?php

namespace App\Http\Controllers\Manager;

use Excel;
use App\Models\Section;
use App\Models\Campagne;
use App\Models\Localite;
use App\Constants\Status;
use App\Models\Producteur;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\Agroevaluation;
use App\Models\Agrodistribution;
use App\Models\Agropostplanting;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\AgroevaluationEspece;
use Illuminate\Support\Facades\Hash;
use App\Models\Agroapprovisionnement;
use App\Models\AgrodistributionEspece;
use App\Models\AgropostplantingEspece;
use App\Imports\AgropostplantingImport;
use App\Exports\ExportAgropostplantings;
use App\Models\AgroapprovisionnementEspece;
use App\Models\AgroapprovisionnementSectionEspece;
use Google\Service\ContainerAnalysis\Distribution;

class AgropostplantingController extends Controller
{

    public function index()
    {
        $pageTitle      = "Evaluation Post-Planting";
        $manager   = auth()->user();
        $localites = Localite::joinRelationship('section')->where([['cooperative_id', $manager->cooperative_id], ['localites.status', 1]])->get();
        $postplanting = Agropostplanting::dateFilter()->searchable([])->latest('id')->joinRelationship('producteur.localite.section')->where('sections.cooperative_id', $manager->cooperative_id)->where(function ($q) {
            if (request()->localite != null) {
                $q->where('localite_id', request()->localite);
            }
        })->paginate(getPaginate());

        return view('manager.postplanting.index', compact('pageTitle', 'postplanting', 'localites'));
    }

    public function create()
    {
        $pageTitle = "Ajouter une évaluation post-planting des arbres à ombres";
        $manager   = auth()->user();
        $producteurDist = array();

        $producteur = Agrodistribution::get();
        if($producteur !=null){
            foreach ($producteur as $data) {
                $producteurDist[] = $data->producteur_id;
            }
        }
        $producteurs = Producteur::joinRelationship('localite.section')
        ->where([['cooperative_id', $manager->cooperative_id],['producteurs.status', 1]])->whereIn('producteurs.id',$producteurDist)->get();

        $sections = Section::get();
        $localites = Localite::joinRelationship('section')->where([['cooperative_id', $manager->cooperative_id], ['localites.status', 1]])->orderBy('nom')->get();
        return view('manager.postplanting.create', compact('pageTitle', 'producteurs', 'localites', 'sections'));
    }

    public function store(Request $request)
    {
        $validationRule = [
            'quantite' => 'required|array',
        ];


        $request->validate($validationRule);

        if ($request->id) {
            $distribution = Agropostplanting::findOrFail($request->id);
            $message = "La distribution a été mise à jour avec succès";
        } else {
            $distribution = new Agropostplanting();
        }
        $manager   = auth()->user();
        $campagne = Campagne::active()->first();
        $distribution->cooperative_id = $manager->cooperative_id;
        $distribution->producteur_id = $request->producteur;
        $distribution->quantite =  $request->total;
        $distribution->quantitePlantee =  $request->qteplante;
        $distribution->quantiteSurvecue =  $request->qtesurvecue;
        $distribution->date_planting =  $request->date_planting;
        $distribution->save();

        $datas = [];
        $k = 0;
        $i = 0;
        $nb = 0;

        if ($distribution->id) {

            $distributionID = $distribution->id;
            $quantiterecue = $request->quantiterecue;
            $quantiteplantee = $request->quantite;
            $quantitesurvecuee = $request->quantitesurvecuee;
            $commentaire = $request->commentaire;

            foreach ($request->quantite as $producteurid => $agroespeces) {

                $agroespeces = array_filter($agroespeces);

                foreach($agroespeces as $agroespecesarbresid => $total) {

                    $find = AgropostplantingEspece::where([
                        ['agropostplanting_id', $distributionID],
                        ['agroespecesarbre_id', $agroespecesarbresid]
                    ])->first();
                    if ($find == null) {

                        if ($total != null) {
                            AgropostplantingEspece::insert([
                                'Agropostplanting_id' => $distributionID,
                                'agroespecesarbre_id' => $agroespecesarbresid,
                                'total' => $quantiterecue[$producteurid][$agroespecesarbresid],
                                'total_plante' => $quantiteplantee[$producteurid][$agroespecesarbresid],
                                'total_survecue' => $quantitesurvecuee[$producteurid][$agroespecesarbresid],
                                'commentaire' => $commentaire[$producteurid][$agroespecesarbresid],
                                'created_at' => NOW()
                            ]);

                            // $agroapprov = AgroapprovisionnementSectionEspece::joinRelationship('agroapprovisionnementSection')->where([['agroapprovisionnement_section_id', $request->agroapprovisionnementsection], ['agroapprovisionnement_section_especes.agroespecesarbre_id', $agroespecesarbresid]])->first();
                            // if ($agroapprov != null) {
                            //     $agroapprov->total_restant = $agroapprov->total_restant + $total;
                            //     $agroapprov->save();
                            // }
                            $i++;
                        } else {
                            $k++;
                        }
                    } else {
                        $k++;
                    }
                }
            }
        }


        $notify[] = ['success', isset($message) ? $message : "L'évaluation post-planting des arbres à ombres a été bien enregistrée."];

        return back()->withNotify($notify);
    }

    public function edit($id)
    {
        $pageTitle = "Mise à jour de la distribution";
        $manager   = auth()->user();
        $campagne = Campagne::active()->first();
        $distribution   = Agropostplanting::findOrFail($id);
        $total = Agroevaluation::where('producteur_id', $distribution->producteur_id)->sum('quantite');
        $evaluation = Agroevaluation::where('producteur_id', $distribution->producteur_id)->first();
        $somme = AgropostplantingEspece::where([['Agropostplanting_id', $id]])->sum('total');

        $especes = AgroapprovisionnementEspece::joinRelationship('agroapprovisionnement', 'agroespecesarbre')->where([['cooperative_id', $manager->cooperative_id], ['campagne_id', $campagne->id]])->get();

        if ($distribution != null && count($especes)) {

            $agroevaluationEspece  = AgroevaluationEspece::joinRelationship('agroevaluation')->where('producteur_id', $distribution->producteur_id)->get();
            $dataEspece = $dataQuantite = array();
            if ($agroevaluationEspece->count()) {
                foreach ($agroevaluationEspece as $data) {
                    $dataEspece[] = $data->agroespecesarbre_id;
                    $dataQuantite[$data->agroespecesarbre_id] = $data->total;
                }
            }

            $results = '<table class="table table-bordered"><thead><tr>';
            $results .= '<th>Variété</th>';
            $results .= '<th></th>';
            $results .= '<th>Demande Producteur</th>';
            $results .= '<th style="text-align:left;">Quantité livrée</th>';


            $results .= ' </tr></thead><tbody>';
            $i = 0;
            $k = 1;
            foreach ($especes as $data) {

                $totalespece = $data->total - $data->total_restant;
                $nombre = 0;
                $existe = AgropostplantingEspece::where([
                    ['Agropostplanting_id', $id],
                    ['agroespecesarbre_id', $data->agroespecesarbre_id]
                ])->first();
                if ($existe != null) {
                    $nombre = $existe->total;
                }
                $qte = AgroevaluationEspece::where([['agroespecesarbre_id', $data->agroespecesarbre_id], ['agroevaluation_id', $evaluation->id]])->select('total')->first();

                $idespeces[] = $data->agroespecesarbre_id;
                $results .= '<tr><td>' . $data->agroespecesarbre->nom . '</td>';
                $results .= '<td><button class="btn btn-primary" type="button">' . $totalespece . '</button></td>';
                $results .= '<td><button class="btn btn-info" type="button">' . @$qte->total . '</button></td>';
                $s = 1;

                $results .= '<td><div class="input-group"><input type="number" name="quantite[' . $distribution->producteur_id . '][' . $data->agroespecesarbre_id . ']" value="' . $nombre . '" min="0" max="' . $totalespece + $nombre . '"  parc-' . $s . '="' . $distribution->quantite . '" id="qte-' . $k . '" st-' . $s . '" class="form-control totaux quantity-' . $i . '" onchange=getQuantite(' . $i . ',' . $k . ',' . $s . ') style="width: 100px;"><span class="input-group-btn"></span></div></td>';
                $k++;
                $s++;
                $i++;
                $results .= '</tr>';
            }

            $results .= '</tbody></table>';
        }
        return view('manager.distribution.edit', compact('pageTitle', 'distribution', 'somme', 'total', 'results'));
    }

    public function update(Request $request)
    {
        $validationRule = [
            'quantite'            => 'required|array',
        ];


        $request->validate($validationRule);

        $manager   = auth()->user();
        $campagne = Campagne::active()->first();

        $k = 0;
        $i = 0;
        $distributionID = $request->id;

        if ($request->quantite) {
            $updateappro = AgropostplantingEspece::where('Agropostplanting_id', $distributionID)->get();
            if ($updateappro->count()) {
                foreach ($updateappro as $appro) {
                    $agroapprov = AgroapprovisionnementEspece::joinRelationship('agroapprovisionnement')->where([['cooperative_id', $manager->cooperative_id], ['campagne_id', $campagne->id], ['agroespecesarbre_id', $appro->agroespecesarbre_id]])->first();
                    if ($agroapprov != null) {
                        $agroapprov->total_restant = $agroapprov->total_restant - $appro->total;
                        $agroapprov->save();
                        AgropostplantingEspece::where([['Agropostplanting_id', $distributionID], ['agroespecesarbre_id', $appro->agroespecesarbre_id]])->delete();
                    }
                }
            }

            foreach ($request->quantite as $producteurid => $agroespeces) {

                $agroespeces = array_filter($agroespeces);
                foreach ($agroespeces as $agroespecesarbresid => $total) {

                    $find = AgropostplantingEspece::where([
                        ['Agropostplanting_id', $distributionID],
                        ['agroespecesarbre_id', $agroespecesarbresid]
                    ])->first();
                    if ($find == null) {

                        if ($total != null) {
                            AgropostplantingEspece::insert([
                                'Agropostplanting_id' => $distributionID,
                                'agroespecesarbre_id' => $agroespecesarbresid,
                                'total' => $total,
                                'created_at' => NOW()
                            ]);

                            $agroapprov = AgroapprovisionnementEspece::joinRelationship('agroapprovisionnement')->where([['cooperative_id', $manager->cooperative_id], ['campagne_id', $campagne->id], ['agroespecesarbre_id', $agroespecesarbresid]])->first();
                            if ($agroapprov != null) {
                                $agroapprov->total_restant = $agroapprov->total_restant + $total;
                                $agroapprov->save();
                            }
                            $i++;
                        } else {
                            $k++;
                        }
                    } else {
                        $k++;
                    }
                }
            }
        }


        $notify[] = ['success', isset($message) ? $message : "$i nouveau(x) types d'arbres à ombrage ont été modifiés avec succès."];

        return back()->withNotify($notify);
    }


    public function getAgroParcellesArbres()
    {
        $input = request()->all();
        $totalrecu = 0;
        $producteurId = request()->producteur;

        if(request()->producteur !=null){

        $especes = AgrodistributionEspece::joinRelationship('agrodistribution')->joinRelationship('agroespecesarbre')->where('producteur_id', request()->producteur)->get();


        if (count($especes)) {
            $existe = Agropostplanting::where('producteur_id', request()->producteur)->latest('id')->first();

            if($existe !=null)
            {
            $especes2 = AgropostplantingEspece::joinRelationship('agropostplanting')->joinRelationship('agroespecesarbre')->where('agropostplanting_id', $existe->id)->get();
                if(count($especes2)) {
                    $especes = $especes2;
                }
            }
                        $somme = 0;

                        $results = '<table class="table table-bordered"><thead><tr>';
                        $results .= '<th>Variété</th>';
                        $results .= '<th>Quantité reçue</th>';
                        $results .= '<th>Quantité Plantée</th>';
                        $results .= '<th>Quantité Survécue</th>';
                        $results .= '<th>Commentaires</th>';
                        $results .= ' </tr></thead><tbody>';

                        $i = 0;
                        $k = 1;
                        $totalrecu = 0;

                        foreach($especes as $data) {

                            $s = 1;
                                $totalespece = isset($data->total_survecue) ? $data->total_survecue : $data->total;
                                if($totalespece==0){
                                    continue;
                                }
                                $totalrecu = $totalrecu + $totalespece;
                                $results .= '<tr><td><input type="hidden" name="agroapprovisionnementsection" value="' . $data->agroapprovisionnement_section_id . '">' . $data->agroespecesarbre->nom . '</td>';
                                $results .= '<td><input type="hidden" name="quantiterecue[' . $producteurId . '][' . $data->agroespecesarbre_id . ']" min="0" max="' . $totalespece . '" value="' . $totalespece . '" id="qterecue-' . $k . '" /><button class="btn btn-primary" type="button">' . $totalespece . '</button></td>';
                                $results .= '<td><div class="input-group"><input type="number" name="quantite[' . $producteurId . '][' . $data->agroespecesarbre_id . ']" min="0" max="' . $totalespece . '" value="' . $totalespece . '" id="qte-' . $k . '"  class="form-control totaux quantity-' . $i . ' st-' . $s . '" onchange=getQuantite(' . $i . ',' . $k . ',' . $s . ') ></div></td>';
                                $results .= '<td><div class="input-group"><input type="number" name="quantitesurvecuee[' . $producteurId . '][' . $data->agroespecesarbre_id . ']" min="0" max="' . $totalespece . '" value="' . $totalespece . '" id="qte2-' . $k . '"  class="form-control totaux2 quantity2-' . $i . ' st2-' . $s . '" onchange=getQuantite2(' . $i . ',' . $k . ',' . $s . ') ></div></td>';
                                $results .= '<td><div class="input-group"><textarea name="commentaire[' . $producteurId . '][' . $data->agroespecesarbre_id . ']" id="commentaire-' . $k . '"  class="form-control""></textarea></div></td>';
                                $k++;
                                $s++;
                                $i++;
                                $results .= '</tr>';

                        }

                        $results .= '</tbody></table>';
        } else {
            $results = '<span style="
                text-align: center;
                color: #f70000;
                font-weight: bold;
            ">Ce producteur n\'a pas encore reçu d\'arbres à ombres.</span>';
        }
    }else {
            $results = '<span style="
                text-align: center;
                color: #f70000;
                font-weight: bold;
            ">Veuillez choisir un producteur.</span>';
        }

        $contents['tableau'] = $results;
        $contents['total'] = $totalrecu;

        return $contents;
    }

    public function status($id)
    {
        return Agropostplanting::changeStatus($id);
    }

    public function exportExcel()
    {
        return (new ExportAgropostplantings())->download('distributions.xlsx');
    }

    public function delete($id)
    {
        Agropostplanting::where('id', decrypt($id))->delete();
        $notify[] = ['success', 'Le contenu supprimé avec succès'];
        return back()->withNotify($notify);
    }
}
