<?php

namespace App\Exports;
 
use Illuminate\Contracts\View\View;
use App\Models\SuiviParcellesAnimal;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\Exportable;
use App\Models\SuiviParcellesAgroforesterie;
use App\Models\SuiviParcellesIntrantAnneeDerniere;

class SuiviParcellesIntrantAnneeDerniereExport implements FromView, WithTitle
{
    /**
    * @return \Illuminate\Support\Collection
    */
    use Exportable;

    public function view(): View
    {
        // TODO: Implement view() method.
        
        return view('manager.suiviparcelle.SuiviIntrantAnneeExcel',[
            'intrantanneederniere' => SuiviParcellesIntrantAnneeDerniere::joinRelationship('suiviParcelle.parcelle.producteur.localite.section')->where('cooperative_id',auth()->user()->cooperative_id)->get()
        ]);
    }

    public function title(): string
    {
        Return "Intrants Annee Derniere";
    }
}
