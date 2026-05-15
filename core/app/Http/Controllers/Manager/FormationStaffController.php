<?php

namespace App\Http\Controllers\Manager;

use Excel;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Campagne;
use App\Constants\Status;
use App\Models\Localite;
use App\Models\Entreprise;
use App\Models\Producteur;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\FormationStaff;
use App\Exports\ExportFormations;
use Illuminate\Support\Facades\DB;
use App\Models\FormationStaffListe;
use App\Models\FormationStaffTheme;
use App\Models\ThemeFormationStaff;
use App\Http\Controllers\Controller;
use App\Models\FormateurStaff;
use App\Models\FormationStaffFormateur;
use App\Models\FormationStaffModuleTheme;
use App\Models\ModuleFormationStaff;
use Illuminate\Support\Facades\Hash;
use App\Models\FormationStaffVisiteur;
use Google\Service\PolyService\Format;

class FormationStaffController extends Controller
{

    public function index()
    {
        $pageTitle      = "Gestion des formations STAFF";
        $manager   = auth()->user();

        $modules = ModuleFormationStaff::active()->get();
        $formations = FormationStaff::dateFilter()->searchable(['lieu_formation'])->latest('id')->joinRelationship('cooperative')->where('cooperative_id', $manager->cooperative_id)->where(function ($q) {
            if (request()->module != null) {
                $q->where('module_formation_staff_id', request()->module);
            }
        })->with('cooperative', 'campagne', 'ModuleFormationStaff','formateurs','entreprises')->paginate(getPaginate());
        return view('manager.formation-staff.index', compact('pageTitle', 'formations', 'modules'));
    }

    public function create()
    {
        $pageTitle = "Ajouter une formation aux staffs";
        $manager   = auth()->user();
        $ModuleFormationStaffs  = ModuleFormationStaff::all();
        $themes  = ThemeFormationStaff::with('ModuleFormationStaff')->get();
        $entreprises = Entreprise::all();
        $formateurs = FormateurStaff::with('entreprise')->get();
        $staffs  = User::where('cooperative_id', $manager->cooperative_id)->get();
        return view('manager.formation-staff.create', compact('pageTitle', 'ModuleFormationStaffs', 'themes', 'staffs', 'entreprises', 'formateurs'));
    }

    public function store(Request $request)
    {
        $validationRule = [
            'formateur' => 'required|max:255',
            'lieu_formation'  => 'required|max:255',
            'module_formation'  => 'required|max:255',
            'theme'  => 'required',
        ];

        $manager   = auth()->user();

        $request->validate($validationRule);


        if ($request->id) {
            $formation = FormationStaff::findOrFail($request->id);
            $message = "La formation a été mise à jour avec succès";
        } else {
            $formation = new FormationStaff();
        }
        $campagne = Campagne::active()->first();
        $formation->cooperative_id  = $manager->cooperative_id;
        $formation->campagne_id  = $campagne->id;
        $formation->lieu_formation  = $request->lieu_formation;
        $formation->observation_formation = $request->observation_formation;
        $formation->duree_formation     = $request->duree_formation;
        $formation->userid = auth()->user()->id;

        $formation->date_debut_formation = $request->multiStartDate;
        $formation->date_fin_formation = $request->multiEndDate;

        if ($request->hasFile('photo_formation')) {
            try {
                $formation->photo_formation = $request->file('photo_formation')->store('public/formation-staffs');
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Impossible de télécharger votre image'];
                return back()->withNotify($notify);
            }
        }
        if ($request->hasFile('rapport_formation')) {
            try {
                $formation->rapport_formation = $request->file('rapport_formation')->store('public/formation-staffs');
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Impossible de télécharger votre image'];
                return back()->withNotify($notify);
            }
        }
        //dd(json_encode($request->all()));
        $formation->save();
        if ($formation != null) {
            $id = $formation->id;
            $datas = $datas2 = $datas3 = $datas4 = [];
            if (($request->user != null)) {
                FormationStaffListe::where('formation_staff_id', $id)->delete();
                $i = 0;
                foreach ($request->user as $data) {
                    if ($data != null) {
                        $datas[] = [
                            'formation_staff_id' => $id,
                            'user_id' => $data,
                        ];
                    }
                    $i++;
                }
            }
            if (($request->visiteurs != null)) {
                FormationStaffVisiteur::where('formation_staff_id', $id)->delete();
                $i = 0;
                foreach ($request->visiteurs as $data) {
                    if ($data != null) {
                        $datas2[] = [
                            'formation_staff_id' => $id,
                            'visiteur' => $data,
                        ];
                    }
                    $i++;
                }
            }

            $selectedThemes = $request->theme;
            $selectedModules = $request->module_formation;

            $selectedFormateurs = $request->formateur;
            $selectedEntreprises = $request->entreprise_formateur;

            if ($selectedThemes != null && $selectedModules != null) {
                FormationStaffModuleTheme::where('formation_staff_id', $id)->delete();

                foreach ($selectedThemes as $themeId) {
                    list($moduleFormationId, $themeItemId) = explode('-', $themeId);
                    $datas3[] = [
                        'formation_staff_id' => $id,
                        'module_formation_staff_id' => $moduleFormationId,
                        'theme_formation_staff_id' => $themeItemId,
                    ];
                }
                FormationStaffModuleTheme::insert($datas3);
            }
            if ($selectedFormateurs != null && $selectedEntreprises != null) {
                FormationStaffFormateur::where('formation_staff_id', $id)->delete();
                foreach ($selectedFormateurs as $formateurId) {
                    list($entrepriseId, $formateurItemId) = explode('-', $formateurId);
                    $datas4[] = [
                        'formation_staff_id' => $id,
                        'entreprise_id' => $entrepriseId,
                        'formateur_staff_id' => $formateurItemId,
                    ];
                }
                FormationStaffFormateur::insert($datas4);
            }

            FormationStaffListe::insert($datas);
            FormationStaffVisiteur::insert($datas2);
        }
        $notify[] = ['success', isset($message) ? $message : 'Le formation a été crée avec succès.'];
        return back()->withNotify($notify);
    }

    public function edit($id)
    {
        $pageTitle = "Mise à jour de la formation aux staffs";
        $manager   = auth()->user();

        $entreprises = Entreprise::all();
        $formateurs = FormateurStaff::with('entreprise')->get();
        $formateur_staff_formations = FormationStaffFormateur::where('formation_staff_id', $id)->get();

        $staffs  = User::get();
        $formation   = FormationStaff::findOrFail($id);
        $staffsListe = FormationStaffListe::where('formation_staff_id', $formation->id)->get();
        $visiteurStaff = FormationStaffVisiteur::where('formation_staff_id', $formation->id)->get();

        $moduleFormationStaffs  = ModuleFormationStaff::all();
        $themes  = ThemeFormationStaff::with('ModuleFormationStaff')->get();

        $entreprises = Entreprise::all();
        $formateurs = FormateurStaff::with('entreprise')->get();

        $dataUser = $dataVisiteur = $modules = $themesSelected = $entreprisess = $formateurSelected= array();

        foreach ($formation->formationStaffModuleTheme as $item) {
            $modules[] = $item->module_formation_staff_id;
            $themesSelected[] = $item->theme_formation_staff_id;
        }
        foreach ($formation->formationStaffEntrepriseFormateur as $item) {
            $entreprisess[] = $item->entreprise_id;
            $formateurSelected[] = $item->formateur_staff_id;
        }

        if ($staffsListe->count()) {
            foreach ($staffsListe as $data) {
                $dataUser[] = $data->user_id;
            }
        }
        return view('manager.formation-staff.edit', compact('pageTitle', 'formation', 'themes', 'staffs', 'dataUser', 'visiteurStaff', 'entreprises', 'formateurs', 'moduleFormationStaffs', 'themes', 'themesSelected', 'modules', 'entreprisess', 'formateurSelected'));
    }

    public function status($id)
    {
        return FormationStaff::changeStatus($id);
    }

    public function exportExcel()
    {
        $filename = 'formations-' . gmdate('dmYhms') . '.xlsx';
        return Excel::download(new ExportFormations, $filename);
    }

    public function delete($id)
    {
        FormationStaff::where('id', decrypt($id))->delete();
        $notify[] = ['success', 'Le contenu supprimé avec succès'];
        return back()->withNotify($notify);
    }
}
