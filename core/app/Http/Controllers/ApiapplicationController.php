<?php

namespace App\Http\Controllers;

use App\Models\Campagne;
use App\Models\Localite;
use App\Constants\Status;
use App\Models\Application;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\MatiereActive;
use App\Models\ApplicationMaladie;
use Illuminate\Support\Facades\DB;
use App\Models\ApplicationPesticide;
use Illuminate\Support\Facades\File;
use App\Models\ApplicationAutreMaladie;

class ApiapplicationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
 
        //dd(response()->json($request));
 

        if ($request->id) {
            $application = Application::findOrFail($request->id);
            $message = "L'application a été mise à jour avec succès";
        } else {
            $application = new Application();
        }
        $campagne = Campagne::active()->first();
        $application->campagne_id  = $campagne->id;
        $application->applicateur_id  = $request->applicateur;
        $application->parcelle_id  = $request->parcelle_id;
        $application->suiviFormation = $request->suiviFormation;
        $application->attestion = $request->attestion;
        $application->bilanSante = $request->bilanSante;
        $application->independantEpi = $request->independantEpi;
        $application->etatEpi = $request->etatEpi;
        $application->superficiePulverisee = $request->superficiePulverisee;
        $application->delaisReentree = $request->delaisReentree;
        $application->personneApplication = $request->personneApplication;
        $application->date_application = $request->date_application;
        $application->heure_application = $request->heure_application;
        $application->reponse = $request->reponse;
        $application->userid = $request->userid;
        $application->save();

        if ($application != null) {
            $id = $application->id;
            if ($request->maladies != null) {
                ApplicationMaladie::where('application_id', $id)->delete();
                foreach ($request->maladies as $maladie) {
                    $data[] = [
                        'application_id' => $id,
                        'nom' => $maladie,
                    ];
                }
                ApplicationMaladie::insert($data);
            }
            if ($request->pesticides != null) {
                ApplicationPesticide::where('application_id', $id)->delete();
                MatiereActive::where('application_id', $id)->delete();
                foreach ($request->pesticides as $pesticide) {
                    $applicationPesticide = new ApplicationPesticide();
                    $applicationPesticide->application_id = $id;
                    $applicationPesticide->nom = $pesticide['nom'];
                    $applicationPesticide->nomCommercial = $pesticide['nomCommercial'];
                    $applicationPesticide->dosage = $pesticide['dosage'];
                    $applicationPesticide->doseUnite = $pesticide['doseUnite'];
                    $applicationPesticide->quantiteUnite = $pesticide['quantiteUnite'];
                    $applicationPesticide->quantite = $pesticide['quantite'];
                    $applicationPesticide->toxicicologie = $pesticide['toxicicologie'];
                    $applicationPesticide->frequence = $pesticide['frequence'];
                    $applicationPesticide->save();

                    if ($applicationPesticide != null) {
                        MatiereActive::where('application_pesticide_id', $applicationPesticide->id)->delete();
                        $idApplicationPesticide = $applicationPesticide->id;
                        $matiereActive = explode(',', $pesticide['matiereActive']);
                        foreach ($matiereActive as $matiere) {
                            $applicationMatieresactive = new MatiereActive();
                            $applicationMatieresactive->application_id = $id;
                            $applicationMatieresactive->application_pesticide_id = $idApplicationPesticide;
                            $applicationMatieresactive->nom = trim($matiere);
                            $applicationMatieresactive->save();
                        }
                    }
                }
            }
            if ($request->autreMaladie != null) {
                ApplicationAutreMaladie::where('application_id', $id)->delete();
                foreach ($request->autreMaladie as $maladie) {
                    $data1[] = [
                        'application_id' => $id,
                        'libelle' => $maladie,
                    ];
                }
                ApplicationAutreMaladie::insert($data1);
            }
        }
        return response()->json($application, 201);
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
