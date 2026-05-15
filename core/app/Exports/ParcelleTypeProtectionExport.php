<?php

namespace App\Exports;

use App\Models\Parcelle_type_protection; 
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;

class ParcelleTypeProtectionExport implements FromView, WithTitle
{
    /**
    * @return \Illuminate\Support\Collection
    */
    use Exportable;

    public function view(): View
    {
        // TODO: Implement view() method.
        
        return view('manager.parcelle.TypeProtectionExcel',[
            'protections' => Parcelle_type_protection::joinRelationship('parcelle.producteur.localite.section')->where('cooperative_id',auth()->user()->cooperative_id)->get()
        ]);
    }

    public function title(): string
    {
        Return "Types de protection";
    }
}
