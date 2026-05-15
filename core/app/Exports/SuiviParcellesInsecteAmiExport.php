<?php

namespace App\Exports;
 
use Illuminate\Contracts\View\View;
use App\Models\SuiviParcellesAnimal;
use App\Models\SuiviParcellesInsecteAmi;
use App\Models\SuiviParcellesTraitement;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\Exportable;
use App\Models\SuiviParcellesAutreParasite;
use App\Models\SuiviParcellesAgroforesterie;
use App\Models\SuiviParcellesIntrantAnneeDerniere;

class SuiviParcellesInsecteAmiExport implements FromView, WithTitle
{
    /**
    * @return \Illuminate\Support\Collection
    */
    use Exportable;

    public function view(): View
    {
        // TODO: Implement view() method.
        
        return view('manager.suiviparcelle.SuiviInsectesAmiesExcel',[
            'insectesamies' => SuiviParcellesInsecteAmi::joinRelationship('suiviParcelle.parcelle.producteur.localite.section')->where('cooperative_id',auth()->user()->cooperative_id)->get()
        ]);
    }

    public function title(): string
    {
        Return "Insectes Amies du Cacao";
    }
}
