<?php

namespace App\Exports;
 
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ExportParcelles implements WithMultipleSheets
{
     
  public function sheets(): array 
    {    
      $feuilles=array();  
      $feuilles[] = new ParcelleTypeProtectionExport(); 
      $feuilles[] = new ParcelleAgroForesterieExport();  

    $sheets = [ new ParcellesExport(), ];
    $sheets = array_merge($sheets, $feuilles);
   

    return $sheets; 
    }
        
}
