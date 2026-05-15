<?php

namespace App\Exports;

use App\Models\AgroapprovisionnementEspece;
use App\Models\Agroevaluation;
use App\Models\Parcelle;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;

class ExportAgroapprovisionnements implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */
    use Exportable;

    public function view(): View
    {
        // TODO: Implement view() method.
        return view('manager.approvisionnement.ApprovisionnementAllExcel',[
            'approvisionnements' => AgroapprovisionnementEspece::joinRelationship('agroapprovisionnement','agroespecesarbre')->where('cooperative_id',auth()->user()->cooperative_id)->get()
        ]);
    }
        
}
