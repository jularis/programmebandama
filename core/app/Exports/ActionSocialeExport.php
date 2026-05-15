<?php

namespace App\Exports;

use App\Models\ActionSociale;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\Exportable;

class ActionSocialeExport implements  FromView, WithTitle
{
  /**
    * @return \Illuminate\Support\Collection
    */
    use Exportable;

    public function view(): View
    {
        // TODO: Implement view() method.
        return view('manager.action-sociale.ActionSocialeAllExcel',[
            'actions' => ActionSociale::joinRelationship('cooperative')->where('cooperative_id',auth()->user()->cooperative_id)->get()
        ]);
    } 

    public function title(): string
    {
        Return "Action Sociale";
    }
}
