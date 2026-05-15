<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Campagne;
use App\Constants\Status;
use App\Models\Livraison;
use App\Models\Programme;
use App\Models\Producteur;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\Models\Transporteur;
use Illuminate\Http\Request;
use App\Models\LivraisonInfo;
use App\Models\LivraisonPrime;
use App\Models\MagasinCentral;
use App\Models\MagasinSection;
use App\Models\ProgrammePrime;
use App\Models\CampagnePeriode;
use App\Models\LivraisonScelle;
use App\Models\LivraisonPayment;
use App\Models\LivraisonProduct;
use App\Models\AdminNotification;
use Illuminate\Support\Facades\DB;
use App\Models\StockMagasinCentral;
use App\Models\StockMagasinSection;
use Illuminate\Support\Facades\File;
use App\Models\Livraisons_temporaire;
use App\Models\LivraisonProductDetail;
use Illuminate\Database\Query\JoinClause;
use App\Models\LivraisonMagasinCentralProducteur;

class ApilivraisonController extends Controller
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

        

        $campagne = Campagne::active()->first();
        $periode = CampagnePeriode::where([['campagne_id', $campagne->id], ['periode_debut', '<=', gmdate('Y-m-d')], ['periode_fin', '>=', gmdate('Y-m-d')]])->latest()->first();

        $sender                      = User::where('id', $request->userid)->first();
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
        $livraison->save();

        $prod = new StockMagasinSection();
        $prod->livraison_info_id = $livraison->id;
        $prod->magasin_section_id = $request->magasin_section;
        $prod->campagne_id = $campagne->id;
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
            $productExists = LivraisonProduct::where([['campagne_id', $campagne->id], ['parcelle_id', $item['parcelle']], ['certificat', $item['certificat']], ['type_produit', $item['type']]])->first();
            if ($productExists == null) {
                $productExists = new LivraisonProduct();
            }
            $productExists->livraison_info_id = $livraison->id;
            $productExists->parcelle_id = $item['parcelle'];
            $productExists->campagne_id = $campagne->id;
            $productExists->campagne_periode_id = $periode->id;
            $productExists->qty = $item['quantity'] + $productExists->qty;
            $productExists->type_produit = $item['type'];
            $productExists->certificat = isset($item['certificat']) ? $item['certificat'] : null;
            $productExists->fee = $price + $productExists->fee;
            $productExists->type_price = $periode->prix_champ;
            $productExists->save();
            $data[] = [
                'livraison_info_id' => $livraison->id,
                'parcelle_id' => $item['parcelle'],
                'campagne_id' => $campagne->id,
                'campagne_periode_id' => $periode->id,
                'qty'             => $item['quantity'],
                'type_produit'     => $item['type'],
                'certificat' => isset($item['certificat']) ? $item['certificat'] : null,
                'fee'             => $price,
                'type_price'      => $periode->prix_champ,
                'created_at'      => now(),
            ];

            $product = Producteur::where('id', $item['producteur'])->first();
            if ($product != null) {
                $programme = $product->programme_id;

                $prime = ProgrammePrime::where('programme_id', $programme)->latest()->first();
                $price_prime = $prime->prime * $item['quantity'];
                $data3[] = [
                    'livraison_info_id' => $livraison->id,
                    'parcelle_id' => $item['parcelle'],
                    'campagne_id' => $campagne->id,
                    'campagne_periode_id' => $periode->id,
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
        $livraisonPayment->campagne_id  = $campagne->id;
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


        return response()->json($livraison, 201);
    }

    public function store_livraison_magasincentral(Request $request)
    {

        // dd(response()->json($request));

        $request->validate([
            'magasin_central' => 'required',
            'sender_magasin' =>  'required',
            'sender_transporteur' =>  'required',
            'sender_vehicule' =>  'required',
            'producteur_id' => 'required|array',
            'type' => 'required',
            'estimate_date'    => 'required|date|date_format:Y-m-d',
        ]);

        $manager = User::where('id', $request->userid)->first();
        $livraison = new StockMagasinCentral();
        $campagne = Campagne::active()->first();
        $periode = CampagnePeriode::where([['campagne_id', $campagne->id], ['periode_debut', '<=', gmdate('Y-m-d')], ['periode_fin', '>=', gmdate('Y-m-d')]])->latest()->first();

        if ($request->code == null) {
            $livraison->numero_connaissement = $this->generecodeConnais();
        }

        $livraison->cooperative_id   = $manager->cooperative_id;
        $livraison->campagne_id    = $campagne->id;
        $livraison->campagne_periode_id = $periode->id;
        $livraison->magasin_centraux_id = $request->magasin_central;
        $livraison->magasin_section_id = $request->sender_magasin;
        //$livraison->numero_connaissement = $request->code;
        $livraison->type_produit = json_encode($request->type);
        $livraison->stocks_mag_entrant = $request->poidsnet;
        $livraison->stocks_mag_sacs_entrant = $request->nombresacs;
        $livraison->stocks_mag_sortant = 0;
        $livraison->stocks_mag_sacs_sortant   = 0;
        $livraison->transporteur_id = $request->sender_transporteur;
        $livraison->vehicule_id = $request->sender_vehicule;
        $livraison->remorque_id = $request->sender_remorque;
        $livraison->date_livraison = $request->estimate_date;
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
                    'campagne_id' => $campagne->id,
                    'campagne_periode_id' => $periode->id,
                    'quantite' => $quantite[$i],
                    'type_produit' => $typeproduit[$i],
                    'parcelle_id' => $parcelle[$i],
                    'certificat' => $certificat[$i],
                    'created_at'      => now(),
                ];
                $product = LivraisonProduct::where([['campagne_id', $campagne->id], ['parcelle_id', $parcelle[$i]], ['certificat', $certificat[$i]], ['type_produit', $typeproduit[$i]]])->first();
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
        return response()->json($livraison, 201);
    }

    public function getMagasinsection(Request $request)
    {
        $staff = User::where('id', $request->userid)->first();
        $magasins = MagasinSection::joinRelationship('section.cooperative')->where([['cooperative_id', $staff->cooperative_id]])->get();
        return response()->json($magasins, 201);
    }
    public function livraisonbroussebymagasinsection(){

        $magasins = MagasinSection::join('livraison_infos', 'livraison_infos.receiver_magasin_section_id', '=', 'magasin_sections.id')
            ->join('livraison_products', 'livraison_products.livraison_info_id', '=', 'livraison_infos.id')
            ->join('parcelles', 'parcelles.id', '=', 'livraison_products.parcelle_id')
            ->join('producteurs', 'producteurs.id', '=', 'parcelles.producteur_id')
            ->select('magasin_sections.id as id', 'magasin_sections.section_id as section', 'magasin_sections.staff_id as magasinier', 'magasin_sections.nom as magasinSection', 'magasin_sections.code as codeMagasinSection','livraison_infos.sender_staff_id as Delegue','livraison_products.type_produit as typeProduit','livraison_products.certificat as certificat','livraison_products.parcelle_id as parcelle','livraison_products.qty as quantiteMagasinSection','livraison_products.qty_sortant as quantiteLivreMagCentral ','producteurs.id as producteur','producteurs.nom','producteurs.prenoms')
            ->get();
            return response()->json($magasins, 201);
    }

    public function getMagasincentraux(Request $request)
    {
        $staff = User::where('id', $request->userid)->first();
        $magasins = MagasinCentral::where('cooperative_id',$staff->cooperative_id)->get();

        return response()->json($magasins, 201);
    }
    public function gettransporteurs()
    {
        $transporteurs = Transporteur::join('entreprises', 'entreprises.id', '=', 'transporteurs.entreprise_id')
            ->select('transporteurs.*','entreprises.nom_entreprise as entreprise')
            ->get();

        return response()->json($transporteurs, 201);
    }

    public function getvehicules()
    {
        $vehicules = DB::table('vehicules')
            ->join('marques', 'marques.id', '=', 'vehicules.marque_id')
            ->select('vehicules.id','vehicules.vehicule_immat','vehicules.cooperative_id', 'vehicules.marque_id','vehicules.status','marques.nom as marque')
            ->get();
        return response()->json($vehicules, 201);
    }
    public function getremorques()
    {
        $remorques = DB::table('remorques')->get();
        return response()->json($remorques, 201);
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

    public function generecodeliv()
    {

        $data = Livraison::select('codeLiv')->orderby('id', 'desc')->limit(1)->get();

        if (count($data) > 0) {
            $code = $data[0]->codeLiv;
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
        $sub = 'BL-';
        $lastCode = $chaine_number + 1;
        $codeLiv = $sub . $zero . $lastCode;

        return $codeLiv;
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
