<?php

namespace App\Http\Controllers\Manager;

use PDF;
use App\Models\Pays;
use App\Models\Product;
use App\Models\Section;
use App\Models\Countrie;
use App\Models\Localite;
use App\Constants\Status;
use App\Models\Programme;
use App\Models\Producteur;
use App\Models\Cooperative;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\Certification;
use App\Models\Producteur_info;
use Illuminate\Validation\Rule;
use App\Imports\ProducteurImport;
use App\Exports\ExportProducteurs;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ExportProducteursAll;
use App\Http\Requests\StoreInfoRequest;
use App\Imports\ProducteurUpdateImport;
use App\Models\Producteur_infos_mobile;
use App\Models\Producteur_certification;
use App\Models\Producteur_infos_typeculture;
use App\Http\Requests\StoreProducteurRequest;
use App\Http\Requests\UpdateProducteurRequest;
use App\Models\Producteur_infos_maladieenfant;
use Illuminate\Validation\ValidationException;
use App\Models\Producteur_infos_autresactivite;
use Google\Service\Dfareporting\Resource\Countries;

class ProducteurController extends Controller
{

    public function index()
    {
        $pageTitle      = "Gestion des producteurs";
        $manager   = auth()->user();
        $localites = Localite::joinRelationship('section')->where([['cooperative_id', $manager->cooperative_id]])->get();

        $programmes = Programme::all();

        $producteurs = Producteur::dateFilter()
            ->searchable(["nationalite", "type_piece", "codeProd", "codeProdapp", "producteurs.nom", "prenoms", "sexe", "dateNaiss", "phone1", "niveau_etude", "numPiece", "consentement", "statut", "certificat"])
            ->latest('id')
            ->joinRelationship('localite.section')
            ->when(request()->localite, function ($query, $localite) {
                $query->where('producteurs.localite_id', $localite);
            })
            ->when(request()->status, function ($query, $status){
                if($status==2) $status = 0;
                $query->where('producteurs.status', $status);
            })
            ->when(request()->etat, function ($query, $etat){
                $query->where('producteurs.statut', $etat);
            })
            ->when(request()->programme, function ($query, $programme){
                $query->where('producteurs.programme_id', $programme);
            })
            ->with('localite.section')
            ->where('producteurs.status', 1)
            ->where([['cooperative_id', $manager->cooperative_id]]);
            $producteursFiltre = $producteurs->get();
        $producteurs = $producteurs->paginate(getPaginate());
        $total_prod = $producteursFiltre->count();
        $total_prod_h = $producteursFiltre->where('sexe','Homme')->count();
        $total_prod_f = $producteursFiltre->where('sexe','Femme')->count();
        $total_prod_cert = $producteursFiltre->where('statut','Certifie')->count();
        $total_prod_cand = $producteursFiltre->where('statut','Candidat')->count();

        if (request()->download) {
            $producteur = Producteur::find(decrypt(request()->download));
            $producteurNameFile = Str::slug($producteur->nom . $producteur->prenoms . $producteur->codeProd, '-');
            $producteurNameFile = $producteurNameFile . '.pdf';
            if (!file_exists(storage_path() . "/app/public/producteurs-pdf")) {
                File::makeDirectory(storage_path() . "/app/public/producteurs-pdf", 0777, true);
            }
            @unlink(storage_path('app/public/producteurs-pdf') . "/" . $producteurNameFile);

            return PDF::loadView('manager.producteur.pdf-producteur', compact('producteur'))
                ->download($producteurNameFile);
            // ->save(storage_path(). "/app/public/producteurs-pdf/".$producteurNameFile);
        }
        return view('manager.producteur.index', compact('pageTitle', 'producteurs', 'localites', 'programmes','total_prod','total_prod_h','total_prod_f','total_prod_cert','total_prod_cand'));
    }

    public function infos($id)
    {

        $pageTitle = "Gestion des informations du producteur";
        $infosproducteurs = Producteur_info::all()->where('producteur_id', decrypt($id));

        return view('manager.producteur.infos', compact('pageTitle', 'infosproducteurs', 'id'));
    }

    public function editinfo($id)
    {

        $pageTitle      = "Gestion des informations du producteur";
        $infosproducteur = Producteur_info::findOrFail(decrypt($id));

        return view('manager.producteur.editinfo', compact('pageTitle', 'infosproducteur', 'id'));
    }

    public function storeinfo(StoreInfoRequest $request)
    {

        DB::beginTransaction();

        try {

            $request->validated();
            $manager = auth()->user();

            $producteur = Producteur::joinRelationship('localite.section')
                ->where([['cooperative_id', $manager->cooperative_id], ['producteurs.status', 1]])->where('producteurs.id', $request->producteur_id)->first();

            if ($producteur->status == Status::NO) {
                $notify[] = ['error', 'Ce producteur est désactivé'];
                return back()->withNotify($notify)->withInput();
            }

            if ($request->id) {
                $infoproducteur = Producteur_info::findOrFail($request->id);
                $message = "L'info du producteur a été mise à jour avec succès";
            } else {
                $infoproducteur = new Producteur_info();

                $hasInfoProd = Producteur_info::where('producteur_id', $request->producteur_id)->exists();

                if ($hasInfoProd) {
                    $notify[] = ['error', "L'info existe déjà pour ce producteur. Veuillez apporter des mises à jour."];
                    return back()->withNotify($notify);
                }
            }
            $infoproducteur->producteur_id = $request->producteur_id;
            $infoproducteur->foretsjachere  = $request->foretsjachere;
            $infoproducteur->superficie  = $request->superficie;
            $infoproducteur->autresCultures = $request->autresCultures;
            $infoproducteur->autreActivite = $request->autreActivite;
            $infoproducteur->travailleurs = $request->travailleurs;
            $infoproducteur->travailleurspermanents = $request->travailleurspermanents;
            $infoproducteur->travailleurstemporaires = $request->travailleurstemporaires;
            $infoproducteur->mobileMoney = $request->mobileMoney;
            $infoproducteur->compteBanque    = $request->compteBanque;
            $infoproducteur->nomBanque    = $request->nomBanque;
            $infoproducteur->autreBanque    = $request->autreBanque;
            $infoproducteur->mainOeuvreFamilial    = $request->mainOeuvreFamilial;
            $infoproducteur->travailleurFamilial    = $request->travailleurFamilial;
            $infoproducteur->societeTravail = $request->societeTravail;
            $infoproducteur->nombrePersonne = $request->nombrePersonne;
            $infoproducteur->userid = auth()->user()->id;
            // dd(json_encode($request->all()));
            $infoproducteur->save();

            if ($infoproducteur != null) {

                $id = $infoproducteur->id;

                if (($request->typeculture != null)) {

                    $verification   = Producteur_infos_typeculture::where('producteur_info_id', $id)->get();
                    if ($verification->count()) {
                        DB::table('producteur_infos_typecultures')->where('producteur_info_id', $id)->delete();
                    }
                    $i = 0;

                    foreach ($request->typeculture as $data) {
                        if ($data != null) {
                            DB::table('producteur_infos_typecultures')->insert(['producteur_info_id' => $id, 'typeculture' => $data, 'superficieculture' => $request->superficieculture[$i]]);
                        }
                        $i++;
                    }
                }
                if ($request->typeactivite != null) {
                    $verification   = Producteur_infos_autresactivite::where('producteur_info_id', $id)->get();
                    if ($verification->count()) {
                        DB::table('producteur_infos_autresactivites')->where('producteur_info_id', $id)->delete();
                    }
                    $i = 0;
                    foreach ($request->typeactivite as $data) {
                        if ($data != null) {
                            DB::table('producteur_infos_autresactivites')->insert(['producteur_info_id' => $id, 'typeactivite' => $data]);
                        }
                        $i++;
                    }
                }
                if ($request->operateurMM != null && $request->numeros != null) {
                    $verification   = Producteur_infos_mobile::where('producteur_info_id', $id)->get();
                    if ($verification->count()) {
                        DB::table('producteur_infos_mobiles')->where('producteur_info_id', $id)->delete();
                    }
                    $i = 0;
                    foreach ($request->operateurMM as $data) {
                        if ($data != null) {
                            DB::table('producteur_infos_mobiles')->insert(['producteur_info_id' => $id, 'operateur' => $data, 'numero' => $request->numeros[$i]]);
                        }
                        $i++;
                    }
                }
            }
        } catch (ValidationException $e) {
            DB::rollBack();
        }
        DB::commit();

        $notify[] = ['success', isset($message) ? $message : "L'info du producteur a été crée avec succès."];
        return back()->withNotify($notify);
    }
    public function showinfosproducteur($id)
    {
        $pageTitle = "Informations du producteur";
        $infosproducteur = Producteur_info::findOrFail(decrypt($id));
        return view('manager.producteur.show', compact('pageTitle', 'infosproducteur', 'id'));
    }
    public function showproducteur($id)
    {
        $pageTitle = "Informations du producteur";
        $manager = auth()->user();
        $producteur = Producteur::joinRelationship('localite.section')
            ->where([['cooperative_id', $manager->cooperative_id], ['producteurs.status', 1]])->findOrFail($id);
        $manager   = auth()->user();
        $sections = Section::with('cooperative')->where('cooperative_id', $manager->cooperative_id)->get();
        $localites = Localite::active()->with('section')->get();
        $countries = Pays::all();
        $programmes = Programme::all();
        $certificationAll = Certification::all();
        $certifications = $producteur->certifications->pluck('certification')->all();


        return view('manager.producteur.showproducteur', compact('pageTitle', 'producteur', 'id', 'localites', 'sections', 'certifications', 'certificationAll', 'countries', 'programmes'));
    }

    public function create()
    {
        $pageTitle = "Ajouter un producteur";
        $manager   = auth()->user();
        $sections = Section::where('cooperative_id', $manager->cooperative_id)->get();
        $localites = Localite::active()->with('section')->get();
        $programmes = Programme::all();
        $certifications = Certification::all();
        $countries = Pays::all();
        return view('manager.producteur.create', compact('pageTitle', 'localites', 'sections', 'programmes', 'certifications', 'countries'));
    }

    public function store(StoreProducteurRequest $request)
    {
        $request->validated();

        $localite = Localite::where('id', $request->localite_id)->first();

        if ($localite->status == Status::NO) {
            $notify[] = ['error', 'Cette localité est désactivé'];
            return back()->withNotify($notify)->withInput();
        }
        $producteur = new Producteur();
        $producteur->proprietaires = $request->proprietaires;
        $producteur->statutMatrimonial = $request->statutMatrimonial;
        $producteur->programme_id = $request->programme_id;
        $producteur->localite_id = $request->localite_id;
        $producteur->habitationProducteur = $request->habitationProducteur;
        $producteur->autreMembre = $request->autreMembre;
        $producteur->autrePhone = $request->autrePhone;
        $producteur->numPiece = $request->numPiece;
        $producteur->num_ccc = $request->num_ccc;
        $producteur->carteCMU = $request->carteCMU;
        $producteur->carteCMUDispo = $request->carteCMUDispo;
        $producteur->typeCarteSecuriteSociale = $request->typeCarteSecuriteSociale;
        $producteur->numSecuriteSociale = $request->numSecuriteSociale;
        $producteur->numCMU = $request->numCMU;
        $producteur->anneeDemarrage = $request->anneeDemarrage;
        $producteur->anneeFin = $request->anneeFin;
        $producteur->autreCertificats = $request->autreCertificats;
        $producteur->consentement  = $request->consentement;
        $producteur->statut  = $request->statut;
        $producteur->certificat     = $request->certificat;
        $producteur->nom = $request->nom;
        $producteur->prenoms    = $request->prenoms;
        $producteur->sexe    = $request->sexe;
        $producteur->nationalite    = $request->nationalite;
        $producteur->dateNaiss    = $request->dateNaiss;
        $producteur->phone1    = $request->phone1;
        $producteur->phone2    = $request->phone2;
        $producteur->niveau_etude    = $request->niveau_etude;
        $producteur->type_piece    = $request->type_piece;
        $producteur->numPiece    = $request->numPiece;
        $producteur->userid = auth()->user()->id;
        $producteur->codeProd = $request->codeProd;
        $producteur->plantePartage = $request->plantePartage;
        $producteur->numeroAssocie = $request->numeroAssocie;
        if ($request->hasFile('picture')) {
            try {
                $producteur->picture = $request->file('picture')->store('public/producteurs/photos');
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Impossible de télécharger votre image'];
                return back()->withNotify($notify);
            }
        }
        $coop = DB::table('sections as s')->join('cooperatives as c', 's.cooperative_id', '=', 'c.id')->join('localites as l', 's.id', '=', 'l.section_id')->where('l.id', $request->localite_id)->select('c.codeApp')->first();
        if ($coop != null) {
            $producteur->codeProdapp = $this->generecodeProdApp($request->nom, $request->prenoms, $coop->codeApp);
        } else {
            $producteur->codeProdapp = null;
        }
        // dd(json_encode($request->all()));
        $producteur->save();

        if ($producteur != null) {
            $id = $producteur->id;
            if ($producteur != null) {
                $id = $producteur->id;
                $datas  = [];
                if ($request->certificats != null) {
                    $certificats = array_filter($request->certificats);
                    if (!empty($certificats)) {
                        Producteur_certification::where('producteur_id', $id)->delete();
                        foreach ($certificats as $certificat) {
                            if ($certificat == 'Autre') {
                                $autre = new Certification();
                                $autre->nom = $request->autreCertificats;
                                $autre->save();
                                $certificat = $request->autreCertificats;
                            }
                            $datas[] = [
                                'producteur_id' => $id,
                                'certification' => $certificat,
                            ];
                        }
                        Producteur_certification::insert($datas);
                    }
                }
            }
        }

        $notify[] = ['success', isset($message) ? $message : 'Le producteur a été crée avec succès.'];
        return back()->withNotify($notify);
    }
    public function update(Request $request, $id)
    {
        $producteur = Producteur::findOrFail($id);
        $validationRule = [
            'programme_id' => ['required', 'exists:programmes,id'],
            'proprietaires' => 'required',
            'certificats' => 'required',
            'habitationProducteur' => 'required',
            'statut' => 'required',
            'statutMatrimonial' => 'required',
            'localite_id'    => 'required|exists:localites,id',
            'nom' => 'required|max:255',
            'prenoms'  => 'required|max:255',
            'sexe'  => 'required|max:255',
            'nationalite'  => 'required|max:255',
            'dateNaiss'  => 'required|max:255',
            'phone1'  => 'required',
            // 'phone1'  => ['required', 'regex:/^\d{10}$/', 'unique:producteurs,phone1,' . $request->id],
            'niveau_etude'  => 'required|max:255',
            'type_piece'  => 'required|max:255',
            'num_ccc' => ['nullable', 'regex:/^\d{11}$/', 'unique:producteurs,num_ccc,' . $request->id],
            'anneeDemarrage' => 'required_if:proprietaires,==,Garantie',
            'anneeFin' => 'required_if:proprietaires,==,Garantie',
            'plantePartage' => 'required_if:proprietaires,==,Planté-partager',
            'typeCarteSecuriteSociale' => 'required',
            'autreCertificats' => 'required_if:certificats,==,Autre',
            'codeProd' => 'required_if:statut,==,Certifie',
            'certificat' => 'required_if:statut,==,Certifie',
            'autrePhone' => 'required_if:autreMembre,==,oui',
            'phone2' => Rule::when($request->autreMembre == 'oui', function () use ($id) {
                // return ['required', 'regex:/^\d{10}$/', Rule::unique('producteurs', 'phone2')->ignore($id)];
                return ['required'];
            }),
            'numeroAssocie' => Rule::when($this->proprietaires == 'Planté-partager', function () {
                return ['required', 'regex:/^\d{10}$/'];
            }),
            //'phone2' => 'required_if:autreMembre,oui|regex:/^\d{10}$/|unique:producteurs,phone2,' . $request->id,
        ];
        $messages = [
            'programme_id.required' => 'Le programme est obligatoire',
            'proprietaires.required' => 'Le type de propriétaire est obligatoire',
            'certificats.required' => 'Le type de certificat est obligatoire',
            'habitationProducteur.required' => 'Le type d\'habitation est obligatoire',
            'statut.required' => 'Le statut est obligatoire',
            'statutMatrimonial.required' => 'Le statut matrimonial est obligatoire',
            'localite_id.required' => 'La localité est obligatoire',
            'nom.required' => 'Le nom est obligatoire',
            'prenoms.required' => 'Le prénom est obligatoire',
            'sexe.required' => 'Le sexe est obligatoire',
            'nationalite.required' => 'La nationalité est obligatoire',
            'dateNaiss.required' => 'La date de naissance est obligatoire',
            'phone1.required' => 'Le numéro de téléphone est obligatoire',
            // 'phone1.regex' => 'Le numéro de téléphone doit contenir exactement 10 chiffres.',
            // 'phone1.unique' => 'Ce numéro de téléphone est déjà utilisé.',
            'niveau_etude.required' => 'Le niveau d\'étude est obligatoire',
            'type_piece.required' => 'Le type de pièce est obligatoire',
            'numPiece.required' => 'Le numéro de pièce est obligatoire',
            'anneeDemarrage.required_if' => 'L\'année de démarrage est obligatoire',
            'anneeFin.required_if' => 'L\'année de fin est obligatoire',
            'plantePartage.required_if' => 'Le type de plante est obligatoire',
            'typeCarteSecuriteSociale.required' => 'Le type de carte de sécurité sociale est obligatoire',
            'autreCertificats.required_if' => 'Le type de certificat est obligatoire',
            'codeProdapp.required_if' => 'Le code Prodapp est obligatoire',
            'certificat.required_if' => 'Le certificat est obligatoire',
            'phone2.required_if' => 'Le numéro de téléphone est obligatoire',
            // 'phone2.regex' => 'Le numéro de téléphone doit contenir exactement 10 chiffres.',
            // 'phone2.unique' => 'Ce numéro de téléphone est déjà utilisé.',
            'autrePhone.required_if' => 'Le champ membre de famille est obligatoire',
            'num_ccc.regex' => 'numéro du conseil café cacao doit contenir 11 chiffres',
        ];
        $request->validate($validationRule, $messages);

        $producteur->proprietaires = $request->proprietaires;
        $producteur->statutMatrimonial = $request->statutMatrimonial;
        $producteur->programme_id = $request->programme_id;
        $producteur->localite_id = $request->localite_id;
        $producteur->habitationProducteur = $request->habitationProducteur;
        $producteur->autreMembre = $request->autreMembre;
        $producteur->autrePhone = $request->autrePhone;
        $producteur->numPiece = $request->numPiece;
        $producteur->num_ccc = $request->num_ccc;
        $producteur->carteCMU = $request->carteCMU;
        $producteur->typeCarteSecuriteSociale = $request->typeCarteSecuriteSociale;
        $producteur->numSecuriteSociale = $request->numSecuriteSociale;
        $producteur->numCMU = $request->numCMU;
        $producteur->carteCMUDispo = $request->carteCMUDispo;
        $producteur->anneeDemarrage = $request->anneeDemarrage;
        $producteur->anneeFin = $request->anneeFin;
        $producteur->autreCertificats = $request->autreCertificats;
        $producteur->consentement  = $request->consentement;
        $producteur->statut  = $request->statut;
        $producteur->certificat     = $request->certificat;
        $producteur->nom = $request->nom;
        $producteur->prenoms    = $request->prenoms;
        $producteur->sexe    = $request->sexe;
        $producteur->nationalite    = $request->nationalite;
        $producteur->dateNaiss    = $request->dateNaiss;
        $producteur->phone1    = $request->phone1;
        $producteur->phone2    = $request->phone2;
        $producteur->niveau_etude    = $request->niveau_etude;
        $producteur->type_piece    = $request->type_piece;
        $producteur->numPiece    = $request->numPiece;
        $producteur->userid = auth()->user()->id;
        $producteur->codeProd = $request->codeProd;
        $producteur->plantePartage = $request->plantePartage;
        $producteur->numeroAssocie = $request->numeroAssocie;
        if ($request->hasFile('picture')) {
            try {
                $producteur->picture = $request->file('picture')->store('public/producteurs/photos');
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Impossible de télécharger votre image'];
                return back()->withNotify($notify);
            }
        }

        if ($producteur->codeProdapp == null) {
            $coop = DB::table('localites as l')->join('cooperatives as c', 'l.cooperative_id', '=', 'c.id')->where('l.id', $request->localite)->select('c.codeApp')->first();
            if ($coop != null) {
                $producteur->codeProdapp = $this->generecodeProdApp($request->nom, $request->prenoms, $coop->codeApp);
            } else {
                $producteur->codeProdapp = null;
            }
        }
        // dd(json_encode($request->all()));
        $producteur->save();
        if ($producteur != null) {
            $id = $producteur->id;
            $datas  = [];
            if ($request->certificats != null) {
                $certificats = array_filter($request->certificats);
                if (!empty($certificats)) {
                    Producteur_certification::where('producteur_id', $id)->delete();
                    foreach ($certificats as $certificat) {
                        if ($certificat == 'Autre') {
                            $autre = new Certification();
                            $autre->nom = $request->autreCertificats;
                            $autre->save();
                            $certificat = $request->autreCertificats;
                        }
                        $datas[] = [
                            'producteur_id' => $id,
                            'certification' => $certificat,
                        ];
                    }
                    Producteur_certification::insert($datas);
                }
            }
        }
        $notify[] = ['success', isset($message) ? $message : 'Le producteur a été mise à jour avec succès.'];
        return back()->withNotify($notify);
    }





    public function edit($id)
    {
        $pageTitle = "Mise à jour de la producteur";
        $manager   = auth()->user();
        $sections = Section::with('cooperative')->where('cooperative_id', $manager->cooperative_id)->get();
        $localites = Localite::active()->with('section')->get();
        $countries = Pays::all();
        $programmes = Programme::all();
        $producteur   = Producteur::findOrFail($id);
        $certificationAll = Certification::all();
        $certifications = $producteur->certifications->pluck('certification')->all();
        return view('manager.producteur.edit', compact('pageTitle', 'localites', 'producteur', 'programmes', 'sections', 'certifications', 'certificationAll', 'countries'));
    }

    private function generecodeProdApp($nom, $prenoms, $codeApp)
    {
        $action = 'non';

        $data = Producteur::select('codeProdapp')->join('localites as l', 'producteurs.localite_id', '=', 'l.id')->join('sections as s', 'l.section_id', '=', 's.id')->join('cooperatives as c', 's.cooperative_id', '=', 'c.id')->where([
            ['codeProdapp', '!=', null], ['codeApp', $codeApp]
        ])->orderby('producteurs.id', 'desc')->first();

        if ($data != null) {

            $code = $data->codeProdapp;
            if ($code != null) {
                $chaine_number = Str::afterLast($code, '-');
            } else {
                $chaine_number = 0;
            }
        } else {
            $chaine_number = 0;
        }

        $lastCode = $chaine_number + 1;
        $codeP = $codeApp . '-' . gmdate('Y') . '-' . $lastCode;

        do {

            $verif = Producteur::select('codeProdapp')->where('codeProdapp', $codeP)->orderby('id', 'desc')->first();
            if ($verif == null) {
                $action = 'non';
            } else {
                $action = 'oui';
                $code = $codeP;
                $chaine_number = Str::afterLast($code, '-');
                $lastCode = $chaine_number + 1;
                $codeP = $codeApp . '-' . gmdate('Y') . '-' . $lastCode;
            }
        } while ($action != 'non');

        return $codeP;
    }

    public function status($id)
    {
        return Producteur::changeStatus($id);
    }

    public function localiteManager($id)
    {
        $localite         = Localite::findOrFail($id);
        $pageTitle      = $localite->name . " Manager List";
        $localiteProducteurs = Producteur::producteur()->where('localite_id', $id)->orderBy('id', 'DESC')->with('localite')->paginate(getPaginate());
        return view('manager.producteur.index', compact('pageTitle', 'localiteProducteurs'));
    }
    public function exportExcel()
    {
        $filename = 'producteurs-' . gmdate('dmYhms') . '.xlsx';
        return Excel::download(new ExportProducteurs, $filename);
    }

    public function exportExcelAllList()
    {
        $filename = 'producteurs-all-liste-' . gmdate('dmYhms') . '.xlsx';
        return Excel::download(new ExportProducteursAll, $filename);
    }
    public function  uploadContent(Request $request)
    {
        Excel::import(new ProducteurImport, $request->file('uploaded_file'));
        return back();
    }
    public function  updateUploadContent(Request $request)
    {
        Excel::import(new ProducteurUpdateImport, $request->file('uploaded_file_update'));
        return back();
    }
    public function delete($id)
    {
        Producteur::where('id', decrypt($id))->delete();
        $notify[] = ['success', 'Le contenu supprimé avec succès'];
        return back()->withNotify($notify);
    }
}
