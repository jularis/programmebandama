<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets; 

class ExportSsrteclmrs implements WithMultipleSheets
{
     
  public function sheets(): array 
    {    
      $feuilles=array();  
      $feuilles[] = new LieutravauxdangereuxExport();
      $feuilles[] = new LieutravauxlegersExport();    
      $feuilles[] = new TravauxdangereuxExport();
      $feuilles[] = new TravauxlegersExport();
      $feuilles[] = new RaisonarretecolesExport();

    $sheets = [ new SsrteclmrsExport(), ];
    $sheets = array_merge($sheets, $feuilles);
   

    return $sheets; 
    }
  
}

