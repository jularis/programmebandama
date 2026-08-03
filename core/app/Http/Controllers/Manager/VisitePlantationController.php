<?php

namespace App\Http\Controllers\Manager;

use Excel;
use App\Models\Localite;
use App\Models\Producteur;
use App\Models\Cooperative;
use App\Models\ClasseEtude;
use App\Models\NiveauxEtude;
use App\Models\VisitePlantation;
use App\Models\VisitePlantationEnfant;
use App\Models\VisitePlantationRaisonRefus;
use App\Models\VisitePlantationEnfantMesureEnfant;
use App\Models\VisitePlantationEnfantMesureMenage;
use App\Models\VisitePlantationEnfantMesureCommunaute;
use App\Models\VisitePlantationEnfantSituationPfte;
use App\Models\VisitePlantationEnfantRaisonPasExtrait;
use App\Models\VisitePlantationEnfantRaisonTravailAbus;
use App\Models\VisitePlantationEnfantRaisonNonScolarisation;
use Illuminate\Http\Request;
use App\Exports\ExportVisitePlantations;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;

class VisitePlantationController extends Controller
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
            'lienParenteEnfant' => $this->lienParenteEnfant,
            'raisonsNeVitPasParents' => $this->raisonsNeVitPasParents,
            'raisonsNonScolarisation' => DB::table('raisons_non_scolarisations')->pluck('nom', 'nom')->all(),
            'raisonsPasExtrait' => DB::table('raisons_pas_extraits')->pluck('nom', 'nom')->all(),
            'situationsPfte' => DB::table('situations_pftes')->pluck('nom', 'nom')->all(),
            'raisonsTravailAbus' => DB::table('raisons_travail_abus')->pluck('nom', 'nom')->all(),
            'mesuresEnfant' => DB::table('mesures_enfants')->pluck('nom', 'nom')->all(),
            'mesuresMenage' => DB::table('mesures_menages')->pluck('nom', 'nom')->all(),
            'mesuresCommunaute' => DB::table('mesures_communautes')->pluck('nom', 'nom')->all(),
        ];
    }

    public function index()
    {
        $pageTitle = "Gestion des visites de plantation";
        $manager = auth()->user();
        $localites = Localite::joinRelationship('section')->where([['cooperative_id', $manager->cooperative_id], ['localites.status', 1]])->get();

        $visitePlantations = VisitePlantation::searchable(['nomEnqueteur', 'statutFin'])
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

        return view('manager.visiteplantation.index', compact('pageTitle', 'visitePlantations', 'localites'));
    }

    public function create()
    {
        $pageTitle = "Ajouter une visite de plantation";
        $manager = auth()->user();
        $options = $this->optionsCommunes($manager);

        return view('manager.visiteplantation.create', array_merge(compact('pageTitle'), $options));
    }

    public function edit($id)
    {
        $pageTitle = "Mise à jour de la visite de plantation";
        $manager = auth()->user();
        $options = $this->optionsCommunes($manager);
        $visitePlantation = VisitePlantation::with([
            'enfants.raisonsNonScolarisation',
            'enfants.raisonsPasExtrait',
            'enfants.situationsPfte',
            'enfants.raisonsTravailAbus',
            'enfants.mesuresEnfant',
            'enfants.mesuresMenage',
            'enfants.mesuresCommunaute',
            'raisonsRefus',
        ])->findOrFail($id);

        return view('manager.visiteplantation.edit', array_merge(compact('pageTitle', 'visitePlantation'), $options));
    }

    public function show($id)
    {
        $pageTitle = "Détails de la visite de plantation";
        $manager = auth()->user();
        $options = $this->optionsCommunes($manager);
        $visitePlantation = VisitePlantation::with([
            'enfants.raisonsNonScolarisation',
            'enfants.raisonsPasExtrait',
            'enfants.situationsPfte',
            'enfants.raisonsTravailAbus',
            'enfants.mesuresEnfant',
            'enfants.mesuresMenage',
            'enfants.mesuresCommunaute',
            'raisonsRefus',
        ])->findOrFail($id);

        return view('manager.visiteplantation.show', array_merge(compact('pageTitle', 'visitePlantation'), $options));
    }

    public function store(Request $request)
    {
        $localite = Localite::where('id', $request->localite)->first();
        if (!$localite || $localite->status == 0) {
            $notify[] = ['error', 'Cette localité est désactivée'];
            return back()->withNotify($notify)->withInput();
        }

        $entretienPoursuivi = $request->producteurDisponible == 'Oui' && $request->consentement == 'Oui';
        $raisonRefus = (array) $request->input('raisonRefus', []);

        $rules = [
            'section' => 'required|exists:sections,id',
            'localite' => 'required|exists:localites,id',
            'producteur' => 'required|exists:producteurs,id',
            'dateEnquete' => 'required|date',
            'nomEnqueteur' => 'required|max:150',
            'latitude' => 'required|max:50',
            'longitude' => 'required|max:50',
            'estProducteurRepondant' => 'required|in:Oui,Non',
            'nomRepondant' => 'required_if:estProducteurRepondant,Non|nullable|max:150',
            'titreRepondant' => 'required_if:estProducteurRepondant,Non|nullable|max:100',
            'producteurDisponible' => 'required|in:Oui,Non',
            'raisonIndisponibilite' => 'required_if:producteurDisponible,Non|nullable|max:150',
            'datePlanification' => [
                Rule::requiredIf($request->producteurDisponible == 'Non' && $request->raisonIndisponibilite == 'Temporairement absent'),
                'nullable',
                'date',
            ],
            'raisonRefus' => [
                Rule::requiredIf($request->producteurDisponible == 'Non' && $request->raisonIndisponibilite == 'Refus'),
                'nullable',
                'array',
                'min:1',
            ],
            'raisonRefus.*' => 'required|max:150',
            'autreRaisonRefus' => [
                Rule::requiredIf(in_array('Autre raison', $raisonRefus)),
                'nullable',
                'max:150',
            ],
            'consentement' => 'required_if:producteurDisponible,Oui|nullable|in:Oui,Non',
            'superficiePlantation' => [Rule::requiredIf($entretienPoursuivi), 'nullable', 'numeric'],
            'nombreManoeuvresPermanents' => [Rule::requiredIf($entretienPoursuivi), 'nullable', 'integer', 'min:0'],
            'manoeuvresPermanentsMoins18' => [Rule::requiredIf($entretienPoursuivi), 'nullable', 'in:Oui,Non'],
            'nombreManoeuvresJournaliers' => [Rule::requiredIf($entretienPoursuivi), 'nullable', 'integer', 'min:0'],
            'manoeuvresJournaliersMoins18' => [Rule::requiredIf($entretienPoursuivi), 'nullable', 'in:Oui,Non'],
            'nombreEnfants0a4' => [Rule::requiredIf($entretienPoursuivi), 'nullable', 'integer', 'min:0'],
            'nombreEnfants5a17' => [Rule::requiredIf($entretienPoursuivi), 'nullable', 'integer', 'min:0'],
            'nombrePersonnesTrouvees' => [Rule::requiredIf($entretienPoursuivi), 'nullable', 'integer', 'min:0'],
            'enfants' => 'nullable|array',
            'enfants.*.nom' => [Rule::requiredIf($entretienPoursuivi), 'nullable', 'max:150'],
            'enfants.*.dateNaissance' => [Rule::requiredIf($entretienPoursuivi), 'nullable', 'date'],
            'enfants.*.sexe' => [Rule::requiredIf($entretienPoursuivi), 'nullable', 'in:M,F'],
            'enfants.*.lienParente' => [Rule::requiredIf($entretienPoursuivi), 'nullable', 'max:150'],
            'enfants.*.raisonNeVitPasParents' => [Rule::requiredIf($entretienPoursuivi), 'nullable', 'max:150'],
            'enfants.*.situationScolaire' => [Rule::requiredIf($entretienPoursuivi), 'nullable', 'max:150'],
            'enfants.*.extraitNaissance' => [Rule::requiredIf($entretienPoursuivi), 'nullable', 'in:Oui,Non'],
            'enfants.*.situationsPfte' => [Rule::requiredIf($entretienPoursuivi), 'nullable', 'array', 'min:1'],
            'enfants.*.situationsPfte.*' => [Rule::requiredIf($entretienPoursuivi), 'nullable', 'max:255'],
            'enfants.*.mesuresEnfant' => [Rule::requiredIf($entretienPoursuivi), 'nullable', 'array', 'min:1'],
            'enfants.*.mesuresEnfant.*' => [Rule::requiredIf($entretienPoursuivi), 'nullable', 'max:255'],
            'enfants.*.mesuresMenage' => [Rule::requiredIf($entretienPoursuivi), 'nullable', 'array', 'min:1'],
            'enfants.*.mesuresMenage.*' => [Rule::requiredIf($entretienPoursuivi), 'nullable', 'max:255'],
            'enfants.*.mesuresCommunaute' => [Rule::requiredIf($entretienPoursuivi), 'nullable', 'array', 'min:1'],
            'enfants.*.mesuresCommunaute.*' => [Rule::requiredIf($entretienPoursuivi), 'nullable', 'max:255'],
        ];

        $validator = Validator::make($request->all(), $rules);

        $validator->after(function ($validator) use ($request, $entretienPoursuivi) {
            if (!$entretienPoursuivi) {
                return;
            }

            foreach ((array) $request->input('enfants', []) as $index => $enfant) {
                if (($enfant['lienParente'] ?? null) == 'Autre' && blank($enfant['autreLienParente'] ?? null)) {
                    $validator->errors()->add("enfants.$index.autreLienParente", 'Le champ autre lien de parenté est obligatoire.');
                }

                if (($enfant['raisonNeVitPasParents'] ?? null) == 'Autres' && blank($enfant['autreRaisonNeVitPasParents'] ?? null)) {
                    $validator->errors()->add("enfants.$index.autreRaisonNeVitPasParents", "Le champ autre raison est obligatoire.");
                }

                $situationScolaire = $enfant['situationScolaire'] ?? '';
                $contientScol = str_contains($situationScolaire, 'scol') || str_contains($situationScolaire, 'Scol');
                $estJamaisScolarise = str_contains($situationScolaire, 'Jamais');
                $estScolarise = str_starts_with($situationScolaire, 'Scol');
                $estDescolarise = $contientScol && !$estScolarise && !$estJamaisScolarise;

                if (($estScolarise || $estDescolarise) && blank($enfant['niveauScolaire'] ?? null)) {
                    $validator->errors()->add("enfants.$index.niveauScolaire", 'Le champ niveau scolaire est obligatoire.');
                }

                if (($estDescolarise || $estJamaisScolarise) && empty($enfant['raisonNonScolarisation'])) {
                    $validator->errors()->add("enfants.$index.raisonNonScolarisation", 'Le champ raison de non scolarisation est obligatoire.');
                }

                if (!empty($enfant['raisonNonScolarisation']) && in_array('Autre', (array) $enfant['raisonNonScolarisation']) && blank($enfant['autreRaisonNonScolarisation'] ?? null)) {
                    $validator->errors()->add("enfants.$index.autreRaisonNonScolarisation", 'Le champ autre raison de non scolarisation est obligatoire.');
                }

                if (($enfant['extraitNaissance'] ?? null) == 'Non' && empty($enfant['raisonPasExtrait'])) {
                    $validator->errors()->add("enfants.$index.raisonPasExtrait", "Le champ raison d'absence d'extrait est obligatoire.");
                }

                $situationsPfte = (array) ($enfant['situationsPfte'] ?? []);
                $besoinRaisonTravailAbus = count($situationsPfte) > 0 && !(count($situationsPfte) == 1 && $situationsPfte[0] == 'Aucune');
                if ($besoinRaisonTravailAbus && empty($enfant['raisonTravailAbus'])) {
                    $validator->errors()->add("enfants.$index.raisonTravailAbus", 'Le champ raison du travail dangereux ou abus est obligatoire.');
                }

                if (!empty($enfant['raisonTravailAbus']) && in_array('Autre', (array) $enfant['raisonTravailAbus']) && blank($enfant['autreRaisonTravailAbus'] ?? null)) {
                    $validator->errors()->add("enfants.$index.autreRaisonTravailAbus", 'Le champ autre raison du travail dangereux ou abus est obligatoire.');
                }

                $mesures = array_merge(
                    (array) ($enfant['mesuresEnfant'] ?? []),
                    (array) ($enfant['mesuresMenage'] ?? []),
                    (array) ($enfant['mesuresCommunaute'] ?? [])
                );
                $besoinAutreMesure = collect($mesures)->contains(function ($mesure) {
                    return str_contains($mesure, 'Autre') || str_contains($mesure, 'prÃ©ciser');
                });

                if ($besoinAutreMesure && blank($enfant['autreMesure'] ?? null)) {
                    $validator->errors()->add("enfants.$index.autreMesure", 'Le champ autre mesure est obligatoire.');
                }
            }
        });

        $validator->validate();

        $isUpdate = (bool) $request->id;
        $visitePlantation = $isUpdate ? VisitePlantation::findOrFail($request->id) : new VisitePlantation();
        $producteur = Producteur::find($request->producteur);

        $visitePlantation->section_id = $request->section;
        $visitePlantation->localite_id = $request->localite;
        $visitePlantation->producteur_id = $request->producteur;
        $visitePlantation->sexeProducteur = $producteur->sexe;
        $visitePlantation->codeProducteur = $producteur->codeProdapp;

        $visitePlantation->raisonInterview = 'Enquête initiale';
        $visitePlantation->typeEnquete = 'visite plantation';
        $visitePlantation->dateEnquete = $request->dateEnquete;
        $visitePlantation->nomEnqueteur = $request->nomEnqueteur;

        $visitePlantation->latitude = $request->latitude;
        $visitePlantation->longitude = $request->longitude;
        $visitePlantation->altitude = $request->altitude;
        $visitePlantation->precisionGps = $request->precisionGps;

        $visitePlantation->estProducteurRepondant = $request->estProducteurRepondant;
        $visitePlantation->nomRepondant = $request->nomRepondant;
        $visitePlantation->titreRepondant = $request->titreRepondant;

        $visitePlantation->producteurDisponible = $request->producteurDisponible;
        $visitePlantation->raisonIndisponibilite = $request->raisonIndisponibilite;
        $visitePlantation->datePlanification = $request->datePlanification;
        $visitePlantation->autreRaisonRefus = $request->autreRaisonRefus;
        $visitePlantation->consentement = $request->consentement;

        $visitePlantation->superficiePlantation = $request->superficiePlantation;
        $visitePlantation->nombreManoeuvresPermanents = $request->nombreManoeuvresPermanents;
        $visitePlantation->manoeuvresPermanentsMoins18 = $request->manoeuvresPermanentsMoins18;
        $visitePlantation->nombreManoeuvresJournaliers = $request->nombreManoeuvresJournaliers;
        $visitePlantation->manoeuvresJournaliersMoins18 = $request->manoeuvresJournaliersMoins18;
        $visitePlantation->nombreEnfants0a4 = $request->nombreEnfants0a4;
        $visitePlantation->nombreEnfants5a17 = $request->nombreEnfants5a17;
        $visitePlantation->nombrePersonnesTrouvees = $request->nombrePersonnesTrouvees;
        $visitePlantation->nombreEnfantsTrouves = $request->enfants ? count($request->enfants) : 0;

        $visitePlantation->etatSoumission = $request->etatSoumission == 'Brouillon' ? 'Brouillon' : 'Soumis';
        $visitePlantation->statutFin = $this->determinerStatutFin($request);

        $visitePlantation->userid = auth()->id();
        $visitePlantation->save();

        $id = $visitePlantation->id;

        VisitePlantationRaisonRefus::where('visite_id', $id)->delete();
        if ($request->raisonRefus != null) {
            $rows = [];
            foreach ($request->raisonRefus as $valeur) {
                $rows[] = ['visite_id' => $id, 'valeur' => $valeur];
            }
            VisitePlantationRaisonRefus::insert($rows);
        }

        VisitePlantationEnfant::where('visite_id', $id)->delete();
        if ($request->enfants != null) {
            foreach ($request->enfants as $index => $data) {
                $enfant = new VisitePlantationEnfant();
                $enfant->visite_id = $id;
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
                    VisitePlantationEnfantRaisonNonScolarisation::insert($rows);
                }

                if (!empty($data['raisonPasExtrait'])) {
                    $rows = [];
                    foreach ($data['raisonPasExtrait'] as $valeur) {
                        $rows[] = ['enfant_id' => $enfantId, 'valeur' => $valeur];
                    }
                    VisitePlantationEnfantRaisonPasExtrait::insert($rows);
                }

                if (!empty($data['situationsPfte'])) {
                    $rows = [];
                    foreach ($data['situationsPfte'] as $valeur) {
                        $rows[] = ['enfant_id' => $enfantId, 'valeur' => $valeur];
                    }
                    VisitePlantationEnfantSituationPfte::insert($rows);
                }

                if (!empty($data['raisonTravailAbus'])) {
                    $rows = [];
                    foreach ($data['raisonTravailAbus'] as $valeur) {
                        $rows[] = ['enfant_id' => $enfantId, 'valeur' => $valeur];
                    }
                    VisitePlantationEnfantRaisonTravailAbus::insert($rows);
                }

                if (!empty($data['mesuresEnfant'])) {
                    $rows = [];
                    foreach ($data['mesuresEnfant'] as $valeur) {
                        $rows[] = ['enfant_id' => $enfantId, 'valeur' => $valeur];
                    }
                    VisitePlantationEnfantMesureEnfant::insert($rows);
                }

                if (!empty($data['mesuresMenage'])) {
                    $rows = [];
                    foreach ($data['mesuresMenage'] as $valeur) {
                        $rows[] = ['enfant_id' => $enfantId, 'valeur' => $valeur];
                    }
                    VisitePlantationEnfantMesureMenage::insert($rows);
                }

                if (!empty($data['mesuresCommunaute'])) {
                    $rows = [];
                    foreach ($data['mesuresCommunaute'] as $valeur) {
                        $rows[] = ['enfant_id' => $enfantId, 'valeur' => $valeur];
                    }
                    VisitePlantationEnfantMesureCommunaute::insert($rows);
                }
            }
        }

        $notify[] = ['success', $isUpdate ? "La visite de plantation a été mise à jour avec succès" : "La visite de plantation a été créée avec succès"];
        return redirect()->route('manager.suivi.visiteplantation.index')->withNotify($notify);
    }

    private function determinerStatutFin(Request $request)
    {
        if ($request->producteurDisponible == 'Non') {
            return $request->raisonIndisponibilite == 'Refus' ? 'Refus' : 'Indisponible';
        }
        if ($request->consentement == 'Non') {
            return 'Non consentement';
        }
        if (($request->enfants ? count($request->enfants) : 0) == 0) {
            return 'Aucun enfant trouvé';
        }
        return 'Terminée';
    }

    private function genererCodeEnfant($codeProd, $visitePlantationId, $index)
    {
        $numero = $index + 1;
        return $codeProd ? $codeProd . '-VP' . $visitePlantationId . '.' . $numero : 'VP' . $visitePlantationId . '.' . $numero;
    }

    public function status($id)
    {
        return VisitePlantation::changeStatus($id);
    }

    public function exportExcel()
    {
        $filename = 'visite-plantation-' . gmdate('dmYhms') . '.xlsx';
        return Excel::download(new ExportVisitePlantations, $filename);
    }

    public function delete($id)
    {
        VisitePlantation::where('id', decrypt($id))->delete();
        $notify[] = ['success', 'Le contenu supprimé avec succès'];
        return back()->withNotify($notify);
    }
}
