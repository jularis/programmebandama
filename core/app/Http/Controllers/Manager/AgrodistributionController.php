<?php

namespace App\Http\Controllers\Manager;

use Excel;
use App\Models\Campagne;
use App\Models\Section;
use App\Constants\Status;
use App\Models\Localite;
use App\Models\Producteur;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\Agroevaluation;
use App\Models\Agrodistribution;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\AgroevaluationEspece;
use Illuminate\Support\Facades\Hash;
use App\Models\Agroapprovisionnement;
use App\Models\AgrodistributionEspece;
use App\Imports\AgrodistributionImport;
use App\Exports\ExportAgrodistributions;
use App\Models\AgroapprovisionnementEspece;
use App\Models\AgroapprovisionnementSectionEspece;
use Google\Service\ContainerAnalysis\Distribution;

class AgrodistributionController extends Controller
{

    public function index()
    {
        $pageTitle      = "Gestion des distributions";
        $manager   = auth()->user();
        $localites = Localite::joinRelationship('section')->where([['cooperative_id', $manager->cooperative_id], ['localites.status', 1]])->get();
        $distributions = Agrodistribution::dateFilter()->searchable([])->latest('id')->joinRelationship('producteur.localite.section')->where('sections.cooperative_id', $manager->cooperative_id)->where(function ($q) {
            if (request()->localite != null) {
                $q->where('localite_id', request()->localite);
            }
        })->paginate(getPaginate());

        return view('manager.distribution.index', compact('pageTitle', 'distributions', 'localites'));
    }

    public function create()
    {
        $pageTitle = "Ajouter une distribution";
        $manager   = auth()->user();
        $producteurDistri = array();
        // $producteurs  = Producteur::joinRelationship('localite.section')
        // ->where([['cooperative_id', $manager->cooperative_id],['producteurs.status', 1]])->with('localite')->get();
        $campagne = Campagne::active()->where('cooperative_id', auth()->user()->cooperative_id)->first();


        $producteurs = Agroevaluation::joinRelationship('producteur.localite.section')->where([['cooperative_id', $manager->cooperative_id], ['producteurs.status', 1], ['campagne_id', $campagne->id]])->with('producteur')->get();

        $produc = Agrodistribution::select('producteur_id')->get();

        if ($produc) {
            foreach ($produc as $data) {
                $producteurDistri[] = $data->producteur_id;
            }
        }
        $sections = Section::get();
        $localites = Localite::joinRelationship('section')->where([['cooperative_id', $manager->cooperative_id], ['localites.status', 1]])->orderBy('nom')->get();
        return view('manager.distribution.create', compact('pageTitle', 'producteurs', 'localites', 'sections', 'producteurDistri'));
    }

    public function store(Request $request)
    {
        $validationRule = [
            'quantite' => 'required|array',
        ];


        $request->validate($validationRule);

        if ($request->id) {
            $distribution = Agrodistribution::findOrFail($request->id);
            $message = "La distribution a été mise à jour avec succès";
        } else {
            $distribution = new Agrodistribution();
        }
        $manager   = auth()->user();
        $campagne = Campagne::active()->where('cooperative_id', auth()->user()->cooperative_id)->first();

        $datas = [];
        $k = 0;
        $i = 0;
        $nb = 0;

        if ($request->quantite) {
            foreach ($request->quantite as $producteurid => $agroespeces) {

                $existe = Agrodistribution::where('producteur_id', $producteurid)->first();

                if ($existe != null) {
                    $distributionID = $existe->id;
                } else {

                    $distribution = new Agrodistribution();
                    $distribution->cooperative_id = $manager->cooperative_id;
                    $distribution->producteur_id = $producteurid;
                    $distribution->quantite = $request->qtelivre;
                    $distribution->save();
                    $distributionID = $distribution->id;
                    $nb++;
                }
                $agroespeces = array_filter($agroespeces);
                foreach ($agroespeces as $agroespecesarbresid => $total) {

                    $find = AgrodistributionEspece::where([
                        ['agrodistribution_id', $distributionID],
                        ['agroespecesarbre_id', $agroespecesarbresid]
                    ])->first();
                    if ($find == null) {

                        if ($total != null) {
                            AgrodistributionEspece::insert([
                                'agrodistribution_id' => $distributionID,
                                'agroespecesarbre_id' => $agroespecesarbresid,
                                'total' => $total,
                                'created_at' => NOW()
                            ]);

                            $agroapprov = AgroapprovisionnementSectionEspece::joinRelationship('agroapprovisionnementSection')->where([['agroapprovisionnement_section_id', $request->agroapprovisionnementsection], ['agroapprovisionnement_section_especes.agroespecesarbre_id', $agroespecesarbresid]])->first();
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


        $notify[] = ['success', isset($message) ? $message : "$i nouveau(x) types d'arbres à ombrage ont été distribué au producteur."];

        return back()->withNotify($notify);
    }

    public function edit($id)
    {
        $pageTitle = "Mise à jour de la distribution";
        $manager   = auth()->user();
        $campagne = Campagne::active()->where('cooperative_id', auth()->user()->cooperative_id)->first();
        $distribution   = Agrodistribution::findOrFail($id);
        $total = Agroevaluation::where('producteur_id', $distribution->producteur_id)->sum('quantite');
        $evaluation = Agroevaluation::where('producteur_id', $distribution->producteur_id)->first();
        $somme = AgrodistributionEspece::where([['agrodistribution_id', $id]])->sum('total');
        // $approvionnement = Agroapprovisionnement::where('cooperative_id',auth()->user()->cooperative_id)->select('id')->get();

        $approvisionnementId = Agroapprovisionnement::where('cooperative_id', auth()->user()->cooperative_id)
    ->value('id');


        $especes = AgroapprovisionnementEspece::joinRelationship('agroapprovisionnement', 'agroespecesarbre')->where([['agroapprovisionnement_id',$approvisionnementId]])->get();

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
                $existe = AgrodistributionEspece::where([
                    ['agrodistribution_id', $id],
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
        $campagne = Campagne::active()->where('cooperative_id', auth()->user()->cooperative_id)->first();
        $approvisionnementId = Agroapprovisionnement::where('cooperative_id', auth()->user()->cooperative_id)
        ->value('id');

        $k = 0;
        $i = 0;
        $distributionID = $request->id;
        //'agroapprovisionnement_id',$approvisionnementId

        if ($request->quantite) {
            $updateappro = AgrodistributionEspece::where('agrodistribution_id', $distributionID)->get();
            if ($updateappro->count()) {
                foreach ($updateappro as $appro) {
                    $agroapprov = AgroapprovisionnementEspece::joinRelationship('agroapprovisionnement')->where([['agroapprovisionnement_id',$approvisionnementId], ['agroespecesarbre_id', $appro->agroespecesarbre_id]])->first();
                    if ($agroapprov != null) {
                        $agroapprov->total_restant = $agroapprov->total_restant - $appro->total;
                        $agroapprov->save();
                        AgrodistributionEspece::where([['agrodistribution_id', $distributionID], ['agroespecesarbre_id', $appro->agroespecesarbre_id]])->delete();
                    }
                }
            }

            foreach ($request->quantite as $producteurid => $agroespeces) {

                $agroespeces = array_filter($agroespeces);
                foreach ($agroespeces as $agroespecesarbresid => $total) {

                    $find = AgrodistributionEspece::where([
                        ['agrodistribution_id', $distributionID],
                        ['agroespecesarbre_id', $agroespecesarbresid]
                    ])->first();
                    if ($find == null) {

                        if ($total != null) {
                            AgrodistributionEspece::insert([
                                'agrodistribution_id' => $distributionID,
                                'agroespecesarbre_id' => $agroespecesarbresid,
                                'total' => $total,
                                'created_at' => NOW()
                            ]);

                            $agroapprov = AgroapprovisionnementEspece::joinRelationship('agroapprovisionnement')->where([['agroapprovisionnement_id',$approvisionnementId]])->first();
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
        $campagne = Campagne::active()->where('cooperative_id', auth()->user()->cooperative_id)->first();
        $manager = auth()->user();
        $somme = 0;
        $producteurId = $input['producteur'];
        $sectionid = $input['section'];
        $cooperative = $manager->cooperative_id;

        $campagneId = $campagne->id;
        $especes = AgroapprovisionnementSectionEspece::joinRelationship('agroapprovisionnementSection', 'agroespecesarbre')->where([['section_id', $sectionid]])->get();
        $agroeval = Agroevaluation::where('producteur_id', $producteurId)->first();


        if (count($especes)) {

            if ($producteurId != null) {
                if ($agroeval != null) {

                    $existing = Agrodistribution::where('producteur_id', $producteurId)->exists();
                    if (!$existing) {
                        $somme = $agroeval->quantite;

                        $results = '<table class="table table-bordered"><thead><tr>';
                        $results .= '<th>Variété</th>';
                        $results .= '<th>Quantité Section</th>';
                        $results .= '<th>Demande Producteur</th>';
                        $results .= '<th>Quantité acceptée</th>';

                        $results .= ' </tr></thead><tbody>';

                        $i = 0;
                        $k = 1;

                        foreach ($especes as $data) {

                            if ($data->total != $data->total_restant) {
                                $totalespece = $data->total - $data->total_restant;

                                // if(in_array($data->id, $dataEspece)){$qte = $dataQuantite[$data->id];}else{$qte=0;}
                                $qte = AgroevaluationEspece::where([['agroespecesarbre_id', $data->agroespecesarbre_id], ['agroevaluation_id', $agroeval->id]])->select('total')->first();

                                $results .= '<tr><td><input type="hidden" name="agroapprovisionnementsection" value="' . $data->agroapprovisionnement_section_id . '">' . $data->agroespecesarbre->nom . '</td>';
                                $results .= '<td><button class="btn btn-primary" type="button">' . $totalespece . '</button></td>';
                                $results .= '<td><button class="btn btn-info" type="button">' . @$qte->total . '</button></td>';
                                $s = 1;

                                $results .= '<td><div class="input-group"><input type="number" name="quantite[' . $producteurId . '][' . $data->agroespecesarbre_id . ']" value="0" min="0" max="' . $totalespece . '" id="qte-' . $k . '"  class="form-control totaux quantity-' . $i . ' st-' . $s . '" onchange=getQuantite(' . $i . ',' . $k . ',' . $s . ') style="width: 100px;"><span class="input-group-btn"></span></div></td>';
                                $k++;
                                $s++;
                                $i++;
                                $results .= '</tr>';
                            }
                        }

                        $results .= '</tbody></table>';
                        $n = 1;
                        $n++;
                    } else {
                        $results = '<span style="
                text-align: center;
                color: #f70000;
                font-weight: bold;
            ">Ce producteur a déjà bénéficié des arbres à ombrage. Veuillez procéder aux modifications dépuis la liste des distributions</span>';
                    }
                } else {
                    $results = '<span style="
                        text-align: center;
                        color: #f70000;
                        font-weight: bold;
                    ">Ce producteur n\'a pas été évalué. Veuillez procéder à son évaluation puis revenez faire la distribution.</span>';
                }
            } else {
                $results = '<span style="
                    text-align: center;
                    color: #f70000;
                    font-weight: bold;
                ">Veuillez choisir un producteur.</span>';
            }
        } else {
            $results = '<span style="
                text-align: center;
                color: #f70000;
                font-weight: bold;
            ">Il n\'y a pas d\'arbres disponibles pour cette section.</span>';
        }

        $contents['tableau'] = $results;
        $contents['total'] = $somme;

        return $contents;
    }

    public function status($id)
    {
        return Agrodistribution::changeStatus($id);
    }

    public function exportExcel()
    {
        return (new ExportAgrodistributions())->download('distributions.xlsx');
    }
}
