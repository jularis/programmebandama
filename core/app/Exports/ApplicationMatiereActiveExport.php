<?php

namespace App\Exports;

use App\Models\MatiereActive;
use App\Models\ApplicationInsecte;
use Illuminate\Contracts\View\View;
use App\Models\SuiviParcellesParasite;
use Maatwebsite\Excel\Concerns\FromView;
use App\Models\ApplicationMatieresactive;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\Exportable;

class ApplicationMatiereActiveExport implements FromView, WithTitle
{
    /**
    * @return \Illuminate\Support\Collection
    */
    use Exportable;

    public function view(): View
    {
        // TODO: Implement view() method.
        
        return view('manager.application.MatieresactiveExcel',[
            'matieresactives' => MatiereActive::joinRelationship('application.parcelle.producteur.localite.section')->where('cooperative_id',auth()->user()->cooperative_id)->get()
        ]);
    }

    public function title(): string
    {
        Return "Matieres actives";
    }
}
