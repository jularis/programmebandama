<?php

namespace App\Exports;

use App\Models\ActionSocialeLocalite;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\Exportable;

class ActionSocialeLocaliteExport implements FromView, WithTitle
{
   /**
    * @return \Illuminate\Support\Collection
    */
    use Exportable;

    public function view(): View
    {
        // TODO: Implement view() method.
        return view('manager.action-sociale.BeneficiairesExcel',[
            'beneficiares' => ActionSocialeLocalite::joinRelationship('actionSociale')->where('cooperative_id',auth()->user()->cooperative_id)->get()
        ]);
    } 

    public function title(): string
    {
        Return "Beneficiaires";
    }
}
