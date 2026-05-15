<?php

namespace App\Imports;

use App\Models\Parcelle;
use App\Models\Producteur;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithValidation;

class EstimationImport implements ToCollection, WithHeadingRow, WithValidation
{
    /**
    * @param Collection $collection
    */
    public function rules(): array
    {
        return[
            'estimproduction' => 'required', 
            'codeproducteur' => 'required',
            'superficie' => 'required',
        ];
    }
    public function collection(Collection $collection)
    {
        $cooperatives_id = request()->coop_id;
        $j=0;
        $k='';
        if(count($collection)){
 
        foreach($collection as $row)
         {
  $superficie = $row['superficie'];
  $codeProd = $row['codeproducteur']; 
  //Get the user emails
  $superficie=is_numeric(trim($superficie)) ? round(trim($superficie),2) : trim($superficie);
  $verification = DB::table('parcelles as pa')->join('producteurs as p','pa.producteur_id','=','p.id')->orWhere('p.codeProd',$codeProd)->orWhere('p.codeProdapp',$codeProd)->where('pa.superficie',$superficie)->select('pa.*','p.codeProdapp','p.codeProd')->first();

if($verification !=null)
{
    $codeProdapp = $verification->codeProdapp; 
    $codeProd = $verification->codeProd;
    $estimproduction = $row['estimproduction'];
    $estimproduction = Str::before($estimproduction,' ');
    if(Str::contains($estimproduction,","))
    {
    $estimproduction = Str::replaceFirst( ',','.',$estimproduction);
    if(Str::contains($estimproduction,","))
        {
        $estimproduction = Str::replaceFirst( 'm²','',$estimproduction);
        } 
    }

    $campagne = DB::table('campagnes')->where('status',1)->orderby('id','DESC')->select('id')->first();

      $insert_data = array(  
  'parcelle_id' => $verification->id,
  'campagne_id' => $campagne->id,  
  'EsP' => $estimproduction, 
  'date_estimation' => NOW(),
  'userid' => auth()->user()->id,
  'created_at' => NOW(),
  'updated_at' => NOW() 
      );
      DB::table('estimations')->insert($insert_data); 
      $j++;
     }else{
         $k .=$codeProd.' , ';   
         $notify[] = ['error',"Les Producteurs dont les codes suivent : $k n'ont pas de parcelles dans la base."];
      return back()->withNotify($notify);
    }

    }

    if(!empty($j))
    {
     $notify[] = ['success',"$j Estimations ont été crée avec succès."];
      return back()->withNotify($notify);
     if($k !=''){ 
        $notify[] = ['error',"Les Producteurs dont les codes suivent : $k n'ont pas de parcelles dans la base."];
      return back()->withNotify($notify);
     }
     
    }else{
        if($k !=''){ 
            $notify[] = ['error',"Les Producteurs dont les codes suivent : $k n'ont pas de parcelles dans la base."];
      return back()->withNotify($notify);
         } 
   } 
}else{ 
    $notify[] = ['error',"Il n'y a aucune données dans le fichier."];
      return back()->withNotify($notify);
}

    }


     
    
}
