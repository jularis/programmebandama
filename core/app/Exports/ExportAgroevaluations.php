<?php

namespace App\Exports;

use App\Models\Agroevaluation;
use App\Models\Parcelle;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ExportAgroevaluations implements WithMultipleSheets
{

    public function sheets(): array
    {
        $feuilles = array();
       $feuilles[] = new AgroEvaluationEspeceArbreExport();
        $sheets = [new AgroEvaluationExport,];
        $sheets = array_merge($sheets, $feuilles);


        return $sheets;
    }
}

