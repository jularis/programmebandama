<?php

namespace App\Imports;

use App\Models\Campagne;
use App\Models\Parcelle;
use App\Models\Producteur;
use App\Models\Application;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\MatiereActive;
use App\Models\ApplicationMaladie;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Models\ApplicationPesticide;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class PhytoImport implements ToCollection, WithHeadingRow, WithValidation
{
    /**
    * @param Collection $collection
    */
    public function rules(): array
    {
        return[
            'codeproducteur' => 'required', 
            'codeparcelle' => 'required', 
        ];
    }
    public function collection(Collection $rows)
    {
        
        $j=0;
        $k='';
        if(count($rows)){
           
        foreach($rows as $row)
         {
          
  $codeProd = $row['codeproducteur'];  
  $codeParc = $row['codeparcelle'];
    $personneApplication = $row['qui_a_realise_lapplication'];
    $applicateur = $row['applicateur'];
    $suiviFormation = $row['a_t_il_suivi_une_formation'];
    $attestion = $row['a_t_il_une_attestation'];
    $bilanSante = $row['a_t_il_fait_un_bilan_de_sante'];
    $independantEpi = $row['possede_t_il_un_epi'];
    $etatEpi = $row['est_il_en_bon_etat'];
    $nom = $row['pesticides'];
    $nomCommercial = $row['nom_commercial'];
    $matiereActive = $row['matieres_actives'];
    $toxicicologie = $row['toxicicologie'];
    $dosage = $row['dose'];
    $doseUnite = $row['unite_dose'];
    $quantite = $row['quantite'];
    $quantiteUnite = $row['unite_quantite'];
    $frequence = $row['frequence'];
    $maladies = $row['maladies_observees_dans_la_parcelle'];
    $superficiePulverisee = $row['superficie_pulverisee'];
    if(Str::contains($superficiePulverisee,","))
    {
    $superficiePulverisee = Str::replaceFirst( ',','.',$superficiePulverisee); 
    }
    $delaisReentree = $row['delais_de_reentree_du_produit_en_jours'];
    $heure_application = $row['duree_dapplication'];
    $date_application = $row['date_dapplication'];
 if($date_application){
  if(is_numeric($date_application)){
    //dd(date('Y-m-d', strtotime($date_application));
    $date_application = date('Y-m-d', strtotime($date_application));
    $heure_application = date('H:i:s', strtotime($heure_application));
     
  }else{
  $date_application=explode('/',$date_application);
  $date_application = $date_application[2].'-'.$date_application[1].'-'.$date_application[0];
  }
 }
  $verification = Parcelle::joinRelationship('producteur')->where([['codeProd',$codeProd],['codeParc',$codeParc]])->first();
   //dd($verification);
if($verification !=null)
{ 

  $campagne = Campagne::active()->first();
  $application = Application::where([['campagne_id',$campagne->id],['parcelle_id',$verification->id]])->first();
        if($application ==null){
          $application = new Application();
      } 
        $application->campagne_id  = $campagne->id;
        $application->parcelle_id  = $verification->id;  
        $application->applicateur_id  = $applicateur; 
        $application->suiviFormation = $suiviFormation;
        $application->attestion = $attestion;
        $application->bilanSante = $bilanSante;
        $application->independantEpi = $independantEpi;
        $application->etatEpi = $etatEpi;
        $application->superficiePulverisee = $superficiePulverisee;
        $application->delaisReentree = $delaisReentree;
        $application->personneApplication = $personneApplication;
        $application->date_application = $date_application;
        $application->heure_application = $heure_application;   
        $application->userid = auth()->user()->id;
        $application->save();
        
        if ($application != null) {
          $id = $application->id;
          
          if ($maladies != null) {
            $maladies = explode(',',$maladies);
            foreach ($maladies as $maladie) {
            $appMaladie = new ApplicationMaladie();
            $appMaladie->application_id = $id;
            $appMaladie->nom = $maladie;
            $appMaladie->save(); 
            }
          }
          if($nom != null){
               
                  $applicationPesticide = new ApplicationPesticide();
                  $applicationPesticide->application_id = $id;
                  $applicationPesticide->nom = $nom;
                  $applicationPesticide->nomCommercial = $nomCommercial;
                  $applicationPesticide->dosage = $dosage;
                  $applicationPesticide->doseUnite = $doseUnite;
                  $applicationPesticide->quantiteUnite = $quantiteUnite;
                  $applicationPesticide->quantite = $quantite;
                  $applicationPesticide->toxicicologie = $toxicicologie;
                  $applicationPesticide->frequence = $frequence;
                  $applicationPesticide->save();

                  if($applicationPesticide != null){
                      $idApplicationPesticide = $applicationPesticide->id;
                      $matiereActive = explode(',',$matiereActive);
                      foreach ($matiereActive as $matiere) {
                          $applicationMatieresactive = new MatiereActive();
                          $applicationMatieresactive->application_id = $id;
                          $applicationMatieresactive->application_pesticide_id = $idApplicationPesticide;
                          $applicationMatieresactive->nom = trim($matiere);
                          $applicationMatieresactive->save();
                      }
                  }
               
          }
         
      }
 
      $j++;
     }else{
         $k .= "parcelle $codeParc du producteur $codeProd ,";    
    }

    }

    if(!empty($j))
    {
      $notify[] = ['success',"$j Application(s) ont été crée avec succès"];
      return back()->withNotify($notify); 
     if($k !=''){ 
        $notify[] = ['error',"La $k n'a pas été trouvée."];
      return back()->withNotify($notify); 
     }
     
    }else{
        if($k !=''){
             
            $notify[] = ['error',"La $k n'a pas été trouvée."];
      return back()->withNotify($notify); 
         } 
   } 
}else{
    
    $notify[] = ['error',"Il n'y a aucune données dans le fichier"];
      return back()->withNotify($notify); 
}

    }

 
    
}
