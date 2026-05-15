<?php

namespace App\Http\Controllers\Manager;

use Excel;
use App\Models\Campagne;
use App\Models\Localite;
use App\Models\Parcelle;
use App\Constants\Status;
use App\Models\Producteur;
use App\Models\Cooperative;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\SuiviParcelle;
use App\Models\Agroespecesarbre;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\SuiviParcellesAnimal;
use Illuminate\Support\Facades\Hash;
use App\Exports\ExportSuiviParcelles;
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

class SuiviParcelleController extends Controller
{

    public function index()
    {
        $pageTitle      = "Gestion des suivi parcelles";
        $manager   = auth()->user();
        $cooperative = Cooperative::with('sections.localites', 'sections.localites.section')->find($manager->cooperative_id);
        $localites = $cooperative->sections->flatMap->localites->filter(function ($localite) {
            return $localite->active();
        });
        $sections = $cooperative->sections;
        $suiviparcelles = SuiviParcelle::dateFilter()
            ->searchable(["nombreSauvageons","activiteTaille", "activiteEgourmandage", "activiteDesherbageManuel", "activiteRecolteSanitaire", "presencePourritureBrune","presenceFourmisRouge", "presenceAraignee", "presenceVerTerre", "presenceMenteReligieuse", "presenceSwollenShoot", "presenceInsectesParasites", "nombreDesherbage"])
            ->latest('id')
            ->joinRelationship('parcelle.producteur.localite')
            ->where(function ($q) {
                if (request()->localite != null) {
                    $q->where('localite.localite_id', request()->localite);
                }
            })
            ->with(['parcelle.producteur.localite'])
            ->where('suivi_parcelles.userid', $manager->id)
            ->paginate(getPaginate());


        return view('manager.suiviparcelle.index', compact('pageTitle', 'suiviparcelles', 'localites'));
    }

    public function create()
    {
        $pageTitle = "Ajouter un suivi parcelle";
        $manager   = auth()->user();
        $producteurs  = Producteur::joinRelationship('localite.section')
        ->where([['cooperative_id', $manager->cooperative_id],['producteurs.status', 1]])->with('localite')->get();
        $cooperative = Cooperative::with('sections.localites', 'sections.localites.section')->find($manager->cooperative_id);
        $sections = $cooperative->sections;
        $localites = $cooperative->sections->flatMap->localites->filter(function ($localite) {
            return $localite->active();
        });
        $campagnes = Campagne::active()->pluck('nom', 'id');
        $parcelles  = Parcelle::with('producteur')->get();
        $arbres = Agroespecesarbre::all();
        return view('manager.suiviparcelle.create', compact('pageTitle', 'producteurs', 'localites', 'campagnes', 'parcelles', 'sections', 'arbres'));
    }

    public function store(Request $request)
    {
        $validationRule = [
            'parcelle_id'    => 'required|exists:parcelles,id',
            'campagne_id' => 'required|max:255',
            'dateVisite'  => 'required|max:255',
            'items.*.arbre'     => 'required|integer',
            'items.*.nombre'     => 'required|integer',

            'pesticidesAnneDerniere.*.nom'     => 'required|string',
            'pesticidesAnneDerniere.*.unite'     => 'required|string',
            'pesticidesAnneDerniere.*.quantite'     => 'required|integer',
            'pesticidesAnneDerniere.*.contenant'     => 'required|string',
            'pesticidesAnneDerniere.*.frequence'     => 'required|string',

            'intrantsAnneDerniere.*.nom'     => 'required|string',
            'intrantsAnneDerniere.*.unite'     => 'required|string',
            'intrantsAnneDerniere.*.quantite'     => 'required|integer',
            'intrantsAnneDerniere.*.contenant'     => 'required|string',
            'intrantsAnneDerniere.*.frequence'     => 'required|string',


            'traitement.*.nom'     => 'required_if:traiterParcelle,oui',
            'traitement.*.unite'     => 'required_if:traiterParcelle,oui',
            'traitement.*.quantite'     => 'required_if:traiterParcelle,oui',
            'traitement.*.contenant'     => 'required_if:traiterParcelle,oui',
            'traitement.*.frequence'     => 'required_if:traiterParcelle,oui',

            'presenceAutreInsecte.*.autreInsecteNom' => 'required_if:autreInsecte,oui',
            'presenceAutreInsecte.*.nombreAutreInsectesParasites' => 'required_if:autreInsecte,oui',

            'insectesParasites.*.nom' => 'required_if:presenceInsectesParasites,oui',
            'insectesParasites.*.nombreinsectesParasites' => 'required_if:presenceInsectesParasites,oui',

            'insectesAmis.*' => 'required_if:presenceInsectesAmis,oui',
            'nombreinsectesAmis.*' => 'required_if:presenceInsectesAmis,oui',
        ];

        $request->validate($validationRule);

        $localite = Localite::where('id', $request->localite)->first();

        if ($localite->status == Status::NO) {
            $notify[] = ['error', 'Cette localité est désactivée'];
            return back()->withNotify($notify)->withInput();
        }

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
        $suivi_parcelle->userid   = auth()->user()->id;
    //    dd(json_encode($request->all()));
    //dd($request->all());
        
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
            if ( $request->insectesAmis != null && !collect($request->insectesAmis)->contains(null)) {
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
            if ( $request->animauxRencontres != null && !collect($request->animauxRencontres)->contains(null)) {
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

        $notify[] = ['success', isset($message) ? $message : "Le suivi parcelle a été crée avec succès."];
        return back()->withNotify($notify);
    }



    public function edit($id)
    {
        $pageTitle = "Mise à jour du suivi parcelle";
        $manager   = auth()->user();
        $producteurs  = Producteur::joinRelationship('localite.section')
        ->where([['cooperative_id', $manager->cooperative_id],['producteurs.status', 1]])->with('localite')->get();
        $cooperative = Cooperative::with('sections.localites', 'sections.localites.section')->find($manager->cooperative_id);
        $sections = $cooperative->sections;
        $localites = $cooperative->sections->flatMap->localites->filter(function ($localite) {
            return $localite->active();
        });
        $campagnes = Campagne::active()->pluck('nom', 'id');
        $parcelles  = Parcelle::with('producteur')->get();
        $suiviparcelle   = SuiviParcelle::findOrFail($id);
        $pesticidesAnneDerniere = $suiviparcelle->pesticidesAnneDerniere;
        $intrantsAnneDerniere = $suiviparcelle->intrantsAnneDerniere;
        $traitements = $suiviparcelle->traitements;
        $parasites = $suiviparcelle->parasites;
        $autreParasites = $suiviparcelle->autreParasites;
        $amis = $suiviparcelle->insectes;
        $arbres = Agroespecesarbre::all();
        $arbreAgroForestiers = SuiviParcellesAgroforesterie::where('suivi_parcelle_id', $id)->get();
        $arbreOmbrages = SuiviParcellesOmbrage::where('suivi_parcelle_id', $id)->pluck('agroespecesarbre_id')->toArray();
        $parasites = SuiviParcellesParasite::where('suivi_parcelle_id', $id)->get();

        return view('manager.suiviparcelle.edit', compact('pageTitle', 'suiviparcelle', 'producteurs', 'localites', 'campagnes', 'parcelles', 'sections', 'arbres', 'arbreOmbrages', 'arbreAgroForestiers', 'parasites', 'pesticidesAnneDerniere', 'intrantsAnneDerniere', 'traitements','autreParasites','amis'));
    }
    public function show($id){
        $pageTitle = "Détails du suivi parcelle";
        $manager   = auth()->user();
        $producteurs  = Producteur::joinRelationship('localite.section')
        ->where([['cooperative_id', $manager->cooperative_id],['producteurs.status', 1]])->with('localite')->get();
        $cooperative = Cooperative::with('sections.localites', 'sections.localites.section')->find($manager->cooperative_id);
        $sections = $cooperative->sections;
        $localites = $cooperative->sections->flatMap->localites->filter(function ($localite) {
            return $localite->active();
        });
        $campagnes = Campagne::active()->pluck('nom', 'id');
        $parcelles  = Parcelle::with('producteur')->get();
        $suiviparcelle   = SuiviParcelle::findOrFail($id);
        $pesticidesAnneDerniere = $suiviparcelle->pesticidesAnneDerniere;
        $intrantsAnneDerniere = $suiviparcelle->intrantsAnneDerniere;
        $traitements = $suiviparcelle->traitements;
        $parasites = $suiviparcelle->parasites;
        $autreParasites = $suiviparcelle->autreParasites;
        $amis = $suiviparcelle->insectes;
        $arbres = Agroespecesarbre::all();
        $arbreAgroForestiers = SuiviParcellesAgroforesterie::where('suivi_parcelle_id', $id)->get();
        $arbreOmbrages = SuiviParcellesOmbrage::where('suivi_parcelle_id', $id)->pluck('agroespecesarbre_id')->toArray();
        $parasites = SuiviParcellesParasite::where('suivi_parcelle_id', $id)->get();

        return view('manager.suiviparcelle.show', compact('pageTitle', 'suiviparcelle', 'producteurs', 'localites', 'campagnes', 'parcelles', 'sections', 'arbres', 'arbreOmbrages', 'arbreAgroForestiers', 'parasites', 'pesticidesAnneDerniere', 'intrantsAnneDerniere', 'traitements','autreParasites','amis'));
    }

    public function status($id)
    {
        return SuiviParcelle::changeStatus($id);
    }

   

    public function exportExcel()
    {
        $filename = 'suiviparcelles-' . gmdate('dmYhms') . '.xlsx';
        return Excel::download(new ExportSuiviParcelles, $filename);
    }
    public function delete($id)
    { 
        SuiviParcelle::where('id', decrypt($id))->delete();
        $notify[] = ['success', 'Le contenu supprimé avec succès'];
        return back()->withNotify($notify);
    }
}
