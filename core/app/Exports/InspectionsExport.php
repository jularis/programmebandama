<?php

namespace App\Exports;

use App\Models\Inspection; 
use Illuminate\Contracts\View\View; 
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\Exportable;

class InspectionsExport implements FromView, WithTitle
{
    /**
    * @return \Illuminate\Support\Collection
    */
    use Exportable;

    public function view(): View
    {
        // TODO: Implement view() method.
        return view('manager.inspection.InspectionsAllExcel',[
            'inspections' => Inspection::joinRelationship('producteur.localite.section')->where('cooperative_id',auth()->user()->cooperative_id)
            ->when(request()->id, function($query, $id) {
                $query->where('inspections.id',decrypt($id)); 
           })
            ->get()
        ]);
    } 
    public function title(): string
    {
        Return "Inspections";
    }
}
