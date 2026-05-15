<?php

namespace App\Exports;

use App\Models\LivraisonInfo;
use App\Models\StockMagasinCentral;
use App\Models\StockMagasinSection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\Exportable;

class StockCentralLivraisonExport implements FromView, WithTitle
{
    /**
    * @return \Illuminate\Support\Collection
    */
    use Exportable;

    public function view(): View
    {
        // TODO: Implement view() method.
        return view('manager.livraison-centrale.StockAllExcel',[
            'stockscentral' => StockMagasinCentral::where('cooperative_id',auth()->user()->cooperative_id)->get()
        ]);
    } 
    public function title(): string
    {
        Return "Stock Central";
    }
}
