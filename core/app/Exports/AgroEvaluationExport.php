<?php

namespace App\Exports;

use App\Models\ActionSociale;
use App\Models\Agroevaluation;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\Exportable;

class AgroEvaluationExport implements  FromView, WithTitle
{
  /**
    * @return \Illuminate\Support\Collection
    */
    use Exportable;

    public function view(): View
    {
        // TODO: Implement view() method.
        return view('manager.agroevaluation.AgroevaluationAllExcel',[
            'agroevaluations' => Agroevaluation::joinRelationship('producteur.localite.section')->where('cooperative_id',auth()->user()->cooperative_id)->get()
        ]);
    }

    public function title(): string
    {
        Return "Agro Evaluation";
    }
}
