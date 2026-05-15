<?php

namespace App\Http\Controllers;
use App\Constants\Status;
use App\Models\Campagne;
use Illuminate\Http\Request;
use App\Models\User; 
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use App\Models\Cooperative; 
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Facades\DB;
class AuthController extends Controller
{
    //
    // methode d'authentification

    public function getdelegues(Request $request)
    {
       
        $input = $request->all(); 
        $cooperativesid = $input['cooperativesid'];
        $delegues = User::whereHas(
            'roles', function($q){
                $q->where('name', 'Delegue');
                }
                )
                ->where('cooperative_id', $cooperativesid)
                ->select('id',DB::raw("CONCAT(lastname,' ', firstname) as nom"))
                ->get();
      return response()->json($delegues, 201);
    }

    public function getapplicateurs(Request $request)
    { 
        $input = $request->all(); 
        $cooperativesid = $input['cooperativesid'];
        $applicateur = User::whereHas(
            'roles', function($q){
                $q->where('name', 'Applicateur');
                }
                )
                ->where('cooperative_id', $cooperativesid)
                ->select('id',DB::raw("CONCAT(lastname,' ', firstname) as nom"))
                ->get();
      return response()->json($applicateur, 201);
    }

    public function getdomain(Request $request){
        $input = $request->all(); 
        $domaine = DB::table('cooperatives')->where('codeApp', $input['codeapp'])->select('domaine')->first();
        if (!$domaine) {
            return response()->json([
                'status_code' => 500,
                'message' => 'Ce code n\'existe pas.'
            ]);
            }
        
            return response()->json([ 
            'status_code' => 200,
            'message' => $domaine->domaine,
            ]);
    }
    public function getCampagne(){

        $donnees = Campagne::active()->first();
        //modif de campage
        return response()->json(array($donnees) , 201);
    }
    
    public function getUpdateapp(){

        $donnees = DB::table('updateapps')->orderby('id','DESC')->select('version','url')->first();
        return response()->json($donnees , 201);
    }

    public function connexion(Request $request)
    {
	 
        try {

            $request->validate([
            'username' => 'required',
            'password' => 'required'
            ]);
    
            
            if (!Auth::attempt($request->only('username', 'password'))) {
            return response()->json([
                'status_code' => 500,
                'message' => 'Désolé, mot de passe non reconnu. Avez-vous oublié vos identifiants? Merci de contacter l\'administrateur.'
            ]);
            }
            
            $user = User::active()->where('username', $request->input('username'))->first();
            $cooperative = null;

            if($user)
            {
                $cooperative = $user->cooperative()->first();
                // dd($user->getAllPermissions()->map (function ($item, $key) {
                //     return $item->name;
                // })->toArray());
                
                if(($user->type_compte=='mobile') || ($user->type_compte=='mobile-web'))
                {
                $permissionsroles = array();
                $menuliste = array();
                $tokenResult = $user->createToken('authToken')->plainTextToken;
            if(!empty($user->getAllPermissions()))
            { 
                foreach($user->getAllPermissions() as $v)
                {
                    $permissionsrolesName=Str::replace("manager.staff.","",$v->name);
                    
                    $permissionsrolesName=Str::replace("manager.suivi.","",$permissionsrolesName);
                    $permissionsrolesName=Str::replace("manager.traca.","",$permissionsrolesName);
                    //ajout de agro
                    $permissionsrolesName=Str::replace("agro.","",$permissionsrolesName);
                    $permissionsrolesName=Str::replace("manager.","",$permissionsrolesName);  
                    $permissionsrolesName=Str::before($permissionsrolesName,".exportExcel");
                    $permissionsrolesName=Str::before($permissionsrolesName,".code");
                    $permissionsrolesName=Str::before($permissionsrolesName,".verify");
                    $permissionsrolesName=Str::before($permissionsrolesName,".reset");
                    $permissionsrolesName=Str::before($permissionsrolesName,".attendance-settings");
                    $permissionsrolesName=Str::before($permissionsrolesName,".leaves-settings");
                    $permissionsrolesName=Str::before($permissionsrolesName,".cooperative-settings");
                    $permissionsrolesName=Str::before($permissionsrolesName,".durabilite-settings");
                    $permissionsrolesName=Str::before($permissionsrolesName,".section-settings");
                    $permissionsrolesName=Str::before($permissionsrolesName,".localite-settings");
                    $permissionsrolesName=Str::before($permissionsrolesName,".campagne");
                    $permissionsrolesName=Str::before($permissionsrolesName,".travauxDangereux");
                    $permissionsrolesName=Str::before($permissionsrolesName,".travauxLegers");
                    $permissionsrolesName=Str::before($permissionsrolesName,".arretEcole");
                    $permissionsrolesName=Str::before($permissionsrolesName,".get");
                    $permissionsrolesName=Str::before($permissionsrolesName,".mapping");
                    $permissionsrolesName=Str::before($permissionsrolesName,".update");
                    $permissionsrolesName=Str::before($permissionsrolesName,".usine");
                    $permissionsrolesName=Str::before($permissionsrolesName,".magcentral");
                    $permissionsrolesName=Str::before($permissionsrolesName,".section");
                    $permissionsrolesName=Str::before($permissionsrolesName,".stock"); 
                    
                    $permissionsroles[]=Str::before($permissionsrolesName,".");

                    $permissionsroles[]=Str::replace(".","_", Str::beforeLast($permissionsrolesName,".")); 
                   
                }
            //dd($permissionsroles);
                
                $nolisting = array(
                    "localites",
                    "postplanting", 
                    "producteur",
                    "parcelle", 
                    "livraison",
                    "parcelles", 
                    "formation", 
                    "estimation",
                    "evaluation",
                    "inspection",
                    "ssrteclmrs",
                    "menage",
                    "formation_visiteur",
                    "livraison_magcentral",
                    "evaluation",
                    "distribution",
                    "application",  
                );
                

                $permissionsroles = array_unique($permissionsroles);  
                foreach($permissionsroles as $res){
                    // if(in_array($res,$nolisting)){
                        
                    // }
                     if($res !=""){
                        $menuliste[] = strtoupper($res);
                     }
                    
                }
                
                
                
            }
            // $menuliste = array("LOCALITES",
            // "PRODUCTEURS",
            // "PARCELLES", 
            // "LIVRAISONS",
            // "SUIVIPARCELLES",
            // "SUIVIFORMATIONS", 
            // "ESTIMATIONS",
            // "EVALUATIONS",
            // "SSRTECLMRS",
            // "MENAGES",
            // "SUIVIAPPLICATIONS"
            //     );
            asort($menuliste);
            // $user = User::where('id',$user->id)->first();
            return response()->json([
                'menu' =>array_values(Arr::whereNotNull($menuliste)),
                'results' => $user,
                'cooperative' => $cooperative, 
            'status_code' => 200,
            'access_token' => $tokenResult,
            'token_type' => 'Jularis',
            ]);
        }else{
            return response()->json([
                'status_code' => 500,
                'message' => 'Vous n\'êtes pas autorisé à utiliser l\'application mobile.'
            ]);
        }
            }else{
                return response()->json([
                    'status_code' => 500,
                    'message' => 'Ce compte n\'existe pas ou a été désactivé par l\'administrateur. Veuillez contacter votre administrateur pour rétablir votre compte.'
                ]);
            }
            
            
        } catch (Exception $error) {
            return response()->json([
            'status_code' => 500,
            'message' => 'Error in Login',
            'error' => $error,
            ]);
        }
    } 

    public function getRoleUser(Request $request)
    {
	   
            $user = User::active()->where([['id', $request->userid],['cooperative_id',$request->cooperativeid]])->first();
            $cooperative = null;

            if($user)
            {
                $cooperative = $user->cooperative()->first();
                // dd($user->getAllPermissions()->map (function ($item, $key) {
                //     return $item->name;
                // })->toArray());
                
                if(($user->type_compte=='mobile') || ($user->type_compte=='mobile-web'))
                {
                $permissionsroles = array();
                $menuliste = array();
                $tokenResult = $user->createToken('authToken')->plainTextToken;
            if(!empty($user->getAllPermissions()))
            { 
                foreach($user->getAllPermissions() as $v)
                {
                    $permissionsrolesName=Str::replace("manager.staff.","",$v->name);
                    
                    $permissionsrolesName=Str::replace("manager.suivi.","",$permissionsrolesName);
                    $permissionsrolesName=Str::replace("manager.traca.","",$permissionsrolesName);
                    //ajout de agro
                    $permissionsrolesName=Str::replace("agro.","",$permissionsrolesName);
                    $permissionsrolesName=Str::replace("manager.","",$permissionsrolesName);  
                    $permissionsrolesName=Str::before($permissionsrolesName,".exportExcel");
                    $permissionsrolesName=Str::before($permissionsrolesName,".code");
                    $permissionsrolesName=Str::before($permissionsrolesName,".verify");
                    $permissionsrolesName=Str::before($permissionsrolesName,".reset");
                    $permissionsrolesName=Str::before($permissionsrolesName,".attendance-settings");
                    $permissionsrolesName=Str::before($permissionsrolesName,".leaves-settings");
                    $permissionsrolesName=Str::before($permissionsrolesName,".cooperative-settings");
                    $permissionsrolesName=Str::before($permissionsrolesName,".durabilite-settings");
                    $permissionsrolesName=Str::before($permissionsrolesName,".section-settings");
                    $permissionsrolesName=Str::before($permissionsrolesName,".localite-settings");
                    $permissionsrolesName=Str::before($permissionsrolesName,".campagne");
                    $permissionsrolesName=Str::before($permissionsrolesName,".travauxDangereux");
                    $permissionsrolesName=Str::before($permissionsrolesName,".travauxLegers");
                    $permissionsrolesName=Str::before($permissionsrolesName,".arretEcole");
                    $permissionsrolesName=Str::before($permissionsrolesName,".get");
                    $permissionsrolesName=Str::before($permissionsrolesName,".mapping");
                    $permissionsrolesName=Str::before($permissionsrolesName,".update");
                    $permissionsrolesName=Str::before($permissionsrolesName,".usine");
                    $permissionsrolesName=Str::before($permissionsrolesName,".magcentral");
                    $permissionsrolesName=Str::before($permissionsrolesName,".section");
                    $permissionsrolesName=Str::before($permissionsrolesName,".stock"); 
                    
                    $permissionsroles[]=Str::before($permissionsrolesName,".");

                    $permissionsroles[]=Str::replace(".","_", Str::beforeLast($permissionsrolesName,".")); 
                   
                }
            //dd($permissionsroles);
                 

                $permissionsroles = array_unique($permissionsroles);  
                foreach($permissionsroles as $res){
                     
                     if($res !=""){
                        $menuliste[] = strtoupper($res);
                     }
                    
                }
                
                
                
            }
            
            asort($menuliste); 
            return response()->json([
                'menu' =>array_values(Arr::whereNotNull($menuliste)),
                'results' => $user,
                'cooperative' => $cooperative, 
            'status_code' => 200,
            'access_token' => $tokenResult,
            'token_type' => 'Jularis',
            ]);
        }else{
            return response()->json([
                'status_code' => 500,
                'message' => 'Vous n\'êtes pas autorisé à utiliser l\'application mobile.'
            ]);
        }
            }else{
                return response()->json([
                    'status_code' => 500,
                    'message' => 'Ce compte n\'existe pas ou a été désactivé par l\'administrateur. Veuillez contacter votre administrateur pour rétablir votre compte.'
                ]);
            }
            
            
        
    }

}
