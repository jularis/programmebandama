<?php

namespace App\Exports;

use App\Models\EnqueteMenage;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;

class ExportEnqueteMenages implements FromView
{
    use Exportable;

    public function view(): View
    {
        return view('manager.enquetemenage.EnqueteMenagesAllExcel', [
            'enqueteMenages' => EnqueteMenage::with(['producteur', 'localite', 'enfants'])
                ->joinRelationship('producteur.localite.section')
                ->where('cooperative_id', auth()->user()->cooperative_id)
                ->get(),
        ]);
    }
}
