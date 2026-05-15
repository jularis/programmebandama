<?php

namespace App\Exports;

use App\Models\SsrteclmrsLieutravauxleger;
use App\Models\SsrteclmrsTravauxdangereux;
use App\Models\SuiviParcellesParasite;
use App\Models\TravauxDangereux;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;

class TravauxdangereuxExport implements FromView, WithTitle
{
    /**
    * @return \Illuminate\Support\Collection
    */
    use Exportable;

    public function view(): View
    {
        // TODO: Implement view() method.
        
        return view('manager.ssrteclmrs.TravauxdangereuxExcel',[
            'travaux' => SsrteclmrsTravauxdangereux::joinRelationship('ssrteclmrs.producteur.localite.section')->where('cooperative_id',auth()->user()->cooperative_id)->get()
        ]);
    }

    public function title(): string
    {
        Return "Travaux Dangereux";
    }
}
