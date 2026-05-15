<?php

namespace App\Http\Controllers;

use App\Models\Campagne;
use App\Models\Localite;
use App\Constants\Status;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\SuiviFormation;
use App\Models\ThemeSousTheme;
use App\Models\Suivi_formation;
use App\Models\SousThemeFormation;
use App\Models\TypeFormationTheme;
use Illuminate\Support\Facades\DB;
use App\Models\SuiviFormationTheme;
use Illuminate\Support\Facades\File;
use App\Models\SuiviFormationVisiteur;
use App\Models\SuiviFormationProducteur;
use App\Models\FormationProducteurFormateur;

class ApisuiviformationController extends Controller
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

        if (!file_exists(storage_path() . "/app/public/formations")) {
            File::makeDirectory(storage_path() . "/app/public/formations", 0777, true);
        }
     
        if ($request->id) {
            $formation = SuiviFormation::findOrFail($request->id);
        } else {
            $formation = new SuiviFormation();
        }

        $campagne = Campagne::active()->first();
        $formation->localite_id  = $request->localite;
        $formation->campagne_id  = $campagne->id;
        $formation->user_id  = $request->staff;
        $formation->lieu_formation  = $request->lieu_formation;
        $formation->duree_formation = $request->duree_formation;
        $formation->observation_formation = $request->observation_formation;
        $formation->formation_type = $request->formation_type;
        $formation->date_debut_formation = $request->multiStartDate;
        $formation->date_fin_formation = $request->multiEndDate;
        $formation->userid = $request->userid;

        $photo_fileNameExtension = Str::afterLast($request->photo_filename, '.');
        $rapport_fileNameExtension = Str::afterLast($request->rapport_filename, '.');
        $photo_docListePresence_fileNameExtension = Str::afterLast($request->photo_docListePresence_filename, '.');
        $docListePresence_fileNameExtension = Str::afterLast($request->docListePresence_filename, '.');


        if ($request->photo_formation) {
            $image = $request->photo_formation;
            $image = Str::after($image, 'base64,');
            $image = str_replace(' ', '+', $image);
            $imageName = (string) Str::uuid() . '.' . $photo_fileNameExtension;
            File::put(storage_path() . "/app/public/formations/" . $imageName, base64_decode($image));
            $photo_formations = "public/formations/$imageName";
            $formation->photo_formation = $photo_formations;
        }

        if ($request->rapport_formation) {
            $rapport_formation = $request->rapport_formation;
            $rapport_formation = Str::after($rapport_formation, 'base64,');
            $rapport_formation = str_replace(' ', '+', $rapport_formation);
            $rapportName = (string) Str::uuid() . '.' . $rapport_fileNameExtension;
            File::put(storage_path() . "/app/public/formations/" . $rapportName, base64_decode($rapport_formation));
            $rapport_formation = "public/formations/$rapportName";

            $formation->rapport_formation = $rapport_formation;
        }
        if ($request->photo_docListePresence) {
            $photo_docListePresence = $request->photo_docListePresence;
            $photo_docListePresence = Str::after($photo_docListePresence, 'base64,');
            $photo_docListePresence = str_replace(' ', '+', $photo_docListePresence);
            $photo_docListePresenceName = (string) Str::uuid() . '.' . $photo_docListePresence_fileNameExtension;
            File::put(storage_path() . "/app/public/formations/" . $photo_docListePresenceName, base64_decode($photo_docListePresence));
            $photo_docListePresence = "public/formations/$photo_docListePresenceName";

            $formation->photo_docListePresence = $photo_docListePresence;
        }
        if($request->docListePresence){
            $docListePresence = $request->docListePresence;
            $docListePresence = Str::after($docListePresence, 'base64,');
            $docListePresence = str_replace(' ', '+', $docListePresence);
            $docListePresenceName = (string) Str::uuid() . '.' . $docListePresence_fileNameExtension;
            File::put(storage_path() . "/app/public/formations/" . $docListePresenceName, base64_decode($docListePresence));
            $docListePresence = "public/formations/$docListePresenceName";

            $formation->docListePresence = $docListePresence;
        }
        
        $formation->save();


        if ($formation != null) {
            $id = $formation->id;
            $datas = $datas2 = [];

            if (($request->producteur != null)) {
                SuiviFormationProducteur::where('suivi_formation_id', $id)->delete();
                $i = 0;
                foreach ($request->producteur as $data) {
                    if ($data != null) {
                        $datas[] = [
                            'suivi_formation_id' => $id,
                            'producteur_id' => $data,
                        ];
                    }
                    $i++;
                }
                SuiviFormationProducteur::insert($datas);
            }

            $selectedThemes = $request->theme;
            if ($selectedThemes != null) {
                TypeFormationTheme::where('suivi_formation_id', $id)->delete();

                foreach ($selectedThemes as $themeId) {
                    list($typeFormationId, $themeItemId) = explode('-', $themeId);
                    $datas3 = [
                        'suivi_formation_id' => $id,
                        'type_formation_id' => $typeFormationId,
                        'theme_formation_id' => $themeItemId,
                    ];
                    TypeFormationTheme::insert($datas3);
                }
            }

            $selectedSousThemes = $request->sous_theme;
            if ($selectedSousThemes != null) {
                ThemeSousTheme::where('suivi_formation_id', $id)->delete();
                foreach ($selectedSousThemes as $sthemeId) {
                    list($themeFormationId, $sousthemeItemId) = explode('-', $sthemeId);
                    $datas2 = [
                        'suivi_formation_id' => $id,
                        'theme_id' => $themeFormationId,
                        'sous_theme_id' => $sousthemeItemId,
                    ];
                    ThemeSousTheme::insert($datas2);
                }
            }
            $selectedFormateurs = $request->formateur;
            $selectedEntreprises = $request->entreprise_formateur;
            if ($selectedFormateurs != null && $selectedEntreprises != null) {
                FormationProducteurFormateur::where('suivi_formation_id', $id)->delete();
                foreach ($selectedFormateurs as $formateurId) {
                    list($entrepriseId, $formateurItemId) = explode('-', $formateurId);
                    $datas4[] = [
                        'suivi_formation_id' => $id,
                        'entreprise_id' => $entrepriseId,
                        'formateur_staff_id' => $formateurItemId,
                    ];
                }
                FormationProducteurFormateur::insert($datas4);
            }
        }


        return response()->json($formation, 201);
    }
    public function getsousthemes()
    {
        $sousthemes = SousThemeFormation::get();

        return response()->json($sousthemes, 201);
    }
    
    public function getvisiteurs(Request $request)
    {
        $suivi_formation_id = $request->suivi_formation_id;
        $visiteurs = SuiviFormationVisiteur::where('suivi_formation_id', $suivi_formation_id)->get();
        return response()->json($visiteurs);
    }

    public function storeVisiteur(Request $request)
    { 
        $visiteur = new SuiviFormationVisiteur();
        $visiteur->producteur_id  = $request->producteur ?? null;
        $visiteur->nom  = $request->nom;
        $visiteur->prenom  = $request->prenom;
        $visiteur->sexe  = $request->sexe;
        $visiteur->telephone  = $request->telephone;
        $visiteur->lien = $request->lien;
        $visiteur->autre_lien = $request->autre_lien;
        $visiteur->representer = $request->representer;
        $visiteur->suivi_formation_id = $request->suivi_formation_id;
        $visiteur->save();
        return response()->json($visiteur, 201);
    }

    public function getTypethemeformation()
    {
        $typeformations = DB::table('type_formations')->select('nom', 'id')->get();
        $donnees = DB::table('themes_formations')->get();
        $type_formations_theme = array();
        foreach ($typeformations as $res) {

            foreach ($donnees as $data) {
                if ($data->type_formation_id == $res->id) {
                    $gestlist[] = array('id' => $data->id, 'libelle' => $data->nom);
                }
            }
            $type_formations_theme[] = array(
                'titretype' => $res->nom,
                'idtype' => $res->id,
                "theme" => $gestlist
            );

            $gestlist = array();
        }
        return response()->json($type_formations_theme, 201);
    }

    public function getTypeformation()
    {

        $typeformations = DB::table('type_formations')->get();

        return response()->json($typeformations, 201);
    }
    public function getThemes()
    {

        $themes = DB::table('themes_formations')->get();

        return response()->json($themes, 201);
    }

    /**
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getformationsByUser(Request $request)
    {
        $request->validate([
            'userid' => 'required|integer|exists:users,id',
        ]);

        $userid = $request->userid;

        $formations = SuiviFormation::where('userid', $userid)
            ->with([
                'formationProducteur' => function ($query) {
                    $query->select('producteur_id', 'suivi_formation_id');
                },
                'typeFormationTheme' => function ($query) {
                    $query->select('type_formation_id', 'suivi_formation_id', 'theme_formation_id');
                },
                'themeSousTheme' => function ($query) {
                    $query->select('sous_theme_id', 'suivi_formation_id');
                },
            ])
            ->get();

        foreach ($formations as $formation) {
            $formation->producteurs_ids = $formation->formationProducteur->pluck('producteur_id');
            unset($formation->formationProducteur);

            $formation->type_formation_ids = $formation->typeFormationTheme->pluck('type_formation_id');
            unset($formation->typeFormationTheme);

            $formation->theme_ids = $formation->typeFormationTheme->pluck('theme_formation_id');
            unset($formation->typeFormationTheme);

            $formation->sous_themes_ids = $formation->themeSousTheme->pluck('sous_theme_id');
            unset($formation->themeSousTheme);
        }

        return response()->json($formations, 201);
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
