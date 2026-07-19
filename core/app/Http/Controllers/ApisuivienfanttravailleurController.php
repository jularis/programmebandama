<?php

namespace App\Http\Controllers;

use App\Models\User;
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
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class ApisuivienfanttravailleurController extends Controller
{
    /**
     * Liste des enfants (issus des enquêtes ménage) disponibles pour un suivi,
     * limitée à la coopérative de l'utilisateur mobile.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getenfants(Request $request)
    {
        $userid = $request->userid;
        $manager = User::where('id', $userid)->first();

        $enfants = EnqueteMenageEnfant::whereHas('menage.producteur.localite.section', function ($query) use ($manager) {
            $query->where('cooperative_id', $manager->cooperative_id);
        })
            ->where('status', 1)
            ->with('menage.producteur', 'menage.localite')
            ->orderBy('nom')
            ->get();

        return response()->json($enfants, 201);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $enfant = EnqueteMenageEnfant::find($request->enfant_id);
        if (!$enfant) {
            return response()->json("Enfant introuvable", 501);
        }

        $isUpdate = (bool) $request->id;
        $suivi = $isUpdate ? SuiviEnfantTravailleur::find($request->id) : new SuiviEnfantTravailleur();

        $suivi->enfant_id = $request->enfant_id;
        $suivi->raisonInterview = 'Visite de suivi';
        $suivi->dateEnquete = $request->dateEnquete;
        $suivi->nomEnqueteur = $request->nomEnqueteur;

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
        $suivi->autreRaisonTravailAbus = $request->autreRaisonTravailAbus;
        $suivi->autreMesure = $request->autreMesure;

        $suivi->autreThemeSensibilisation = $request->autreThemeSensibilisation;
        $suivi->nombreHommesSensibilises = $request->nombreHommesSensibilises;
        $suivi->nombreFemmesSensibilisees = $request->nombreFemmesSensibilisees;
        $suivi->nombreGarconsSensibilises = $request->nombreGarconsSensibilises;
        $suivi->nombreFillesSensibilisees = $request->nombreFillesSensibilisees;
        $suivi->totalPersonnesSensibilisees = (int) $request->nombreHommesSensibilises + (int) $request->nombreFemmesSensibilisees + (int) $request->nombreGarconsSensibilises + (int) $request->nombreFillesSensibilisees;
        $suivi->telephoneProducteurSensibilisation = $request->telephoneProducteurSensibilisation;

        if ($request->photoSensibilisation) {
            if (!file_exists(storage_path() . "/app/public/suivienfanttravailleur/photos")) {
                File::makeDirectory(storage_path() . "/app/public/suivienfanttravailleur/photos", 0777, true);
            }
            $image = Str::after($request->photoSensibilisation, 'base64,');
            $image = str_replace(' ', '+', $image);
            $imageName = (string) Str::uuid() . '.jpg';
            File::put(storage_path() . "/app/public/suivienfanttravailleur/photos/" . $imageName, base64_decode($image));
            $suivi->photoSensibilisation = "public/suivienfanttravailleur/photos/$imageName";
        }

        $suivi->etatSoumission = $request->etatSoumission == 'Brouillon' ? 'Brouillon' : 'Soumis';
        $suivi->userid = $request->userid;
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

        return response()->json($suivi, 201);
    }
}
