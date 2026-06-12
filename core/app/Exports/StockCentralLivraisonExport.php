<?php

namespace App\Exports;

use App\Models\StockMagasinCentral;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\Exportable;

class StockCentralLivraisonExport implements FromView, WithTitle
{
    use Exportable;

    protected $date;
    protected $magasin;

    public function __construct($date = null, $magasin = null)
    {
        $this->date    = $date;
        $this->magasin = $magasin;
    }

    public function view(): View
    {
        $query = StockMagasinCentral::where('cooperative_id', auth()->user()->cooperative_id)
            ->with(['cooperative', 'campagne', 'campagnePeriode', 'magasinSection', 'magasinCentral', 'transporteur.entreprise', 'vehicule', 'remorque'])
            ->orderBy('id', 'desc');

        if ($this->magasin) {
            $query->where('magasin_section_id', $this->magasin);
        }

        if ($this->date) {
            $parts     = explode('-', $this->date);
            $startDate = Carbon::parse(trim($parts[0]))->format('Y-m-d');
            $endDate   = isset($parts[1]) ? Carbon::parse(trim($parts[1]))->format('Y-m-d') : $startDate;
            $query->whereDate('created_at', '>=', $startDate)
                  ->whereDate('created_at', '<=', $endDate);
        }

        return view('manager.livraison-centrale.StockAllExcel', [
            'stockscentral' => $query->get(),
        ]);
    }

    public function title(): string
    {
        return "Stock Central";
    }
}
