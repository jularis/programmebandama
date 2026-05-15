<?php

namespace App\Exports;
 
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ExportStockMagasinCentral implements WithMultipleSheets
{
     
  public function sheets(): array 
    {    
      $feuilles=array();  
    //   $feuilles[] = new StockSectionLivraisonProducteursExport();  

    $sheets = [ new StockCentralLivraisonExport(), ];
    $sheets = array_merge($sheets, $feuilles);
   

    return $sheets; 
    }
        
}
