<?php

namespace App\Http\Controllers\Manager;

use App\Models\User;
use App\Models\Section;
use App\Models\Campagne;
use App\Models\Parcelle;
use App\Models\Remorque;
use App\Models\Vehicule;
use App\Constants\Status;
use App\Models\Entreprise;
use App\Models\Producteur;
use App\Models\Cooperative;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\Models\Transporteur;
use Illuminate\Http\Request;
use App\Models\Connaissement;
use App\Models\LivraisonInfo;
use App\Models\FormateurStaff;
use App\Models\LivraisonPrime;
use App\Models\MagasinCentral;
use App\Models\MagasinSection;
use App\Models\CampagnePeriode;
use App\Models\LivraisonScelle;
use App\Models\LivraisonPayment;
use App\Models\LivraisonProduct;
use App\Exports\ExportLivraisons;
use App\Models\AdminNotification;
use Illuminate\Support\Facades\DB;
use App\Models\StockMagasinCentral;
use App\Models\StockMagasinSection;
use App\Http\Controllers\Controller;
use App\Models\ConnaissementProduit;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\SuiviConnaissementUsine;
use App\Exports\ExportStockMagasinCentral;
use App\Models\LivraisonMagasinCentralProducteur;

class LivraisonCentraleController extends Controller
{

    public function index()
    {
        $staff = auth()->user();
        $livraisonProd = LivraisonMagasinCentralProducteur::dateFilter()->joinRelationship('stockMagasinCentral')->with('stockMagasinCentral', 'campagne', 'producteur', 'campagnePeriode')
            ->where('cooperative_id', $staff->cooperative_id)
            ->when(request()->code, function ($query, $code) {
                $query->where('numero_connaissement', $code);
            })
            ->when(request()->magasin, function ($query, $magasin) {
                $query->where('stock_magasin_central_id', $magasin);
            })
            ->when(request()->produit, function ($query, $produit) {
                $query->whereIn('livraison_magasin_central_producteurs.type_produit', json_decode($produit));
            })
            ->when(request()->producteur, function ($query, $producteur) {
                $query->where('producteur_id', $producteur);
            })
            ->select('livraison_magasin_central_producteurs.*')
            ->orderBy('livraison_magasin_central_producteurs.id', 'desc')
            ->paginate(getPaginate());

        $total = $livraisonProd->sum('quantite');
        $pageTitle    = "Livraison des Magasins de Section ($total)";
        $magasins  = MagasinCentral::where('cooperative_id', $staff->cooperative_id)->get();
        $sections = Section::get();
        return view('manager.livraison-centrale.index', compact('pageTitle', 'livraisonProd', 'total', 'sections', 'magasins'));
    }

    public function stock()
    {

        $staff = auth()->user();
        $stocks = StockMagasinCentral::dateFilter()->where([['cooperative_id', $staff->cooperative_id]])
            ->when(request()->magasin, function ($query, $magasin) {
                $query->where('magasin_section_id', $magasin);
            })
            ->orderBy('stock_magasin_centraux.id', 'desc')
            ->with('cooperative', 'vehicule', 'transporteur', 'campagne', 'magasinCentral', 'magasinSection', 'campagnePeriode', 'vehicule.marque')
            ->paginate(getPaginate());

        $total = $stocks->sum('stocks_mag_entrant');
        $pageTitle    = "Stock des Magasins Centraux (" . showAmount($total) . ") Kg";
        $magasins  = MagasinCentral::where('cooperative_id', $staff->cooperative_id)->with('cooperative')->get();
        $sections = Section::get();
        $allcampagnes  = Campagne::where('cooperative_id', $staff->cooperative_id)->get();
        $allperiodes  = CampagnePeriode::get();
        return view('manager.livraison-centrale.stock', compact('pageTitle', 'stocks', 'total', 'sections', 'magasins','allcampagnes','allperiodes'));
    }

    public function connaissement()
    {

        $staff = auth()->user();
        $stocks = Connaissement::dateFilter()->where([['cooperative_id', $staff->cooperative_id]])
            ->when(request()->magasin, function ($query, $magasin) {
                $query->where('magasin_centraux_id', $magasin);
            })
            ->with('cooperative', 'vehicule', 'transporteur', 'campagne', 'magasinCentral', 'campagnePeriode', 'vehicule.marque')
            ->orderBy('connaissements.id', 'desc')
            ->paginate(getPaginate());

        $total = $stocks->sum('quantite_livre');
        $pageTitle    = "Connaissements Usine (" . showAmount($total) . ") Kg";
        $magasins  = MagasinCentral::where('cooperative_id', $staff->cooperative_id)->with('cooperative')->get();
        $sections = Section::get();
        return view('manager.livraison-centrale.connaissement', compact('pageTitle', 'stocks', 'total', 'sections', 'magasins'));
    }
    public function suiviLivraison($id)
    {
        $staff = auth()->user();
        $livraison = Connaissement::where('id', decrypt($id))->first();
        $pageTitle    = "Suivi de la livraison à l'Usine";
        $id = decrypt($id);
        $suivi = SuiviConnaissementUsine::where('connaissement_id', $id)->first();
        return view('manager.livraison-centrale.suivi', compact('pageTitle', 'livraison', 'id', 'suivi'));
    }
    public function prime()
    {

        $staff = auth()->user();
        $stocks = LivraisonPrime::dateFilter()->joinRelationship('livraisonInfo', 'parcelle')
            ->join('parcelles', 'livraison_primes.parcelle_id', '=', 'parcelles.id')
            ->where([['sender_cooperative_id', $staff->cooperative_id]])
            ->when(request()->magasin, function ($query, $magasin) {
                $query->where('magasin_section_id', $magasin);
            })
            ->with('livraisonInfo', 'parcelle', 'campagnePeriode', 'campagne', 'parcelle.producteur')
            ->groupBy('producteur_id', 'campagne_id', 'campagne_periode_id')
            ->select('livraison_primes.*', 'parcelles.producteur_id', DB::raw('SUM(quantite) as qty,SUM(montant) as somme'))
            ->orderBy('livraison_primes.id', 'desc')
            ->paginate(getPaginate());

        $total = $stocks->sum('montant');
        $pageTitle    = "Prime des producteurs (" . showAmount($total) . ") FCFA";
        $magasins  = MagasinSection::joinRelationship('section')->where([['cooperative_id', $staff->cooperative_id]])->with('user')->orderBy('nom')->get();
        $sections = Section::get();
        return view('manager.livraison-centrale.prime', compact('pageTitle', 'stocks', 'total', 'sections', 'magasins'));
    }

    public function create()
    {
        $staff = auth()->user();
        
        $cooperative = Cooperative::where([['id', $staff->cooperative_id]])->first();
         
        $cooperatives = Cooperative::active()->orderBy('name')->get();
        $magCentraux = MagasinCentral::where([['cooperative_id', $staff->cooperative_id]])->with('user')->orderBy('nom')->get();
        $magSections = MagasinSection::joinRelationship('section')->where([['cooperative_id', $staff->cooperative_id]])->with('user')->orderBy('nom')->get();

        $transporteurs = Transporteur::where([['cooperative_id', $staff->cooperative_id]])->with('cooperative', 'entreprise')->get();
        $vehicules = Vehicule::where('cooperative_id', $staff->cooperative_id)->with('marque')->get();
        $remorques = Remorque::where('cooperative_id', $staff->cooperative_id)->get();
        $producteurs  = Producteur::joinRelationship('localite.section')->where([['sections.cooperative_id', $staff->cooperative_id],['producteurs.status',1]])->select('producteurs.*')->orderBy('producteurs.nom')->get();

        $campagne = Campagne::active()->first() ?? null;
        if($campagne != null)
        {
        $nomCamp = $campagne->nom;
        $campagne = CampagnePeriode::where([['campagne_id', $campagne->id], ['periode_debut', '<=', gmdate('Y-m-d')], ['periode_fin', '>=', gmdate('Y-m-d')]])->latest()->first();
 
        $codeCoop = $cooperative->codeCoop ?? null;
 
        $code = $codeCoop ?? null . '-' . Str::before(Str::after($nomCamp, 'Campagne '), '-') . '-2-';

        }else{
            $code = null;
        }
        $lastnumber = Connaissement::where([['cooperative_id', $staff->cooperative_id]])->latest()->first();
        
        if ($lastnumber != null) {
            $lastnumber = preg_replace('/\D/', '', $lastnumber->numeroCU);
            // dd($lastnumber);
            // $lastnumber = Str::afterLast($lastnumber->numeroCU,'SC'); 
            // $lastnumber = Str::afterLast($lastnumber->numeroCU, '-');
            $lastnumber = $lastnumber + 1;
        } else {
            $lastnumber = 1;
        }
        $parcelles  = Parcelle::with('producteur')->get();
        $pageTitle = 'Connaissement vers Usine';
        $entreprises = Entreprise::where('cooperative_id', $staff->cooperative_id)->pluck('nom_entreprise', 'id');
        $formateurs = FormateurStaff::with('entreprise')->get();
        $allcampagnes  = Campagne::where('cooperative_id', $staff->cooperative_id)->get();
        $allperiodes  = CampagnePeriode::get();

        return view('manager.livraison-centrale.create', compact('pageTitle', 'cooperatives', 'magSections', 'magCentraux', 'producteurs', 'transporteurs', 'parcelles', 'campagne', 'vehicules', 'code', 'entreprises', 'formateurs', 'lastnumber', 'remorques','allcampagnes','allperiodes'));
    }
    private function generecodeConnais()
    {


        $data = Connaissement::orderby('id', 'desc')->limit(1)->get();

        if (count($data) > 0) {
            $code = $data[0]->numeroCU;
            $chaine_number = Str::afterLast($code, '-');
            if ($chaine_number < 10) {
                $zero = "00000";
            } else if ($chaine_number < 100) {
                $zero = "0000";
            } else if ($chaine_number < 1000) {
                $zero = "000";
            } else if ($chaine_number < 10000) {
                $zero = "00";
            } else if ($chaine_number < 100000) {
                $zero = "0";
            } else {
                $zero = "";
            }
        } else {
            $zero = "00000";
            $chaine_number = 0;
        }
        if (!$chaine_number) $chaine_number = 0;
        $sub = 'NC-';
        $lastCode = $chaine_number + 1;
        $codeLiv = $sub . $zero . $lastCode;

        return $codeLiv;
    }
    public function store(Request $request)
    {
        // dd(response()->json($request));

        $request->validate([
            'magasin_central' => 'required',
            'sender_transporteur' =>  'required',
            'sender_vehicule' =>  'required',
            'type' => 'required',
            'estimate_date'    => 'required|date|date_format:Y-m-d',
        ]);

        $manager = auth()->user();
        $livraison = new Connaissement();
        $campagne = Campagne::active()->first();
        $periode = CampagnePeriode::where([['campagne_id', $campagne->id], ['periode_debut', '<=', gmdate('Y-m-d')], ['periode_fin', '>=', gmdate('Y-m-d')]])->latest()->first();

        $livraison->cooperative_id   = $manager->cooperative_id;
        $livraison->campagne_id    = $request->campagne;
        $livraison->campagne_periode_id = $request->periode;
        $livraison->magasin_centraux_id = $request->magasin_central;
        $livraison->numeroCU = $request->code . $request->lastcode;
        $livraison->type_produit = $request->type;
        $livraison->quantite_livre = $request->poidsnet;
        $livraison->sacs_livre = $request->nombresacs;
        $livraison->transporteur_id = $request->sender_transporteur;
        $livraison->vehicule_id = $request->sender_vehicule;
        $livraison->remorque_id = $request->sender_remorque;
        $livraison->numeroCMC = json_encode($request->connaissement_id);
        $livraison->date_livraison = $request->estimate_date;

        $livraison->save();

        $i = 0;
        $data = [];
        $quantite = $request->quantite;
        $typeproduit = $request->type;
        $producteurs = $request->producteurs;
        $certificat = $request->certificat;
        $parcelle = $request->parcelle;
        $stock_magasin_central = $request->stock_magasin_central;
        foreach ($producteurs as $item) {

            if ($quantite[$i] > 0) {
                $data[] = [
                    'connaissement_id' => $livraison->id,
                    'producteur_id' => $item,
                    'campagne_id' => $request->campagne,
                    'campagne_periode_id' => $request->periode,
                    'quantite' => $quantite[$i],
                    'stock_magasin_central_id' => $stock_magasin_central[$i],
                    'type_produit' => $typeproduit,
                    'certificat' => $certificat,
                    'parcelle_id' => $parcelle[$i],
                    'created_at'      => now(),
                ];
                $prod = LivraisonMagasinCentralProducteur::where([['campagne_id', $request->campagne], ['stock_magasin_central_id', $stock_magasin_central[$i]], ['producteur_id', $item], ['type_produit', $typeproduit]])->first();
                if ($prod != null) {

                    $prod->quantite = $prod->quantite - $quantite[$i];
                    $prod->quantite_sortant = $prod->quantite_sortant + $quantite[$i];

                    $prod->save();
                }

                $stockCent = StockMagasinCentral::where('id', $stock_magasin_central[$i])->first();
                if ($stockCent != null) {

                    $stockCent->stocks_mag_entrant = $stockCent->stocks_mag_entrant - $quantite[$i];
                    $stockCent->stocks_mag_sortant = $stockCent->stocks_mag_sortant + $quantite[$i];

                    $stockCent->save();
                }
            }
            $i++;
        }


        ConnaissementProduit::insert($data);

        $notify[] = ['success', 'Le connaissement vers l\'Usine a été ajouté avec succès'];
        return to_route('manager.livraison.usine.connaissement')->withNotify($notify);
    }

    public function getProducteur()
    {
        $input = request()->all();


        $campagne = Campagne::active()->first();
        $periode = CampagnePeriode::where([['campagne_id', $campagne->id]])->latest()->first();
        $contents = '';
        if (request()->type || request()->magasin_central || request()->certificat) {
            $stocks = LivraisonMagasinCentralProducteur::joinRelationship('stockMagasinCentral')->where([['livraison_magasin_central_producteurs.campagne_id', $input['campagne']], ['stocks_mag_entrant', '>', 0], ['quantite', '>', 0]])
            ->when(request()->magasin_central, function ($query, $magasin_central) {
                $query->where('stock_magasin_centraux.magasin_centraux_id', $magasin_central);
            })
            ->when(request()->type, function ($query, $type) {
                $query->where('livraison_magasin_central_producteurs.type_produit', $type);
            })
            ->when(request()->certificat, function ($query, $certificat) {
                $query->where('livraison_magasin_central_producteurs.certificat', $certificat);
            })
            ->select('stock_magasin_centraux.*')
            ->groupBy('numero_connaissement')->get();
            if ($stocks->count()) {
                foreach ($stocks as $data) {

                    $contents .= '<option value="' . $data->id . '">' . $data->numero_connaissement . '</option>';
                }
            }
        }

        return $contents;
    }

    public function getListeProducteurConnaiss()
    {

        $results = '';
        $total = 0;
        $totalsacs = 0;
        $campagne = Campagne::active()->first();
        if(request()->connaissement_id) {
            $stock = LivraisonMagasinCentralProducteur::joinRelationship('stockMagasinCentral')->where([['livraison_magasin_central_producteurs.campagne_id', request()->campagne], ['stocks_mag_entrant', '>', 0], ['quantite', '>', 0]])
            ->when(request()->magasin_central, function ($query, $magasin_central) {
                $query->where('stock_magasin_centraux.magasin_centraux_id', $magasin_central);
            })
            ->when(request()->type, function ($query, $type) {
                $query->where('livraison_magasin_central_producteurs.type_produit', $type);
            })
            ->when(request()->certificat, function ($query, $certificat) {
                $query->where('livraison_magasin_central_producteurs.certificat', $certificat);
            })
            ->whereIn('stock_magasin_central_id', request()->connaissement_id)
            ->select('livraison_magasin_central_producteurs.*')
            ->get();

            if($stock->count()) {

                $v = 1;
                $tv = count($stock);
                foreach ($stock as $data) {

                    $results .= '<tr><td colspan="2"><h5>' . $data->producteur->nom . ' ' . $data->producteur->prenoms . '(' . $data->producteur->codeProdapp . ')</h5><input type="hidden" name="producteurs[]" value="' . $data->producteur_id . '"/><input type="hidden" name="parcelle[]" value="' . $data->parcelle_id . '"/></td>
                    <td style="width: 300px;"><input type="hidden" name="typeproduit[]" value="' . $data->type_produit . '"/>' . $data->type_produit . '<input type="hidden" name="stock_magasin_central[]" value="' . $data->stock_magasin_central_id . '"/></td>
                    <td style="width: 400px;"> <input type="number" name="quantite[]" value="' . $data->quantite . '" min="0" max="' . $data->quantite . '"  class="form-control quantity" style="width: 115px;"/></td></tr>';

                    $total = $total + $data->quantite;
                    // $totalsacs = $totalsacs+$data->nb_sacs_entrant;
                    $v++;
                }

            }else{
                $results = '<span style="text-align:center;color:#FF0000;">Aucune donnée</span>';
            }
        }

        $contents['results'] = $results;
        $contents['total'] = $total;
        $contents['totalsacs'] = $totalsacs;

        return $contents;
    }

    public function deliveryStore(Request $request)
    {
        $request->validate([
            'code' => 'required',
        ]);
        $user = auth()->user();
        $livraison = StockMagasinCentral::where('numero_connaissement', $request->code)->where('status', Status::COURIER_DISPATCH)->firstOrFail();

        $livraison->status            = Status::COURIER_DELIVERYQUEUE;
        $livraison->save();
        $notify[] = ['success', 'Reception terminée'];
        return back()->withNotify($notify);
    }

    public function deliveryUsineStore(Request $request)
    {
        $request->validate([
            'code' => 'required',
            'quantite_confirme' => 'required'
        ]);
        $user = auth()->user();
        $livraison = Connaissement::where('numeroCU', $request->code)->where('status', Status::COURIER_DISPATCH)->firstOrFail();

        $livraison->status = Status::COURIER_DELIVERYQUEUE;
        $livraison->quantite_confirme = $request->quantite_confirme;
        $livraison->save();
        $notify[] = ['success', 'Reception terminée'];
        return back()->withNotify($notify);
    }
    public function suiviStore(Request $request)
    {
        $request->validate([
            'id' => 'required',
        ]);
        $user = auth()->user();
        $suivi = SuiviConnaissementUsine::where('connaissement_id', $request->id)->first();
        if ($suivi == null) {
            $suivi = new SuiviConnaissementUsine();
        }
        $suivi->connaissement_id = $request->id;
        $suivi->step1 = $request->step1;
        $suivi->step2 = $request->step2;
        $suivi->step3 = $request->step3;
        $suivi->step4 = $request->step4;
        $suivi->step5 = $request->step5;
        $suivi->save();

        return $suivi;
    }
    public function refouleUsineStore(Request $request)
    {
        $request->validate([
            'code' => 'required',
        ]);
        $user = auth()->user();
        $livraison = Connaissement::where('numeroCU', $request->code)->where('status', Status::COURIER_DISPATCH)->firstOrFail();
        $idconnaissement = $livraison->id;
        $numeroCMC = $livraison->numeroCMC;

        $livraison->status            = Status::COURIER_DELIVERED;
        $livraison->save();

        $productSend = ConnaissementProduit::where('connaissement_id', $idconnaissement)->get();
        $tableNumeroCMC = json_decode($numeroCMC);
        foreach ($productSend as $data) {
            $check = LivraisonMagasinCentralProducteur::where([['stock_magasin_central_id', $data->stock_magasin_central_id], ['campagne_id', $data->campagne_id], ['stock_magasin_central_id', $data->stock_magasin_central_id], ['parcelle_id', $data->parcelle_id], ['certificat', $data->certificat], ['type_produit', $data->type_produit]])->first();

            if ($check != null) {
                $check->quantite = $check->quantite + $data->quantite;
                $check->quantite_sortant = $check->quantite_sortant - $data->quantite;
                $check->save();

                $stockreduc = StockMagasinCentral::where('id', $check->stock_magasin_central_id)->first();
                if ($stockreduc != null) {
                    $stockreduc->stocks_mag_entrant = $stockreduc->stocks_mag_entrant + $data->quantite;
                    $stockreduc->stocks_mag_sortant = $stockreduc->stocks_mag_sortant - $data->quantite;
                    $stockreduc->save();
                }
            }
        }
        $notify[] = ['error', 'Livraison refoulée'];
        return back()->withNotify($notify);
    }
    public function deliveryPrimeStore(Request $request)
    {
        $request->validate([
            'campagne' => 'required',
            'periode' => 'required',
            'producteur' => 'required',
        ]);

        $livraison = LivraisonPrime::join('parcelles', 'livraison_primes.parcelle_id', '=', 'parcelles.id')->where([['campagne_id', $request->campagne], ['campagne_periode_id', $request->periode], ['producteur_id', $request->producteur]])->where('livraison_primes.status', Status::COURIER_DISPATCH)->select('livraison_primes.id')->get();

        $keys = Arr::whereNotNull(Arr::pluck($livraison, 'id'));
        LivraisonPrime::whereIn('id', $keys)->update(array('status' => Status::COURIER_DELIVERYQUEUE));


        $notify[] = ['success', 'Paiement effectué'];
        return back()->withNotify($notify);
    }

    public function invoice($id)
    {
        $id                  = decrypt($id);
        $pageTitle           = "Bon de livraison";
        $livraisonInfo         = StockMagasinCentral::with('cooperative', 'vehicule', 'transporteur', 'campagne', 'magasinCentral', 'magasinSection', 'campagnePeriode', 'vehicule.marque')->findOrFail($id);
        return view('manager.livraison-centrale.invoice', compact('pageTitle', 'livraisonInfo'));
    }

    public function usineInvoice($id)
    {
        $id                  = decrypt($id);
        $pageTitle           = "Bon de livraison";
        $livraisonInfo         = Connaissement::with('cooperative', 'vehicule', 'transporteur', 'campagne', 'magasinCentral', 'campagnePeriode', 'vehicule.marque')->findOrFail($id);
        return view('manager.livraison-centrale.invoice-usine', compact('pageTitle', 'livraisonInfo'));
    }
    public function primeInvoice(Request $request)
    {

        $pageTitle = "Facture";
        $livraisonInfo = LivraisonPrime::join('parcelles', 'livraison_primes.parcelle_id', '=', 'parcelles.id')->where([['campagne_id', $request->campagne], ['campagne_periode_id', $request->periode], ['producteur_id', $request->producteur]])->select('livraison_primes.*')->get();

        return view('manager.livraison-centrale.invoice-prime', compact('pageTitle', 'livraisonInfo'));
    }
    public function exportExcel()
    {
        $filename = 'stock-magasin-central-' . gmdate('dmYhms') . '.xlsx';
        return Excel::download(new ExportStockMagasinCentral, $filename);
    }
}
