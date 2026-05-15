<?php

namespace App\Http\Controllers\Admin;

use Excel;
use SimpleXMLElement;
use App\Models\Section;
use App\Constants\Status;
use App\Models\Localite; 
use App\Models\Parcelle; 
use App\Models\Cooperative;
use App\Models\Producteur; 
use Illuminate\Support\Str;
use App\Models\ForetClassee;
use Illuminate\Http\Request;
use App\Imports\ParcelleImport;
use App\Exports\ExportParcelles;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\ForetClassees;
use App\Models\ForetClasseeTampon;
use Illuminate\Support\Facades\Hash;

class ForetClasseeController extends Controller
{

    public function index()
    {
        $manager   = auth()->user();
  
        $foretclassees = ForetClassee::get();
        $foretclasseetampons = ForetClasseeTampon::get();
            $total = count($foretclassees);
            $pageTitle  = "Gestion des Forêts Classées($total)";
         
        return view('admin.foretclassee.index',compact('pageTitle','foretclassees','foretclasseetampons'));
    }
 
    public function create()
    {
        $pageTitle = "Importation KML des Forêts Classées";
        $manager   = auth()->user();
       
        return view('admin.foretclassee.create', compact('pageTitle'));
    }

    public function store(Request $request)
    { 
        if($request->file('fichier_kml') !=null){
            $file = $request->file('fichier_kml');
            @unlink(public_path('upload/foretclassee/'));
            $filename = $file->getClientOriginalName();
            $file->move(public_path('upload/foretclassee'), $filename);
            $filePath = public_path('upload/foretclassee/'.$filename); 
            $dataPolygones = $this->getCoordinatesFromKML($filePath); 
         DB::table('foret_classees')->delete();
        //    $old_foret = new ForetClassee(); 
        //    $old_foret->delete();
            $i=0;
            foreach($dataPolygones as $index => $data) {
                
                $foret = new ForetClassee(); 
                $centroid = $this->calculateCentroid($data['coordinates']);
                 
                $foret->nomForet  = htmlentities($data['nom'], ENT_QUOTES | ENT_IGNORE, "UTF-8");  
                $foret->region  = htmlentities($data['region'], ENT_QUOTES | ENT_IGNORE, "UTF-8"); 
                $foret->superficie  =  $data['superficie'];
                $foret->latitude = round($centroid['y'],6);
                $foret->longitude = round($centroid['x'],6);
                $foret->waypoints = $data['coordinates'];
                $foret->save(); 
                $i++;
            }
         
            $notify[] = ['success', "$i Polygones ont été importés avec succès"];

        return back()->withNotify($notify);
        } 
    }
    public function createTampon()
    {
        $pageTitle = "Importation KML des Zones Tampons Forêts Classées";
        $manager   = auth()->user();
       
        return view('admin.foretclassee.createTampon', compact('pageTitle'));
    }

    public function storeTampon(Request $request)
    { 
        if($request->file('fichier_kml') !=null){
            $file = $request->file('fichier_kml');
            @unlink(public_path('upload/foretclasseetampon/'));
            $filename = $file->getClientOriginalName();
            $file->move(public_path('upload/foretclasseetampon'), $filename);
            $filePath = public_path('upload/foretclasseetampon/'.$filename); 
            $dataPolygones = $this->getCoordinatesFromKML($filePath); 
            DB::table('foret_classee_tampons')->delete();
        //    $old_tampon = new ForetClasseeTampon(); 
        //    $old_tampon->delete();
            $i=0;
            foreach($dataPolygones as $index => $data) {
                
                $foret = new ForetClasseeTampon(); 
                $centroid = $this->calculateCentroid($data['coordinates']);
                
                $foret->nomForet  = htmlentities($data['nom'], ENT_QUOTES | ENT_IGNORE, "UTF-8");  
                $foret->region  = htmlentities($data['region'], ENT_QUOTES | ENT_IGNORE, "UTF-8"); 
                $foret->superficie  =  $data['superficie'];
                $foret->latitude = round($centroid['y'],6);
                $foret->longitude = round($centroid['x'],6);
                $foret->waypoints = $data['coordinates'];
                $foret->save(); 
                $i++;
            }
         
            $notify[] = ['success', "$i Polygones ont été importés avec succès"];

        return back()->withNotify($notify);
        } 
    }
    public function getCoordinatesFromKML($filePath) {
        // Charger le fichier KML
        $kmlContent = file_get_contents($filePath);
        
        // Créer un objet SimpleXML pour parcourir le fichier KML
        $kml = new SimpleXMLElement($kmlContent);
        
        // Initialiser un tableau pour stocker les coordonnées 
        $dataArray = array();
        
        // Parcourir chaque Placemark dans le document KML
        foreach ($kml->Document->Folder->Placemark as $placemark) {
            // Récupérer les coordonnées de la balise <coordinates>
            $coordinates = (string)$placemark->MultiGeometry->Polygon->outerBoundaryIs->LinearRing->coordinates;
            $region = (string)$placemark->ExtendedData->SchemaData->SimpleData[0];
            $nom = (string)$placemark->ExtendedData->SchemaData->SimpleData[2]; 
            $superficie = (string)$placemark->ExtendedData->SchemaData->SimpleData[3];
            // Ajouter les données au tableau
            $coordinatesArray[] = $coordinates;
            $dataArray[] = array(
                'coordinates' => trim($coordinates),
                'region' => $region,
                'nom' => $nom,
                'superficie' => $superficie
            );
        }
        
        // Retourner le tableau des coordonnées
        return $dataArray;
    }

    public function calculateCentroid($coordinates) {
        // Séparer les paires de coordonnées
        $points = explode("\n", trim($coordinates));
        
        $xSum = 0;
        $ySum = 0;
        $pointCount = count($points);
    
        // Parcourir chaque paire de coordonnées
        foreach ($points as $point) {
            $coords = explode(',', trim($point));
    
            // Ajouter les coordonnées au total
            $xSum += (float)$coords[0];
            $ySum += (float)$coords[1];
        }
    
        // Calculer le centroïde
        $centroidX = $xSum / $pointCount;
        $centroidY = $ySum / $pointCount;
    
        return array('x' => $centroidX, 'y' => $centroidY);
    }
    private function generecodeparc($idProd,$codeProd)
    { 
      if($codeProd)
      {
        $action = 'non'; 

        $data = Parcelle::select('codeParc')->where([ 
          ['producteur_id',$idProd],
          ['codeParc','!=',null]
          ])->orderby('id','desc')->first();
          
        if($data !=''){
         
            $code = $data->codeParc;  
            
            if($code !=''){
              $chaine_number = Str::afterLast($code,'-');
        $numero = Str::after($chaine_number, 'P');
        $numero = $numero+1;
            }else{
              $numero = 1;
            } 
        $codeParc=$codeProd.'-P'.$numero;

        do{

          $verif = Parcelle::select('codeParc')->where('codeParc',$codeParc)->orderby('id','desc')->first(); 
        if($verif ==null){
            $action = 'non';
        }else{
            $action = 'oui';
            $code = $data->codeParc;  
            
            if($code !=''){
              $chaine_number = Str::afterLast($code,'-');
        $numero = Str::after($chaine_number, 'P');
        $numero = $numero+1;
            }else{
              $numero = 1;
            } 
        $codeParc=$codeProd.'-P'.$numero;

        }

    }while($action !='non');

        }else{ 
            $codeParc=$codeProd.'-P1';
        }
      }
      else{
        $codeParc='';
      }

        return $codeParc;
    }


    public function edit($id)
    {
        $pageTitle = "Mise à jour de la parcelle";
        $localites = Localite::joinRelationship('section')->where([['cooperative_id',$manager->cooperative_id],['localites.status',1]])->get();
        $producteurs  = Producteur::with('localite')->get();
        $parcelle   = Parcelle::findOrFail($id);
        return view('admin.parcelle.edit', compact('pageTitle', 'localites', 'parcelle','producteurs'));
    } 

    public function status($id)
    {
        return Parcelle::changeStatus($id);
    }

    public function exportExcel()
    {
        return (new ExportParcelles())->download('parcelles.xlsx');
    }

    public function  uploadContent(Request $request)
    {
        Excel::import(new ParcelleImport, $request->file('uploaded_file'));
        return back();
    }
}
