<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ExportActionSociales implements WithMultipleSheets
{

    public function sheets(): array
    {
        $feuilles = array();
        $feuilles[] = new ActionSocialeAutreBeneficiaireExport();
        $feuilles[] = new ActionSocialeLocaliteExport();
        $feuilles[] = new ActionSocialePartenaireExport();
        $sheets = [new ActionSocialeExport,];
        $sheets = array_merge($sheets, $feuilles);


        return $sheets;
    }
}
