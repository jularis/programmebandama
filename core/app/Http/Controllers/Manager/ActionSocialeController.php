<?php

namespace App\Http\Controllers\Manager;

use PDF;
use App\Models\Localite;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\ActionSociale;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ExportActionSociales;
use App\Models\ActionSocialeLocalite;
use App\Models\ActionSocialePartenaire;
use Illuminate\Support\Facades\Storage;
use Google\Service\CloudLifeSciences\Action;
use App\Models\ActionSocialeAutreBeneficiaire;

class ActionSocialeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $pageTitle      = "Gestion des Actions Sociales";
        $manager   = auth()->user();
        $actions = ActionSociale::dateFilter()->searchable([])->latest('id')->where('cooperative_id', $manager->cooperative_id)->where(function ($q) {
            if (request()->localite != null) {
                $q->where('localite_id', request()->localite);
            }
        })->with('cooperative')->paginate(getPaginate());

        if (request()->download) {
            $actionsociale = ActionSociale::find(decrypt(request()->download));
            $actionsocialeNameFile = Str::slug($actionsociale->titre_projet . '-' . $actionsociale->code, '-');
            $actionsocialeNameFile = $actionsocialeNameFile . '.pdf';
            if (!file_exists(storage_path() . "/app/public/actionsociales-pdf")) {
                File::makeDirectory(storage_path() . "/app/public/actionsociales-pdf", 0777, true);
            }
            @unlink(storage_path('app/public/actionsociales-pdf') . "/" . $actionsocialeNameFile);

            return PDF::loadView('manager.action-sociale.pdf-actionsociale', compact('actionsociale'))
                ->download($actionsocialeNameFile);
            // ->save(storage_path(). "/app/public/producteurs-pdf/".$producteurNameFile);
        }

        return view('manager.action-sociale.index', compact('pageTitle', 'actions'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $pageTitle = "Ajouter une Action Sociale";
        $manager = auth()->user();
        $localites = Localite::joinRelationship('section')->where([['cooperative_id', $manager->cooperative_id], ['localites.status', 1]])->orderBy('nom')->get();

        return view('manager.action-sociale.create', compact('pageTitle', 'localites'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validationRule = [
            'type_projet' => 'required',
            'titre_projet' => 'required',
            'description_projet' => 'required',
            'niveau_realisation' => 'required',
            'date_livraison' => 'required',
            'partenaires.*.partenaire' => 'required',
            'partenaires.*.type_partenaire' => 'required',
            'partenaires.*.montant_contribution' => 'required',
            'photos.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'documents_joints.*' => 'nullable|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx|max:2048'
        ];
        $request->validate($validationRule);
        if ($request->id) {
            $action = ActionSociale::find($request->id);
            $message = 'Action Sociale modifiée avec succès.';
        } else {
            $action = new ActionSociale();

            $action->code = $this->generateCode($request);
            $message = 'Action Sociale ajoutée avec succès.';
        }
        $action->type_projet = $request->type_projet;
        $action->titre_projet = $request->titre_projet;
        $action->description_projet = $request->description_projet;

        $action->niveau_realisation = $request->niveau_realisation;
        $action->date_demarrage = $request->date_demarrage;
        $action->date_fin_projet = $request->date_fin_projet;
        $action->cout_projet = $request->cout_projet;
        $action->date_livraison = $request->date_livraison;
        $action->commentaires = $request->commentaires;
        $action->cooperative_id = auth()->user()->cooperative_id;
        
        if ($request->has('photos')) {
            $paths = [];
            foreach ($request->file('photos') as $photo) {
                try {
                    $originalName = $photo->getClientOriginalName();
                    $fileName = pathinfo($originalName, PATHINFO_FILENAME);
                    $extension = pathinfo($originalName, PATHINFO_EXTENSION);

                    $counter = 1;
                    while (Storage::exists('public/actionSociales/photos/' . $originalName)) {
                        $originalName = $fileName . '_' . $counter . '.' . $extension;
                        $counter++;
                    }

                    $path = $photo->storeAs('public/actionSociales/photos', $originalName);
                    $paths[] = $path;
                } catch (\Exception $exp) {
                    $notify[] = ['error', 'Impossible de télécharger votre image'];
                    return back()->withNotify($notify);
                }
            }
            $action->photos = json_encode($paths);
        }
        if ($request->has('documents_joints')) {
            $paths = [];
            foreach ($request->file('documents_joints') as $document) {
                try {
                    $originalName = $document->getClientOriginalName();
                    $fileName = pathinfo($originalName, PATHINFO_FILENAME);
                    $extension = pathinfo($originalName, PATHINFO_EXTENSION);

                    $counter = 1;
                    while (Storage::exists('public/actionSociales/documents/' . $originalName)) {
                        $originalName = $fileName . '_' . $counter . '.' . $extension;
                        $counter++;
                    }

                    $path = $document->storeAs('public/actionSociales/documents', $originalName);
                    $paths[] = $path;
                } catch (\Exception $exp) {
                    $notify[] = ['error', 'Impossible de télécharger votre image'];
                    return back()->withNotify($notify);
                }
            }
            $action->documents_joints = json_encode($paths);
        }
        $action->save();
        if ($action != null) {
            if ($request->partenaires != null && !collect($request->partenaires)->contains(null)) {
                ActionSocialePartenaire::where('action_sociale_id', $action->id)->delete();
                $data = [];
                foreach ($request->partenaires as $partenaire) {
                    $data[] = [
                        'action_sociale_id' => $action->id,
                        'partenaire' => $partenaire['partenaire'],
                        'type_partenaire' => $partenaire['type_partenaire'],
                        'montant' => $partenaire['montant_contribution']
                    ];
                }
                ActionSocialePartenaire::insert($data);
            }
            if ($request->beneficiaires_projet != null && !collect($request->beneficiaires_projet)->contains(null)) {
                ActionSocialeLocalite::where('action_sociale_id', $action->id)->delete();
                $data1 = [];
                foreach ($request->beneficiaires_projet as $beneficiaire) {
                    $data1[] = [
                        'action_sociale_id' => $action->id,
                        'localite_id' => $beneficiaire
                    ];
                }
                ActionSocialeLocalite::insert($data1);
            }
            if ($request->autreBeneficiaire != null && !collect($request->autreBeneficiaire)->contains(null)) {
                ActionSocialeAutreBeneficiaire::where('action_sociale_id', $action->id)->delete();
                $data2 = [];
                foreach ($request->autreBeneficiaire as $beneficiaire) {
                    $data2[] = [
                        'action_sociale_id' => $action->id,
                        'libelle' => $beneficiaire
                    ];
                }
                ActionSocialeAutreBeneficiaire::insert($data2);
            }
        }

        $notify[] = ['success', isset($message) ? $message : 'Action Sociale ajoutée avec succès.'];
        return back()->withNotify($notify);
    }
    // public function generCode(Request $request)
    // {
    //     if ($request->has('date')) {
    //         $date = \Carbon\Carbon::parse($request->date);
    //         $year = $date->year;
    //         return $year;
    //     }
    // }

    private function generateCode(Request $request)
    {
        $number = ActionSociale::count();
        $number++;

        $year = \Carbon\Carbon::parse($request->date_livraison)->year;

        return sprintf('CR-AS-%s-%03d', $year, $number);
    }

    // private function generateCode(Request $request)
    // {
        
    //     $code = \App\Models\Code::first();

        
    //     if ($code === null) {
    //         $code = new \App\Models\Code;
    //         $code->last_number = 0;
    //     }

    //     $code->last_number++;

    //     $code->save();

    //     $year = \Carbon\Carbon::parse($request->date_livraison)->year;

    //     return sprintf('CR-AS-%s-%03d', $year, $code->last_number);
    // }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $pageTitle = "Détail Action Sociale";
        $actionSociale = ActionSociale::find($id); // Remplacez ActionSociale par le nom de votre modèle
        $partenaires = $actionSociale->partenaires;
        return view('manager.action-sociale.show', compact('actionSociale', 'pageTitle', 'partenaires'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $pageTitle = "Modifier une Action Sociale";
        $actionSociale = ActionSociale::find($id); // Remplacez ActionSociale par le nom de votre modèle
        $partenaires = $actionSociale->partenaires;
        $localites = Localite::joinRelationship('section')->where([['cooperative_id', $actionSociale->cooperative_id], ['localites.status', 1]])->orderBy('nom')->get();
        $dataLocalite = ActionSocialeLocalite::where('action_sociale_id', $id)->pluck('localite_id')->toArray();
        return view('manager.action-sociale.edit', compact('actionSociale', 'pageTitle', 'partenaires', 'localites', 'dataLocalite'));
    }
    public function exportExcel()
    {
        $filename = 'actionSociale-' . gmdate('dmYhms') . '.xlsx';
        return Excel::download(new ExportActionSociales, $filename);
    }
    public function delete($id)
    { 
        ActionSociale::where('id', decrypt($id))->delete();
        $notify[] = ['success', 'Le contenu supprimé avec succès'];
        return back()->withNotify($notify);
    }
}
