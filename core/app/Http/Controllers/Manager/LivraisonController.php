<?php

namespace App\Http\Controllers\Manager;

use App\Models\User;
use App\Models\Section;
use App\Models\Campagne;
use App\Models\Parcelle;
use App\Models\Remorque;
use App\Models\Vehicule;
use App\Constants\Status;
use App\Models\Programme;
use App\Models\Entreprise;
use App\Models\Producteur;
use App\Models\Cooperative;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\Models\Transporteur;

use Illuminate\Http\Request;
use App\Models\LivraisonInfo;
use App\Models\FormateurStaff;
use App\Models\LivraisonPrime;
use App\Models\MagasinCentral;
use App\Models\MagasinSection;
use App\Models\ProgrammePrime;
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
use Maatwebsite\Excel\Facades\Excel;
use App\Models\LivraisonProductDetail;
use App\Models\Producteur_certification;
use App\Exports\ExportStockMagasinSection;
use App\Models\Certification;
use App\Models\Estimation;
use App\Models\LivraisonMagasinCentralProducteur;

class LivraisonController extends Controller
{

    public function livraisonInfo()
    {
        $staff = auth()->user();
        $livraisonProd = LivraisonProduct::dateFilter()->searchable(['code'])->joinRelationship('livraisonInfo')->joinRelationship('parcelle')->with('livraisonInfo', 'campagne', 'parcelle')
            ->where(function ($query) use ($staff) {
                $query->where('sender_cooperative_id', $staff->cooperative_id)->orWhere('receiver_cooperative_id', $staff->cooperative_id);
            })
            ->when(request()->magasin, function ($query, $magasin) {
                $query->where('receiver_magasin_section_id', $magasin);
            })
            ->when(request()->produit, function ($query, $produit) {
                $query->where('type_produit', $produit);
            })
            ->when(request()->producteur, function ($query, $producteur) {
                $query->where('producteur_id', $producteur);
            })
            ->paginate(getPaginate());

        $total = $livraisonProd->sum('qty');
        $pageTitle    = "Livraison des Magasins de Section (" . showAmount($total) . ") Kg";
        $magasins  = MagasinSection::joinRelationship('section')->where('cooperative_id', $staff->cooperative_id)->get();
        $sections = Section::get();
        return view('manager.livraison.index', compact('pageTitle', 'livraisonProd', 'total', 'sections', 'magasins'));
    }

    public function stockSection()
    {
        $staff = auth()->user();
        $stocks = StockMagasinSection::dateFilterWithTable()->joinRelationship('livraisonInfo')
            ->where([['sender_cooperative_id', $staff->cooperative_id]])
            ->when(request()->magasin, function ($query, $magasin) {
                $query->where('magasin_section_id', $magasin);
            })
            ->with('campagne', 'campagnePeriode', 'magasinSection')
            ->orderBy('stock_magasin_sections.id', 'desc')->paginate(getPaginate());
        $total = $stocks->sum('stocks_entrant');
        $pageTitle    = "Stock des Magasins de Section (" . showAmount($total) . ") Kg";
        $magasins  = MagasinSection::joinRelationship('section')->where('cooperative_id', $staff->cooperative_id)->get();
        $sections = Section::get();
        return view('manager.livraison.stock', compact('pageTitle', 'stocks', 'total', 'sections', 'magasins'));
    }

    public function create()
    {
        $pageTitle = 'Enregistrement de livraison';
        $staff = auth()->user();
        $campagne = Campagne::active()->first();
        $allcampagnes  = Campagne::where('cooperative_id', $staff->cooperative_id)->get();
        $allperiodes  = CampagnePeriode::get();
        $periode = CampagnePeriode::where([['campagne_id', $campagne->id], ['periode_debut', '<=', gmdate('Y-m-d')], ['periode_fin', '>=', gmdate('Y-m-d')]])->latest()->first();

        $cooperatives = Cooperative::active()->where('id', $staff->cooperative_id)->orderBy('name')->get();
        $magasins = MagasinSection::join('users', 'magasin_sections.staff_id', '=', 'users.id')->where([['cooperative_id', $staff->cooperative_id], ['magasin_sections.status', 1]])->with('user')->orderBy('nom')->select('magasin_sections.*')->get();

        $staffs = User::whereHas('roles', function ($q) {
            $q->whereIn('name', ['Delegue', 'Magasinier']);
        })
            ->where('cooperative_id', $staff->cooperative_id)
            ->select('users.*')
            ->get();

        $producteurs  = Producteur::joinRelationship('localite.section')->where([['cooperative_id', $staff->cooperative_id], ['producteurs.status', 1]])->select('producteurs.*')->orderBy('producteurs.nom')->get();

        $certification = Producteur_certification::joinRelationship('producteur.localite.section')->where('cooperative_id', $staff->cooperative_id)->groupby('certification')->get();
        $parcelles  = Parcelle::joinRelationship('producteur.localite.section')->where('cooperative_id', $staff->cooperative_id)->with('producteur')->get();

        return view('manager.livraison.create', compact('pageTitle', 'cooperatives', 'staffs', 'magasins', 'producteurs', 'parcelles', 'campagne', 'periode', 'certification','allcampagnes','allperiodes'));
    }

    public function stockSectionCreate()
    {

        $staff = auth()->user();
        $cooperatives = Cooperative::active()->where('id', auth()->user()->cooperative_id)->orderBy('name')->get();
        //$cooperatives = Cooperative::active()->orderBy('name')->get();
        $magCentraux = MagasinCentral::where([['cooperative_id', $staff->cooperative_id]])->with('user')->orderBy('nom')->get();
        $magSections = MagasinSection::joinRelationship('section')->where([['cooperative_id', $staff->cooperative_id]])->with('user')->orderBy('nom')->get();

        $transporteurs = Transporteur::where([['cooperative_id', $staff->cooperative_id]])->with('cooperative', 'entreprise')->get();
        $vehicules = Vehicule::where('cooperative_id', auth()->user()->cooperative_id)->with('marque')->get();
        $producteurs  = Producteur::joinRelationship('localite.section')->where([['sections.cooperative_id', $staff->cooperative_id], ['producteurs.status', 1]])->select('producteurs.*')->orderBy('producteurs.nom')->get();

        $allcampagnes  = Campagne::where('cooperative_id', $staff->cooperative_id)->get();
         $campagne = Campagne::active()->first();
        $campagne = CampagnePeriode::where([['campagne_id', $campagne->id], ['periode_debut', '<=', gmdate('Y-m-d')], ['periode_fin', '>=', gmdate('Y-m-d')]])->latest()->first();
       $allperiodes  = CampagnePeriode::get();
        $code = $this->generecodeConnais();
        $parcelles  = Parcelle::with('producteur')->get();
        $pageTitle = 'Connaissement vers le Magasin Central N° ' . $code;
        $entreprises = Entreprise::where('cooperative_id', auth()->user()->cooperative_id)->pluck('nom_entreprise', 'id');
        $formateurs = FormateurStaff::with('entreprise')->get();
        $remorques = Remorque::where('cooperative_id', auth()->user()->cooperative_id)->get();

        return view('manager.livraison.section-create', compact('pageTitle', 'cooperatives', 'magSections', 'magCentraux', 'producteurs', 'transporteurs', 'parcelles', 'campagne', 'vehicules', 'code', 'entreprises', 'formateurs', 'remorques','allcampagnes','allperiodes'));
    }

    public function store(Request $request)
    {

        $request->validate([
            'sender_staff' => 'required|exists:users,id',
            'magasin_section' =>  'required|exists:magasin_sections,id',
            'items'            => 'required|array',
            'items.*.type'     => 'required',
            'items.*.producteur'     => 'required|integer',
            'items.*.parcelle'     => 'required|integer',
            'items.*.quantity' => 'required|numeric|gt:0',
            'items.*.amount'   => 'required|numeric|gt:0',
            'estimate_date'    => 'required|date|date_format:Y-m-d',
        ]);

        $manager = auth()->user();
        // $campagne = Campagne::active()->first();
        // $periode = CampagnePeriode::where([['campagne_id', $campagne->id], ['periode_debut', '<=', gmdate('Y-m-d')], ['periode_fin', '>=', gmdate('Y-m-d')]])->latest()->first();
$campagne = Campagne::where('id', $request->campagne)->first();
        $periode = CampagnePeriode::where('id', $request->periode)->latest()->first();

        $sender                      = auth()->user();
        $livraison                     = new LivraisonInfo();
        $livraison->invoice_id         = getTrx();
        $livraison->code               = getTrx();
        $livraison->sender_cooperative_id   = $sender->cooperative_id;
        $livraison->sender_staff_id    = $request->sender_staff;
        $livraison->sender_name        = $request->sender_name;
        $livraison->sender_email       = $request->sender_email;
        $livraison->sender_phone       = $request->sender_phone;
        $livraison->sender_address     = $request->sender_address;
        $livraison->receiver_name      = $request->receiver_name;
        $livraison->receiver_email     = $request->receiver_email;
        $livraison->receiver_phone     = $request->receiver_phone;
        $livraison->receiver_address   = $request->receiver_address;
        $livraison->receiver_cooperative_id = $sender->cooperative_id;
        $livraison->receiver_magasin_section_id = $request->magasin_section;
        $livraison->estimate_date      = $request->estimate_date;
        $livraison->quantity      = array_sum(Arr::pluck($request->items, 'quantity'));
        // dd(json_encode($request->all()));
        $livraison->save();

        $prod = new StockMagasinSection();
        $prod->livraison_info_id = $livraison->id;
        $prod->magasin_section_id = $request->magasin_section;
        $prod->campagne_id = $request->campagne;
        $prod->campagne_periode_id = $periode->id;
        $prod->stocks_entrant = array_sum(Arr::pluck($request->items, 'quantity'));
        $prod->save();

        $subTotal = $stock = 0;

        $data = $data2 = $data3 = [];
        foreach ($request->items as $item) {

            $price = $periode->prix_champ * $item['quantity'];
            $subTotal += $price;
            if ($item['type'] == 'Ordinaire') {
                $item['certificat'] = null;
            }
            $productExists = LivraisonProduct::where([['campagne_id', $request->campagne], ['parcelle_id', $item['parcelle']], ['certificat', $item['certificat']], ['type_produit', $item['type']]])->first();
            if ($productExists == null) {
                $productExists = new LivraisonProduct();
            }
            $productExists->livraison_info_id = $livraison->id;
            $productExists->parcelle_id = $item['parcelle'];
            $productExists->campagne_id = $request->campagne;
            $productExists->campagne_periode_id = $request->periode;
            $productExists->qty = $item['quantity'] + $productExists->qty;
            $productExists->type_produit = $item['type'];
            $productExists->certificat = isset($item['certificat']) ? $item['certificat'] : null;
            $productExists->fee = $price + $productExists->fee;
            $productExists->type_price = $periode->prix_champ;
            $productExists->save();
            $data[] = [
                'livraison_info_id' => $livraison->id,
                'parcelle_id' => $item['parcelle'],
                'campagne_id' => $request->campagne,
                'campagne_periode_id' => $request->periode,
                'qty'             => $item['quantity'],
                'type_produit'     => $item['type'],
                'certificat' => isset($item['certificat']) ? $item['certificat'] : null,
                'fee'             => $price,
                'type_price'      => $periode->prix_champ,
                'created_at'      => now(),
            ];

            $estimation = Estimation::where([['campagne_id', $request->campagne], ['parcelle_id', $item['parcelle']]])->first();

            if ($estimation != null) {
                $estima_prod = $estimation->EsP;
                $production = $estimation->productionAnnuelle + $item['quantity'];
                if ($production >= $estima_prod) {
                    $estimation->etat = 'Atteint';
                }
                $estimation->productionAnnuelle = $estimation->productionAnnuelle + $item['quantity'];
                $estimation->save();
            }

            $product = Producteur::joinRelationship('localite.section')
                ->where([['cooperative_id', $manager->cooperative_id], ['producteurs.status', 1]])->where('producteurs.id', $item['producteur'])->first();
            if ($product != null) {
                $programme = $product->programme_id;

                $prime = ProgrammePrime::where('programme_id', $programme)->latest()->first();
                $price_prime = $prime->prime * $item['quantity'];
                $data3[] = [
                    'livraison_info_id' => $livraison->id,
                    'parcelle_id' => $item['parcelle'],
                    'campagne_id' => $request->campagne,
                    'campagne_periode_id' => $request->periode,
                    'quantite'             => $item['quantity'],
                    'montant'             => $price_prime,
                    'prime_campagne'      => $prime->prime,
                    'created_at'      => now(),
                ];
            }
        }

        LivraisonProductDetail::insert($data);
        LivraisonPrime::insert($data3);

        $totalAmount                     = $subTotal;

        $livraisonPayment                  = new LivraisonPayment();
        $livraisonPayment->livraison_info_id = $livraison->id;
        $livraisonPayment->campagne_id  = $request->campagne;
        $livraisonPayment->amount          = $subTotal;
        $livraisonPayment->final_amount    = $totalAmount;
        $livraisonPayment->save();

        if ($livraisonPayment->status == Status::PAYE) {
            $adminNotification            = new AdminNotification();
            $adminNotification->user_id   = $sender->id;
            $adminNotification->title     = 'Livraison Payment ' . $sender->username;
            $adminNotification->click_url = urlPath('admin.livraison.info.details', $livraison->id);
            $adminNotification->save();
        }

        $adminNotification            = new AdminNotification();
        $adminNotification->user_id   = $sender->id;
        $adminNotification->title     = 'New livraison created to' . $sender->username;
        $adminNotification->click_url = urlPath('admin.livraison.info.details', $livraison->id);
        $adminNotification->save();

        $notify[] = ['success', 'Livraison added successfully'];
        return to_route('manager.livraison.stock.section', encrypt($livraison->id))->withNotify($notify);
    }

    public function sectionStore(Request $request)
    {
        $request->validate([
            'magasin_central' => 'required',
            'sender_magasin' =>  'required',
            'sender_transporteur' =>  'required',
            'sender_vehicule' =>  'required',
            'producteur_id' => 'required|array',
            'type' => 'required',
            'estimate_date'    => 'required|date|date_format:Y-m-d',
        ]);

        $manager = auth()->user();
        $livraison = new StockMagasinCentral();
        $staff = auth()->user();
        $campagne = Campagne::active()->first();
        $allcampagnes  = Campagne::where('cooperative_id', $staff->cooperative_id)->get();
        $allperiodes  = CampagnePeriode::get();
        $campagne = Campagne::active()->first();
        $periode = CampagnePeriode::where([['campagne_id', $campagne->id], ['periode_debut', '<=', gmdate('Y-m-d')], ['periode_fin', '>=', gmdate('Y-m-d')]])->latest()->first();

        $livraison->cooperative_id   = $manager->cooperative_id;
        $livraison->campagne_id    = $request->campagne;
        $livraison->campagne_periode_id = $request->periode;
        $livraison->magasin_centraux_id = $request->magasin_central;
        $livraison->magasin_section_id = $request->sender_magasin;
        $livraison->numero_connaissement = $request->code;
        $livraison->type_produit = json_encode($request->type);
        $livraison->stocks_mag_entrant = $request->poidsnet;
        $livraison->stocks_mag_sacs_entrant = $request->nombresacs;
        $livraison->stocks_mag_sortant = 0;
        $livraison->stocks_mag_sacs_sortant   = 0;
        $livraison->transporteur_id = $request->sender_transporteur;
        $livraison->vehicule_id = $request->sender_vehicule;
        $livraison->remorque_id = $request->sender_remorque;
        $livraison->date_livraison = $request->estimate_date;
        // dd(json_encode($request->all()));
        $livraison->save();

        $i = 0;
        $data = [];
        $quantite = $request->quantite;
        $typeproduit = $request->typeproduit;
        $producteurs = $request->producteurs;
        $parcelle = $request->parcelle;
        $certificat = $request->certificat;

        foreach ($producteurs as $item) {

            if ($quantite[$i] > 0) {
                $data[] = [
                    'stock_magasin_central_id' => $livraison->id,
                    'producteur_id' => $item,
                    'campagne_id' => $request->campagne,
                    'campagne_periode_id' => $request->periode,
                    'quantite' => $quantite[$i],
                    'type_produit' => $typeproduit[$i],
                    'parcelle_id' => $parcelle[$i],
                    'certificat' => $certificat[$i],
                    'created_at'      => now(),
                ];
                $product = LivraisonProduct::where([['campagne_id', $request->campagne], ['parcelle_id', $parcelle[$i]], ['certificat', $certificat[$i]], ['type_produit', $typeproduit[$i]]])->first();
                if ($product != null) {
                    $productinfo = $product->livraison_info_id;
                    $product->qty = $product->qty - $quantite[$i];
                    $product->qty_sortant = $product->qty_sortant + $quantite[$i];
                    $product->save();

                    $prod = StockMagasinSection::where('livraison_info_id', $productinfo)->first();
                    $prod->stocks_entrant = $prod->stocks_entrant - $quantite[$i];
                    $prod->stocks_sortant = $prod->stocks_sortant + $quantite[$i];

                    $prod->save();
                }
            }
            $i++;
        }


        LivraisonMagasinCentralProducteur::insert($data);

        $notify[] = ['success', 'Le connaissement vers le magasin central a été ajouté avec succès'];
        return to_route('manager.livraison.magcentral.stock')->withNotify($notify);
    }


    public function getParcelle()
    {
        $input = request()->all();
        $id = $input['id'];
        $parcelles = Parcelle::where('producteur_id', $id)->get();
        if ($parcelles->count()) {
            $contents = '';

            foreach ($parcelles as $data) {
                $contents .= '<option value="' . $data->id . '" >Parcelle ' . $data->codeParc . '</option>';
            }
        } else {
            $contents = null;
        }

        return $contents;
    }


    public function getCertificat()
    {
        $input = request()->all();
        $id = $input['id'];
        $certificats = Producteur_certification::where('producteur_id', $id)->get();
        if ($certificats->count()) {
            $contents = '';

            foreach ($certificats as $data) {
                $contents .= '<option value="' . $data->certification . '" >' . $data->certification . '</option>';
            }
        } else {
            $contents = null;
        }

        return $contents;
    }
    public function getProducteur()
    {
        $input = request()->all();

        $id = $input['sender_magasin'];
        $campagne = Campagne::where('id',$input['campagne'])->first();

        $periode = CampagnePeriode::where([['id', $input['periode']]])->latest()->first();


        if (request()->type && request()->sender_magasin) {

            $stocks = LivraisonProduct::joinRelationship('livraisonInfo')
                ->joinRelationship('parcelle')
                ->where([['campagne_id', $input['campagne']], ['receiver_magasin_section_id', $input['sender_magasin']], ['qty', '>', 0]])->whereIn('type_produit',request()->type)->with('parcelle', 'parcelle.producteur')->groupBy('producteur_id')->get();

                $contents = '';

            foreach ($stocks as $data) {
                $nom = $data->parcelle->producteur->nom . ' ' . $data->parcelle->producteur->prenoms;
                $contents .= '<option value="' . $data->parcelle->producteur_id . '">' . $nom . '(' . $data->parcelle->producteur->codeProdapp . ')</option>';
            }
        } else {
            $contents = null;
        }

        return $contents;
    }
    private function generecodeConnais()
    {

        $data = StockMagasinCentral::select('numero_connaissement')->orderby('id', 'desc')->first();

        if ($data != null) {
            $code = $data->numero_connaissement;
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
        $sub = 'CMS-';
        $lastCode = $chaine_number + 1;
        $codeLiv = $sub . $zero . $lastCode;

        return $codeLiv;
    }
    public function getListeProducteurConnaiss()
    {
        $input = request()->all();
        $magasinsection = $input['sender_magasin'];

        $results = '';
        $total = 0;
        $totalsacs = 0;
        $campagne = Campagne::active()->first();
        if (request()->type && request()->producteur_id) {
            $stock = LivraisonProduct::joinRelationship('livraisonInfo')
                ->joinRelationship('parcelle')
                ->where([['campagne_id', $input['campagne']], ['receiver_magasin_section_id', $magasinsection], ['qty', '>', 0]])->whereIn('type_produit', request()->type)->whereIn('producteur_id', request()->producteur_id)->with('parcelle', 'parcelle.producteur')->get();

            if (count($stock)) {
                $v = 1;
                $tv = count($stock);
                foreach ($stock as $data) {
                    if ($v == $tv) {
                        $read = '';
                    } else {
                        $read = 'readonly';
                    }
                    $results .= '<tr>
        <td colspan="2"><h5>' . $data->parcelle->producteur->nom . ' ' . $data->parcelle->producteur->prenoms . '(' . $data->parcelle->producteur->codeProdapp . ')</h5>
        <input type="hidden" name="producteurs[]" value="' . $data->parcelle->producteur_id . '"/></td>
        <td style="width: 300px;"><input type="hidden" name="parcelle[]" value="' . $data->parcelle_id . '"/><input type="hidden" name="certificat[]" value="' . $data->certificat . '"/>' . $data->certificat . '</td>
        <td style="width: 300px;"><input type="hidden" name="typeproduit[]" value="' . $data->type_produit . '"/>' . $data->type_produit . '</td>
        <td style="width: 400px;"> <input type="number" name="quantite[]" value="' . $data->qty . '" min="0" max="' . $data->qty . '"  class="form-control quantity" style="width: 115px;"/></td>
        </tr>';
                    $total = $total + $data->qty;
                    $totalsacs = $totalsacs + $data->nb_sacs_entrant;
                    $v++;
                }
            }
        }
        $contents['results'] = $results;
        $contents['total'] = $total;
        $contents['totalsacs'] = $totalsacs;

        return $contents;
    }

    public function sentInQueue()
    {
        $pageTitle    = "Liste des livraisons en attente";
        $livraisonInfos = $this->livraisons('queue');
        return view('manager.livraison.index', compact('pageTitle', 'livraisonInfos'));
    }

    public function sentLivraison()
    {
        $manager      = auth()->user();
        $pageTitle    = "Liste des livraisons envoyées";
        $livraisonInfos = LivraisonInfo::where('sender_cooperative_id', $manager->cooperative_id)->where('status', '!=', Status::COURIER_QUEUE)->dateFilter()->searchable(['code'])->with('senderCooperative', 'receiverCooperative', 'senderStaff', 'receiverStaff', 'paymentInfo')->paginate(getPaginate());
        return view('manager.livraison.index', compact('pageTitle', 'livraisonInfos'));
    }

    public function deliveryInQueue()
    {
        $pageTitle    = "Liste des livraisons en attente de reception";
        $livraisonInfos = $this->livraisons('deliveryQueue');
        return view('manager.livraison.index', compact('pageTitle', 'livraisonInfos'));
    }

    public function dispatchLivraison()
    {
        $pageTitle    = "Liste des livraisons expédiées";
        $livraisonInfos = $this->livraisons('dispatched');
        return view('manager.livraison.index', compact('pageTitle', 'livraisonInfos'));
    }

    public function delivered()
    {
        $pageTitle    = "Liste des livraisons reçues";
        $livraisonInfos = $this->livraisons('delivered');
        return view('manager.livraison.index', compact('pageTitle', 'livraisonInfos'));
    }

    public function upcoming()
    {
        $pageTitle    = "Liste des livraisons encours";
        $livraisonInfos = $this->livraisons('upcoming');
        return view('manager.livraison.index', compact('pageTitle', 'livraisonInfos'));
    }

    protected function livraisons($scope = null)
    {
        $user     = auth()->user();
        $livraisons = LivraisonInfo::where(function ($query) use ($user) {
            $query->where('sender_cooperative_id', $user->cooperative_id)->orWhere('receiver_cooperative_id', $user->cooperative_id);
        });
        if ($scope) {
            $livraisons = $livraisons->$scope();
        }
        $livraisons = $livraisons->dateFilter()->searchable(['code'])->with('senderCooperative', 'receiverCooperative', 'senderStaff', 'receiverStaff', 'paymentInfo')->paginate(getPaginate());
        return $livraisons;
    }

    public function invoice($id)
    {
        $id                  = decrypt($id);
        $pageTitle           = "Facture";
        $livraisonInfo         = LivraisonInfo::with('payment')->findOrFail($id);

        return view('manager.livraison.invoice', compact('pageTitle', 'livraisonInfo'));
    }

    public function exportExcel()
    {
        $filename = 'stock-magasin-section-' . gmdate('dmYhms') . '.xlsx';
        return Excel::download(new ExportStockMagasinSection, $filename);
    }

    public function delete($id)
    {
        LivraisonInfo::where('id', decrypt($id))->delete();
        $notify[] = ['success', 'Le contenu supprimé avec succès'];
        return back()->withNotify($notify);
    }
}
