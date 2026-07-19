<?php

namespace App\Http\Controllers;

use App\Models\Producteur;
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

class ApivisiteplantationController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $producteur = Producteur::find($request->producteur_id);
        if (!$producteur) {
            return response()->json("Producteur introuvable", 501);
        }

        $isUpdate = (bool) $request->id;
        $visitePlantation = $isUpdate ? VisitePlantation::find($request->id) : new VisitePlantation();

        $visitePlantation->raisonInterview = 'Enquête initiale';
        $visitePlantation->typeEnquete = 'visite plantation';

        $visitePlantation->section_id = $request->section_id;
        $visitePlantation->localite_id = $request->localite_id;
        $visitePlantation->producteur_id = $request->producteur_id;
        $visitePlantation->sexeProducteur = $producteur->sexe;
        $visitePlantation->codeProducteur = $producteur->codeProdapp;

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

        $visitePlantation->userid = $request->userid;
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

        return response()->json($visitePlantation, 201);
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
}
