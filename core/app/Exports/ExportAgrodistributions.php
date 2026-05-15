<?php

namespace App\Exports;

use App\Models\AgroapprovisionnementEspece;
use App\Models\Agrodistribution;
use App\Models\AgrodistributionEspece;
use App\Models\Agroevaluation;
use App\Models\Parcelle;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;

class ExportAgrodistributions implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */
    use Exportable;

    public function view(): View
    {
        // TODO: Implement view() method.
        return view('manager.distribution.DistributionAllExcel',[
            'distributions' => AgrodistributionEspece::joinRelationship('agrodistribution.producteur','agroespecesarbre')->where('cooperative_id',auth()->user()->cooperative_id)->get()
        ]);
    }
        
}
