<?php

namespace App\Exports;

use App\Models\Menage;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;

class ExportMenages implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */
    use Exportable;

    public function view(): View
    {
        // TODO: Implement view() method.
        return view('manager.menage.MenagesAllExcel',[
            'menages' => Menage::joinRelationship('producteur.localite.section')->where('cooperative_id',auth()->user()->cooperative_id)->get()
        ]);
    } 
}
