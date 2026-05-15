<?php

namespace App\Http\Controllers;

use App\Models\Localite;
use App\Constants\Status;
use Illuminate\Http\Request;
use App\Models\SuiviParcelle;
use App\Models\Suivi_parcelle;
use Illuminate\Support\Facades\DB;
use App\Models\SuiviParcellesAnimal;
use App\Models\SuiviParcellesInsecte;
use App\Models\SuiviParcellesOmbrage;
use App\Models\SuiviParcellesParasite;
use App\Models\Suivi_parcelle_pesticide;
use App\Models\SuiviParcellesInsecteAmi;
use App\Models\SuiviParcellesTraitement;
use App\Models\SuiviParcellesAutreParasite;
use App\Models\SuiviParcellesAgroforesterie;
use App\Models\SuiviParcellesIntrantAnneeDerniere;
use App\Models\SuiviParcellesPesticideAnneDerniere;

class ApisuiviparcelleController extends Controller
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
       

        if ($request->id) {
            $suivi_parcelle = SuiviParcelle::findOrFail($request->id);
            $message = "Le suivi parcelle a été mise à jour avec succès";
        } else {
            $suivi_parcelle = new SuiviParcelle();
        }

        $suivi_parcelle->parcelle_id  = $request->parcelle_id;
        $suivi_parcelle->campagne_id  = $request->campagne_id;
        $suivi_parcelle->nombreSauvageons  = $request->nombreSauvageons;
        $suivi_parcelle->recuArbreAgroForestier  = $request->recuArbreAgroForestier;
        $suivi_parcelle->activiteTaille  = $request->activiteTaille;
        $suivi_parcelle->activiteEgourmandage = $request->activiteEgourmandage;
        $suivi_parcelle->activiteDesherbageManuel = $request->activiteDesherbageManuel;
        $suivi_parcelle->activiteRecolteSanitaire = $request->activiteRecolteSanitaire;
        $suivi_parcelle->activiteRecolteSanitaire = $request->activiteRecolteSanitaire;
        $suivi_parcelle->presencePourritureBrune    = $request->presencePourritureBrune;
        $suivi_parcelle->presenceSwollenShoot    = $request->presenceSwollenShoot;
        $suivi_parcelle->presenceInsectesParasites    = $request->presenceInsectesParasites;
        // $suivi_parcelle->presenceInsectesParasitesRavageur    = $request->presenceInsectesParasitesRavageur;
        $suivi_parcelle->presenceFourmisRouge    = $request->presenceFourmisRouge;
        $suivi_parcelle->presenceAraignee    = $request->presenceAraignee;
        $suivi_parcelle->presenceVerTerre    = $request->presenceVerTerre;
        $suivi_parcelle->presenceMenteReligieuse    = $request->presenceMenteReligieuse;
        $suivi_parcelle->nombreDesherbage    = $request->nombreDesherbage;
        $suivi_parcelle->presenceFourmisRouge   = $request->presenceFourmisRouge;
        $suivi_parcelle->presenceAraignee   = $request->presenceAraignee;
        $suivi_parcelle->presenceVerTerre   = $request->presenceVerTerre;
        $suivi_parcelle->presenceMenteReligieuse   = $request->presenceMenteReligieuse;
        $suivi_parcelle->dateVisite    = $request->dateVisite;
        $suivi_parcelle->traiterParcelle    = $request->traiterParcelle;
        $suivi_parcelle->autreInsecte    = $request->autreInsecte;
        $suivi_parcelle->presenceAutreTypeInsecteAmi   = $request->presenceAutreTypeInsecteAmi;
        $suivi_parcelle->arbresagroforestiers  = $request->arbresagroforestiers;
        $suivi_parcelle->userid   = $request->userid;
        $suivi_parcelle->save();
        if ($suivi_parcelle != null) {
            $datas2=$datas3=$datas6=$datas7=$datas8=$datas9=[];
            $id = $suivi_parcelle->id;
           
            //pesticide utilisé l'année dernière
            if($request->pesticidesAnneDerniere != null){
                SuiviParcellesPesticideAnneDerniere::where('suivi_parcelle_id', $id)->delete();
                foreach ($request->pesticidesAnneDerniere as $pesticide) {
                    $datas2[] = [
                        'suivi_parcelle_id' => $id,
                        'nom' => $pesticide['nom'],
                        'unite' => $pesticide['unite'],
                        'quantite' => $pesticide['quantite'],
                        'contenant' => $pesticide['contenant'],
                        'frequence' => $pesticide['frequence'],
                    ];
                }
            }
            //fin pesticide utilisé l'année dernière 

            //intrants utilisés l'année dernière
            if($request->intrantsAnneDerniere != null){
                SuiviParcellesIntrantAnneeDerniere::where('suivi_parcelle_id', $id)->delete();
                foreach ($request->intrantsAnneDerniere as $intrant) {
                    $datas3[] = [
                        'suivi_parcelle_id' => $id,
                        'nom' => $intrant['nom'],
                        'unite' => $intrant['unite'],
                        'quantite' => $intrant['quantite'],
                        'contenant' => $intrant['contenant'],
                        'frequence' => $intrant['frequence'],
                    ];
                }
            
            }
            //fin
            //arbre d'ombrage souhaite tu avoir
            if (($request->arbre != null)) {
                SuiviParcellesOmbrage::where('suivi_parcelle_id', $id)->delete();
                $i = 0;
                foreach ($request->arbre as $data) {
                    if ($data != null) {
                        $datas[] = [
                            'suivi_parcelle_id' => $id,
                            'agroespecesarbre_id' => $data,
                        ];
                    }
                    $i++;
                }
            }
            //fin arbre d'ombrage souhaite tu avoir

            //les arbres agro-forestiers obtenus
            if (($request->items != null)) {
                SuiviParcellesAgroforesterie::where('suivi_parcelle_id', $id)->delete();
                foreach ($request->items as $item) {

                    $datas8[] = [
                        'suivi_parcelle_id' => $id,
                        'nombre' => $item['nombre'],
                        'agroespeceabre_id' => $item['arbre'],
                    ];
                }
            }
            //fin les arbres agro-forestiers obtenus

            //insectes parasites ou ravageurs$request->insectesParasites != null)
            if ( $request->insectesParasites != null && !collect($request->insectesParasites)->contains(null)) {
                SuiviParcellesParasite::where('suivi_parcelle_id', $id)->delete();
               $datas5 = [];
               foreach ($request->insectesParasites as $parasite) {
                   // Vérifier que la clé "contenant" n'est pas nulle
                   if ($parasite['nom'] !== null) {
                       $datas5[] = [
                           'suivi_parcelle_id' => $id,
                           'parasite' =>$parasite['nom'],
                           'nombre' => $parasite['nombreinsectesParasites']
                       ];
                   }
               }
               if (!empty($datas5)) {
                   SuiviParcellesParasite::insert($datas5);
               }
            }
            //fin insectes parasites ou ravageurs

            //autre parasite ne figurant pas dans la liste
             if ( $request->presenceAutreInsecte != null && !collect($request->presenceAutreInsecte)->contains(null)) {
                SuiviParcellesAutreParasite::where('suivi_parcelle_id', $id)->delete();
                $datas6 = [];
                foreach ($request->presenceAutreInsecte as $autreParasite) {
                    if ($autreParasite['autreInsecteNom'] !== null) {
                        $datas6[] = [
                            'suivi_parcelle_id' => $id,
                            'parasite' => $autreParasite['autreInsecteNom'],
                            'nombre' => $autreParasite['nombreAutreInsectesParasites']
                        ];
                    }
                }
                if (!empty($datas6)) {
                    SuiviParcellesAutreParasite::insert($datas6);
                }
            }
           
            //fin autre parasite ne figurant pas dans la liste

            //traitement parcelle

            if ( $request->traitement != null && !collect($request->traitement)->contains(null)) {
                SuiviParcellesTraitement::where('suivi_parcelle_id', $id)->delete();
            
                $datas4 = [];
            
                foreach ($request->traitement as $trait) {
                    // Vérifier que la clé "contenant" n'est pas nulle
                    if ($trait['contenant'] !== null) {
                        $datas4[] = [
                            'suivi_parcelle_id' => $id,
                            'nom' => $trait['nom'],
                            'unite' => $trait['unite'],
                            'quantite' => $trait['quantite'],
                            'contenant' => $trait['contenant'],
                            'frequence' => $trait['frequence'],
                        ];
                    }
                }
            
                // Insérer les données seulement si le tableau $datas4 n'est pas vide
                if (!empty($datas4)) {
                    SuiviParcellesTraitement::insert($datas4);
                }
            }
            
            //fin traitement parcelle

            //insectes amis
            if ($request->insectesAmis && !collect($request->insectesAmis)->contains(null)) {
                SuiviParcellesInsecteAmi::where('suivi_parcelle_id', $id)->delete();
                $i = 0;
                foreach ($request->insectesAmis as $data) {
                    if ($data != null) {
                        $datas7[] = [
                            'suivi_parcelle_id' => $id,
                            'nom' => $data,
                            'nombre' => $request->nombreinsectesAmis[$i]
                        ];
                    }
                    $i++;
                }
            }
            //fin insectes amis

            //animaux rencontres
            if ($request->animauxRencontres != null && !collect($request->animauxRencontres)->contains(null)) {
                SuiviParcellesAnimal::where('suivi_parcelle_id', $id)->delete();
                $i = 0;
                foreach ($request->animauxRencontres as $data) {
                    if ($data != null) {
                        $datas9[] = [
                            'suivi_parcelle_id' => $id,
                            'animal' => $data
                        ];
                    }
                    $i++;
                }
            }
            //fin animaux rencontres

            SuiviParcellesPesticideAnneDerniere::insert($datas2);
            SuiviParcellesIntrantAnneeDerniere::insert($datas3);
            SuiviParcellesInsecteAmi::insert($datas7);
            SuiviParcellesOmbrage::insert($datas);
            SuiviParcellesAgroforesterie::insert($datas8);
            SuiviParcellesAnimal::insert($datas9);
        }



        return response()->json($suivi_parcelle, 201);
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
