<?php

namespace App\Exports;

use App\Models\SsrteclmrsTravauxleger;
use App\Models\TravauxLegers;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;

class TravauxlegersExport implements FromView, WithTitle
{
    /**
    * @return \Illuminate\Support\Collection
    */
    use Exportable;

    public function view(): View
    {
        // TODO: Implement view() method.
        
        return view('manager.ssrteclmrs.TravauxlegersExcel',[
            'travaux' => SsrteclmrsTravauxleger::joinRelationship('ssrteclmrs.producteur.localite.section')->where('cooperative_id',auth()->user()->cooperative_id)->get()
        ]);
    }

    public function title(): string
    {
        Return "Travaux Legers";
    }
}
