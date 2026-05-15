<?php

namespace App\Exports;
 
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ExportStockMagasinSection implements WithMultipleSheets
{
     
  public function sheets(): array 
    {    
      $feuilles=array();  
      $feuilles[] = new StockSectionLivraisonProducteursExport();  

    $sheets = [ new StockSectionLivraisonExport(), ];
    $sheets = array_merge($sheets, $feuilles);
   

    return $sheets; 
    }
        
}
