<?php

namespace App\Http\Controllers\Manager;

use Excel;
use App\Models\Campagne;
use App\Models\Section;
use App\Constants\Status;
use App\Models\Localite;
use App\Models\Producteur;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\Agroevaluation;
use App\Models\Agroespecesarbre;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\AgroevaluationEspece;
use Illuminate\Support\Facades\Hash;
use App\Models\Agroapprovisionnement;
use App\Models\AgrodistributionEspece;
use App\Models\AgroapprovisionnementEspece;
use App\Imports\AgroapprovisionnementImport;
use App\Models\AgroapprovisionnementSection;
use App\Exports\ExportAgroapprovisionnements;
use App\Models\AgroapprovisionnementSectionEspece;

class AgroapprovisionnementController extends Controller
{

    public function index()
    {
        $pageTitle      = "Gestion des approvisionnements";
        $manager   = auth()->user();
        $localites = Localite::joinRelationship('section')->where([['cooperative_id', $manager->cooperative_id], ['localites.status', 1]])->get();
        $approvisionnements = Agroapprovisionnement::dateFilter()->searchable([])->latest('id')->where('cooperative_id', $manager->cooperative_id)->where(function ($q) {
            if (request()->localite != null) {
                $q->where('localite_id', request()->localite);
            }
        })->with('cooperative')->paginate(getPaginate());

        return view('manager.approvisionnement.index', compact('pageTitle', 'approvisionnements', 'localites'));
    }
    public function section(Request $request)
    {
        $pageTitle      = "Gestion des approvisionnements de section";
        $manager   = auth()->user();
        $idapprov = decrypt($request->id);
        $localites = Localite::joinRelationship('section')->where([['cooperative_id', $manager->cooperative_id], ['localites.status', 1]])->get();
        $approvisionnements = AgroapprovisionnementSection::dateFilter()->joinRelationship('section')->searchable([])->latest('id')->where('agroapprovisionnement_id', $idapprov)->where(function ($q) {
            if (request()->localite != null) {
                $q->where('localite_id', request()->localite);
            }
        })->with('section', 'section.cooperative')->paginate(getPaginate());
        $total_section = $approvisionnements->sum('total');
        $approv = Agroapprovisionnement::where('id', $idapprov)->first();
        $total = $approv->total;
        return view('manager.approvisionnement.section', compact('pageTitle', 'approvisionnements', 'localites', 'total', 'total_section'));
    }
    public function create()
    {
        $pageTitle = "Ajouter un approvisionnement";
        $manager   = auth()->user();
        $especesarbres  = Agroespecesarbre::orderby('strate', 'asc')->orderby('nom', 'asc')->get();
        return view('manager.approvisionnement.create', compact('pageTitle', 'especesarbres'));
    }
    public function create_section(Request $request)
    {
        $pageTitle = "Ajouter un approvisionnement de section";
        $manager   = auth()->user();
        $sections = Section::where('cooperative_id', $manager->cooperative_id)->get();
        $campagne = Campagne::active()->where('cooperative_id', auth()->user()->cooperative_id)->first();
        $approvSect = $dataSection = array();
        $idapprov = decrypt($request->id);
        if ($campagne != null && $idapprov) {

            $agroaprov = Agroapprovisionnement::where([['id', $idapprov]])->first();
            if ($agroaprov != null) {
                $agroaprovEspece = AgroapprovisionnementEspece::where('agroapprovisionnement_id', $agroaprov->id)->get();
                $dataEspece = $dataQuantite = array();
                foreach ($agroaprovEspece as $data) {
                    $dataEspece[] = $data->agroespecesarbre_id;
                    $dataQuantite[$data->agroespecesarbre_id] = $data->total;
                }

                $approvSect = AgroapprovisionnementSection::joinRelationship('section')->where([['cooperative_id', $manager->cooperative_id]])->select('section_id')->get();
                if ($approvSect != null) {
                    foreach ($approvSect as $data2) {
                        $dataSection[] = $data2->section_id;
                    }
                }
            }
        }
        $especesarbres  = Agroespecesarbre::orderby('strate', 'asc')->orderby('nom', 'asc')->get();
        return view('manager.approvisionnement.create-section', compact('pageTitle', 'especesarbres', 'sections', 'dataEspece', 'dataQuantite', 'dataSection'));
    }


    public function store(Request $request)
    {
        $validationRule = [
            'especesarbre'            => 'required|array',
            'quantite'            => 'required|array',
        ];


        $request->validate($validationRule);

        if ($request->id) {
            $approvisionnement = Agroapprovisionnement::findOrFail($request->id);
            $message = "La approvisionnement a été mise à jour avec succès";
        } else {
            $approvisionnement = new Agroapprovisionnement();
        }
        $manager   = auth()->user();
        $campagne = Campagne::active()->where('cooperative_id', auth()->user()->cooperative_id)->first();

        // if(!$request->id) {
        //     $hasCooperative = Agroapprovisionnement::where([['cooperative_id', $manager->cooperative_id],['campagne_id', $campagne->id]])->exists();
        //     if ($hasCooperative) {
        //         $notify[] = ['error', 'Cette coopérative a déjà été approvisionnée pour cette campagne.'];
        //         return back()->withNotify($notify)->withInput();
        //     }
        // }

        $approvisionnement->campagne_id = $campagne->id;
        $approvisionnement->total = array_sum($request->quantite);
        $approvisionnement->cooperative_id = $manager->cooperative_id;

        if ($request->hasFile('bon_livraison')) {
            try {
                $approvisionnement->bon_livraison = $request->file('bon_livraison')->store('public/approvisionnements');
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Impossible de télécharger votre image'];
                return back()->withNotify($notify);
            }
        }
        $approvisionnement->save();

        $datas = [];
        $k = 0;
        $i = 0;

        if ($approvisionnement != null) {
            $id = $approvisionnement->id;
            if ($request->especesarbre != null) {
                AgroapprovisionnementEspece::where('agroapprovisionnement_id', $id)->delete();
                $quantite = $request->quantite;
                foreach ($request->especesarbre as $key => $data) {

                    $total = $quantite[$key];
                    if ($total != null) {
                        $datas[] = [
                            'agroapprovisionnement_id' => $id,
                            'agroespecesarbre_id' => $data,
                            'total' => $total,
                        ];
                        $i++;
                    } else {
                        $k++;
                    }
                }
                AgroapprovisionnementEspece::insert($datas);
            }
        }
        $notify[] = ['success', isset($message) ? $message : "$i nouveau(x) types d'arbres à ombrage ont été ajoutés."];

        return back()->withNotify($notify);
    }
    public function store_section(Request $request)
    {
        $validationRule = [
            'especesarbre'            => 'required|array',
            'quantite'            => 'required|array',
            'section' => 'required',
        ];


        $request->validate($validationRule);

        if ($request->id) {
            $approvisionnement = AgroapprovisionnementSection::findOrFail($request->id);
            $message = "La approvisionnement a été mise à jour avec succès";
        } else {
            $approvisionnement = new AgroapprovisionnementSection();
        }
        $manager   = auth()->user();
        $campagne = Campagne::active()->where('cooperative_id', auth()->user()->cooperative_id)->first();

        if (!$request->id) {
            $hasSection = AgroapprovisionnementSection::where([['section_id', $request->section], ['campagne_id', $campagne->id]])->exists();
            if ($hasSection) {
                $notify[] = ['error', 'Cette section a déjà été approvisionnée pour cette campagne.'];
                return back()->withNotify($notify)->withInput();
            }
        }

        $approvisionnement->campagne_id = $campagne->id;
        $approvisionnement->agroapprovisionnement_id = $request->agroapprovisionnement;
        $approvisionnement->total = array_sum($request->quantite);
        $approvisionnement->section_id = $request->section;

        if ($request->hasFile('bon_livraison')) {
            try {
                $approvisionnement->bon_livraison = $request->file('bon_livraison')->store('public/approvisionnements');
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Impossible de télécharger votre image'];
                return back()->withNotify($notify);
            }
        }
        $approvisionnement->save();

        $datas = [];
        $k = 0;
        $i = 0;

        if ($approvisionnement != null) {
            $id = $approvisionnement->id;
            if ($request->especesarbre != null) {
                AgroapprovisionnementSectionEspece::where('agroapprovisionnement_section_id', $id)->delete();
                $quantite = $request->quantite;
                foreach ($request->especesarbre as $key => $data) {

                    $total = $quantite[$key];
                    if ($total != null) {
                        $datas[] = [
                            'agroapprovisionnement_section_id' => $id,
                            'agroespecesarbre_id' => $data,
                            'total' => $total,
                        ];
                        $i++;
                    } else {
                        $k++;
                    }
                }
                AgroapprovisionnementSectionEspece::insert($datas);
            }
        }
        $notify[] = ['success', isset($message) ? $message : "$i nouveau(x) types d'arbres à ombrage ont été ajoutés."];

        return back()->withNotify($notify);
    }

    public function edit($id)
    {
        $pageTitle = "Mise à jour de la approvisionnement";

        $especesarbres  = Agroespecesarbre::orderby('strate', 'asc')->orderby('nom', 'asc')->get();
        $approvisionnement   = Agroapprovisionnement::find($id);
        return view('manager.approvisionnement.edit', compact('pageTitle', 'especesarbres', 'approvisionnement'));
    }
    public function show_section($id)
    {
        $pageTitle = "Détail de l'approvisionnement";

        $especesarbres  = Agroespecesarbre::orderby('strate', 'asc')->orderby('nom', 'asc')->get();
        $approvisionnement   = AgroapprovisionnementSection::find(decrypt($id));

        return view('manager.approvisionnement.show-section', compact('pageTitle', 'especesarbres', 'approvisionnement'));
    }
    public function edit_section(Request $request)
    {

        $pageTitle = "Modification de l'approvisionnement";

        $approvisionnement   = AgroapprovisionnementSection::find(decrypt($request->id));


        return view('manager.approvisionnement.edit-section', compact('pageTitle','approvisionnement'));
    }
    public function update_section(Request $request){
        // $validationRule = [
        //     'especesarbre'            => 'required|array',
        //     'quantite'            => 'required|array',
        // ];

        if ($request->id) {
            $approvisionnement = AgroapprovisionnementSection::findOrFail($request->id);
            $message = "La approvisionnement a été mise à jour avec succès";
        } else {
            $notify[] = ['error', 'Impossible de faire la mise à jour du contenu actuellement.'];
                return back()->withNotify($notify);
        }
        $manager   = auth()->user();
        $campagne = Campagne::active()->where('cooperative_id', auth()->user()->cooperative_id)->first();


        $approvisionnement->campagne_id = $campagne->id;
        $approvisionnement->agroapprovisionnement_id = $request->agroapprovisionnement;
        $approvisionnement->total = array_sum($request->quantite);
        $approvisionnement->section_id = $request->section;
        $approvisionnement->save();

        $datas = [];
        $k = 0;
        $i = 0;


            $id = $request->id;
            if ($request->especesarbre != null) {
                AgroapprovisionnementSectionEspece::where('agroapprovisionnement_section_id', $id)->delete();
                $quantite = $request->quantite;
                foreach ($request->especesarbre as $key => $data) {

                    $total = $quantite[$key];
                    if ($total != null) {
                        $datas[] = [
                            'agroapprovisionnement_section_id' => $id,
                            'agroespecesarbre_id' => $data,
                            'total' => $total,
                            'created_at' => now()
                        ];
                        $i++;
                    } else {
                        $k++;
                    }
                }
                AgroapprovisionnementSectionEspece::insert($datas);
            }

        $notify[] = ['success', isset($message) ? $message : "$i nouveau(x) types d'arbres à ombrage ont été mise à jour."];

        return back()->withNotify($notify);
    }

    public function status($id)
    {
        return Agroapprovisionnement::changeStatus($id);
    }

    public function exportExcel()
    {
        return (new ExportAgroapprovisionnements())->download('approvisionnements.xlsx');
    }

    public function  uploadContent(Request $request)
    {
        Excel::import(new AgroapprovisionnementImport, $request->file('uploaded_file'));
        return back();
    }
    public function delete($id)
    {
        Agroapprovisionnement::where('id', decrypt($id))->delete();
        $notify[] = ['success', 'Le contenu supprimé avec succès'];
        return back()->withNotify($notify);
    }
}
