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
use Illuminate\Http\Request;
use App\Exports\ExportEnqueteMenages;
use Illuminate\Support\Facades\DB;
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

        return view('manager.enquetemenage.create', array_merge(compact('pageTitle'), $options));
    }

    public function edit($id)
    {
        $pageTitle = "Mise à jour de l'enquête ménage";
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

        return view('manager.enquetemenage.edit', array_merge(compact('pageTitle', 'enqueteMenage'), $options));
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
            'nomEnqueteur' => 'required|max:150',
            'estProducteurRepondant' => 'required|in:Oui,Non',
            'nomRepondant' => 'required_if:estProducteurRepondant,Non|nullable|max:150',
            'titreRepondant' => 'required_if:estProducteurRepondant,Non|nullable|max:100',
            'producteurDisponible' => 'required|in:Oui,Non',
            'raisonIndisponibilite' => 'required_if:producteurDisponible,Non|nullable|max:150',
            'consentement' => 'nullable|in:Oui,Non',
            'enfants' => 'nullable|array',
            'enfants.*.nom' => 'required|max:150',
            'enfants.*.dateNaissance' => 'required|date',
            'enfants.*.sexe' => 'required|in:M,F',
        ];

        $request->validate($rules);

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

        $enqueteMenage->dateEnquete = $request->dateEnquete;
        $enqueteMenage->nomEnqueteur = $request->nomEnqueteur;
        $enqueteMenage->nombreEnfantsEnquetes = $request->nombreEnfantsEnquetes;

        $enqueteMenage->latitude = $request->latitude;
        $enqueteMenage->longitude = $request->longitude;
        $enqueteMenage->altitude = $request->altitude;
        $enqueteMenage->precisionGps = $request->precisionGps;

        $enqueteMenage->estProducteurRepondant = $request->estProducteurRepondant;
        $enqueteMenage->nomRepondant = $request->nomRepondant;
        $enqueteMenage->titreRepondant = $request->titreRepondant;

        $enqueteMenage->producteurDisponible = $request->producteurDisponible;
        $enqueteMenage->raisonIndisponibilite = $request->raisonIndisponibilite;
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
