<?php

namespace App\Exports;

use App\Models\VisitePlantation;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;

class ExportVisitePlantations implements FromView
{
    use Exportable;

    public function view(): View
    {
        return view('manager.visiteplantation.VisitePlantationsAllExcel', [
            'visitePlantations' => VisitePlantation::with(['producteur', 'localite', 'enfants'])
                ->joinRelationship('producteur.localite.section')
                ->where('cooperative_id', auth()->user()->cooperative_id)
                ->get(),
        ]);
    }
}
