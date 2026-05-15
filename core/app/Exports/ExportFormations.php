<?php

namespace App\Exports;
 
use Maatwebsite\Excel\Concerns\WithMultipleSheets;  

class ExportFormations implements WithMultipleSheets
{
     
  public function sheets(): array 
    {    
      $feuilles=array();  
      $feuilles[] = new SuiviFormationProducteurExport();
      $feuilles[] = new SuiviFormationTypeFormationThemeExport();   
      $feuilles[] = new SuiviFormationThemeSousThemeExport();  
      $feuilles[] = new SuiviFormationFormationVisiteursExport();  

    $sheets = [ new SuiviFormationsExport(), ];
    $sheets = array_merge($sheets, $feuilles);
   

    return $sheets; 
    }
  
}
