<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Campagne;
use App\Models\Localite;
use App\Constants\Status;
use App\Models\Producteur;
use Illuminate\Http\Request;
use App\Models\Agroevaluation;
use App\Models\Agrodistribution;
use App\Models\Agroespecesarbre;
use App\Models\Agropostplanting;
use Illuminate\Support\Facades\DB;
use App\Models\AgroevaluationEspece;
use App\Models\AgrodistributionEspece;
use App\Models\AgropostplantingEspece;
use App\Models\AgroapprovisionnementSectionEspece;

class ApiAgroEvaluationContoller extends Controller
{
    public function store(Request $request)
    { 

        if ($request->id) {
            $agroevaluation = Agroevaluation::findOrFail($request->id);
            $message = "Le contenu a été mise à jour avec succès";
        } else {
            $agroevaluation = new Agroevaluation();
        }
        if ($agroevaluation->producteur_id != $request->producteur) {
            $hasEvalution = Agroevaluation::where('producteur_id', $request->producteur)->exists();
            if ($hasEvalution) {
                return response()->json("Ce producteur a déjà une évaluation de besoin enregistré", 501);
            }
        }
        $campagne = Campagne::active()->first();
        $agroevaluation->campagne_id  = $campagne->id;
        $agroevaluation->producteur_id  = $request->producteur;
        $agroevaluation->userid  = $request->userid;
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

        return response()->json($agroevaluation, 201);

        $message = "$i arbres à ombrage ont été exprimés comme besoin de cet Producteur.";
    }

    // public function getproducteursBesoin(Request $request){
    // $manager = User::where('id',$request->userid)->get()->first();
    // $listeprod = Agroevaluation::select('producteur_id')->get();
    // $dataProd = array();
    // if ($listeprod->count()) {
    //     foreach ($listeprod as $data) {
    //         $dataProd[] = $data->producteur_id;
    //     }
    // }
    // $producteurs = Producteur::joinRelationship('localite.section')->where('cooperative_id', $manager->cooperative_id)->whereNotIn('producteurs.id', $dataProd)->select('producteurs.id')->get();
    // return response()->json([
    //     'producteurs' => $producteurs,
    // ], 201);

    // }
    public function besoinproducteur(Request $request)
    {
        $manager = User::where('id', $request->userid)->get()->first();
        $producteurDistri = array();
        $producteurs  = Producteur::joinRelationship('localite.section')
            ->join('agroevaluations', 'agroevaluations.producteur_id', '=', 'producteurs.id')
            ->join('agroevaluation_especes', 'agroevaluation_especes.agroevaluation_id', '=', 'agroevaluations.id')
            ->where('cooperative_id', $manager->cooperative_id)
            ->select('producteurs.id as producteur_id', 'agroevaluation_especes.agroespecesarbre_id', 'agroevaluation_especes.total')
            ->get();

        $produc = Agrodistribution::select('producteur_id')->get();
        if ($produc) {
            foreach ($produc as $data) {
                $producteurDistri[] = $data->producteur_id;
            }
        }

        $producteurs2 = [];
        foreach ($producteurs as $producteur) {
            if (in_array($producteur->producteur_id, $producteurDistri) == false) {
                $producteurs2[] = $producteur;
            }
        }
        return response()->json([
            'evaluations' => $producteurs2,
        ], 201);
    }


    // public function producteursDistribues(Request $request)
    // {
    //     $manager = User::where('id', $request->userid)->get()->first();
    //     $producteurs  = Producteur::joinRelationship('localite.section')
    //         ->join('agrodistributions', 'agrodistributions.producteur_id', '=', 'producteurs.id')
    //         ->join('agrodistribution_especes', 'agrodistribution_especes.agrodistribution_id', '=', 'agrodistributions.id')
    //         ->where('agrodistributions.cooperative_id', $manager->cooperative_id)
    //         ->select('producteurs.id as producteur_id','producteurs.nom','producteurs.prenoms', 'agrodistribution_especes.agroespecesarbre_id', 'agrodistribution_especes.total')
    //         ->get();


    //     return response()->json($producteurs, 200);
    // }
    public function producteursDistribues(Request $request)
    {
        $manager = User::where('id', $request->userid)->get()->first();
        $producteurs  = Producteur::joinRelationship('localite.section')
            ->join('agrodistributions', 'agrodistributions.producteur_id', '=', 'producteurs.id')
            ->join('agrodistribution_especes', 'agrodistribution_especes.agrodistribution_id', '=', 'agrodistributions.id')
            ->where('agrodistributions.cooperative_id', $manager->cooperative_id)
            ->select('producteurs.id as producteur_id', 'producteurs.nom', 'producteurs.prenoms', 'agrodistribution_especes.agroespecesarbre_id', 'agrodistribution_especes.total')
            ->get();

        $formattedProducteurs = [];
        foreach ($producteurs as $producteur) {
            if (!isset($formattedProducteurs[$producteur->producteur_id])) {
                $formattedProducteurs[$producteur->producteur_id] = [
                    'nom' => $producteur->nom,
                    'prenoms' => $producteur->prenoms,
                    'id' => $producteur->producteur_id,
                    'arbres' => []
                ];
            }
            $formattedProducteurs[$producteur->producteur_id]['arbres'][] = [
                'id_arbre' => $producteur->agroespecesarbre_id,
                'quantite' => $producteur->total
            ];
        }

        return response()->json(array_values($formattedProducteurs), 200);
    }
    public function store_distribution(Request $request)
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
        $manager   = User::where('id', $request->userid)->first();
        $campagne = Campagne::active()->first();

        $datas = [];
        $k = 0;
        $i = 0;
        $nb = 0;

        if ($request->quantite) {
            $datas = [];
            foreach ($request->quantite as $producteurid => $agroespeces) {

                $existe = Agrodistribution::where('producteur_id', $producteurid)->first();

                if ($existe != null) {
                    $distributionID = $existe->id;
                    $datas = [
                        'id' => $distributionID,
                        'quantite' => $request->qtelivre,
                    ];
                } else {

                    $distribution = new Agrodistribution();
                    $distribution->cooperative_id = $manager->cooperative_id;
                    $distribution->producteur_id = $producteurid;
                    $distribution->quantite = $request->qtelivre;
                    $distribution->save();
                    $distributionID = $distribution->id;
                    $nb++;
                    $datas = [
                        'id' => $distributionID,
                        'quantite' => $request->qtelivre,
                    ];
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
            return response()->json($datas, 201);
        }
        return response()->json([], 409);
    }
    public function getApprovisionnementSection(Request $request)
    {
        $approvisionnements = AgroapprovisionnementSectionEspece::joinRelationship('agroapprovisionnementSection', 'agroespecesarbre')->joinRelationship('agroapprovisionnementSection.section')->where([['cooperative_id', $request->cooperativeid]])->select('agroapprovisionnement_section_especes.*','agroapprovisionnement_sections.section_id')->get();

        return response()->json($approvisionnements, 201);
    }
    public function storePostPlanting(Request $request)
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
        $manager   = User::where('id', $request->userid)->first();
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

                foreach ($agroespeces as $agroespecesarbresid => $total) {

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

        return response()->json($distribution, 201);
    }
    public function getdistributionproducteur()
    {
    }
}
