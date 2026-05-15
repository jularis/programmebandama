<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;  

class ExportProducteurs implements WithMultipleSheets
{
     
  public function sheets(): array 
    {    
    //   $feuilles=array();  
    //   $feuilles[] = new InfosProducteurExport();
    //   $feuilles[] = new InfosCertificationExport();   
    //   $feuilles[] = new InfosTypeculturesExport(); 
    //   $feuilles[] = new InfosAutresActivitesExport();  
    //   $feuilles[] = new InfosMobilesExport();  

    // $sheets = [ new ProducteursExport(), ];
    // $sheets = array_merge($sheets, $feuilles);
    $sheets = [ new ProducteursExport(), ];

    return $sheets; 
    }
  
}
