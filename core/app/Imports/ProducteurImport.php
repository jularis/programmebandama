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

class ProducteurImport implements ToCollection, WithHeadingRow, WithValidation
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
        $cooperatives_id = auth()->user()->cooperative_id;
        $j=0;
        $k='';
        if(count($collection)){

        foreach($collection as $row)
         {

      $local_nom = trim($row['localites']); //Get user names
  $localite = DB::table('localites')->where('nom',$local_nom)->first();

  if($localite !=null)
  {
  $localites_id = $localite->id;

  $coop = DB::table('cooperatives')->where('id', $cooperatives_id)->select('codeApp')->first();
        if($coop !=null)
        {
        $codeProdapp = $this->generecodeProdApp($row['nom'],$row['prenoms'], $coop->codeApp);

        }else{
          $codeProdapp = '';
        }
  $codeProd = $row['codeproducteur']; //Get the user emails

  if(is_null($codeProd))
  {
    $verification ='';
  }else{
    $verification = DB::table('producteurs')->where('codeProd',$codeProd)->first();
  }

if($verification ==null)
{

  $producteur = new Producteur();
  $nationalite = $programme = null;
 if(trim($row['numpiece'])){
  $nationalite = Str::limit(trim($row['numpiece']), 2, "");
  if($nationalite=='C0'){
    $nationalite="CI";
  }
$nationalite = Country::where('iso',$nationalite)->first();
  if($nationalite !=null){
    $nationalite = $nationalite->id;
  }else{
    $nationalite = null;
  }
 }

 if(trim($row['programme']))
 {
  $programme = Programme::where('libelle', trim($row['programme']))->first();
  $programme = $programme->id;

 }

  $agent = DB::table('user_localites')->select('user_id')->where('localite_id', $localites_id)->inRandomOrder()->first();
  if($agent !=null){

      $producteur->localite_id = $localites_id;
      $producteur->codeProd = trim($row['codeproducteur']);
      $producteur->codeProdapp = $codeProdapp;
      $producteur->nom = trim($row['nom']);
      $producteur->prenoms = trim($row['prenoms']);
      $producteur->sexe = trim(ucfirst($row['genre']));
      //$producteur->dateNaiss = Date::excelToDateTimeObject($row['datenaissance'])->format('Y-m-d');
      $producteur->dateNaiss = date('Y-m-d', strtotime(trim($row['datenaissance'])));
      $producteur->phone1 = trim($row['phone1']);
      $producteur->phone2 = trim($row['phone2']);
      $producteur->nationalite = $nationalite;
      $producteur->consentement = trim($row['consentement']);
      $producteur->statut = trim($row['statut']);
      $producteur->certificat = trim($row['anneecertification']);
      $producteur->numPiece = trim($row['numpiece']);
      $producteur->programme_id = $programme;
      $producteur->save();

      if($producteur !=null)
      {
        if(trim($row['statut']) !='Candidat'){
        $certification = Certification::where('nom', trim($row['certification']))->first();

        $prodcertif = new Producteur_certification();
        $prodcertif->producteur_id = $producteur->id;
        $prodcertif->certification = $certification->nom;
        $prodcertif->save();
        }
      }
      $j++;
  }else{

      $producteur->localite_id = $localites_id;
      $producteur->codeProd = trim($row['codeproducteur']);
      $producteur->codeProdapp = $codeProdapp;
      $producteur->nom = trim($row['nom']);
      $producteur->prenoms = trim($row['prenoms']);
      $producteur->sexe = ucfirst(trim($row['genre']));
      //$producteur->dateNaiss = Date::excelToDateTimeObject(trim($row['datenaissance'])->format('Y-m-d');
      $producteur->dateNaiss = date('Y-m-d', strtotime(trim($row['datenaissance'])));
      $producteur->phone1 = trim($row['phone1']);
      $producteur->phone2 = trim($row['phone2']);
      $producteur->consentement = trim($row['consentement']);
      $producteur->statut = trim($row['statut']);
      $producteur->certificat = trim($row['anneecertification']);
      $producteur->numPiece = trim($row['numpiece']);
      $producteur->nationalite = $nationalite;
      $producteur->programme_id = $programme;
      $producteur->userid = auth()->user()->id;
      $producteur->save();
      if($producteur !=null)
      {
        if(trim($row['statut']) !='Candidat'){
          $certification = Certification::where('nom', trim($row['certification']))->first();
          $prodcertif = new Producteur_certification();
          $prodcertif->producteur_id = $producteur->id;
          $prodcertif->certification = $certification->nom;
          $prodcertif->save();
        }

      }
      $j++;
  }


 }else{
    $k .=$codeProd.' , ';
   }

    }

  }
  if(!empty($K)){
    $notify[] = ['error',"Les producteurs dont les codes suivent : $k existent déjà dans la base."];
          return back()->withNotify($notify);
   }
  if(!empty($j))
  {
    $notify[] = ['success',"$j Producteurs ont été crée avec succès."];
    return back()->withNotify($notify);
  }else{
    $notify[] = ['error',"Aucun Producteur n'a été ajouté à la base car ils existent déjà."];
    return back()->withNotify($notify);
 }

}else{
  $notify[] = ['error',"Il n'y a aucune données dans le fichier"];
      return back()->withNotify($notify);
}


  }
    private function generecodeProdApp($nom,$prenoms,$codeApp)
    {
      $action = 'non';

      $data = Producteur::select('codeProdapp')->join('localites as l', 'producteurs.localite_id', '=', 'l.id')->join('sections as s', 'l.section_id', '=', 's.id')->join('cooperatives as c', 's.cooperative_id', '=', 'c.id')->where([
          ['codeProdapp', '!=', null], ['codeApp', $codeApp]
      ])->orderby('producteurs.id', 'desc')->first();

      if ($data != null) {

          $code = $data->codeProdapp;
          if ($code != null) {
              $chaine_number = Str::afterLast($code, '-');
          } else {
              $chaine_number = 0;
          }
      } else {
          $chaine_number = 0;
      }

      $lastCode = $chaine_number + 1;
      $codeP = $codeApp . '-' . gmdate('Y') . '-' . $lastCode;

      do {

          $verif = Producteur::select('codeProdapp')->where('codeProdapp', $codeP)->orderby('id', 'desc')->first();
          if ($verif == null) {
              $action = 'non';
          } else {
              $action = 'oui';
              $code = $codeP;
              $chaine_number = Str::afterLast($code, '-');
              $lastCode = $chaine_number + 1;
              $codeP = $codeApp . '-' . gmdate('Y') . '-' . $lastCode;
          }
      } while ($action != 'non');

      return $codeP;
    }

}
