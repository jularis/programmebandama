<?php

namespace App\Http\Controllers\Admin;
use App\Constants\Status;
use App\Models\LivraisonInfo;
use App\Http\Controllers\Controller;
use App\Models\Connaissement;
use App\Models\ConnaissementProduit;
use App\Models\Cooperative;
use App\Models\LivraisonPayment;
use App\Models\LivraisonMagasinCentralProducteur;
use App\Models\MagasinCentral;
use App\Models\StockMagasinCentral;
use App\Models\SuiviConnaissementUsine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LivraisonController extends Controller
{

    public function livraisonInfo()
    {
        $pageTitle    = "Information de Livraison";
        $livraisonInfos = LivraisonInfo::dateFilter()->searchable(['code'])->filter(['status','receiver_cooperative_id','sender_cooperative_id'])->where(function ($q) {
            $q->OrWhereHas('payment', function ($myQuery) {
                if(request()->payment_status != null){
                    $myQuery->where('status',request()->payment_status);
                }
            });
        })->orderBy('id', 'DESC')->with('senderCooperative', 'receiverCooperative', 'senderStaff', 'receiverStaff', 'paymentInfo','magasinSection')->paginate(getPaginate());
        return view('admin.livraison.index', compact('pageTitle', 'livraisonInfos'));
    }

    public function livraisonDetail($id)
    {
        $livraisonInfo = LivraisonInfo::with('senderCooperative', 'receiverCooperative', 'senderStaff', 'receiverStaff', 'paymentInfo','magasinSection')->findOrFail($id);
        $pageTitle   = "Detail de Livraison: " . $livraisonInfo->code;
        return view('admin.livraison.details', compact('pageTitle', 'livraisonInfo'));
    }

    public function invoice($id)
    {
        $livraisonInfo = LivraisonInfo::with('senderCooperative', 'receiverCooperative', 'senderStaff', 'receiverStaff', 'paymentInfo','magasinSection')->findOrFail($id);
        $pageTitle   = "Facture";
        return view('admin.livraison.invoice', compact('pageTitle', 'livraisonInfo'));
    }

    public function cooperativeIncome()
    {
        $pageTitle     = "Cooperative Historique des Revenus";
        $cooperatives      = Cooperative::active()->latest('id')->get();
        $cooperativeIncomes = LivraisonPayment::where('cooperative_id','!=',0)->dateFilter()->filter(['cooperative_id'])->where('status', Status::PAYE)->select(DB::raw("*,SUM(final_amount) as totalAmount"))
            ->groupBy('cooperative_id')->with('cooperative')->paginate(getPaginate());
        return view('admin.livraison.income', compact('pageTitle', 'cooperativeIncomes', 'cooperatives'));
    }

    public function connaissementUsine()
    {
        $stockQuery = Connaissement::dateFilter()
            ->searchable(['numeroCU'])
            ->filter(['cooperative_id', 'status'])
            ->when(request()->magasin, function ($query, $magasin) {
                $query->where('magasin_centraux_id', $magasin);
            });

        $total = (clone $stockQuery)->sum('quantite_livre');
        $stocks = $stockQuery
            ->with('cooperative', 'vehicule', 'transporteur', 'campagne', 'magasinCentral', 'campagnePeriode', 'vehicule.marque')
            ->orderBy('connaissements.id', 'desc')
            ->paginate(getPaginate());

        $pageTitle = "Connaissements Usine (" . showAmount($total) . ") Kg";
        $cooperatives = Cooperative::active()->orderBy('name')->get();
        $magasins = MagasinCentral::when(request()->cooperative_id, function ($query, $cooperativeId) {
            $query->where('cooperative_id', $cooperativeId);
        })->with('cooperative')->orderBy('nom')->get();

        return view('admin.livraison.connaissement-usine', compact('pageTitle', 'stocks', 'total', 'cooperatives', 'magasins'));
    }

    public function connaissementUsineInvoice($id)
    {
        $id = decrypt($id);
        $pageTitle = "Bon de livraison";
        $livraisonInfo = Connaissement::with('cooperative', 'vehicule', 'transporteur', 'campagne', 'magasinCentral', 'campagnePeriode', 'vehicule.marque', 'products.producteur.programme', 'products.parcelle')->findOrFail($id);

        return view('admin.livraison.invoice-usine', compact('pageTitle', 'livraisonInfo'));
    }

    public function connaissementUsineSuivi($id)
    {
        $id = decrypt($id);
        $livraison = Connaissement::findOrFail($id);
        $pageTitle = "Suivi de la livraison à l'Usine";
        $suivi = SuiviConnaissementUsine::where('connaissement_id', $id)->first();

        return view('admin.livraison.suivi-usine', compact('pageTitle', 'livraison', 'id', 'suivi'));
    }

    public function connaissementUsineDelivery(Request $request)
    {
        $request->validate([
            'code' => 'required',
            'quantite_confirme' => 'required|numeric|min:0'
        ]);

        $livraison = Connaissement::where('numeroCU', $request->code)->where('status', Status::COURIER_DISPATCH)->firstOrFail();
        $livraison->status = Status::COURIER_DELIVERYQUEUE;
        $livraison->quantite_confirme = $request->quantite_confirme;
        $livraison->save();

        $notify[] = ['success', 'Reception terminée'];
        return back()->withNotify($notify);
    }

    public function connaissementUsineRefoule(Request $request)
    {
        $request->validate([
            'code' => 'required',
            'commentaire' => 'required|max:500',
        ]);

        DB::transaction(function () use ($request) {
            $livraison = Connaissement::where('numeroCU', $request->code)->where('status', Status::COURIER_DISPATCH)->firstOrFail();
            $livraison->status = Status::COURIER_DELIVERED;
            $livraison->commentaire = $request->commentaire;
            $livraison->save();

            $productSend = ConnaissementProduit::where('connaissement_id', $livraison->id)->get();

            foreach ($productSend as $data) {
                $check = LivraisonMagasinCentralProducteur::where([
                    ['stock_magasin_central_id', $data->stock_magasin_central_id],
                    ['campagne_id', $data->campagne_id],
                    ['parcelle_id', $data->parcelle_id],
                    ['certificat', $data->certificat],
                    ['type_produit', $data->type_produit],
                ])->first();

                if ($check) {
                    $check->quantite = $check->quantite + $data->quantite;
                    $check->quantite_sortant = $check->quantite_sortant - $data->quantite;
                    $check->save();

                    $stockreduc = StockMagasinCentral::find($check->stock_magasin_central_id);
                    if ($stockreduc) {
                        $stockreduc->stocks_mag_entrant = $stockreduc->stocks_mag_entrant + $data->quantite;
                        $stockreduc->stocks_mag_sortant = $stockreduc->stocks_mag_sortant - $data->quantite;
                        $stockreduc->save();
                    }
                }
            }
        });

        $notify[] = ['error', 'Livraison refoulée'];
        return back()->withNotify($notify);
    }

    public function connaissementUsineSuiviStore(Request $request)
    {
        $request->validate([
            'id' => 'required',
        ]);

        $suivi = SuiviConnaissementUsine::firstOrNew(['connaissement_id' => $request->id]);
        $suivi->step1 = $request->step1;
        $suivi->step2 = $request->step2;
        $suivi->step3 = $request->step3;
        $suivi->step4 = $request->step4;
        $suivi->step5 = $request->step5;
        $suivi->save();

        return $suivi;
    }
}
