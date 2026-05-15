<?php

namespace App\Http\Controllers\Manager;

use App\Constants\Status;
use App\Exports\ExportAgrodistributions;
use App\Http\Controllers\Controller;
use App\Imports\AgrodistributionImport;
use App\Models\Agroapprovisionnement;
use App\Models\AgroapprovisionnementEspece;
use App\Models\Localite;
use App\Models\Section;
use App\Models\Producteur;
use App\Models\Agrodistribution;
use App\Models\AgrodistributionEspece;
use App\Models\Agroevaluation;
use App\Models\Campagne;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Excel;

class AgrodistributionController extends Controller
{

    public function index()
    {
        $pageTitle      = "Gestion des distributions";
        $manager   = auth()->user();
        $localites = Localite::joinRelationship('section')->where([['cooperative_id', $manager->cooperative_id], ['localites.status', 1]])->get();
        $distributions = Agrodistribution::dateFilter()->searchable([])->latest('id')->joinRelationship('parcelle.producteur.localite.section')->where('sections.cooperative_id', $manager->cooperative_id)->where(function ($q) {
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
        $producteurs  = Producteur::joinRelationship('localite.section')
        ->where([['cooperative_id', $manager->cooperative_id],['producteurs.status', 1]])->with('localite')->get();
        $localites = Localite::joinRelationship('section')->where([['cooperative_id', $manager->cooperative_id], ['localites.status', 1]])->orderBy('nom')->get();
        return view('manager.distribution.create', compact('pageTitle', 'producteurs', 'localites'));
    }

    public function store(Request $request)
    {
        $validationRule = [
            'quantite'            => 'required|array',
        ];


        $request->validate($validationRule);

        if ($request->id) {
            $distribution = Agrodistribution::findOrFail($request->id);
            $message = "La distribution a été mise à jour avec succès";
        } else {
            $distribution = new Agrodistribution();
        }
        $manager   = auth()->user();
        $campagne = Campagne::active()->first();

        $datas = [];
        $k = 0;
        $i = 0;
        $nb = 0;
        if ($request->quantite) {
            foreach ($request->quantite as $parcellesid => $agroespeces) {

                $existe = Agrodistribution::where('parcelle_id', $parcellesid)->first();

                if ($existe != null) {
                    $distributionID = $existe->id;
                } else {

                    $distribution = new Agrodistribution();
                    $distribution->cooperative_id = $manager->cooperative_id;
                    $distribution->parcelle_id = $parcellesid;
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


        $notify[] = ['success', isset($message) ? $message : "$i nouveau(x) types d'arbres à ombrage ont été distribué pour $nb parcelle(s)."];

        return back()->withNotify($notify);
    }

    public function edit($id)
    {
        $pageTitle = "Mise à jour de la distribution";
        $manager   = auth()->user();
        $campagne = Campagne::active()->first();
        $distribution   = Agrodistribution::findOrFail($id);
        $total = Agroevaluation::where('parcelle_id', $distribution->parcelle_id)->sum('quantite');
        $somme = AgrodistributionEspece::where([['agrodistribution_id', $id]])->sum('total');

        $especes = AgroapprovisionnementEspece::joinRelationship('agroapprovisionnement', 'agroespecesarbre')->where([['cooperative_id', $manager->cooperative_id], ['campagne_id', $campagne->id]])->get();
        $parcelles = Agroevaluation::joinRelationship('parcelle')->where('parcelle_id', $distribution->parcelle_id)->get();
        $results = ''; // Initialisation de la variable $results

        if (count($parcelles) && count($especes)) {

            $nbparcelle = count($parcelles);
            $results = '<table class="table table-bordered"><thead><tr><th scope="col">&nbsp;</th>';
            foreach ($parcelles as $data) {
                if ($data->parcelle->anneeCreation != null) $annee = '(' . $data->parcelle->anneeCreation . ')';
                else $annee = '';
                $results .= '<th style="text-align:left;">Parcelle ' . $data->parcelle->codeParc . $annee . ' <button class="btn btn-primary" type="button">' . $data->quantite . '</button></th>';
            }

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
                $idespeces[] = $data->agroespecesarbre_id;
                $results .= '<tr><td>' . $data->agroespecesarbre->nom . ' <button class="btn btn-primary" type="button">' . $totalespece . '</button></td>';
                $s = 1;
                foreach ($parcelles as $data2) {

                    $results .= '<td><div class="input-group"><input type="number" name="quantite[' . $data2->parcelle_id . '][' . $data->agroespecesarbre_id . ']" value="' . $nombre . '" min="0" max="' . $totalespece + $nombre . '"  parc-' . $s . '="' . $data2->quantite . '" id="qte-' . $k . '" st-' . $s . '" class="form-control totaux quantity-' . $i . '" onchange=getQuantite(' . $i . ',' . $k . ',' . $s . ') style="width: 100px;"><span class="input-group-btn"></span></div></td>';
                    $k++;
                    $s++;
                }
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
            $updateappro = AgrodistributionEspece::where('agrodistribution_id', $distributionID)->get();
            if ($updateappro->count()) {
                foreach ($updateappro as $appro) {
                    $agroapprov = AgroapprovisionnementEspece::joinRelationship('agroapprovisionnement')->where([['cooperative_id', $manager->cooperative_id], ['campagne_id', $campagne->id], ['agroespecesarbre_id', $appro->agroespecesarbre_id]])->first();
                    if ($agroapprov != null) {
                        $agroapprov->total_restant = $agroapprov->total_restant - $appro->total;
                        $agroapprov->save();
                        AgrodistributionEspece::where([['agrodistribution_id', $distributionID], ['agroespecesarbre_id', $appro->agroespecesarbre_id]])->delete();
                    }
                }
            }

            foreach ($request->quantite as $parcellesid => $agroespeces) {

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
        $campagne = Campagne::active()->first();
        $manager = auth()->user();
        $somme = 0;
        $producteurId = $input['producteur'];
        $cooperative = $manager->cooperative_id;

        $campagneId = $campagne->id;
        $especes = AgroapprovisionnementEspece::joinRelationship('agroapprovisionnement', 'agroespecesarbre')->where([['cooperative_id', $manager->cooperative_id], ['campagne_id', $campagneId]])->get();
        $parcelles = Agroevaluation::joinRelationship('parcelle')->where('producteur_id', $producteurId)->get();


        if (count($parcelles) && count($especes)) {

            foreach ($parcelles as $verif) {
                $existing = Agrodistribution::where('parcelle_id', $verif->parcelle_id)->exists();
                if (!$existing) {
                    $somme = $somme + $verif->quantite;
                    $nbparcelle = count($parcelles);
                    $results = '<table class="table table-bordered"><thead><tr><th scope="col">&nbsp;</th>';
                    foreach ($parcelles as $data) {

                        if ($data->parcelle->anneeCreation != null) $annee = '(' . $data->parcelle->anneeCreation . ')';
                        else $annee = '';
                        $results .= '<th>Parcelle ' . $data->parcelle->codeParc . $annee . ' <button class="btn btn-primary" type="button">' . $data->quantite . '</button></th>';
                    }

                    $results .= ' </tr></thead><tbody>';

                    $i = 0;
                    $k = 1;

                    foreach ($especes as $data) {
                        if ($data->total != $data->total_restant) {
                            $totalespece = $data->total - $data->total_restant;
                            $max[] = $totalespece;
                            $idespeces[] = $data->agroespecesarbre_id;
                            $results .= '<tr><td>' . $data->agroespecesarbre->nom . ' <button class="btn btn-primary" type="button">' . $totalespece . '</button></td>';
                            $s = 1;
                            foreach ($parcelles as $data2) {

                                $results .= '<td><div class="input-group"><input type="number" name="quantite[' . $data2->parcelle_id . '][' . $data->agroespecesarbre_id . ']" value="0" min="0" max="' . $totalespece . '" parc-' . $s . '="' . $data2->quantite . '" id="qte-' . $k . '"  class="form-control totaux quantity-' . $i . ' st-' . $s . '" onchange=getQuantite(' . $i . ',' . $k . ',' . $s . ') style="width: 100px;"><span class="input-group-btn"></span></div></td>';
                                $k++;
                                $s++;
                            }
                            $i++;
                            $results .= '</tr>';
                        }
                    }

                    $results .= '</tbody><tfooter><tr><td scope="col" style="font-weight:bold; font-size: 20px;">Total</td>';
                    $n = 1;
                    foreach ($parcelles as $data) {

                        $results .= '<td><input type="number" name="soustotal[]" id="soustotal-' . $n . '" value="0" class="form-control" style="font-weight:bold; font-size: 20px;"/></td>';
                        $n++;
                    }

                    $results .= '</tr></tfooter></table>';
                } else {
                    $results = '<span style="
                text-align: center;
                color: #f70000;
                font-weight: bold;
            ">Ce producteur a déjà des parcelles qui ont bénéficié des arbres à ombrage. Veuillez procéder aux modifications dépuis la liste des distributions</span>';
                }
            }
        } else {
            $results = '<span style="
                text-align: center;
                color: #f70000;
                font-weight: bold;
            ">Aucune donnée n\'a été trouvé</span>';
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

    public function delete($id)
    {
        Agrodistribution::where('id', decrypt($id))->delete();
        $notify[] = ['success', 'Le contenu supprimé avec succès'];
        return back()->withNotify($notify);
    }
}
