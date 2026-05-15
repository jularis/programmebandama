<?php

namespace App\Http\Controllers\Manager;

use App\Models\Menage;
use App\Models\Section;
use App\Models\Localite;
use App\Constants\Status;
use App\Models\Producteur;
use App\Models\Cooperative;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\Menage_ordure;
use App\Exports\ExportMenages;
use App\Rules\VlidateEnfantTotal;
use Illuminate\Support\Facades\DB;
use App\Rules\Enfants0A5PasExtrait;
use App\Http\Controllers\Controller;
use App\Models\Menage_sourceEnergie;
use App\Rules\Enfants6A17PasExtrait;
use App\Rules\NbreEnft6A17Scolarise;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\StoreMenageRequest;
use App\Rules\Enfants6A17Scolarise;

class MenageController extends Controller
{

    public function index()
    {
        $pageTitle = "Gestion des ménages";
        $manager = auth()->user();
        $cooperative = Cooperative::with('sections.localites')->find($manager->cooperative_id);
        $localites = $cooperative->sections->flatMap->localites->filter(function ($localite) {
            return $localite->active();
        });
        $menages = Menage::dateFilter()->searchable([
            "quartier", "boisChauffe",
            "separationMenage", "eauxToillette", "eauxVaisselle", "wc",
            "menages.sources_eaux", "type_machines", "garde_machines", "equipements",
            "traitementChamps", "activiteFemme", "nomActiviteFemme", "champFemme", "nombreHectareFemme"
        ])->latest('id')
            ->joinRelationship('producteur.localite.section')
            ->where('cooperative_id', $manager->cooperative_id)
            ->where(function ($q) {
                if (request()->localite != null) {
                    $q->where('localite_id', request()->localite);
                }
            })
            ->with(['producteur.localite', 'producteur.localite.section']) // Charger les relations "localite" et "section" des producteurs
            ->paginate(getPaginate());

        return view('manager.menage.index', compact('pageTitle', 'menages', 'localites'));
    }

    public function create()
    {
        $pageTitle = "Ajouter un ménage";
        $manager = auth()->user();
        $cooperative = Cooperative::with('sections.localites', 'sections.localites.section')->find($manager->cooperative_id);
        $sections = $cooperative->sections;
        $localites = $cooperative->sections->flatMap->localites->filter(function ($localite) {
            return $localite->active();
        });
        $producteurs = Producteur::joinRelationship('localite.section')
        ->where([['cooperative_id', $manager->cooperative_id],['producteurs.status', 1]])->with('localite')->get();

        return view('manager.menage.create', compact('pageTitle', 'producteurs', 'sections', 'localites'));
    }

    public function store(Request $request)
    {
        $localite = Localite::where('id', $request->localite)->first();

        if ($localite->status == Status::NO) {
            $notify[] = ['error', 'Cette localité est désactivé'];
            return back()->withNotify($notify)->withInput();
        }

        if ($request->id) {
            $menage = Menage::findOrFail($request->id);
            $rules = [
                'producteur_id'    => 'required|exists:producteurs,id',
                'quartier' => 'required|max:255',
                'ageEnfant0A5' => ['required', 'integer'],
                'ageEnfant6A17' => ['required', 'integer'],
                'enfantscolarises' => ['required', 'integer', new Enfants6A17Scolarise],
                'enfantsPasExtrait' => ['required', 'integer', new Enfants0A5PasExtrait],
                'enfantsPasExtrait6A17' => ['required', 'integer', new Enfants6A17PasExtrait],
                'separationMenage'  => 'required|max:255',
                'eauxToillette'  => 'required|max:255',
                'eauxVaisselle'  => 'required|max:255',
                'wc'  => 'required|max:255',
                'sources_eaux'  => 'required|max:255',
                'equipements'  => 'required|max:255',
                'traitementChamps'  => 'required|max:255',
                'activiteFemme'  => 'required|max:255',
                'nomApplicateur' => 'required_if:traitementChamps,==,non',
                'numeroApplicateur' => 'required_if:traitementChamps,non|regex:/^\d{10}$/|nullable|unique:menages,numeroApplicateur,' . $request->id,

            ];
            $attributes = [
                'producteur' => 'Producteur',

            ];
            $messages = [
                'producteur.required' => 'Le champ producteur est obligatoire',
                'quartier.required' => 'Le champ quartier est obligatoire',
                'ageEnfant0A5.required' => 'Le champ ageEnfant0A5 est obligatoire',
                'ageEnfant6A17.required' => 'Le champ ageEnfant6A17 est obligatoire',
                'enfantscolarises.required' => 'Le champ enfantscolarises est obligatoire',
                'enfantsPasExtrait.required' => 'Le champ enfantsPasExtrait est obligatoire',
                'separationMenage.required' => 'Le champ separationMenage est obligatoire',
                'eauxToillette.required' => 'Le champ eauxToillette est obligatoire',
                'eauxVaisselle.required' => 'Le champ eauxVaisselle est obligatoire',
                'wc.required' => 'Le champ wc est obligatoire',
                'sources_eaux.required' => 'Le champ sources_eaux est obligatoire',
                'garde_machines.required' => 'Le champ garde_machines est obligatoire',
                'equipements.required' => 'Le champ equipements est obligatoire',
                'traitementChamps.required' => 'Le champ traitementChamps est obligatoire',
                'activiteFemme.required' => 'Le champ activitFemme est obligatoire',
                'enfantscolarises.max' => 'Le nombre d\'enfants de 6 à 17 ans scolarisés ne peut pas être supérieur au nombre d\'enfants de 6 à 17 ans du ménage',

                'enfantsPasExtrait6A17.max' => 'Le nombre d\'enfants de 6 à 17 n\'ayant pas d\'extrait ne peut pas être supérieur au nombre d\'enfants de 6 à 17 ans du ménage',

                'enfantsPasExtrait.max' => 'Le nombre d\'enfants de 0 à 5 n\'ayant pas d\'extrait ne peut pas être supérieur au nombre d\'enfants de 0 à 5 ans du ménage',

            ];
            $this->validate($request, $rules, $messages, $attributes);
            $message = "Le menage a été mise à jour avec succès";
        } else {
            $menage = new Menage();
            $rules = [
                'producteur_id'    => 'required|exists:producteurs,id',
                'quartier' => 'required|max:255',
                'ageEnfant0A5' => ['required', 'integer'],
                'ageEnfant6A17' => ['required', 'integer'],
                'enfantscolarises' => ['required', 'integer', new Enfants6A17Scolarise],
                'enfantsPasExtrait' => ['required', 'integer', new Enfants0A5PasExtrait],
                'enfantsPasExtrait6A17' => ['required', 'integer', new Enfants6A17PasExtrait],
                'separationMenage'  => 'required|max:255',
                'eauxToillette'  => 'required|max:255',
                'eauxVaisselle'  => 'required|max:255',
                'wc'  => 'required|max:255',
                'sources_eaux'  => 'required|max:255',
                'equipements'  => 'required|max:255',
                'traitementChamps'  => 'required|max:255',
                'activiteFemme'  => 'required|max:255',
                'nomApplicateur' => 'required_if:traitementChamps,==,non',
                'numeroApplicateur' => 'required_if:traitementChamps,==,non|regex:/^\d{10}$/|nullable|unique:menages,numeroApplicateur',
            ];
            $attributes = [
                'producteur' => 'Producteur',

            ];
            $messages = [
                'producteur.required' => 'Le champ producteur est obligatoire',
                'quartier.required' => 'Le champ quartier est obligatoire',
                'ageEnfant0A5.required' => 'Le champ ageEnfant0A5 est obligatoire',
                'ageEnfant6A17.required' => 'Le champ ageEnfant6A17 est obligatoire',
                'enfantscolarises.required' => 'Le champ enfantscolarises est obligatoire',
                'enfantsPasExtrait.required' => 'Le champ enfantsPasExtrait est obligatoire',
                'separationMenage.required' => 'Le champ separationMenage est obligatoire',
                'eauxToillette.required' => 'Le champ eauxToillette est obligatoire',
                'eauxVaisselle.required' => 'Le champ eauxVaisselle est obligatoire',
                'wc.required' => 'Le champ wc est obligatoire',
                'sources_eaux.required' => 'Le champ sources_eaux est obligatoire',
                'garde_machines.required' => 'Le champ garde_machines est obligatoire',
                'equipements.required' => 'Le champ equipements est obligatoire',
                'traitementChamps.required' => 'Le champ traitementChamps est obligatoire',
                'activiteFemme.required' => 'Le champ activitFemme est obligatoire',
            ];
            $this->validate($request, $rules, $messages, $attributes);

            $message = "Le menage a été crée avec succès";
        }
       
        if ($menage->producteur_id != $request->producteur_id) {
            $hasMenage = Menage::where('producteur_id', $request->producteur_id)->exists();
            if ($hasMenage) {
                $notify[] = ['error', 'Ce producteur a déjà un menage enregistré'];
                return back()->withNotify($notify)->withInput();
            }
        }
        $menage->producteur_id  = $request->producteur_id;
        $menage->quartier  = $request->quartier;
        $menage->ageEnfant0A5  = $request->ageEnfant0A5;
        $menage->ageEnfant6A17  = $request->ageEnfant6A17;
        $menage->enfantscolarises  = $request->enfantscolarises;
        $menage->enfantsPasExtrait = $request->enfantsPasExtrait;
        $menage->enfantsPasExtrait6A17 = $request->enfantsPasExtrait6A17;
        $menage->boisChauffe     = $request->boisChauffe;
        $menage->separationMenage = $request->separationMenage;
        $menage->eauxToillette    = $request->eauxToillette;
        $menage->eauxVaisselle    = $request->eauxVaisselle;
        $menage->wc    = $request->wc;
        $menage->sources_eaux    = $request->sources_eaux;
        $menage->type_machines    = $request->type_machines;
        $menage->garde_machines    = $request->garde_machines;
        $menage->equipements    = $request->equipements;
        $menage->traitementChamps    = $request->traitementChamps;
        $menage->nomApplicateur   = $request->nomApplicateur;
        $menage->numeroApplicateur   = $request->numeroApplicateur;
        $menage->activiteFemme    = $request->activiteFemme;
        $menage->nomActiviteFemme    = $request->nomActiviteFemme;
        $menage->champFemme    = $request->champFemme;
        $menage->nombreHectareFemme    = $request->nombreHectareFemme;
        $menage->autreMachine    = $request->autreMachine;
        $menage->autreEndroit    = $request->autreEndroit;
        $menage->userid = auth()->user()->id;
        $menage->autreSourceEau   = $request->autreSourceEau;
        $menage->etatAutreMachine   = $request->etatAutreMachine;
        $menage->etatatomiseur   = $request->etatatomiseur;
        $menage->etatEpi  = $request->etatEpi;
        $menage->typeActivite = $request->typeActivite;
        $menage->nomActiviteAgricole = $request->nomActiviteAgricole;
        $menage->autreActiviteAgricole = $request->autreActiviteAgricole;
        $menage->nomActiviteNonAgricole = $request->nomActiviteNonAgricole;
        $menage->autreActiviteNonAgricole = $request->autreActiviteNonAgricole;
        $menage->capitalDemarrage = $request->capitalDemarrage;
        $menage->formation = $request->formation;
        $menage->dureeActivite = $request->dureeActivite;
        $menage->autreCapital = $request->autreCapital;
        $menage->entite = $request->entite;
        $menage->nombreHectareConjoint = $request->nombreHectareConjoint;
        // dd(json_encode($request->all()));
        //dd($request->all());
        $menage->save();
        if ($menage != null) {
            $id = $menage->id;
            $datas  = $data2 = [];
            if (($request->sourcesEnergie != null)) {
                Menage_sourceEnergie::where('menage_id', $id)->delete();
                $i = 0;
                foreach ($request->sourcesEnergie as $sourceEnergie) {
                    if (!empty($sourceEnergie)) {
                        $datas[] = [
                            'menage_id' => $id,
                            'source_energie' => $sourceEnergie,
                        ];
                    }

                    $i++;
                }
            }
            if (($request->ordureMenagere != null)) {
                Menage_ordure::where('menage_id', $id)->delete();
                foreach ($request->ordureMenagere as $data) {
                    //dd($ordureMenagere);

                    $data2[] = [
                        'menage_id' => $id,
                        'ordure_menagere' => $data,
                    ];
                }
            }

            Menage_sourceEnergie::insert($datas);
            Menage_ordure::insert($data2);
        }
        $notify[] = ['success', isset($message) ? $message : 'Le menage a été crée avec succès.'];
        return back()->withNotify($notify);
    }


    public function edit($id)
    {
        $pageTitle = "Mise à jour de le menage";
        $manager   = auth()->user();
        $cooperative = Cooperative::with('sections.localites', 'sections.localites.section')->find($manager->cooperative_id);
        $localites = $cooperative->sections->flatMap->localites->filter(function ($localite) {
            return $localite->active();
        });
        $producteurs  = Producteur::joinRelationship('localite.section')
        ->where([['cooperative_id', $manager->cooperative_id],['producteurs.status', 1]])->with('localite')->get();
        $sections = $cooperative->sections;
        $menage   = Menage::findOrFail($id);
        $ordures = $menage->menage_ordure->pluck('ordure_menagere')->toArray();
        $energies = $menage->menage_sourceEnergie->pluck('source_energie')->toArray();
        return view('manager.menage.edit', compact('pageTitle', 'localites', 'menage', 'producteurs', 'sections', 'ordures', 'energies'));
    }
    public function show($id)
    {
        $pageTitle = "Détails du menage";
        $manager   = auth()->user();
        $cooperative = Cooperative::with('sections.localites', 'sections.localites.section')->find($manager->cooperative_id);
        $localites = $cooperative->sections->flatMap->localites->filter(function ($localite) {
            return $localite->active();
        });
        $producteurs  = Producteur::joinRelationship('localite.section')
        ->where([['cooperative_id', $manager->cooperative_id],['producteurs.status', 1]])->with('localite')->get();
        $sections = $cooperative->sections;
        $menage   = Menage::findOrFail($id);
        $ordures = $menage->menage_ordure->pluck('ordure_menagere')->toArray();
        $energies = $menage->menage_sourceEnergie->pluck('source_energie')->toArray();
        return view('manager.menage.show', compact('pageTitle', 'localites', 'menage', 'producteurs', 'sections', 'ordures', 'energies'));
    }

    public function status($id)
    {
        return Menage::changeStatus($id);
    }

    public function exportExcel()
    {
        return (new ExportMenages())->download('menages.xlsx');
    }

    public function delete($id)
    { 
        Menage::where('id', decrypt($id))->delete();
        $notify[] = ['success', 'Le contenu supprimé avec succès'];
        return back()->withNotify($notify);
    }
}
