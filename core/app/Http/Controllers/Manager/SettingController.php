<?php

namespace App\Http\Controllers\manager;

use App\Models\Unit;
use App\Models\User;
use App\Models\Marque;
use App\Models\Section;
use App\Models\Campagne;
use App\Models\Countrie;
use App\Models\Instance;
use App\Models\Remorque;
use App\Models\Vehicule;
use App\Constants\Status;
use App\Models\ArretEcole;
use App\Models\Department;
use App\Models\Entreprise;
use App\Http\Helpers\Files;
use App\Http\Helpers\Reply;
use App\Models\Cooperative;
use App\Models\CourierInfo;
use App\Models\Designation;
use App\Models\TypeArchive;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\Models\NiveauxEtude;
use App\Models\Transporteur;
use App\Models\UserLocalite;
use Illuminate\Http\Request;
use App\Models\Certification;
use App\Models\Questionnaire;
use App\Models\TravauxLegers;
use App\Models\TypeFormation;
use App\Models\CourierPayment;
use App\Models\CourierProduct;
use App\Models\FormateurStaff;
use App\Models\MagasinCentral;
use App\Models\MagasinSection;
use App\Models\ThemesFormation;
use App\Models\Agroespecesarbre;
use App\Models\TravauxDangereux;
use App\Models\SousThemeFormation;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use App\Models\ThemeFormationStaff;
use App\Http\Controllers\Controller;
use App\Models\ModuleFormationStaff;
use App\Models\DocumentAdministratif;
use App\Models\CategorieQuestionnaire;
use Google\Service\Blogger\UserLocale;

class SettingController extends Controller
{


    public function campagneIndex()
    {
        $pageTitle = "Manage Campagne";
        $activeSettingMenu = 'campagne_settings';
        $campagnes     = Campagne::orderBy('id', 'desc')->paginate(getPaginate());
        return view('manager.config.campagne', compact('pageTitle', 'campagnes', 'activeSettingMenu'));
    }

    public function campagneStore(Request $request)
    {
        $request->validate([
            'produit'  => 'required',
            'nom'  => 'required',
            'periode_debut'  => 'required',
            'periode_fin'  => 'required',
            'prix_achat' => 'required|gt:0|numeric',
            'prix_champ' => 'required|gt:0|numeric',
            'prime' => 'required|gt:0|numeric',
        ]);

        if ($request->id) {
            $campagne    = Campagne::findOrFail($request->id);
            $message = "Campagne a été mise à jour avec succès.";
        } else {
            $campagne = new Campagne();
        }
        $manager = auth()->user();
        $campagne->cooperative_id = $manager->cooperative_id;
        $campagne->produit    = $request->produit;
        $campagne->nom = $request->nom;
        $campagne->periode_debut = $request->periode_debut;
        $campagne->periode_fin = $request->periode_fin;
        $campagne->prix_achat   = $request->prix_achat;
        $campagne->prix_champ   = $request->prix_champ;
        $campagne->prime   = $request->prime;
        $campagne->save();
        $notify[] = ['success', isset($message) ? $message  : 'Campagne a été ajouté avec succès.'];
        return back()->withNotify($notify);
    }

    public function travauxDangereuxIndex()
    {
        $pageTitle = "Manage Travaux Dangereux";
        $activeSettingMenu = 'travauxDangereux_settings';
        $travauxDangereux     = TravauxDangereux::orderBy('id', 'desc')->paginate(getPaginate());
        return view('manager.config.travauxDangereux', compact('pageTitle', 'travauxDangereux', 'activeSettingMenu'));
    }

    public function travauxDangereuxStore(Request $request)
    {
        $request->validate([
            'nom'  => 'required',
        ]);

        if ($request->id) {
            $travauxDangereux    = TravauxDangereux::findOrFail($request->id);
            $message = "Travaux Dangereux a été mise à jour avec succès.";
        } else {
            $travauxDangereux = new TravauxDangereux();
        }
        $travauxDangereux->nom = trim($request->nom);
        $travauxDangereux->save();
        $notify[] = ['success', isset($message) ? $message  : 'Travaux Dangereux a été ajouté avec succès.'];
        return back()->withNotify($notify);
    }

    public function travauxLegersIndex()
    {
        $pageTitle = "Manage Travaux Legers";
        $activeSettingMenu = 'travauxLegers_settings';
        $travauxLegers     = TravauxLegers::orderBy('id', 'desc')->paginate(getPaginate());
        return view('manager.config.travauxLegers', compact('pageTitle', 'travauxLegers', 'activeSettingMenu'));
    }

    public function travauxLegersStore(Request $request)
    {
        $request->validate([
            'nom'  => 'required',
        ]);

        if ($request->id) {
            $travauxLegers    = TravauxLegers::findOrFail($request->id);
            $message = "Travaux Legers a été mise à jour avec succès.";
        } else {
            $travauxLegers = new TravauxLegers();
        }
        $travauxLegers->nom = trim($request->nom);
        $travauxLegers->save();
        $notify[] = ['success', isset($message) ? $message  : 'Travaux Legers a été ajouté avec succès.'];
        return back()->withNotify($notify);
    }

    public function arretEcoleIndex()
    {
        $pageTitle = "Manage Arret Ecole";
        $activeSettingMenu = 'arretEcole_settings';
        $arretEcole     = ArretEcole::orderBy('id', 'desc')->paginate(getPaginate());
        return view('manager.config.arretEcole', compact('pageTitle', 'arretEcole', 'activeSettingMenu'));
    }

    public function arretEcoleStore(Request $request)
    {
        $request->validate([
            'nom'  => 'required',
        ]);

        if ($request->id) {
            $arretEcole    = ArretEcole::findOrFail($request->id);
            $message = "Arret Ecole a été mise à jour avec succès.";
        } else {
            $arretEcole = new ArretEcole();
        }
        $arretEcole->nom = trim($request->nom);
        $arretEcole->save();
        $notify[] = ['success', isset($message) ? $message  : 'Arret Ecole a été ajouté avec succès.'];
        return back()->withNotify($notify);
    }

    public function typeFormationIndex()
    {
        $pageTitle = "Manage Type Formation";
        $activeSettingMenu = 'typeFormation_settings';
        $typeFormation     = TypeFormation::orderBy('id', 'desc')->paginate(getPaginate());
        return view('manager.config.typeFormation', compact('pageTitle', 'typeFormation', 'activeSettingMenu'));
    }

    public function typeFormationStore(Request $request)
    {
        $request->validate([
            'nom'  => 'required',
        ]);

        if ($request->id) {
            $typeFormation    = TypeFormation::findOrFail($request->id);
            $message = "Type Formation a été mise à jour avec succès.";
        } else {
            $typeFormation = new TypeFormation();
        }
        $typeFormation->nom = trim($request->nom);
        $typeFormation->save();
        $notify[] = ['success', isset($message) ? $message  : 'Type Formation a été ajouté avec succès.'];
        return back()->withNotify($notify);
    }
    public function themeFormationIndex()
    {
        $pageTitle = "Manage theme de Formation";
        $activeSettingMenu = 'themeFormation_settings';
        $themeFormation     = ThemesFormation::with('typeFormation')->orderBy('id', 'desc')->paginate(getPaginate());
        $typeFormation = TypeFormation::get();
        return view('manager.config.themeFormation', compact('pageTitle', 'themeFormation', 'typeFormation', 'activeSettingMenu'));
    }
    public function sousThemeFormationIndex()
    {
        $pageTitle = "Manage Sous theme de Formation";
        $activeSettingMenu = 'sousThemeFormation_settings';
        $sousThemeFormation     = SousThemeFormation::with('themeFormation')->orderBy('id', 'desc')->paginate(getPaginate());
        $themeFormation = ThemesFormation::with('typeFormation')->get();
        $typeFormation = TypeFormation::get();
        return view('manager.config.sousThemeFormation', compact('pageTitle', 'sousThemeFormation', 'themeFormation', 'activeSettingMenu', 'typeFormation'));
    }

    public function sousThemeFormationStore(Request $request)
    {
        $request->validate([
            'nom'  => 'required',
            'themeFormation' => 'required',
        ]);

        if ($request->id) {
            $sousThemeFormation    = SousThemeFormation::findOrFail($request->id);
            $message = "Sous theme de Formation a été mise à jour avec succès.";
        } else {
            $sousThemeFormation = new SousThemeFormation();
        }
        $sousThemeFormation->nom = trim($request->nom);
        $sousThemeFormation->theme_formation_id = $request->themeFormation;
        $sousThemeFormation->save();
        $notify[] = ['success', isset($message) ? $message  : 'Sous theme de Formation a été ajouté avec succès.'];
        return back()->withNotify($notify);
    }

    public function themeFormationStore(Request $request)
    {
        $request->validate([
            'nom'  => 'required',
            'typeformation' => 'required',
        ]);

        if ($request->id) {
            $themeFormation    = ThemesFormation::findOrFail($request->id);
            $message = "theme de Formation a été mise à jour avec succès.";
        } else {
            $themeFormation = new ThemesFormation();
        }
        $themeFormation->nom = trim($request->nom);
        $themeFormation->type_formation_id = $request->typeformation;
        $themeFormation->save();
        $notify[] = ['success', isset($message) ? $message  : 'theme de Formation a été ajouté avec succès.'];
        return back()->withNotify($notify);
    }

    public function moduleFormationStaffIndex()
    {
        $pageTitle = "Manage Module de Formation Staff";
        $activeSettingMenu = 'moduleFormationStaff_settings';
        $moduleFormations     = ModuleFormationStaff::orderBy('id', 'desc')->paginate(getPaginate());
        return view('manager.config.moduleFormationStaff', compact('pageTitle', 'moduleFormations', 'activeSettingMenu'));
    }

    public function moduleFormationStaffStore(Request $request)
    {
        $request->validate([
            'nom'  => 'required',
        ]);

        if ($request->id) {
            $moduleFormationStaff    = ModuleFormationStaff::findOrFail($request->id);
            $message = "Module de Formation Staff a été mise à jour avec succès.";
        } else {
            $moduleFormationStaff = new ModuleFormationStaff();
        }
        $moduleFormationStaff->nom = trim($request->nom);
        $moduleFormationStaff->save();
        $notify[] = ['success', isset($message) ? $message  : 'Module de Formation Staff a été ajouté avec succès.'];
        return back()->withNotify($notify);
    }

    public function themeFormationStaffIndex()
    {
        $pageTitle = "Manage theme de Formation Staff";
        $activeSettingMenu = 'themeFormationStaff_settings';
        $themeFormationStaff     = ThemeFormationStaff::with('moduleFormationStaff')->orderBy('id', 'desc')->paginate(getPaginate());
        $moduleFormationStaffs = ModuleFormationStaff::get();
        return view('manager.config.themeFormationStaff', compact('pageTitle', 'themeFormationStaff', 'moduleFormationStaffs', 'activeSettingMenu'));
    }

    public function themeFormationStaffStore(Request $request)
    {
        $request->validate([
            'nom'  => 'required',
            'moduleFormationStaff' => 'required',
        ]);

        if ($request->id) {
            $themeFormationStaff    = ThemeFormationStaff::findOrFail($request->id);
            $message = "theme de Formation Staff a été mise à jour avec succès.";
        } else {
            $themeFormationStaff = new ThemeFormationStaff();
        }
        $themeFormationStaff->nom = trim($request->nom);
        $themeFormationStaff->module_formation_staff_id = $request->moduleFormationStaff;
        $themeFormationStaff->save();
        $notify[] = ['success', isset($message) ? $message  : 'theme de Formation Staff a été ajouté avec succès.'];
        return back()->withNotify($notify);
    }

    public function categorieQuestionnaireIndex()
    {
        $pageTitle = "Manage Categorie Questionnaire";
        $activeSettingMenu = 'categorieQuestionnaire_settings';
        $categorieQuestionnaire     = CategorieQuestionnaire::orderBy('id', 'desc')->paginate(getPaginate());
        return view('manager.config.categorieQuestionnaire', compact('pageTitle', 'categorieQuestionnaire', 'activeSettingMenu'));
    }

    public function categorieQuestionnaireStore(Request $request)
    {
        $request->validate([
            'titre'  => 'required',
        ]);

        if ($request->id) {
            $categorieQuestionnaire    = CategorieQuestionnaire::findOrFail($request->id);
            $message = "Categorie Questionnaire a été mise à jour avec succès.";
        } else {
            $categorieQuestionnaire = new CategorieQuestionnaire();
        }
        $categorieQuestionnaire->titre = trim($request->titre);
        $categorieQuestionnaire->save();
        $notify[] = ['success', isset($message) ? $message  : 'Categorie Questionnaire a été ajouté avec succès.'];
        return back()->withNotify($notify);
    }

    public function questionnaireIndex()
    {
        $pageTitle = "Manage Questionnaire";
        $activeSettingMenu = 'questionnaire_settings';
        $questionnaire     = Questionnaire::with('categorieQuestion')->orderBy('id', 'desc')->paginate(getPaginate());
        $categorieQuestion = CategorieQuestionnaire::get();
        $certifications = Certification::get();
        return view('manager.config.questionnaire', compact('pageTitle', 'questionnaire', 'categorieQuestion', 'activeSettingMenu', 'certifications'));
    }

    public function questionnaireStore(Request $request)
    {
        $request->validate([
            'nom'  => 'required',
            'categoriequestionnaire' => 'required',
            'certificat'  => 'required',
        ]);

        if ($request->id) {
            $questionnaire    = Questionnaire::findOrFail($request->id);
            $message = "Questionnaire a été mise à jour avec succès.";
        } else {
            $questionnaire = new Questionnaire();
        }
        $questionnaire->nom = trim($request->nom);
        $questionnaire->categorie_questionnaire_id = $request->categoriequestionnaire;
        $questionnaire->certificat = trim($request->certificat);
        $questionnaire->save();
        $notify[] = ['success', isset($message) ? $message  : 'Questionnaire a été ajouté avec succès.'];
        return back()->withNotify($notify);
    }
    public function especeArbreIndex()
    {
        $pageTitle = "Manage Espaces Arbres";
        $activeSettingMenu = 'especeArbre_settings';
        $especeArbre     = Agroespecesarbre::orderBy('id', 'desc')->paginate(getPaginate());
        return view('manager.config.especeArbre', compact('pageTitle', 'especeArbre', 'activeSettingMenu'));
    }
    public function especeArbreStore(Request $request)
    {
        $request->validate([
            'nom'  => 'required',
        ]);

        if ($request->id) {
            $especeArbre    = Agroespecesarbre::findOrFail($request->id);
            $message = "Espece Arbre a été mise à jour avec succès.";
        } else {
            $especeArbre = new Agroespecesarbre();
        }
        $especeArbre->nom = trim($request->nom);
        $especeArbre->strate = trim($request->strate);
        $especeArbre->nom_scientifique = trim($request->nom_scientifique);
        $especeArbre->save();
        $notify[] = ['success', isset($message) ? $message  : 'Espece Arbre a été ajouté avec succès.'];
        return back()->withNotify($notify);
    }

    public function typeArchiveIndex()
    {
        $pageTitle = "Manage Type Archives";
        $activeSettingMenu = 'typeArchive_settings';
        $typeArchive     = TypeArchive::orderBy('id', 'desc')->paginate(getPaginate());
        return view('manager.config.typeArchive', compact('pageTitle', 'typeArchive', 'activeSettingMenu'));
    }
    public function typeArchiveStore(Request $request)
    {
        $request->validate([
            'nom'  => 'required',
        ]);

        if ($request->id) {
            $typeArchive    = TypeArchive::findOrFail($request->id);
            $message = "Espece Arbre a été mise à jour avec succès.";
        } else {
            $typeArchive = new TypeArchive();
        }
        $typeArchive->nom = trim($request->nom);
        $typeArchive->save();
        $notify[] = ['success', isset($message) ? $message  : 'Le contenu a été ajouté avec succès.'];
        return back()->withNotify($notify);
    }
    public function instanceIndex()
    {
        $this->pageTitle = "Ajouter une instance";
        return view('manager.config.create-instance-modal', $this->data);
    }
    public function instanceStore(Request $request)
    {
        $request->validate([
            'nom'  => 'required',
        ]);

        if ($request->id) {
            $instance    = Instance::findOrFail($request->id);
            $message = "Espece Arbre a été mise à jour avec succès.";
        } else {
            $instance = new Instance();
        }
        $instance->nom = trim($request->nom);
        $instance->save();

        return Reply::successWithData(__('Le contenu a été ajouté avec succès.'), ['page_reload' => $request->page_reload]);
    }
    public function documentadIndex()
    {
        $this->pageTitle = "Ajouter un document administratif";
        return view('manager.config.create-document-modal', $this->data);
    }
    public function documentadStore(Request $request)
    {
        $request->validate([
            'nom'  => 'required',
        ]);

        if ($request->id) {
            $documentad    = Instance::findOrFail($request->id);
        } else {
            $documentad = new DocumentAdministratif();
        }
        $documentad->nom = trim($request->nom);
        $documentad->save();

        return Reply::successWithData(__('Le contenu a été ajouté avec succès.'), ['page_reload' => $request->page_reload]);
    }
    public function entrepriseModalIndex()
    {
        $pageTitle = "Ajouter une entreprise";
        return view('manager.config.create-entreprise-modal', compact('pageTitle'));
    }
    public function entrepriseIndex()
    {
        $manager = auth()->user();
        $pageTitle = "Manage des Entreprises";
        $manager = auth()->user();

        $activeSettingMenu = 'entreprise_settings';
        // $transporteurs = Entreprise::orderBy('id', 'desc')->paginate(getPaginate());
        // $countries = Countrie::get();
        // $niveaux = NiveauxEtude::get();
        //$entreprises = Entreprise::orderBy('id','desc')->paginate(getPaginate());
        $entreprises = Entreprise::where([['cooperative_id', $manager->cooperative_id]])->orderBy('id','desc')->paginate(getPaginate());

        return view('manager.config.entreprise', compact('pageTitle', 'activeSettingMenu', 'entreprises'));
    }
    public function entrepriseStore(Request $request)
    {
        $request->validate([
            'nom_entreprise'  => 'required',
            'telephone_entreprise'  => 'required',
            'adresse_entreprise'  => 'required',
            'mail_entreprise'  => 'required',
        ]);

        if ($request->id) {
            $entreprise    = Entreprise::findOrFail($request->id);
        } else {
            $entreprise = new Entreprise();
        }
        $entreprise->cooperative_id = auth()->user()->cooperative_id;
        $entreprise->nom_entreprise = trim($request->nom_entreprise);
        $entreprise->mail_entreprise = trim($request->mail_entreprise);
        $entreprise->telephone_entreprise = trim($request->telephone_entreprise);
        $entreprise->adresse_entreprise = trim($request->adresse_entreprise);
        $entreprise->save();
        if (!request()->ajax()) {
            $notify[] = ['success', 'Le contenu a été ajouté avec succès.'];
            return back()->withNotify($notify);
        } else {
            return Reply::successWithData(__('L\'entreprise a été ajouté avec succès.'), ['page_reload' => $request->page_reload]);
        }
    }

    public function formateurStaffIndex()
    {
        $pageTitle = "Ajouter un formateur staff";
        $entreprises = Entreprise::get();
        return view('manager.config.create-formateur-modal', compact('pageTitle', 'entreprises'));
    }
    public function formateurList(Request $request)
    {
        $pageTitle = "Manage des Formateurs";
        $manager = auth()->user();
        $activeSettingMenu = 'formateur_settings';
        $entreprises = Entreprise::get();
        $formateurs = FormateurStaff::orderBy('id','desc')->with('entreprise') ->paginate(getPaginate());
        return view('manager.config.formateur', compact('formateurs', 'pageTitle', 'activeSettingMenu', 'entreprises'));
    }
    public function formateurStaffStore(Request $request)
    {
        $request->validate([
            'nom_formateur'  => 'required',
            'entreprise_id' => 'required',
            'prenom_formateur'  => 'required',
            'telephone_formateur'  => 'required',
            'poste_formateur'  => 'required',
        ]);

        if ($request->id) {
            $formateurStaff    = FormateurStaff::findOrFail($request->id);
            $message = "Formateur Staff a été mise à jour avec succès.";
        } else {
            $formateurStaff = new FormateurStaff();
        }
        $formateurStaff->entreprise_id = $request->entreprise_id;
        $formateurStaff->nom_formateur = trim($request->nom_formateur);
        $formateurStaff->prenom_formateur = trim($request->prenom_formateur);
        $formateurStaff->telephone_formateur = trim($request->telephone_formateur);
        $formateurStaff->poste_formateur = trim($request->poste_formateur);

        $formateurStaff->save();
        if (!request()->ajax()) {
            $notify[] = ['success', isset($message) ? $message  : 'Formateur Staff a été ajouté avec succès.'];
            return back()->withNotify($notify);
        } else {
            return Reply::successWithData(__('Formateur Staff a été ajouté avec succès.'), ['page_reload' => $request->page_reload]);
        }
    }

    public function departementIndex()
    {
        $pageTitle = "Manage Départements";
        $activeSettingMenu = 'departement_settings';
        $departements     = Department::where('cooperative_id', auth()->user()->cooperative_id)->orderBy('id', 'desc')->paginate(getPaginate());
        return view('manager.config.departement', compact('pageTitle', 'departements', 'activeSettingMenu'));
    }
    public function departementStore(Request $request)
    {
        $request->validate([
            'nom'  => 'required',
        ]);

        if ($request->id) {
            $department    = Department::findOrFail($request->id);
            $message = "Le contenu a été mise à jour avec succès.";
        } else {
            $department = new Department();
        }
        $department->cooperative_id = auth()->user()->cooperative_id;
        $department->department = trim($request->nom);
        $department->save();

        $notify[] = ['success', isset($message) ? $message  : 'Le contenu a été ajouté avec succès.'];
        return back()->withNotify($notify);
    }
    public function designationIndex()
    {
        $pageTitle = "Manage Désignations";
        $activeSettingMenu = 'designation_settings';
        $departements = Department::where('cooperative_id', auth()->user()->cooperative_id)->get();
        $designations     = Designation::where('cooperative_id', auth()->user()->cooperative_id)->orderBy('id', 'desc')->paginate(getPaginate());
        return view('manager.config.designation', compact('pageTitle', 'departements', 'designations', 'activeSettingMenu'));
    }
    public function designationStore(Request $request)
    {
        $request->validate([
            'nom'  => 'required',
            'departement_id'  => 'required',
        ]);

        if ($request->id) {
            $designation    = Designation::findOrFail($request->id);
            $message = "Le contenu a été mise à jour avec succès.";
        } else {
            $designation = new Designation();
        }
        $designation->cooperative_id = auth()->user()->cooperative_id;
        $designation->name = trim($request->nom);
        $designation->parent_id = trim($request->departement_id);
        $designation->save();
        // if($designation != null) {
        //     if ($request->id) {
        //         $role = Role::where('name', $request->nom)->first();
        //         if ($role == null) {
        //             $role = Role::create(['name' => trim($request->nom)]);
        //         }
        //     } else {
        //         $role = Role::create(['name' => trim($request->nom)]);
        //     }
        // }
        $notify[] = ['success', isset($message) ? $message  : 'Le contenu a été ajouté avec succès.'];
        return back()->withNotify($notify);
    }

    public function magasinSectionIndex()
    {

        $pageTitle = "Manage des Magasins de Section";
        $manager = auth()->user();

        $activeSettingMenu = 'magasinSection_settings';

        $users = User::whereHas('roles', function ($q) {
            $q->where('name', 'Magasinier');
        })
            ->where('cooperative_id', $manager->cooperative_id)
            ->select('id', DB::raw("CONCAT(lastname,' ', firstname) as nom"))
            ->get();


        $usersId  = Arr::whereNotNull(Arr::pluck($users, 'id'));
        $sections = UserLocalite::whereIn('user_id', $usersId)->get();

        //$magasinSections     = MagasinSection::orderBy('id', 'desc')->paginate(getPaginate());
        $magasinSections = MagasinSection::joinRelationship('section')->where('cooperative_id', $manager->cooperative_id)->paginate(getPaginate());
        $codemag = $this->generecodemagasin();
        return view('manager.config.magasinSection', compact('pageTitle', 'magasinSections', 'activeSettingMenu', 'users', 'sections', 'codemag'));
    }
    public function magasinSectionStore(Request $request)
    {
        $request->validate([
            'nom'  => 'required',
            'user'  => 'required',
            'section'  => 'required',
        ]);

        if ($request->id) {
            $magasin    = MagasinSection::findOrFail($request->id);
            $message = "Le contenu a été mise à jour avec succès.";
        } else {
            $magasin = new MagasinSection();
        }
        $magasin->staff_id = trim($request->user);
        $magasin->section_id = trim($request->section);
        $magasin->nom = trim($request->nom);
        $magasin->longitude = trim($request->longitude);
        $magasin->latitude = trim($request->latitude);
        $magasin->code = isset($request->code) ? $request->code : $this->generecodemagasin();
        $magasin->save();

        $notify[] = ['success', isset($message) ? $message  : 'Le contenu a été ajouté avec succès.'];
        return back()->withNotify($notify);
    }
    private function generecodemagasin()
    {

        $data = MagasinSection::select('code')->orderby('id', 'desc')->first();

        if ($data != null) {
            $code = $data->code;
            $chaine_number = Str::afterLast($code, '-');
            if ($chaine_number < 10) {
                $zero = "00000";
            } else if ($chaine_number < 100) {
                $zero = "0000";
            } else if ($chaine_number < 1000) {
                $zero = "000";
            } else if ($chaine_number < 10000) {
                $zero = "00";
            } else if ($chaine_number < 100000) {
                $zero = "0";
            } else {
                $zero = "";
            }
        } else {
            $zero = "00000";
            $chaine_number = 0;
        }
        if (!$chaine_number) $chaine_number = 0;
        $sub = 'MAG-';
        $lastCode = $chaine_number + 1;
        $code = $sub . $zero . $lastCode;

        return $code;
    }
    private function generecodemagasincentral()
    {

        $data = MagasinCentral::select('code')->orderby('id', 'desc')->first();

        if ($data != null) {
            $code = $data->code;
            $chaine_number = intval(Str::afterLast($code, '-'));
            if ($chaine_number < 10) {
                $zero = "00000";
            } else if ($chaine_number < 100) {
                $zero = "0000";
            } else if ($chaine_number < 1000) {
                $zero = "000";
            } else if ($chaine_number < 10000) {
                $zero = "00";
            } else if ($chaine_number < 100000) {
                $zero = "0";
            } else {
                $zero = "";
            }
        } else {
            $zero = "00000";
            $chaine_number = 0;
        }
        if (!$chaine_number) $chaine_number = 0;
        $sub = 'MAG-CENTRAL-';
        $lastCode = $chaine_number + 1;
        $code = $sub . $zero . $lastCode;

        return $code;
    }
    public function magasinCentralIndex()
    {
        $pageTitle = "Manage des Magasins Centraux";
        $manager = auth()->user();
        $activeSettingMenu = 'magasinCentral_settings';
        $users = User::whereHas('roles', function ($q) {
            $q->where('name', 'Magasinier');
            $q->Orwhere('name', 'Magasinier Central');
        })
            ->where('cooperative_id', $manager->cooperative_id)
            ->select('id', DB::raw("CONCAT(lastname,' ', firstname) as nom"))
            ->get();
        $magasinCentraux = MagasinCentral::where('cooperative_id', $manager->cooperative_id)->orderBy('id', 'desc')->paginate(getPaginate());
        $codemag = $this->generecodemagasincentral();
        return view('manager.config.magasinCentral', compact('pageTitle', 'magasinCentraux', 'activeSettingMenu', 'users', 'codemag'));
    }
    public function magasinCentralStore(Request $request)
    {
        $request->validate([
            'nom'  => 'required',
            'user'  => 'required',
        ]);

        if ($request->id) {
            $magasin    = MagasinCentral::findOrFail($request->id);
            $message = "Le contenu a été mise à jour avec succès.";
        } else {
            $magasin = new MagasinCentral();
        }
        $magasin->cooperative_id = auth()->user()->cooperative_id;
        $magasin->staff_id = trim($request->user);
        $magasin->nom = trim($request->nom);
        $magasin->longitude = trim($request->longitude);
        $magasin->latitude = trim($request->latitude);
        $magasin->code = isset($request->code) ? $request->code : $this->generecodemagasincentral();
        $magasin->save();

        $notify[] = ['success', isset($message) ? $message  : 'Le contenu a été ajouté avec succès.'];
        return back()->withNotify($notify);
    }

    public function vehiculeIndex()
    {

        $pageTitle = "Manage des Véhicules";
        $manager = auth()->user();
        $activeSettingMenu = 'vehicule_settings';
        $marques = Marque::get();
        $vehicules = Vehicule::where('cooperative_id', auth()->user()->cooperative_id)->orderBy('id', 'desc')->paginate(getPaginate());

        return view('manager.config.vehicule', compact('pageTitle', 'vehicules', 'activeSettingMenu', 'marques'));
    }
    public function vehiculeStore(Request $request)
    {
        $request->validate([
            'matricule'  => 'required',
            'marque'  => 'required',
        ]);

        if ($request->id) {
            $vehicule    = Vehicule::findOrFail($request->id);
            $message = "Le contenu a été mise à jour avec succès.";
        } else {
            $vehicule = new Vehicule();
        }
        $manager = auth()->user();
        $vehicule->cooperative_id = $manager->cooperative_id;
        $vehicule->vehicule_immat = trim($request->matricule);
        //$vehicule->remorque_immat = trim($request->remorque);
        $vehicule->marque_id = $request->marque;
        $vehicule->save();

        $notify[] = ['success', isset($message) ? $message  : 'Le contenu a été ajouté avec succès.'];
        return back()->withNotify($notify);
    }


    public function remorqueIndex()
    {

        $pageTitle = "Manage des Remorques";
        $manager = auth()->user();
        $activeSettingMenu = 'remorque_settings';
        $remorques = Remorque::where('cooperative_id', auth()->user()->cooperative_id)->orderBy('id', 'desc')->paginate(getPaginate());

        return view('manager.config.remorque', compact('pageTitle', 'remorques', 'activeSettingMenu'));
    }
    public function remorqueStore(Request $request)
    {
        $request->validate([
            'matricule'  => 'required',
        ]);

        if ($request->id) {
            $remorque    = Remorque::findOrFail($request->id);
            $message = "Le contenu a été mise à jour avec succès.";
        } else {
            $remorque = new Remorque();
        }
        $manager = auth()->user();
        $remorque->cooperative_id = $manager->cooperative_id;
        $remorque->remorque_immat = trim($request->matricule);
        $remorque->save();

        $notify[] = ['success', isset($message) ? $message  : 'Le contenu a été ajouté avec succès.'];
        return back()->withNotify($notify);
    }

    public function transporteurModalIndex()
    {
        $pageTitle = "Ajouter un transporteur";
        $entreprises = Entreprise::where('cooperative_id', auth()->user()->cooperative_id)->get();
        $countries = Countrie::get();
        $niveaux = NiveauxEtude::get();
        return view('manager.config.create-transporteur-modal', compact('pageTitle', 'entreprises', 'countries', 'niveaux'));
    }
    public function transporteurIndex()
    {

        $pageTitle = "Manage des Transporteurs";
        $manager = auth()->user();
        $activeSettingMenu = 'transporteur_settings';
        $transporteurs = Transporteur::where('cooperative_id', auth()->user()->cooperative_id)->orderBy('id', 'desc')->paginate(getPaginate());
        $countries = Countrie::get();
        $niveaux = NiveauxEtude::get();
        $entreprises = Entreprise::where('cooperative_id', auth()->user()->cooperative_id)->get();
        return view('manager.config.transporteur', compact('pageTitle', 'transporteurs', 'activeSettingMenu', 'countries', 'niveaux', 'entreprises'));
    }
    public function transporteurStore(Request $request)
    {
        $request->validate([
            'entreprise_id' => 'required',
            'nom'  => 'required',
            'prenoms'  => 'required',
            'sexe'  => 'required',
            'phone1'  => 'required',
        ]);

        if ($request->id) {
            $transporteur    = Transporteur::findOrFail($request->id);
            $message = "Le contenu a été mise à jour avec succès.";
        } else {
            $transporteur = new Transporteur();
        }
        $manager = auth()->user();
        $transporteur->cooperative_id = $manager->cooperative_id;
        $transporteur->entreprise_id = trim($request->entreprise_id);
        $transporteur->nom = trim($request->nom);
        $transporteur->prenoms = trim($request->prenoms);
        $transporteur->sexe = trim($request->sexe);
        $transporteur->date_naiss = trim($request->date_naiss);
        $transporteur->phone1 = trim($request->phone1);
        $transporteur->phone2 = trim($request->phone2);
        $transporteur->nationalite = trim($request->nationalite);
        $transporteur->niveau_etude = trim($request->niveau_etude);
        $transporteur->type_piece = trim($request->type_piece);
        $transporteur->num_piece = trim($request->num_piece);
        $transporteur->num_permis = trim($request->num_permis);
        if ($request->hasFile('photo')) {
            Files::deleteFile($transporteur->photo, 'transporteur');
            $transporteur->photo = Files::uploadLocalOrS3($request->photo, 'transporteur', 600);
        }

        if ($request->hasFile('photo_piece_identite')) {
            Files::deleteFile($transporteur->photo_piece_identite, 'transporteur');
            $transporteur->photo_piece_identite = Files::uploadLocalOrS3($request->photo_piece_identite, 'transporteur', 600);
        }

        if ($request->hasFile('photo_permis_conduire')) {
            Files::deleteFile($transporteur->photo_permis_conduire, 'transporteur');
            $transporteur->photo_permis_conduire = Files::uploadLocalOrS3($request->photo_permis_conduire, 'transporteur', 600);
        }

        $transporteur->save();

        $notify[] = ['success', isset($message) ? $message  : 'Le contenu a été ajouté avec succès.'];
        return back()->withNotify($notify);
    }
    public function transporteurModalStore(Request $request)
    {
        $request->validate([
            'entreprise_id' => 'required',
            'nom'  => 'required',
            'prenoms'  => 'required',
            'sexe'  => 'required',
            'phone1'  => 'required',
        ]);

        if ($request->id) {
            $transporteur    = Transporteur::findOrFail($request->id);
            $message = "Le contenu a été mise à jour avec succès.";
        } else {
            $transporteur = new Transporteur();
        }
        $manager = auth()->user();
        $transporteur->cooperative_id = $manager->cooperative_id;
        $transporteur->entreprise_id = $request->entreprise_id;
        $transporteur->nom = trim($request->nom);
        $transporteur->prenoms = trim($request->prenoms);
        $transporteur->sexe = trim($request->sexe);
        $transporteur->date_naiss = trim($request->date_naiss);
        $transporteur->phone1 = trim($request->phone1);
        $transporteur->phone2 = trim($request->phone2);
        $transporteur->nationalite = trim($request->nationalite);
        $transporteur->niveau_etude = trim($request->niveau_etude);
        $transporteur->type_piece = trim($request->type_piece);
        $transporteur->num_piece = trim($request->num_piece);
        $transporteur->num_permis = trim($request->num_permis);
        if ($request->hasFile('photo')) {
            Files::deleteFile($transporteur->photo, 'transporteur');
            $transporteur->photo = Files::uploadLocalOrS3($request->photo, 'transporteur', 600);
        }

        if ($request->hasFile('photo_piece_identite')) {
            Files::deleteFile($transporteur->photo_piece_identite, 'transporteur');
            $transporteur->photo_piece_identite = Files::uploadLocalOrS3($request->photo_piece_identite, 'transporteur', 600);
        }

        if ($request->hasFile('photo_permis_conduire')) {
            Files::deleteFile($transporteur->photo_permis_conduire, 'transporteur');
            $transporteur->photo_permis_conduire = Files::uploadLocalOrS3($request->photo_permis_conduire, 'transporteur', 600);
        }

        $transporteur->save();

        return Reply::successWithData(__('Transporteur a été ajouté avec succès.'), ['page_reload' => $request->page_reload]);
    }
    public function campagneStatus($id)
    {
        return Campagne::changeStatus($id);
    }
    public function travauxDangereuxStatus($id)
    {
        return TravauxDangereux::changeStatus($id);
    }

    public function travauxLegersStatus($id)
    {
        return TravauxLegers::changeStatus($id);
    }
    public function arretEcoleStatus($id)
    {
        return ArretEcole::changeStatus($id);
    }
    public function typeFormationStatus($id)
    {
        return TypeFormation::changeStatus($id);
    }
    public function themeFormationStatus($id)
    {
        return ThemesFormation::changeStatus($id);
    }
    public function sousThemeFormationStatus($id)
    {
        return SousThemeFormation::changeStatus($id);
    }

    public function categorieQuestionnaireStatus($id)
    {
        return CategorieQuestionnaire::changeStatus($id);
    }

    public function questionnaireStatus($id)
    {
        return Questionnaire::changeStatus($id);
    }
    public function especeArbreStatus($id)
    {
        return Agroespecesarbre::changeStatus($id);
    }
    public function typeArchiveStatus($id)
    {
        return TypeArchive::changeStatus($id);
    }

    public function designationStatus($id)
    {
        return Designation::changeStatus($id);
    }
    public function departementStatus($id)
    {
        return Department::changeStatus($id);
    }
    public function magasinSectionStatus($id)
    {
        return MagasinSection::changeStatus($id);
    }
    public function magasinCentralStatus($id)
    {
        return MagasinCentral::changeStatus($id);
    }
    public function vehiculeStatus($id)
    {
        return Vehicule::changeStatus($id);
    }
    public function transporteurStatus($id)
    {
        return Transporteur::changeStatus($id);
    }
    public function remorqueStatus($id)
    {
        return Remorque::changeStatus($id);
    }
    public function entrepriseStatus($id)
    {
        return Entreprise::changeStatus($id);
    }
    public function formateurStaffStatus($id)
    {
        return FormateurStaff::changeStatus($id);
    }
}
