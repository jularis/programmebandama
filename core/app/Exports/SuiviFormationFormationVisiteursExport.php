<?php

namespace App\Exports;
 
use App\Models\SuiviFormationVisiteur;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;

class SuiviFormationFormationVisiteursExport implements FromView, WithTitle
{
    /**
    * @return \Illuminate\Support\Collection
    */
    use Exportable;

    public function view(): View
    {
        // TODO: Implement view() method.
        
        return view('manager.formation.FormationsVisiteurExcel',[
            'visiteurs' => SuiviFormationVisiteur::joinRelationship('suiviFormation.localite.section')->where('cooperative_id',auth()->user()->cooperative_id)
            ->when(request()->id, function ($query, $id) {
                $query->where('suivi_formation_id',decrypt($id)); 
           })
            ->get()
        ]);
    }

    public function title(): string
    {
        Return "Formation Visiteurs";
    }
}
