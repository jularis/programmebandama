<?php
namespace App\Exports;

use App\Models\InspectionQuestionnaire;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\Exportable;

class InspectionQuestionnairesExport implements FromView, WithTitle
{
    /**
    * @return \Illuminate\Support\Collection
    */
    use Exportable;

    public function view(): View
    {
        // TODO: Implement view() method.
        
        return view('manager.inspection.InspectionsQuestionnaireExcel',[
            'questions' => InspectionQuestionnaire::joinRelationship('inspection.producteur.localite.section')->where('cooperative_id',auth()->user()->cooperative_id)
            ->when(request()->id, function ($query, $id) {
                $query->where('inspection_id',decrypt($id)); 
           })
            ->get()
        ]);
    }

    public function title(): string
    {
        Return "Questionnaires";
    }
}
