<?php

namespace App\Exports;

use App\Models\SuiviFormationProducteur;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;

class SuiviFormationProducteurExport implements FromView, WithTitle
{
    /**
    * @return \Illuminate\Support\Collection
    */
    use Exportable;

    public function view(): View
    {
        // TODO: Implement view() method.
        
        return view('manager.formation.FormationsProducteurExcel',[
            'producteurs' => SuiviFormationProducteur::joinRelationship('suiviFormation.localite.section')->where('cooperative_id',auth()->user()->cooperative_id)
            ->when(request()->id, function ($query, $id) {
                $query->where('suivi_formation_id',decrypt($id)); 
           })
            ->get()
        ]);
    }

    public function title(): string
    {
        Return "Formation Producteurs";
    }
}
