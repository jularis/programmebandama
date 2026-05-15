<?php

namespace App\Exports;

use App\Models\Ssrteclmrs;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;

class SsrteclmrsExport implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */
    use Exportable;

    public function view(): View
    {
        // TODO: Implement view() method.
        return view('manager.ssrteclmrs.SsrteclmrsAllExcel',[
            'ssrteclmrs' => Ssrteclmrs::joinRelationship('producteur.localite.section')->where('cooperative_id',auth()->user()->cooperative_id)->get()
        ]);
    } 
}
