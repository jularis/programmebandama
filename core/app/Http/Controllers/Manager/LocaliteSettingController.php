<?php

namespace App\Http\Controllers\Manager;

use App\Http\Helpers\Reply;
use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Imports\LocaliteImport;
use App\Models\Cooperative;
use App\Models\Localite;
use App\Models\Localite_centre_sante;
use App\Models\Localite_ecoleprimaire;
use App\Models\Localite_jour_marche;
use App\Models\Localite_source_eau;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Excel;
use Illuminate\Validation\ValidationException;

class LocaliteSettingController extends Controller
{

    public function index()
    {
        $pageTitle = "Gestion des localités";
        $manager = auth()->user();

        $cooperativeLocalites = Localite::searchable(['localites.nom', 'localites.codeLocal', 'localites.type_localites', 'localites.sousprefecture', 'sections.libelle'])
            ->latest('id')
            ->joinRelationship('section')
            ->with('section.cooperative') // Préchargez la relation "cooperative" ici
            ->where('cooperative_id', $manager->cooperative_id)
            ->paginate(getPaginate());

        $activeSettingMenu = 'localite_settings';
        $cooperatives = Cooperative::active()->where('id', $manager->cooperative_id)->get();
        $sections = Section::all();

        return view('manager.localite-settings.index', compact('pageTitle', 'cooperativeLocalites', 'cooperatives', 'sections', 'activeSettingMenu'));
    }

    public function create()
    {
        $pageTitle = "Ajouter une localité";
        $manager   = auth()->user();
        $activeSettingMenu = 'localite_settings';
        $sections = Section::where('cooperative_id', $manager->cooperative_id)->get();
        return view('manager.localite-settings.create', compact('pageTitle', 'sections', 'manager', 'activeSettingMenu'));
    }

    public function store(Request $request)
    {
        // DB::beginTransaction();
        // try {

            
        //     }
        // } catch (ValidationException $e) {
        //     DB::rollBack();
        // }
        
        // DB::commit();
        $validationRule = [
            'section_id'    => 'required|exists:sections,id',
            'nom' => 'required|max:255',
            'type_localites'  => 'required|max:255',
            'sousprefecture'  => 'required|max:255',
            'centresante'  => 'required|max:255',
            'ecole'  => 'required|max:255',
            'electricite'  => 'required|max:255',
            'marche'  => 'required|max:255',
            'deversementDechets'  => 'required|max:255',
            'kmEcoleproche' => 'required_if:ecole,==,non',
            'nomEcoleproche' => 'required_if:ecole,==,non',
            'kmCentresante' => 'required_if:centresante,==,non',
            'nomCentresante' => 'required_if:centresante,==,non',
        ];
        $messages = [
            'section_id.required' => 'Le champ section est obligatoire',
            'section_id.exists' => 'Le champ section est invalide',
            'nom.required' => 'Le champ nom est obligatoire',
            'nom.max' => 'Le champ nom ne doit pas dépasser 255 caractères',
            'type_localites.required' => 'Le champ type de localité est obligatoire',
            'type_localites.max' => 'Le champ type de localité ne doit pas dépasser 255 caractères',
            'sousprefecture.required' => 'Le champ sous préfecture est obligatoire',
            'sousprefecture.max' => 'Le champ sous préfecture ne doit pas dépasser 255 caractères',
            'centresante.required' => 'Le champ centre de santé est obligatoire',
            'centresante.max' => 'Le champ centre de santé ne doit pas dépasser 255 caractères',
            'ecole.required' => 'Le champ école est obligatoire',
            'ecole.max' => 'Le champ école ne doit pas dépasser 255 caractères',
            'kmEcoleproche.' => 'Le champ km école proche est obligatoire',
            'nomEcoleproche' => 'Le champ nom école proche est obligatoire',
        ];

        $manager   = auth()->user();


        $request->validate($validationRule, $messages);

        $cooperative = $manager->cooperative;

        if ($cooperative->status == Status::NO) {
            $notify[] = ['error', 'Cette coopérative est désactivé'];
            return back()->withNotify($notify)->withInput();
        }

        if ($request->id) {
            $localite = Localite::findOrFail($request->id);
            $message = "La localité a été mise à jour avec succès";
        } else {
            $localite           = new Localite();
            $localite->nom = $this->verifylocalite($request->nom);
        }
        $localite->section_id = $request->section_id;
        $localite->userid = $manager->id;
        $localite->nom = $request->nom;
        $localite->type_localites  = $request->type_localites;
        $localite->sousprefecture  = $request->sousprefecture;
        $localite->population     = $request->population;
        $localite->centresante    = $request->centresante;
        $localite->kmCentresante    = $request->kmCentresante;
        $localite->typecentre    = $request->typecentre;
        $localite->nomCentresante    = $request->nomCentresante;
        $localite->ecole    = $request->ecole;
        $localite->kmEcoleproche    = $request->kmEcoleproche;
        $localite->nomEcoleproche    = $request->nomEcoleproche;
        $localite->nombrecole    = $request->nombrecole;
        $localite->etatpompehydrau    = $request->etatpompehydrau;
        $localite->marche    = $request->marche;
        $localite->kmmarcheproche    = $request->kmmarcheproche;
        $localite->deversementDechets    = $request->deversementDechets;
        $localite->comiteMainOeuvre    = $request->comiteMainOeuvre;
        $localite->associationFemmes    = $request->associationFemmes;
        $localite->associationJeunes    = $request->associationJeunes;
        $localite->localongitude    = $request->localongitude;
        $localite->localatitude    = $request->localatitude;

        $localite->codeLocal    = isset($request->codeLocal) ? $request->codeLocal : $this->generelocalitecode($request->nom);
        $localite->save();

        if ($localite != null) {
            $id = $localite->id;
            $datas = [];
            if ($request->nomecolesprimaires != null && $request->latitude != null && $request->longitude != null) {
                $verification   = Localite_ecoleprimaire::where('localite_id', $id)->get();
                if ($verification->count()) {
                    DB::table('localite_ecoleprimaires')->where('localite_id', $id)->delete();
                }
                $i = 0;
                foreach ($request->nomecolesprimaires as $datas) {
                    DB::table('localite_ecoleprimaires')->insert(['localite_id' => $id, 'nomecole' =>$datas, 'latitude' => $request->latitude[$i], 'longitude' => $request->longitude[$i]]); 
                    $i++;
                }
            }

            if ($request->nomcentresantes != null && $request->latitude != null && $request->longitude != null) {
                $verification   = Localite_centre_sante::where('localite_id', $id)->get();
                if ($verification->count()) {
                    DB::table('localite_centre_santes')->where('localite_id', $id)->delete();
                }
                $i = 0;
                foreach ($request->nomcentresantes as $datas) {
                    DB::table('localite_centre_santes')->insert(['localite_id' => $id, 'centre_sante' =>$datas, 'latitude' => $request->latitude[$i], 'longitude' => $request->longitude[$i]]); 
                    $i++;
                }
            }
            if($request->jourmarche != null){
                $verification   = Localite_jour_marche::where('localite_id', $id)->get();
                if ($verification->count()) {
                    DB::table('localite_jour_marches')->where('localite_id', $id)->delete();
                }
                $i = 0;
                foreach ($request->jourmarche as $data) {
                    DB::table('localite_jour_marches')->insert(['localite_id' => $id, 'jour_marche' =>$data]); 
                    $i++;
                }
            }
            if($request->eauPotables != null){
                $verification   = Localite_source_eau::where('localite_id', $id)->get();
                if ($verification->count()) {
                    DB::table('localite_source_eaux')->where('localite_id', $id)->delete();
                }
                $i = 0;
                foreach ($request->eauPotables as $data) {
                    DB::table('localite_source_eaux')->insert(['localite_id' => $id, 'souces_deaux' =>$data]); 
                    $i++;
                }
            }
            
        }
        return Reply::successWithData(__('messages.recordSaved'), ['redirectUrl' => route('manager.settings.localite-settings.index')]);
    }

    private function verifylocalite($nom)
    {
        $action = 'non';
        do {
            $data = Localite::select('nom')->where('nom', $nom)->orderby('id', 'desc')->first();
            if ($data != '') {

                $nomLocal = $data->nom;
                $nom = Str::beforeLast($nomLocal, ' ');
                $chaine_number = Str::afterLast($nomLocal, ' ');

                if (is_numeric($chaine_number) && ($chaine_number < 10)) {
                    $zero = "00";
                } else if (is_numeric($chaine_number) && ($chaine_number < 100)) {
                    $zero = "0";
                } else {
                    $zero = "00";
                    $chaine_number = 0;
                }

                $sub = $nom . ' ';
                $lastCode = $chaine_number + 1;
                $nomLocal = $sub . $zero . $lastCode;
            } else {

                $nomLocal = $nom;
            }
            $verif = Localite::select('nom')->where('nom', $nomLocal)->orderby('id', 'desc')->first();
            if ($verif == null) {
                $action = 'non';
            } else {
                $action = 'oui';
                $nom = $verif->nom;
            }
        } while ($action != 'non');

        return $nomLocal;
    }

    private function generelocalitecode($name)
    {
        $action = 'non';
        do {

            $data = Localite::select('codeLocal')->where('nom', $name)->orderby('id', 'desc')->first();

            if ($data != '') {

                $code = $data->codeLocal;

                $chaine_number = Str::afterLast($code, '-');

                if ($chaine_number < 10) {
                    $zero = "00";
                } else if ($chaine_number < 100) {
                    $zero = "0";
                } else {
                    $zero = "";
                }
            } else {
                $zero = "00";
                $chaine_number = 0;
            }

            $abrege = Str::upper(Str::substr($name, 0, 3));
            $sub = $abrege . '-';
            $lastCode = $chaine_number + 1;
            $codeP = $sub . $zero . $lastCode;

            $verif = Localite::select('nom')->where('codeLocal', $codeP)->orderby('id', 'desc')->first();
            if ($verif == null) {
                $action = 'non';
            } else {
                $action = 'oui';
                $name = $verif->nom;
            }
        } while ($action != 'non');

        return $codeP;
    }

    public function edit($id)
    {
        $pageTitle = "Mise à jour de la localité";
        $manager   = auth()->user();
        $localite   = Localite::findOrFail($id);
        $activeSettingMenu = 'localite_settings';
        $sections = Section::where('cooperative_id', $manager->cooperative_id)->get();
        $jours = $localite->marches->pluck('jour_marche')->toArray();
        $eaux = $localite->eaux->pluck('souces_deaux')->toArray();
        // $energies = $menage->menage_sourceEnergie->pluck('source_energie')->toArray();
        return view('manager.localite-settings.edit', compact('pageTitle', 'sections', 'localite', 'manager', 'activeSettingMenu', 'jours', 'eaux'));
    }

    public function status($id)
    {
        return Localite::changeStatus($id);
    }

    public function cooperativeManager($id)
    {
        $cooperative         = Cooperative::findOrFail($id);
        $pageTitle      = $cooperative->name . " Manager List";
        $cooperativeLocalites = Localite::localite()->where('cooperative_id', $id)->orderBy('id', 'DESC')->with('cooperative')->paginate(getPaginate());
        return view('manager.localite-settings.index', compact('pageTitle', 'cooperativeLocalites'));
    }
    public function  uploadContent(Request $request)
    {
        Excel::import(new LocaliteImport, $request->file('uploaded_file'));
        return back();
    }

    public function destroy($id)
    {
        Localite::destroy($id);
        return Reply::success(__('messages.deleteSuccess'));
    }
}
