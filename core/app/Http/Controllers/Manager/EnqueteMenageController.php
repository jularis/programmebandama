<?php

namespace App\Http\Controllers\Manager;

use Excel;
use App\Models\Localite;
use App\Models\Producteur;
use App\Models\Cooperative;
use App\Models\ClasseEtude;
use App\Models\NiveauxEtude;
use App\Models\EnqueteMenage;
use App\Models\EnqueteMenageEnfant;
use App\Models\EnqueteMenageTheme;
use App\Models\EnqueteMenageOutil;
use App\Models\EnqueteMenageRaisonRefus;
use App\Models\EnqueteMenageEnfantMesureEnfant;
use App\Models\EnqueteMenageEnfantMesureMenage;
use App\Models\EnqueteMenageEnfantMesureCommunaute;
use App\Models\EnqueteMenageEnfantSituationPfte;
use App\Models\EnqueteMenageEnfantRaisonPasExtrait;
use App\Models\EnqueteMenageEnfantRaisonTravailAbus;
use App\Models\EnqueteMenageEnfantRaisonNonScolarisation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Exports\ExportEnqueteMenages;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;

class EnqueteMenageController extends Controller
{
    private $titresRepondant = [
        "Gérant(e)_fils/fille",
        "Gérant(e)_conjoint(e)",
        "Gérant(e)_frère/sœur",
        "Gérant(e)_neveu/nièce",
        "Gérant(e)_cousin/cousine",
        "Gérant(e)_employé(e)",
    ];

    private $raisonsIndisponibilite = [
        "Temporairement absent",
        "Refus",
        "N'est plus résident dans la localité",
        "N'est plus membre de la coopérative",
        "Décédé(e)",
    ];

    private $raisonsRefus = [
        "Promesse de la coopérative non tenue",
        "Prime non payée",
        "Aucune réponse du producteur/rice",
        "Autre raison",
    ];

    private $situationsMatrimoniales = [
        "Célibataire",
        "Vit en couple",
        "Divorcé / Séparé",
        "Veuf(ve)",
    ];

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

    private function nomEnqueteurConnecte($user)
    {
        $nomComplet = trim(($user->lastname ?? '') . ' ' . ($user->firstname ?? ''));

        return $nomComplet ?: ($user->fullname ?? $user->username);
    }

    private function optionsCommunes($manager)
    {
        $cooperative = Cooperative::with('sections.localites', 'sections.localites.section')->find($manager->cooperative_id);
        $sections = $cooperative->sections;
        $localites = $cooperative->sections->flatMap->localites->where('status', 1)->values();
        $producteurs = Producteur::joinRelationship('localite.section')
            ->where([['cooperative_id', $manager->cooperative_id], ['producteurs.status', 1]])
            ->with('localite')->get();

        return [
            'sections' => $sections,
            'localites' => $localites,
            'producteurs' => $producteurs,
            'niveauEtude' => NiveauxEtude::get(),
            'classes' => ClasseEtude::with('niveau')->get(),
            'titresRepondant' => $this->titresRepondant,
            'raisonsIndisponibilite' => $this->raisonsIndisponibilite,
            'raisonsRefus' => $this->raisonsRefus,
            'situationsMatrimoniales' => $this->situationsMatrimoniales,
            'lienParenteEnfant' => $this->lienParenteEnfant,
            'raisonsNeVitPasParents' => $this->raisonsNeVitPasParents,
            'raisonsNonScolarisation' => DB::table('raisons_non_scolarisations')->pluck('nom', 'nom')->all(),
            'raisonsPasExtrait' => DB::table('raisons_pas_extraits')->pluck('nom', 'nom')->all(),
            'situationsPfte' => DB::table('situations_pftes')->pluck('nom', 'nom')->all(),
            'raisonsTravailAbus' => DB::table('raisons_travail_abus')->pluck('nom', 'nom')->all(),
            'mesuresEnfant' => DB::table('mesures_enfants')->pluck('nom', 'nom')->all(),
            'mesuresMenage' => DB::table('mesures_menages')->pluck('nom', 'nom')->all(),
            'mesuresCommunaute' => DB::table('mesures_communautes')->pluck('nom', 'nom')->all(),
            'sensibilisationThemes' => DB::table('sensibilisation_themes')->pluck('nom', 'nom')->all(),
            'sensibilisationOutils' => DB::table('sensibilisation_outils')->pluck('nom', 'nom')->all(),
        ];
    }

    public function index()
    {
        $pageTitle = "Gestion des enquêtes ménage";
        $manager = auth()->user();
        $localites = Localite::joinRelationship('section')->where([['cooperative_id', $manager->cooperative_id], ['localites.status', 1]])->get();

        $enqueteMenages = EnqueteMenage::searchable(['nomEnqueteur', 'statutFin'])
            ->latest('id')
            ->joinRelationship('producteur.localite.section')
            ->where('sections.cooperative_id', $manager->cooperative_id)
            ->where(function ($q) {
                if (request()->localite != null) {
                    $q->where('localite_id', request()->localite);
                }
            })
            ->with(['producteur', 'localite', 'enfants'])
            ->paginate(getPaginate());

        return view('manager.enquetemenage.index', compact('pageTitle', 'enqueteMenages', 'localites'));
    }

    public function create()
    {
        $pageTitle = "Ajouter une enquête ménage";
        $manager = auth()->user();
        $options = $this->optionsCommunes($manager);
        $nomEnqueteurConnecte = $this->nomEnqueteurConnecte($manager);

        return view('manager.enquetemenage.create', array_merge(compact('pageTitle', 'nomEnqueteurConnecte'), $options));
    }

    public function edit($id)
    {
        $pageTitle = "Mise à jour de l'enquête ménage";
        $manager = auth()->user();
        $options = $this->optionsCommunes($manager);
        $nomEnqueteurConnecte = $this->nomEnqueteurConnecte($manager);
        $enqueteMenage = EnqueteMenage::with([
            'enfants.raisonsNonScolarisation',
            'enfants.raisonsPasExtrait',
            'enfants.situationsPfte',
            'enfants.raisonsTravailAbus',
            'enfants.mesuresEnfant',
            'enfants.mesuresMenage',
            'enfants.mesuresCommunaute',
            'raisonsRefus',
            'themes',
            'outils',
        ])->findOrFail($id);

        return view('manager.enquetemenage.edit', array_merge(compact('pageTitle', 'enqueteMenage', 'nomEnqueteurConnecte'), $options));
    }

    public function show($id)
    {
        $pageTitle = "Détails de l'enquête ménage";
        $manager = auth()->user();
        $options = $this->optionsCommunes($manager);
        $enqueteMenage = EnqueteMenage::with([
            'enfants.raisonsNonScolarisation',
            'enfants.raisonsPasExtrait',
            'enfants.situationsPfte',
            'enfants.raisonsTravailAbus',
            'enfants.mesuresEnfant',
            'enfants.mesuresMenage',
            'enfants.mesuresCommunaute',
            'raisonsRefus',
            'themes',
            'outils',
        ])->findOrFail($id);

        return view('manager.enquetemenage.show', array_merge(compact('pageTitle', 'enqueteMenage'), $options));
    }

    public function store(Request $request)
    {
        $localite = Localite::where('id', $request->localite)->first();
        if (!$localite || $localite->status == 0) {
            $notify[] = ['error', 'Cette localité est désactivée'];
            return back()->withNotify($notify)->withInput();
        }

        $rules = [
            'section' => 'required|exists:sections,id',
            'localite' => 'required|exists:localites,id',
            'producteur' => 'required|exists:producteurs,id',
            'dateEnquete' => 'required|date',
            'nombreEnfantsEnquetes' => 'required|integer|min:0',
            'latitude' => 'required|string|max:50',
            'longitude' => 'required|string|max:50',
            'altitude' => 'nullable|string|max:50',
            'precisionGps' => 'nullable|string|max:50',
            'estProducteurRepondant' => 'required|in:Oui,Non',
            'nomRepondant' => 'required_if:estProducteurRepondant,Non|nullable|max:150',
            'titreRepondant' => 'required_if:estProducteurRepondant,Non|nullable|max:100',
            'producteurDisponible' => 'required|in:Oui,Non',
            'raisonIndisponibilite' => 'required_if:producteurDisponible,Non|nullable|max:150',
            'datePlanification' => 'required_if:raisonIndisponibilite,Temporairement absent|nullable|date',
            'raisonRefus' => 'required_if:raisonIndisponibilite,Refus|array',
            'raisonRefus.*' => 'required_with:raisonRefus|string|max:150',
            'autreRaisonRefus' => 'nullable|max:150',
            'consentement' => 'required_if:producteurDisponible,Oui|in:Oui,Non',
            'situationMatrimoniale' => 'required_if:producteurDisponible,Oui|string|max:100',
            'nombreAdultes' => 'required|integer|min:0',
            'nombreEnfants0a4' => 'required|integer|min:0',
            'nombreEnfants5a17' => 'required|integer|min:0',
            'aEnfantACharge' => 'required|in:Oui,Non',
            'nombreEnfantsACharge' => 'required_if:aEnfantACharge,Oui|nullable|integer|min:0',
            'enfants' => 'nullable|array',
            'enfants.*.nom' => 'required_with:enfants|string|max:150',
            'enfants.*.dateNaissance' => 'required_with:enfants|date',
            'enfants.*.sexe' => 'required_with:enfants|in:M,F',
            'enfants.*.lienParente' => 'required_with:enfants|string|max:150',
            'enfants.*.autreLienParente' => 'nullable|string|max:150',
            'enfants.*.raisonNeVitPasParents' => 'required_with:enfants|string|max:150',
            'enfants.*.autreRaisonNeVitPasParents' => 'nullable|string|max:150',
            'enfants.*.situationScolaire' => 'required_with:enfants|in:Scolarisé,Déscolarisé,Jamais scolarisé',
            'enfants.*.niveauScolaire' => 'nullable|string|max:150',
            'enfants.*.raisonNonScolarisation' => 'nullable|array',
            'enfants.*.raisonNonScolarisation.*' => 'nullable|string|max:150',
            'enfants.*.autreRaisonNonScolarisation' => 'nullable|string|max:150',
            'enfants.*.extraitNaissance' => 'required_with:enfants|in:Oui,Non',
            'enfants.*.raisonPasExtrait' => 'nullable|array',
            'enfants.*.raisonPasExtrait.*' => 'nullable|string|max:150',
            'enfants.*.situationsPfte' => 'nullable|array',
            'enfants.*.situationsPfte.*' => 'nullable|string|max:150',
            'enfants.*.raisonTravailAbus' => 'nullable|array',
            'enfants.*.raisonTravailAbus.*' => 'nullable|string|max:150',
            'enfants.*.mesuresEnfant' => 'nullable|array',
            'enfants.*.mesuresEnfant.*' => 'nullable|string|max:150',
            'enfants.*.mesuresMenage' => 'nullable|array',
            'enfants.*.mesuresMenage.*' => 'nullable|string|max:150',
            'enfants.*.mesuresCommunaute' => 'nullable|array',
            'enfants.*.mesuresCommunaute.*' => 'nullable|string|max:150',
            'enfants.*.autreMesure' => 'nullable|string|max:150',
            'themesSensibilisation' => 'required_if:aEnfantACharge,Oui|array',
            'themesSensibilisation.*' => 'required_with:themesSensibilisation|string|max:150',
            'autreThemeSensibilisation' => 'nullable|string|max:150',
            'outilsSensibilisation' => 'required_if:aEnfantACharge,Oui|array',
            'outilsSensibilisation.*' => 'required_with:outilsSensibilisation|string|max:150',
            'nombreHommesSensibilises' => 'required_if:aEnfantACharge,Oui|integer|min:0',
            'nombreFemmesSensibilisees' => 'required_if:aEnfantACharge,Oui|integer|min:0',
            'nombreGarconsSensibilises' => 'required_if:aEnfantACharge,Oui|integer|min:0',
            'nombreFillesSensibilisees' => 'required_if:aEnfantACharge,Oui|integer|min:0',
            'telephoneProducteurSensibilisation' => 'required_if:aEnfantACharge,Oui|string|max:30',
            'photoSensibilisation' => 'required_if:aEnfantACharge,Oui|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ];

        $validator = Validator::make($request->all(), $rules);
        $validator->after(function ($validator) use ($request) {
            if ($request->producteurDisponible === 'Non' && is_array($request->raisonRefus) && in_array('Autre raison', $request->raisonRefus) && empty(trim($request->autreRaisonRefus ?? ''))) {
                $validator->errors()->add('autreRaisonRefus', 'Veuillez préciser l\'autre raison de refus.');
            }

            $enfants = $request->input('enfants', []);
            if ($request->aEnfantACharge === 'Oui' && empty($enfants)) {
                $validator->errors()->add('enfants', 'Le champ enfants est requis lorsque a enfant a charge est Oui.');
            }

            foreach ($enfants as $index => $enfant) {
                if (isset($enfant['lienParente']) && $enfant['lienParente'] === 'Autre' && empty(trim($enfant['autreLienParente'] ?? ''))) {
                    $validator->errors()->add("enfants.$index.autreLienParente", 'Veuillez préciser l\'autre lien de parenté.');
                }

                if (isset($enfant['raisonNeVitPasParents']) && $enfant['raisonNeVitPasParents'] === 'Autres' && empty(trim($enfant['autreRaisonNeVitPasParents'] ?? ''))) {
                    $validator->errors()->add("enfants.$index.autreRaisonNeVitPasParents", 'Veuillez préciser l\'autre raison pour laquelle l\'enfant ne vit pas avec ses parents.');
                }

                if (in_array($enfant['situationScolaire'] ?? '', ['Scolarisé', 'Déscolarisé']) && empty(trim($enfant['niveauScolaire'] ?? ''))) {
                    $validator->errors()->add("enfants.$index.niveauScolaire", 'Le niveau scolaire est requis pour cet enfant.');
                }

                if (in_array($enfant['situationScolaire'] ?? '', ['Déscolarisé', 'Jamais scolarisé'])) {
                    if (empty($enfant['raisonNonScolarisation']) || !is_array($enfant['raisonNonScolarisation']) || count(array_filter($enfant['raisonNonScolarisation'])) === 0) {
                        $validator->errors()->add("enfants.$index.raisonNonScolarisation", 'Veuillez préciser pourquoi l\'enfant n\'est pas scolarisé ou est déscolarisé.');
                    }
                    if (is_array($enfant['raisonNonScolarisation']) && in_array('Autre', $enfant['raisonNonScolarisation']) && empty(trim($enfant['autreRaisonNonScolarisation'] ?? ''))) {
                        $validator->errors()->add("enfants.$index.autreRaisonNonScolarisation", 'Veuillez préciser l\'autre raison de non-scolarisation.');
                    }
                }

                if (($enfant['extraitNaissance'] ?? '') === 'Non' && (empty($enfant['raisonPasExtrait']) || !is_array($enfant['raisonPasExtrait']) || count(array_filter($enfant['raisonPasExtrait'])) === 0)) {
                    $validator->errors()->add("enfants.$index.raisonPasExtrait", 'Veuillez indiquer la ou les raisons pour lesquelles l\'enfant n\'a pas d\'extrait de naissance.');
                }

                if (empty($enfant['situationsPfte']) || !is_array($enfant['situationsPfte']) || count(array_filter($enfant['situationsPfte'])) === 0) {
                    $validator->errors()->add("enfants.$index.situationsPfte", 'Veuillez sélectionner au moins une situation PFTE pour cet enfant.');
                }

                if (empty($enfant['raisonTravailAbus']) || !is_array($enfant['raisonTravailAbus']) || count(array_filter($enfant['raisonTravailAbus'])) === 0) {
                    $validator->errors()->add("enfants.$index.raisonTravailAbus", 'Veuillez sélectionner au moins une raison de travail abusif pour cet enfant.');
                }

                if (empty($enfant['mesuresEnfant']) || !is_array($enfant['mesuresEnfant']) || count(array_filter($enfant['mesuresEnfant'])) === 0) {
                    $validator->errors()->add("enfants.$index.mesuresEnfant", 'Veuillez sélectionner au moins une mesure au niveau de l\'enfant.');
                }

                if (empty($enfant['mesuresMenage']) || !is_array($enfant['mesuresMenage']) || count(array_filter($enfant['mesuresMenage'])) === 0) {
                    $validator->errors()->add("enfants.$index.mesuresMenage", 'Veuillez sélectionner au moins une mesure au niveau du ménage.');
                }

                if (empty($enfant['mesuresCommunaute']) || !is_array($enfant['mesuresCommunaute']) || count(array_filter($enfant['mesuresCommunaute'])) === 0) {
                    $validator->errors()->add("enfants.$index.mesuresCommunaute", 'Veuillez sélectionner au moins une mesure au niveau de la communauté.');
                }
            }

            if (is_array($request->themesSensibilisation) && in_array('Autres thèmes', $request->themesSensibilisation) && empty(trim($request->autreThemeSensibilisation ?? ''))) {
                $validator->errors()->add('autreThemeSensibilisation', 'Veuillez préciser l\'autre thème de sensibilisation.');
            }
        });

        $validator->validate();

        $isUpdate = (bool) $request->id;
        $enqueteMenage = $isUpdate ? EnqueteMenage::findOrFail($request->id) : new EnqueteMenage();
        $producteur = Producteur::find($request->producteur);

        $enqueteMenage->raisonInterview = 'Enquête initiale';
        $enqueteMenage->typeEnquete = 'Menage';

        $enqueteMenage->section_id = $request->section;
        $enqueteMenage->localite_id = $request->localite;
        $enqueteMenage->producteur_id = $request->producteur;
        $enqueteMenage->sexeProducteur = $producteur->sexe;
        $enqueteMenage->codeProducteur = $producteur->codeProdapp;

        $enqueteMenage->dateEnquete = $isUpdate ? $enqueteMenage->dateEnquete : Carbon::now()->toDateString();
        $enqueteMenage->nomEnqueteur = $this->nomEnqueteurConnecte(auth()->user());
        $enqueteMenage->nombreEnfantsEnquetes = $request->nombreEnfantsEnquetes;

        $enqueteMenage->latitude = $request->latitude;
        $enqueteMenage->longitude = $request->longitude;
        $enqueteMenage->altitude = $request->altitude;
        $enqueteMenage->precisionGps = $request->precisionGps;

        $enqueteMenage->estProducteurRepondant = $request->estProducteurRepondant;
        $enqueteMenage->nomRepondant = $request->nomRepondant;
        $enqueteMenage->titreRepondant = $request->titreRepondant;

        $enqueteMenage->producteurDisponible = $request->producteurDisponible;
        $enqueteMenage->raisonIndisponibilite = $request->producteurDisponible == 'Oui' ? 'N/A' : $request->raisonIndisponibilite;
        $enqueteMenage->datePlanification = $request->datePlanification;
        $enqueteMenage->autreRaisonRefus = $request->autreRaisonRefus;
        $enqueteMenage->consentement = $request->consentement;

        $enqueteMenage->situationMatrimoniale = $request->situationMatrimoniale;
        $enqueteMenage->nombreAdultes = $request->nombreAdultes;
        $enqueteMenage->nombreEnfants0a4 = $request->nombreEnfants0a4;
        $enqueteMenage->nombreEnfants5a17 = $request->nombreEnfants5a17;
        $enqueteMenage->totalPersonnesMenage = (int) $request->nombreAdultes + (int) $request->nombreEnfants0a4 + (int) $request->nombreEnfants5a17;

        $enqueteMenage->aEnfantACharge = $request->aEnfantACharge;
        $enqueteMenage->nombreEnfantsACharge = $request->nombreEnfantsACharge;

        $enqueteMenage->autreThemeSensibilisation = $request->autreThemeSensibilisation;
        $enqueteMenage->nombreHommesSensibilises = $request->nombreHommesSensibilises;
        $enqueteMenage->nombreFemmesSensibilisees = $request->nombreFemmesSensibilisees;
        $enqueteMenage->nombreGarconsSensibilises = $request->nombreGarconsSensibilises;
        $enqueteMenage->nombreFillesSensibilisees = $request->nombreFillesSensibilisees;
        $enqueteMenage->totalPersonnesSensibilisees = (int) $request->nombreHommesSensibilises + (int) $request->nombreFemmesSensibilisees + (int) $request->nombreGarconsSensibilises + (int) $request->nombreFillesSensibilisees;
        $enqueteMenage->telephoneProducteurSensibilisation = $request->telephoneProducteurSensibilisation;

        if ($request->hasFile('photoSensibilisation')) {
            try {
                $enqueteMenage->photoSensibilisation = $request->file('photoSensibilisation')->store('public/enquetemenages/photos');
            } catch (\Exception $exp) {
                $notify[] = ['error', "Impossible de télécharger la photo de sensibilisation"];
                return back()->withNotify($notify)->withInput();
            }
        }

        $enqueteMenage->etatSoumission = $request->etatSoumission == 'Brouillon' ? 'Brouillon' : 'Soumis';
        $enqueteMenage->statutFin = $this->determinerStatutFin($request);

        $enqueteMenage->userid = auth()->id();
        $enqueteMenage->save();

        $id = $enqueteMenage->id;

        EnqueteMenageRaisonRefus::where('enquete_menage_id', $id)->delete();
        if ($request->raisonRefus != null) {
            $rows = [];
            foreach ($request->raisonRefus as $valeur) {
                $rows[] = ['enquete_menage_id' => $id, 'valeur' => $valeur];
            }
            EnqueteMenageRaisonRefus::insert($rows);
        }

        EnqueteMenageTheme::where('enquete_menage_id', $id)->delete();
        if ($request->themesSensibilisation != null) {
            $rows = [];
            foreach ($request->themesSensibilisation as $valeur) {
                $rows[] = ['enquete_menage_id' => $id, 'valeur' => $valeur];
            }
            EnqueteMenageTheme::insert($rows);
        }

        EnqueteMenageOutil::where('enquete_menage_id', $id)->delete();
        if ($request->outilsSensibilisation != null) {
            $rows = [];
            foreach ($request->outilsSensibilisation as $valeur) {
                $rows[] = ['enquete_menage_id' => $id, 'valeur' => $valeur];
            }
            EnqueteMenageOutil::insert($rows);
        }

        EnqueteMenageEnfant::where('enquete_menage_id', $id)->delete();
        if ($request->enfants != null) {
            foreach ($request->enfants as $index => $data) {
                $enfant = new EnqueteMenageEnfant();
                $enfant->enquete_menage_id = $id;
                $enfant->codeEnfant = $this->genererCodeEnfant($producteur->codeProdapp, $id, $index);
                $enfant->nom = @$data['nom'];
                $enfant->dateNaissance = @$data['dateNaissance'];
                $enfant->sexe = @$data['sexe'];
                $enfant->lienParente = @$data['lienParente'];
                $enfant->autreLienParente = @$data['autreLienParente'];
                $enfant->raisonNeVitPasParents = @$data['raisonNeVitPasParents'];
                $enfant->autreRaisonNeVitPasParents = @$data['autreRaisonNeVitPasParents'];
                $enfant->situationScolaire = @$data['situationScolaire'];
                $enfant->niveauScolaire = @$data['niveauScolaire'];
                $enfant->autreRaisonNonScolarisation = @$data['autreRaisonNonScolarisation'];
                $enfant->extraitNaissance = @$data['extraitNaissance'];
                $enfant->autreRaisonTravailAbus = @$data['autreRaisonTravailAbus'];
                $enfant->autreMesure = @$data['autreMesure'];
                $enfant->save();

                $enfantId = $enfant->id;

                if (!empty($data['raisonNonScolarisation'])) {
                    $rows = [];
                    foreach ($data['raisonNonScolarisation'] as $valeur) {
                        $rows[] = ['enfant_id' => $enfantId, 'valeur' => $valeur];
                    }
                    EnqueteMenageEnfantRaisonNonScolarisation::insert($rows);
                }

                if (!empty($data['raisonPasExtrait'])) {
                    $rows = [];
                    foreach ($data['raisonPasExtrait'] as $valeur) {
                        $rows[] = ['enfant_id' => $enfantId, 'valeur' => $valeur];
                    }
                    EnqueteMenageEnfantRaisonPasExtrait::insert($rows);
                }

                if (!empty($data['situationsPfte'])) {
                    $rows = [];
                    foreach ($data['situationsPfte'] as $valeur) {
                        $rows[] = ['enfant_id' => $enfantId, 'valeur' => $valeur];
                    }
                    EnqueteMenageEnfantSituationPfte::insert($rows);
                }

                if (!empty($data['raisonTravailAbus'])) {
                    $rows = [];
                    foreach ($data['raisonTravailAbus'] as $valeur) {
                        $rows[] = ['enfant_id' => $enfantId, 'valeur' => $valeur];
                    }
                    EnqueteMenageEnfantRaisonTravailAbus::insert($rows);
                }

                if (!empty($data['mesuresEnfant'])) {
                    $rows = [];
                    foreach ($data['mesuresEnfant'] as $valeur) {
                        $rows[] = ['enfant_id' => $enfantId, 'valeur' => $valeur];
                    }
                    EnqueteMenageEnfantMesureEnfant::insert($rows);
                }

                if (!empty($data['mesuresMenage'])) {
                    $rows = [];
                    foreach ($data['mesuresMenage'] as $valeur) {
                        $rows[] = ['enfant_id' => $enfantId, 'valeur' => $valeur];
                    }
                    EnqueteMenageEnfantMesureMenage::insert($rows);
                }

                if (!empty($data['mesuresCommunaute'])) {
                    $rows = [];
                    foreach ($data['mesuresCommunaute'] as $valeur) {
                        $rows[] = ['enfant_id' => $enfantId, 'valeur' => $valeur];
                    }
                    EnqueteMenageEnfantMesureCommunaute::insert($rows);
                }
            }
        }

        $notify[] = ['success', $isUpdate ? "L'enquête ménage a été mise à jour avec succès" : "L'enquête ménage a été créée avec succès"];
        return redirect()->route('manager.suivi.menage.index')->withNotify($notify);
    }

    private function determinerStatutFin(Request $request)
    {
        if ($request->producteurDisponible == 'Non') {
            return $request->raisonIndisponibilite == 'Refus' ? 'Refus' : 'Indisponible';
        }
        if ($request->consentement == 'Non') {
            return 'Non consentement';
        }
        if ($request->aEnfantACharge == 'Non') {
            return "Pas d'enfant à charge";
        }
        return 'Terminée';
    }

    private function genererCodeEnfant($codeProd, $enqueteMenageId, $index)
    {
        $numero = $index + 1;
        return $codeProd ? $codeProd . '-EN' . $enqueteMenageId . '.' . $numero : 'EN' . $enqueteMenageId . '.' . $numero;
    }

    public function status($id)
    {
        return EnqueteMenage::changeStatus($id);
    }

    public function exportExcel()
    {
        $filename = 'enquete-menage-' . gmdate('dmYhms') . '.xlsx';
        return Excel::download(new ExportEnqueteMenages, $filename);
    }

    public function delete($id)
    {
        EnqueteMenage::where('id', decrypt($id))->delete();
        $notify[] = ['success', 'Le contenu supprimé avec succès'];
        return back()->withNotify($notify);
    }
}
