<?php

namespace App\Http\Controllers\Manager;

use PDF;
use App\Models\Localite;
use App\Models\Producteur;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use App\Models\ActiviteCommunautaire;
use Illuminate\Support\Facades\Storage;
use App\Models\ActiviteCommunautaireLocalite;
use App\Models\ActiviteCommunautaireNonMembre;
use App\Models\ActiviteCommunautaireBeneficiaire;

class ActiviteCommunautaireController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $pageTitle      = "Gestion des Activités Communautaires";
        $manager   = auth()->user();
        $activites = ActiviteCommunautaire::dateFilter()->searchable([])->latest('id')->where('cooperative_id', $manager->cooperative_id)->where(function ($q) {
            if (request()->localite != null) {
                $q->where('localite_id', request()->localite);
            }
        })->with('cooperative')->paginate(getPaginate());

        if (request()->download) {
            $activite = ActiviteCommunautaire::find(decrypt(request()->download));
            $activiteNameFile = Str::slug($activite->titre_projet . '-' . $activite->code, '-');
            $activiteNameFile = $activiteNameFile . '.pdf';
            if (!file_exists(storage_path() . "/app/public/activiteCommunautaire-pdf")) {
                File::makeDirectory(storage_path() . "/app/public/activiteCommunautaire-pdf", 0777, true);
            }
            @unlink(storage_path('app/public/activiteCommunautaire-pdf') . "/" . $activiteNameFile);

            return PDF::loadView('manager.activite-communautaire.pdf-activite', compact('activite'))
                ->download($activiteNameFile);
            // ->save(storage_path(). "/app/public/producteurs-pdf/".$producteurNameFile);
        }

        return view('manager.activite-communautaire.index', compact('pageTitle', 'activites'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $pageTitle = "Ajouter une Activité Communautaire";
        $manager = auth()->user();
        $localites = Localite::joinRelationship('section')->where([['cooperative_id', $manager->cooperative_id], ['localites.status', 1]])->orderBy('nom')->get();
        $producteurs = Producteur::joinRelationship('localite.section')
        ->where([['cooperative_id', $manager->cooperative_id],['producteurs.status', 1]])->with('localite')->get();
        return view('manager.activite-communautaire.create', compact('pageTitle', 'localites', 'producteurs'));
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
            'titre_projet' => 'required',
            'description_projet' => 'required',
            'type_projet' => 'required',
            'niveau_realisation' => 'required',
            'cout_projet' => 'required',
            'photos.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'documents_joints.*' => 'nullable|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx|max:2048'
        ];
        $request->validate($validationRule);
        if ($request->id) {
            $communaute = ActiviteCommunautaire::find($request->id);
            $message = "Activité Communautaire modifiée avec succès";
        } else {
            $communaute = new ActiviteCommunautaire();
            $communaute->code = $this->generateCode($request);
            $message = "Activité Communautaire ajoutée avec succès";
        }
        $communaute->titre_projet = $request->titre_projet;
        $communaute->description_projet = $request->description_projet;
        $communaute->type_projet = $request->type_projet;
        $communaute->niveau_realisation = $request->niveau_realisation;
        $communaute->cout_projet = $request->cout_projet;
        $communaute->cooperative_id = auth()->user()->cooperative_id;
        $communaute->localite_id = $request->localite_id;
        $communaute->commentaires = $request->commentaires;
        $communaute->date_livraison = $request->date_livraison;
        $communaute->date_demarrage = $request->date_demarrage;
        $communaute->date_fin_projet = $request->date_fin_projet;
        $communaute->date_demarrage = $request->date_demarrage;
        if ($request->has('photos')) {
            $paths = [];
            foreach ($request->file('photos') as $photo) {
                try {
                    $originalName = $photo->getClientOriginalName();
                    $fileName = pathinfo($originalName, PATHINFO_FILENAME);
                    $extension = pathinfo($originalName, PATHINFO_EXTENSION);

                    $counter = 1;
                    while (Storage::exists('public/activiteCommunautaire/photos/' . $originalName)) {
                        $originalName = $fileName . '_' . $counter . '.' . $extension;
                        $counter++;
                    }

                    $path = $photo->storeAs('public/activiteCommunautaire/photos', $originalName);
                    $paths[] = $path;
                } catch (\Exception $exp) {
                    $notify[] = ['error', 'Impossible de télécharger votre image'];
                    return back()->withNotify($notify);
                }
            }
            $communaute->photos = json_encode($paths);
        }
        if ($request->has('documents_joints')) {
            $paths = [];
            foreach ($request->file('documents_joints') as $document) {
                try {
                    $originalName = $document->getClientOriginalName();
                    $fileName = pathinfo($originalName, PATHINFO_FILENAME);
                    $extension = pathinfo($originalName, PATHINFO_EXTENSION);

                    $counter = 1;
                    while (Storage::exists('public/activiteCommunautaire/documents/' . $originalName)) {
                        $originalName = $fileName . '_' . $counter . '.' . $extension;
                        $counter++;
                    }

                    $path = $document->storeAs('public/activiteCommunautaire/documents', $originalName);
                    $paths[] = $path;
                } catch (\Exception $exp) {
                    $notify[] = ['error', 'Impossible de télécharger votre image'];
                    return back()->withNotify($notify);
                }
            }
            $communaute->documents_joints = json_encode($paths);
        }

        $communaute->save();
        if ($communaute != null) {
            $id = $communaute->id;
            ActiviteCommunautaireLocalite::where('activite_communautaire_id', $id)->delete();
            if ($request->localite != null && !collect($request->localite)->contains(null)) {
                foreach ($request->localite as $localite) {
                    $communauteLocalite = new ActiviteCommunautaireLocalite();
                    $communauteLocalite->activite_communautaire_id = $id;
                    $communauteLocalite->localite_id = $localite;
                    $communauteLocalite->save();
                }
            }
            $selectedLocalites = $request->localite;
            $selectedProducteurs = $request->producteur;
            if ($selectedLocalites != null && $selectedProducteurs != null) {
                ActiviteCommunautaireBeneficiaire::where('activite_communautaire_id', $id)->delete();
                foreach ($selectedProducteurs as $producteurId) {
                    list($localiteId, $producteurId) = explode('-', $producteurId);
                    $data[] = [
                        'activite_communautaire_id' => $id,
                        'localite_id' => $localiteId,
                        'producteur_id' => $producteurId
                    ];
                }
                ActiviteCommunautaireBeneficiaire::insert($data);
            }
        }
        $notify[] = ['success', isset($message) ? $message : 'Activité Communautaire ajoutée avec succès.'];
        return back()->withNotify($notify);
    }
    private function generateCode(Request $request)
    {
        $nbr = ActiviteCommunautaire::count();
        $nbr++;
        $year = \Carbon\Carbon::parse($request->date_livraison)->year;
        return sprintf('CR-AC-%s-%03d', $year, $nbr);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $pageTitle = "Détails de l'Activité Communautaire";
        $manager = auth()->user();
        $communauteSociale = ActiviteCommunautaire::find($id); // Remplacez ActionSociale par le nom de votre modèle
        $dataLocalite = ActiviteCommunautaireLocalite::where('activite_communautaire_id', $id)->pluck('localite_id')->toArray();
        $localites = Localite::joinRelationship('section')->where([['cooperative_id', $manager->cooperative_id], ['localites.status', 1]])->orderBy('nom')->get();
        return view('manager.activite-communautaire.show', compact('pageTitle', 'communauteSociale', 'dataLocalite', 'localites'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $pageTitle = "Modifier une Activité Communautaire";
        $manager = auth()->user();
        $communauteSociale = ActiviteCommunautaire::find($id); // Remplacez ActionSociale par le nom de votre modèle
        // $dataLocalite = ActiviteCommunautaireLocalite::where('activite_communautaire_id', $id)->pluck('localite_id')->toArray();
        $localites = Localite::joinRelationship('section')->where([['cooperative_id', $manager->cooperative_id], ['localites.status', 1]])->orderBy('nom')->get();
        $producteurs = Producteur::joinRelationship('localite.section')
        ->where([['cooperative_id', $manager->cooperative_id],['producteurs.status', 1]])->with('localite')->get();

        foreach ($communauteSociale->beneficiaires as $item) {
            $dataLocalite[] = $item->localite_id;
            $producteursSelected[] = $item->producteur_id;
        }

        return view('manager.activite-communautaire.edit', compact('localites', 'pageTitle', 'communauteSociale', 'dataLocalite', 'producteurs', 'producteursSelected'));
    }

    public function nonmembre($id)
    {
        $pageTitle = "Liste des Non Membres";
        $nonmembres = ActiviteCommunautaireNonMembre::dateFilter()->searchable(['nom', 'prenom'])->latest('id')->paginate(getPaginate());
        return view('manager.activite-communautaire.non-membre', compact('nonmembres', 'pageTitle', 'id'));
    }

    public function createnonmembre($id)
    {
        $pageTitle = "Ajouter un Non Membre";
        $manager   = auth()->user(); 
        $activite = ActiviteCommunautaire::find($id);
        $localiteIds = array_unique($activite->beneficiaires->pluck('localite_id')->toArray());
        $producteurs = Producteur::joinRelationship('localite.section')
        ->where([['cooperative_id', $manager->cooperative_id],['producteurs.status', 1]])->whereIn('localite_id', $localiteIds)->get();
        return view('manager.activite-communautaire.non-membrecreate', compact('pageTitle', 'id', 'producteurs', 'activite', 'localiteIds'));
    }

    public function storenonmembre(Request $request)
    {
        $validationRule = [
            'nom' => 'required',
            'prenom' => 'required',
            'sexe' => 'required',
            'telephone' => 'required',
        ];
        $request->validate($validationRule);
        if ($request->id) {
            $nonmembre = ActiviteCommunautaireNonMembre::find($request->id);
            $message = "Non Membre modifié avec succès .";
        } else {
            $nonmembre = new ActiviteCommunautaireNonMembre();
            $message = "Non Membre ajouté avec succès .";
        }
        $nonmembre->nom = $request->nom;
        $nonmembre->prenom = $request->prenom;
        $nonmembre->sexe = $request->sexe;
        $nonmembre->telephone = $request->telephone;
        $nonmembre->representer = $request->representer;
        $nonmembre->lien = $request->lien;
        $nonmembre->producteur_id = $request->producteur;
        $nonmembre->activite_communautaire_id = $request->activite_communautaire_id;
        $nonmembre->save();
        $notify[] = ['success', isset($message) ? $message : 'Non Membre ajouté avec succès.'];
        return back()->withNotify($notify);
    }
    public function editnonmembre($id)
    {
        $pageTitle = "Modifier un Non Membre";
        $manager = auth()->user();
        $nonmembre = ActiviteCommunautaireNonMembre::find($id);
        $activite = ActiviteCommunautaire::find($nonmembre->activite_communautaire_id);
        $localiteIds = array_unique($activite->beneficiaires->pluck('localite_id')->toArray());
        $producteurs = Producteur::joinRelationship('localite.section')
        ->where([['cooperative_id', $manager->cooperative_id],['producteurs.status', 1]])->whereIn('localite_id', $localiteIds)->get();
        return view('manager.activite-communautaire.non-membreedit', compact('pageTitle', 'nonmembre', 'producteurs', 'activite', 'localiteIds'));
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
    public function delete($id)
    { 
        ActiviteCommunautaire::where('id', decrypt($id))->delete();
        $notify[] = ['success', 'Le contenu supprimé avec succès'];
        return back()->withNotify($notify);
    }
}
