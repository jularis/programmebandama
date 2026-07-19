<?php

namespace App\Http\Controllers;

use App\Models\Producteur;
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
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class ApienquetemenageController extends Controller
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
        $enqueteMenage = $isUpdate ? EnqueteMenage::find($request->id) : new EnqueteMenage();

        $enqueteMenage->raisonInterview = 'Enquête initiale';
        $enqueteMenage->typeEnquete = 'Menage';

        $enqueteMenage->section_id = $request->section_id;
        $enqueteMenage->localite_id = $request->localite_id;
        $enqueteMenage->producteur_id = $request->producteur_id;
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

        if ($request->photoSensibilisation) {
            if (!file_exists(storage_path() . "/app/public/enquetemenages/photos")) {
                File::makeDirectory(storage_path() . "/app/public/enquetemenages/photos", 0777, true);
            }
            $image = Str::after($request->photoSensibilisation, 'base64,');
            $image = str_replace(' ', '+', $image);
            $imageName = (string) Str::uuid() . '.jpg';
            File::put(storage_path() . "/app/public/enquetemenages/photos/" . $imageName, base64_decode($image));
            $enqueteMenage->photoSensibilisation = "public/enquetemenages/photos/$imageName";
        }

        $enqueteMenage->etatSoumission = $request->etatSoumission == 'Brouillon' ? 'Brouillon' : 'Soumis';
        $enqueteMenage->statutFin = $this->determinerStatutFin($request);

        $enqueteMenage->userid = $request->userid;
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

        return response()->json($enqueteMenage, 201);
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
}
