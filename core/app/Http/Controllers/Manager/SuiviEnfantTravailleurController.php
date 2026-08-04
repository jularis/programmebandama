<?php

namespace App\Http\Controllers\Manager;

use Excel;
use App\Models\ClasseEtude;
use App\Models\NiveauxEtude;
use App\Models\EnqueteMenageEnfant;
use App\Models\SuiviEnfantTravailleur;
use App\Models\SuiviEnfantTravailleurTheme;
use App\Models\SuiviEnfantTravailleurOutil;
use App\Models\SuiviEnfantTravailleurMesureEnfant;
use App\Models\SuiviEnfantTravailleurMesureMenage;
use App\Models\SuiviEnfantTravailleurMesureCommunaute;
use App\Models\SuiviEnfantTravailleurSituationPfte;
use App\Models\SuiviEnfantTravailleurRaisonPasExtrait;
use App\Models\SuiviEnfantTravailleurRaisonTravailAbus;
use App\Models\SuiviEnfantTravailleurRaisonNonScolarisation;
use App\Models\SuiviEnfantTravailleurActionRemediation;
use Illuminate\Http\Request;
use App\Exports\ExportSuiviEnfantsTravailleurs;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;

class SuiviEnfantTravailleurController extends Controller
{
    private $lienParenteEnfant = [
        "Fils/Fille du producteur/rice",
        "Nièce/Neveu du producteur/rice",
        "Fils/Fille du travailleur",
        "Nièce/Neveu du travailleur",
        "Petite-sœur/Petit-frère",
        "Autre",
    ];

    private $raisonsNeVitPasParents = [
        "Parents décédés",
        "Parent(s) ne peut/vent pas s'occuper de l'enfant",
        "Abandon",
        "Raisons scolaires",
        "Confié / donné à quelqu'un",
        "Travaille ici",
        "Est en contact avec son père et/ou sa mère",
        "Pour gagner l'argent",
        "Ne sait pas",
        "Autres",
        "N/A",
    ];

    private function optionsCommunes($manager)
    {
        $mesuresEnfant = DB::table('mesures_enfants')->pluck('nom', 'nom')->all();
        $mesuresMenage = DB::table('mesures_menages')->pluck('nom', 'nom')->all();
        $mesuresCommunaute = DB::table('mesures_communautes')->pluck('nom', 'nom')->all();
        $actionsRemediation = collect($mesuresEnfant)->merge($mesuresMenage)->merge($mesuresCommunaute)->unique()->all();

        $enfants = EnqueteMenageEnfant::whereHas('menage.producteur.localite.section', function ($q) use ($manager) {
            $q->where('cooperative_id', $manager->cooperative_id);
        })->where('status', 1)->with('menage.producteur')->orderBy('nom')->get();

        return [
            'enfants' => $enfants,
            'niveauEtude' => NiveauxEtude::get(),
            'classes' => ClasseEtude::with('niveau')->get(),
            'lienParenteEnfant' => $this->lienParenteEnfant,
            'raisonsNeVitPasParents' => $this->raisonsNeVitPasParents,
            'raisonsNonScolarisation' => DB::table('raisons_non_scolarisations')->pluck('nom', 'nom')->all(),
            'raisonsPasExtrait' => DB::table('raisons_pas_extraits')->pluck('nom', 'nom')->all(),
            'situationsPfte' => DB::table('situations_pftes')->pluck('nom', 'nom')->all(),
            'raisonsTravailAbus' => DB::table('raisons_travail_abus')->pluck('nom', 'nom')->all(),
            'mesuresEnfant' => $mesuresEnfant,
            'mesuresMenage' => $mesuresMenage,
            'mesuresCommunaute' => $mesuresCommunaute,
            'actionsRemediation' => $actionsRemediation,
            'sensibilisationThemes' => DB::table('sensibilisation_themes')->pluck('nom', 'nom')->all(),
            'sensibilisationOutils' => DB::table('sensibilisation_outils')->pluck('nom', 'nom')->all(),
        ];
    }

    public function index()
    {
        $pageTitle = "Gestion du suivi des enfants travailleurs";
        $manager = auth()->user();

        $suivis = SuiviEnfantTravailleur::searchable(['nomEnqueteur'])
            ->latest('id')
            ->whereHas('enfant.menage.producteur.localite.section', function ($q) use ($manager) {
                $q->where('cooperative_id', $manager->cooperative_id);
            })
            ->with('enfant')
            ->paginate(getPaginate());

        return view('manager.suivienfanttravailleur.index', compact('pageTitle', 'suivis'));
    }

    public function create()
    {
        $pageTitle = "Ajouter un suivi enfant travailleur";
        $manager = auth()->user();
        $options = $this->optionsCommunes($manager);

        return view('manager.suivienfanttravailleur.create', array_merge(compact('pageTitle'), $options));
    }

    public function edit($id)
    {
        $pageTitle = "Mise à jour du suivi enfant travailleur";
        $manager = auth()->user();
        $options = $this->optionsCommunes($manager);
        $suivi = SuiviEnfantTravailleur::with([
            'enfant',
            'actionsRemediation',
            'raisonsNonScolarisation',
            'raisonsPasExtrait',
            'situationsPfte',
            'raisonsTravailAbus',
            'mesuresEnfant',
            'mesuresMenage',
            'mesuresCommunaute',
            'themes',
            'outils',
        ])->findOrFail($id);

        return view('manager.suivienfanttravailleur.edit', array_merge(compact('pageTitle', 'suivi'), $options));
    }

    public function show($id)
    {
        $pageTitle = "Détails du suivi enfant travailleur";
        $manager = auth()->user();
        $options = $this->optionsCommunes($manager);
        $suivi = SuiviEnfantTravailleur::with([
            'enfant',
            'actionsRemediation',
            'raisonsNonScolarisation',
            'raisonsPasExtrait',
            'situationsPfte',
            'raisonsTravailAbus',
            'mesuresEnfant',
            'mesuresMenage',
            'mesuresCommunaute',
            'themes',
            'outils',
        ])->findOrFail($id);

        return view('manager.suivienfanttravailleur.show', array_merge(compact('pageTitle', 'suivi'), $options));
    }

    public function store(Request $request)
    {
        $isUpdate = (bool) $request->id;

        $rules = [
            'enfant' => 'required|exists:enquete_menage_enfants,id',
            'dateEnquete' => 'required|date',
            'nomEnqueteur' => 'required|max:150',
            'nom' => 'required|max:150',
            'dateNaissance' => 'required|date',
            'sexe' => 'required|in:M,F',
            'lienParente' => 'required',
            'autreLienParente' => 'required_if:lienParente,Autre|max:255',
            'raisonNeVitPasParents' => 'required|max:255',
            'autreRaisonNeVitPasParents' => 'required_if:raisonNeVitPasParents,Autres|max:255',
            'situationScolaire' => 'required|in:Scolarisé,Déscolarisé,Jamais scolarisé',
            'niveauScolaire' => 'required_if:situationScolaire,Scolarisé,Déscolarisé|max:255',
            'raisonNonScolarisation' => 'required_if:situationScolaire,Déscolarisé,Jamais scolarisé|array|min:1',
            'raisonNonScolarisation.*' => 'string|max:255',
            'autreRaisonNonScolarisation' => 'required_if:raisonNonScolarisation,Autre|max:255',
            'extraitNaissance' => 'required|in:Oui,Non',
            'raisonPasExtrait' => 'required_if:extraitNaissance,Non|array|min:1',
            'raisonPasExtrait.*' => 'string|max:255',
            'presentDisponible' => 'required|in:0,1',
            'raisonAbsent' => 'required_if:presentDisponible,0|max:150',
            'heuresTravailSemaine' => 'required|integer|min:0|max:168',
            'joursTravail' => 'required|in:0,1,2,3,4,5,6,7',
            'heuresTravailJournee' => 'required|integer|min:0|max:24',
            'situationsPfte' => 'required|array|min:1',
            'situationsPfte.*' => 'string|max:255',
            'raisonTravailAbus' => 'nullable|array|min:1',
            'raisonTravailAbus.*' => 'string|max:255',
            'mesuresEnfant' => 'required|array|min:1',
            'mesuresEnfant.*' => 'string|max:255',
            'mesuresMenage' => 'required|array|min:1',
            'mesuresMenage.*' => 'string|max:255',
            'mesuresCommunaute' => 'required|array|min:1',
            'mesuresCommunaute.*' => 'string|max:255',
            'autreMesure' => 'required|max:255',
            'themesSensibilisation' => 'required|array|min:1',
            'themesSensibilisation.*' => 'string|max:255',
            'autreThemeSensibilisation' => 'required_if:themesSensibilisation,Autres thèmes|max:255',
            'outilsSensibilisation' => 'required|array|min:1',
            'outilsSensibilisation.*' => 'string|max:255',
            'nombreHommesSensibilises' => 'required|integer|min:0',
            'nombreFemmesSensibilisees' => 'required|integer|min:0',
            'nombreGarconsSensibilises' => 'required|integer|min:0',
            'nombreFillesSensibilisees' => 'required|integer|min:0',
            'telephoneProducteurSensibilisation' => 'required|max:50',
            'photoSensibilisation' => $isUpdate ? 'nullable|image' : 'required|image',
        ];

        $validator = Validator::make($request->all(), $rules);

        $validator->after(function ($validator) use ($request) {
            $situations = $request->input('situationsPfte', []);
            if (is_array($situations) && ! (count($situations) === 1 && $situations[0] === 'Aucune')) {
                if (empty($request->input('raisonTravailAbus'))) {
                    $validator->errors()->add('raisonTravailAbus', 'Le champ raisonTravailAbus est obligatoire.');
                }
            }

            if (is_array($request->input('raisonNonScolarisation', [])) && in_array('Autre', $request->input('raisonNonScolarisation', [])) && ! $request->filled('autreRaisonNonScolarisation')) {
                $validator->errors()->add('autreRaisonNonScolarisation', 'Le champ autreRaisonNonScolarisation est obligatoire.');
            }

            if (is_array($request->input('raisonTravailAbus', [])) && in_array('Autre', $request->input('raisonTravailAbus', [])) && ! $request->filled('autreRaisonTravailAbus')) {
                $validator->errors()->add('autreRaisonTravailAbus', 'Le champ autreRaisonTravailAbus est obligatoire.');
            }

            $mesures = array_merge(
                $request->input('mesuresEnfant', []),
                $request->input('mesuresMenage', []),
                $request->input('mesuresCommunaute', [])
            );
            if (collect($mesures)->contains(function ($val) {
                return str_contains($val, 'Autre') || str_contains($val, 'préciser');
            }) && ! $request->filled('autreMesure')) {
                $validator->errors()->add('autreMesure', 'Le champ autreMesure est obligatoire.');
            }

            if (collect($request->input('themesSensibilisation', []))->contains('Autres thèmes') && ! $request->filled('autreThemeSensibilisation')) {
                $validator->errors()->add('autreThemeSensibilisation', 'Le champ autreThemeSensibilisation est obligatoire.');
            }
        });

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $suivi = $isUpdate ? SuiviEnfantTravailleur::findOrFail($request->id) : new SuiviEnfantTravailleur();

        $suivi->enfant_id = $request->enfant;
        $suivi->raisonInterview = 'Visite de suivi';
        if ($isUpdate) {
            $suivi->dateEnquete = $suivi->dateEnquete;
        } else {
            $suivi->dateEnquete = now()->format('Y-m-d');
        }
        $suivi->nomEnqueteur = trim((auth()->user()->lastname ?? '') . ' ' . (auth()->user()->firstname ?? '')) ?: auth()->user()->username;

        $suivi->nom = $request->nom;
        $suivi->dateNaissance = $request->dateNaissance;
        $suivi->sexe = $request->sexe;
        $suivi->lienParente = $request->lienParente;
        $suivi->autreLienParente = $request->autreLienParente;
        $suivi->raisonNeVitPasParents = $request->raisonNeVitPasParents;
        $suivi->autreRaisonNeVitPasParents = $request->autreRaisonNeVitPasParents;
        $suivi->situationScolaire = $request->situationScolaire;
        $suivi->niveauScolaire = $request->niveauScolaire;
        $suivi->autreRaisonNonScolarisation = $request->autreRaisonNonScolarisation;
        $suivi->extraitNaissance = $request->extraitNaissance;
        $suivi->presentDisponible = $request->presentDisponible;
        $suivi->raisonAbsent = $request->presentDisponible === '0' ? $request->raisonAbsent : null;
        $suivi->heuresTravailSemaine = $request->heuresTravailSemaine;
        $suivi->joursTravail = $request->joursTravail;
        $suivi->heuresTravailJournee = $request->heuresTravailJournee;
        $suivi->autreRaisonTravailAbus = $request->autreRaisonTravailAbus;
        $suivi->autreMesure = $request->autreMesure;

        $suivi->autreThemeSensibilisation = $request->autreThemeSensibilisation;
        $suivi->nombreHommesSensibilises = $request->nombreHommesSensibilises;
        $suivi->nombreFemmesSensibilisees = $request->nombreFemmesSensibilisees;
        $suivi->nombreGarconsSensibilises = $request->nombreGarconsSensibilises;
        $suivi->nombreFillesSensibilisees = $request->nombreFillesSensibilisees;
        $suivi->totalPersonnesSensibilisees = (int) $request->nombreHommesSensibilises + (int) $request->nombreFemmesSensibilisees + (int) $request->nombreGarconsSensibilises + (int) $request->nombreFillesSensibilisees;
        $suivi->telephoneProducteurSensibilisation = $request->telephoneProducteurSensibilisation;

        if ($request->hasFile('photoSensibilisation')) {
            try {
                $suivi->photoSensibilisation = $request->file('photoSensibilisation')->store('public/suivienfanttravailleur/photos');
            } catch (\Exception $exp) {
                $notify[] = ['error', "Impossible de télécharger la photo de sensibilisation"];
                return back()->withNotify($notify)->withInput();
            }
        }

        $suivi->etatSoumission = $request->etatSoumission == 'Brouillon' ? 'Brouillon' : 'Soumis';
        $suivi->userid = auth()->id();
        $suivi->save();

        $id = $suivi->id;

        SuiviEnfantTravailleurActionRemediation::where('suivi_id', $id)->delete();
        if ($request->actionsRemediation != null) {
            $rows = [];
            foreach ($request->actionsRemediation as $valeur) {
                $rows[] = ['suivi_id' => $id, 'valeur' => $valeur];
            }
            SuiviEnfantTravailleurActionRemediation::insert($rows);
        }

        SuiviEnfantTravailleurRaisonNonScolarisation::where('suivi_id', $id)->delete();
        if ($request->raisonNonScolarisation != null) {
            $rows = [];
            foreach ($request->raisonNonScolarisation as $valeur) {
                $rows[] = ['suivi_id' => $id, 'valeur' => $valeur];
            }
            SuiviEnfantTravailleurRaisonNonScolarisation::insert($rows);
        }

        SuiviEnfantTravailleurRaisonPasExtrait::where('suivi_id', $id)->delete();
        if ($request->raisonPasExtrait != null) {
            $rows = [];
            foreach ($request->raisonPasExtrait as $valeur) {
                $rows[] = ['suivi_id' => $id, 'valeur' => $valeur];
            }
            SuiviEnfantTravailleurRaisonPasExtrait::insert($rows);
        }

        SuiviEnfantTravailleurSituationPfte::where('suivi_id', $id)->delete();
        if ($request->situationsPfte != null) {
            $rows = [];
            foreach ($request->situationsPfte as $valeur) {
                $rows[] = ['suivi_id' => $id, 'valeur' => $valeur];
            }
            SuiviEnfantTravailleurSituationPfte::insert($rows);
        }

        SuiviEnfantTravailleurRaisonTravailAbus::where('suivi_id', $id)->delete();
        if ($request->raisonTravailAbus != null) {
            $rows = [];
            foreach ($request->raisonTravailAbus as $valeur) {
                $rows[] = ['suivi_id' => $id, 'valeur' => $valeur];
            }
            SuiviEnfantTravailleurRaisonTravailAbus::insert($rows);
        }

        SuiviEnfantTravailleurMesureEnfant::where('suivi_id', $id)->delete();
        if ($request->mesuresEnfant != null) {
            $rows = [];
            foreach ($request->mesuresEnfant as $valeur) {
                $rows[] = ['suivi_id' => $id, 'valeur' => $valeur];
            }
            SuiviEnfantTravailleurMesureEnfant::insert($rows);
        }

        SuiviEnfantTravailleurMesureMenage::where('suivi_id', $id)->delete();
        if ($request->mesuresMenage != null) {
            $rows = [];
            foreach ($request->mesuresMenage as $valeur) {
                $rows[] = ['suivi_id' => $id, 'valeur' => $valeur];
            }
            SuiviEnfantTravailleurMesureMenage::insert($rows);
        }

        SuiviEnfantTravailleurMesureCommunaute::where('suivi_id', $id)->delete();
        if ($request->mesuresCommunaute != null) {
            $rows = [];
            foreach ($request->mesuresCommunaute as $valeur) {
                $rows[] = ['suivi_id' => $id, 'valeur' => $valeur];
            }
            SuiviEnfantTravailleurMesureCommunaute::insert($rows);
        }

        SuiviEnfantTravailleurTheme::where('suivi_id', $id)->delete();
        if ($request->themesSensibilisation != null) {
            $rows = [];
            foreach ($request->themesSensibilisation as $valeur) {
                $rows[] = ['suivi_id' => $id, 'valeur' => $valeur];
            }
            SuiviEnfantTravailleurTheme::insert($rows);
        }

        SuiviEnfantTravailleurOutil::where('suivi_id', $id)->delete();
        if ($request->outilsSensibilisation != null) {
            $rows = [];
            foreach ($request->outilsSensibilisation as $valeur) {
                $rows[] = ['suivi_id' => $id, 'valeur' => $valeur];
            }
            SuiviEnfantTravailleurOutil::insert($rows);
        }

        $notify[] = ['success', $isUpdate ? "Le suivi a été mis à jour avec succès" : "Le suivi a été créé avec succès"];
        return redirect()->route('manager.suivi.enfanttravailleur.index')->withNotify($notify);
    }

    public function status($id)
    {
        return SuiviEnfantTravailleur::changeStatus($id);
    }

    public function exportExcel()
    {
        $filename = 'suivi-enfant-travailleur-' . gmdate('dmYhms') . '.xlsx';
        return Excel::download(new ExportSuiviEnfantsTravailleurs, $filename);
    }

    public function delete($id)
    {
        SuiviEnfantTravailleur::where('id', decrypt($id))->delete();
        $notify[] = ['success', 'Le contenu supprimé avec succès'];
        return back()->withNotify($notify);
    }
}
