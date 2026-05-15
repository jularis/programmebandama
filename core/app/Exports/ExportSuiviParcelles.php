<?php

namespace App\Exports;
  
use Maatwebsite\Excel\Concerns\WithMultipleSheets;  

class ExportSuiviParcelles implements WithMultipleSheets
{
     
  public function sheets(): array 
    {    
      $feuilles=array();  
      $feuilles[] = new SuiviParcellesAgroforesterieExport(); 
      $feuilles[] = new SuiviParcellesOmbrageExport(); 
      $feuilles[] = new SuiviParcellesAnimalExport(); 
      $feuilles[] = new SuiviParcellesParasiteExport(); 
      $feuilles[] = new SuiviParcellesIntrantAnneeDerniereExport(); 
      $feuilles[] = new SuiviParcellesAutreParasiteExport(); 
      $feuilles[] = new SuiviParcellesTraitementExport(); 
      $feuilles[] = new SuiviParcellesInsecteAmiExport();  

    $sheets = [ new SuiviParcellesExport(), ];
    $sheets = array_merge($sheets, $feuilles);
   

    return $sheets; 
    }
  
}
