<?php

namespace App\Imports;

use App\Models\Certification;
use App\Models\Country;
use App\Models\Producteur;
use App\Models\Producteur_certification;
use App\Models\Programme;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithValidation;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class ProducteurUpdateImport implements ToCollection, WithHeadingRow, WithValidation
{
    /**
    * @param Collection $collection
    */
    public function rules(): array
    {
        return[
            'nom' => 'required',
            'prenoms' => 'required',
        ];
    }
    public function collection(Collection $collection)
    { 
        $j=0;
        $k='';
        if(count($collection)){
 
        foreach($collection as $row)
         {
 
$producteur = Producteur::where('codeProd',$row['code_producteur'])->first();
if($producteur !=null)
{  
  $nationalite = Country::where('nicename',$row['nationalite'])->Orwhere('nationalite',$row['nationalite'])->first();
  if($nationalite !=null){
    $nationalite = $nationalite->id;
  }
      $producteur->codeProd = $row['code_producteur']; 
      $producteur->nom = $row['nom'];
      $producteur->prenoms = $row['prenoms'];
      $producteur->sexe = ucfirst($row['sexe']);
      //$producteur->dateNaiss = Date::excelToDateTimeObject($row['datenaissance'])->format('Y-m-d');
      $producteur->dateNaiss = date('Y-m-d', strtotime($row['date_naissance'])); 
      $producteur->phone1 = $row['phone1'];
      $producteur->phone2 = $row['phone2'];
      $producteur->nationalite = $nationalite;  
      $producteur->autreMembre = $row['autre_membre'];
      $producteur->autrePhone = $row['autre_phone'];
      $producteur->niveau_etude = $row['niveau_etude'];
      $producteur->type_piece = $row['type_piece']; 
      $producteur->numPiece = $row['numero_piece']; 
      $producteur->consentement = $row['consentement'];
      $producteur->statutMatrimonial = $row['statut_matrimonial'];
      $producteur->proprietaires = $row['proprietaires'];
      $producteur->plantePartage = $row['plante_partage'];
      $producteur->habitationProducteur = $row['habitation_producteur'];
      $producteur->anneeFin = $row['annee_fin'];
      $producteur->anneeDemarrage = $row['annee_demarrage'];
      $producteur->num_ccc = $row['numero_ccc'];
      $producteur->numCMU = $row['numero_cmu'];
      $producteur->carteCMU = $row['carte_cmu'];
      $producteur->numSecuriteSociale = $row['numero_securite_sociale'];
      $producteur->typeCarteSecuriteSociale = $row['type_carte_securite_sociale'];
      $producteur->statut = $row['statut'];
      $producteur->certificat = $row['annee_certification'];
      $producteur->userid = auth()->user()->id; 
      $producteur->save();
      // if($producteur !=null)
      // {
      //   $certification = Certification::where('nom', $row['certification'])->first();
      //   $prodcertif = new Producteur_certification();
      //   $prodcertif->producteur_id = $producteur->id;
      //   $prodcertif->certification = $certification->nom;
      //   $prodcertif->save();
      // }
      $j++; 
 }else{
  $k .=$row['code_producteur'].' , ';  
 }
 
  }
  if(!empty($j))
  {
    $notify[] = ['success',"$j Producteurs ont été crée avec succès."];
    return back()->withNotify($notify);
  }else{
    $notify[] = ['error',"Les producteurs dont les codes suivent : $k n'existent pas dans la base."];
    return back()->withNotify($notify);
 } 
    
}else{
  $notify[] = ['error',"Il n'y a aucune données dans le fichier"];
      return back()->withNotify($notify); 
}

   
  }
 
    
}
