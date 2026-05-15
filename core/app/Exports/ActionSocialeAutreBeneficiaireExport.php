<?php

namespace App\Exports;

use App\Models\ActionSocialeAutreBeneficiaire;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;

class ActionSocialeAutreBeneficiaireExport implements FromView, WithTitle
{
   
    /**
    * @return \Illuminate\Support\Collection
    */
    use Exportable;

    public function view(): View
    {
        // TODO: Implement view() method.
        return view('manager.action-sociale.AutreBeneficiaresExcel',[
            'beneficiares' => ActionSocialeAutreBeneficiaire::joinRelationship('actionSociale')->where('cooperative_id',auth()->user()->cooperative_id)->get()
        ]);
    } 

    public function title(): string
    {
        Return "Autre beneficiaires";
    }
}
