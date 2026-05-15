<?php

namespace App\Exports;
 
use Maatwebsite\Excel\Concerns\WithMultipleSheets;  

class ExportInspections implements WithMultipleSheets
{
     
  public function sheets(): array 
    {    
      $feuilles=array();  
      $feuilles[] = new InspectionQuestionnairesExport(); 

    $sheets = [ new InspectionsExport(), ];
    $sheets = array_merge($sheets, $feuilles);
   

    return $sheets; 
    }
  
}
