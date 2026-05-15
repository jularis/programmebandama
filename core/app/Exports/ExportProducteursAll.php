<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;  

class ExportProducteursAll implements WithMultipleSheets
{
     
  public function sheets(): array 
    {    
      $feuilles=array();  
      $feuilles[] = new InfosProducteurExport();
      $feuilles[] = new InfosCertificationExport();   
      $feuilles[] = new InfosTypeculturesExport(); 
      $feuilles[] = new InfosAutresActivitesExport();  
      $feuilles[] = new InfosMobilesExport();  

    $sheets = [ new ProducteursExport(), ];
    $sheets = array_merge($sheets, $feuilles); 

    return $sheets; 
    }
  
}
