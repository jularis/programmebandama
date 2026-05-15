<?php

namespace App\Exports;

use App\Models\ActionSocialeLocalite;
use App\Models\ActionSocialePartenaire;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;

class ActionSocialePartenaireExport implements FromView, WithTitle
{
    /**
    * @return \Illuminate\Support\Collection
    */
    use Exportable;

    public function view(): View
    {
        // TODO: Implement view() method.
        return view('manager.action-sociale.PartenairesExcel',[
            'partenaires' => ActionSocialePartenaire::joinRelationship('actionSociale')->where('cooperative_id',auth()->user()->cooperative_id)->get()
        ]);
    } 

    public function title(): string
    {
        Return "Partenaires";
    }
}
