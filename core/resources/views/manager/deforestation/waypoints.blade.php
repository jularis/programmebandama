@extends('manager.layouts.app')
@section('panel')
<?php
use Illuminate\Support\Arr;
use Illuminate\Support\Str; 
$listePolygon = ['Parcelles Producteurs'=>'PP','Forets classées'=>'FC','Zones Tampons'=>'ZT'];
?>
    <div class="row">
        <div class="col-lg-12">
        <div class="card b-radius--10 mb-3">
        <div class="card-header bg--warning">
            Filtre Général
          </div>
                <div class="card-body">
                    <form action="">
                        <div class="d-flex flex-wrap gap-4">
                            <input type="hidden" name="table" value="parcelles" />
                            <div class="flex-grow-1">
                                <label>@lang('Section')</label>
                                <select name="section" class="form-control" id="section">
                                    <option value="">@lang('Toutes')</option>
                                    @foreach ($sections as $local)
                                        <option value="{{ $local->id }}" {{ request()->section == $local->id ? 'selected' : '' }}>{{ $local->libelle }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="flex-grow-1">
                                <label>@lang('Localité')</label>
                                <select name="localite" class="form-control" id="localite">
                                    <option value="">@lang('Toutes')</option>
                                    @foreach ($localites as $local)
                                        <option value="{{ $local->id }}" data-chained="{{ $local->section_id }}" {{ request()->localite == $local->id ? 'selected' : '' }}>{{ $local->nom }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="flex-grow-1">
                                <label>@lang('Producteur')</label>
                                <select name="producteur" class="form-control" id="producteur">
                                    <option value="">@lang('Tous')</option>
                                    @foreach ($producteurs as $local)
                                        <option value="{{ $local->id }}" data-chained="{{ $local->localite_id }}" {{ request()->producteur == $local->id ? 'selected' : '' }}>{{ $local->nom }} {{ $local->prenoms }} ({{ $local->codeProd }})</option>
                                    @endforeach
                                </select>
                            </div> 
                            <div class="flex-grow-1 align-self-end">
                                <button class="btn btn--primary w-100 h-45"><i class="fas fa-filter"></i>
                                    @lang('Filtrer')</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            </div>
            <div class="col-lg-12">
        <div class="card b-radius--10 mb-3 ">
        <div class="card-header bg--primary">
            Filtre par Type de Polygones
          </div>
                <div class="card-body">
                    
                    <form action="">
                        <div class="d-flex flex-wrap gap-4">
                            <input type="hidden" name="table" value="foretclassees" /> 
                            <div class="flex-grow-1">
                                <label>@lang('Type de Polygone')</label>
                                <select name="typepolygone[]" multiple class="form-control select2-multi-select" id="typepolygone">
                                    <option value="">@lang('Toutes')</option>
                                    @foreach ($listePolygon as $pol=>$keypol)
                                    <option value="{{ $keypol }}" @selected(in_array(@$keypol,@request()->typepolygone ?? $listePolygon))>{{ $pol }}</option>
                                @endforeach
                                </select> 
                                </div> 
                            <div class="flex-grow-1 align-self-end">
                                <button class="btn btn--primary w-100 h-45"><i class="fas fa-filter"></i>
                                    @lang('Filtrer')</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>    
        </div>
        </div>
 <div class="row">
            <div class="col-lg-12">
            <div class="card b-radius--10 ">
                <div class="card-body  p-0">
                    <div class="table-responsive--sm table-responsive" id="map" style="height: 800px;">
                         
                    </div>
                </div>
                
            </div>
        </div>
    </div>
    <?php
$arrData = '';
$newCoord = '';
$lat = '';
$long = '';
$total = 0;
$mappingparcellle ='';
$pointsPolygon = array();
$seriescoordonates=array();
$a=1;

if(isset($parcelles) && count($parcelles)){

    $total = count($parcelles);

    foreach ($parcelles as $data) {
        
        if($data->latitude==0 || $data->latitude==null || $data->latitude==1){
            continue;
        }
            $lat = isset($data->latitude) ? htmlentities($data->latitude, ENT_QUOTES | ENT_IGNORE, "UTF-8") : 'Non Disponible';
            $long= isset($data->longitude) ? htmlentities($data->longitude, ENT_QUOTES | ENT_IGNORE, "UTF-8") : 'Non Disponible'; 
            $producteur = isset($data->producteur->nom) ? htmlentities($data->producteur->nom, ENT_QUOTES | ENT_IGNORE, "UTF-8").' '.htmlentities($data->producteur->prenoms, ENT_QUOTES | ENT_IGNORE, "UTF-8") : 'Non Disponible';
            $code= isset($data->producteur->codeProd) ? htmlentities($data->producteur->codeProd, ENT_QUOTES | ENT_IGNORE, "UTF-8") : 'Non defini';
            $parcelle = isset($data->codeParc) ? htmlentities($data->codeParc, ENT_QUOTES | ENT_IGNORE, "UTF-8") : 'Non Disponible';
            $localite=isset($data->producteur->localite->nom) ? htmlentities($data->producteur->localite->nom, ENT_QUOTES | ENT_IGNORE, "UTF-8") : 'Non Disponible';
            $section=isset($data->producteur->localite->section->libelle) ? htmlentities($data->producteur->localite->section->libelle, ENT_QUOTES | ENT_IGNORE, "UTF-8") : 'Non Disponible';
            $cooperative=isset($data->producteur->localite->section->cooperative->name) ? htmlentities($data->producteur->localite->section->cooperative->name, ENT_QUOTES | ENT_IGNORE, "UTF-8") : 'Non Disponible';
            $annee = isset($data->anneeCreation) ? htmlentities($data->anneeCreation, ENT_QUOTES | ENT_IGNORE, "UTF-8") : 'Non Disponible';
            $culture= isset($data->culture) ? htmlentities($data->culture, ENT_QUOTES | ENT_IGNORE, "UTF-8") : 'Non Disponible';
            $superficie= isset($data->superficie) ? htmlentities($data->superficie, ENT_QUOTES | ENT_IGNORE, "UTF-8") : 'Non Disponible';
            $proprietaire = 'Coopérative:'. $cooperative.'<br>Section:'. $section.'<br>Localite:'. $localite.'<br>Producteur : '.$producteur.'<br>Code producteur:'. $code.'<br>Code Parcelle:'. $parcelle.'<br>Année creation:'. $annee.'<br>Latitude:'. $lat.'<br>Longitude:'. $long.'<br>Superficie:'. $superficie.' ha';
     
 
        $polygonCoordinates = "['".$proprietaire."',".$long.",".$lat."]"; 
    $pointsPolygon[] = $polygonCoordinates;
}
$pointsPolygon = Str::replace('"','',json_encode($pointsPolygon));
 $pointsPolygon = Str::replace("''","'Non Disponible'",$pointsPolygon);
}
// Chargement des forets classées
$lat = '';
$long = '';
$totalF = 0; 
$pointsPolygonF = array();
$seriescoordonatesF=array();
$a=1;

if(isset($foretclassees) && count($foretclassees)){

  $totalF = count($foretclassees);

  foreach ($foretclassees as $data) {
      
       
      if($data->waypoints !=null)
      {
          $lat = htmlentities($data->latitude, ENT_QUOTES | ENT_IGNORE, "UTF-8");
  $long= htmlentities($data->longitude, ENT_QUOTES | ENT_IGNORE, "UTF-8"); 
  $producteur = $data->nomForet; 
  $region= $data->region;
  $superficie= round(htmlentities($data->superficie, ENT_QUOTES | ENT_IGNORE, "UTF-8")*0.0001,2);
   $polygon ='';

      $coords = explode(" ", $data->waypoints);
      
    //   $coords = Arr::where($coords, function ($value, $key) {
    //       if($value !="")
    //       {
    //           return  $value;
    //       }
          
    //   });
      
 
       $nombre = count($coords); 
       
       $i=0; 
      foreach($coords as $data2) {
           
              $i++;
              $coords2 = explode(',', $data2); 
              if(isset($coords2[1]) && isset($coords2[0]))
              {
                  $polygon .='{ lat: ' . $coords2[1] . ', lng: ' . $coords2[0] .'},';
              } 
          
      }
      
      $polygonCoordinates ='['.$polygon.']';
      
      }
      $seriescoordonatesF[]= $polygonCoordinates;
      $pointsPolygonF[] = "['".$producteur."','".$long."','".$lat."','".$region."','".$superficie."']";
  }
 
$pointsPolygonF = Str::replace('"','',json_encode($pointsPolygonF));
$pointsPolygonF = Str::replace("''","'Non Disponible'",$pointsPolygonF);

} 

// Chargement Zones Tampons
$lat = '';
$long = '';
$totalZT = 0; 
$pointsPolygonZT = array();
$seriescoordonatesZT=array();
$a=1;

if(isset($foretclasseetampons) && count($foretclasseetampons)){

  $totalZT = count($foretclasseetampons);

  foreach ($foretclasseetampons as $data) {
      
       
      if($data->waypoints !=null)
      {
          $lat = htmlentities($data->latitude, ENT_QUOTES | ENT_IGNORE, "UTF-8");
  $long= htmlentities($data->longitude, ENT_QUOTES | ENT_IGNORE, "UTF-8"); 
  $producteur = htmlentities($data->nomForet, ENT_QUOTES | ENT_IGNORE, "UTF-8"); 
  $region= htmlentities($data->region, ENT_QUOTES | ENT_IGNORE, "UTF-8");
  $superficie= round(htmlentities($data->superficie, ENT_QUOTES | ENT_IGNORE, "UTF-8")*0.0001,2);
   $polygon ='';

      $coords = explode(' ', $data->waypoints);
      
      $coords = Arr::where($coords, function ($value, $key) {
          if($value !="")
          {
              return  $value;
          }
          
      });
      
 
       $nombre = count($coords); 
       
       $i=0; 
      foreach($coords as $data2) {
           
              $i++;
              $coords2 = explode(',', $data2); 
              if(isset($coords2[1]) && isset($coords2[0]))
              {
                  $polygon .='{ lat: ' . $coords2[1] . ', lng: ' . $coords2[0] .'},';
              } 
          
      }
      
      $polygonCoordinates ='['.$polygon.']';
      
      }
      $seriescoordonatesZT[]= $polygonCoordinates;
      $pointsPolygonZT[] = "['".$producteur."','".$long."','".$lat."','".$region."','".$superficie."']";
  }
 
$pointsPolygonZT = Str::replace('"','',json_encode($pointsPolygonZT));
$pointsPolygonZT = Str::replace("''","'Non Disponible'",$pointsPolygonZT);

} 

$fc=null;
$zt=null;
$pp=null; 
if(isset(request()->typepolygone) && (in_array('FC',request()->typepolygone)))
{
    $fc=1;
}

if(isset(request()->typepolygone) && (in_array('ZT',request()->typepolygone)))
{
    $zt=1;
}

if(isset(request()->typepolygone) && (in_array('PP',request()->typepolygone)))
{
    $pp=1;
}
?>
    <x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')   
            <div class="btn-group h-45" role="group" aria-label="Basic example">
  <button type="button" style="background-color:#FF0000;" class="btn text-white">Parcelles Producteurs</button>
  <button type="button" style="background-color:#FFFF00;" class="btn">Forêts Classées</button> 
</div>
<a href="{{ route('manager.agro.deforestation.index') }}" class="btn  btn-outline--primary h-45"><i
            class="las la-map-marker"></i> Risque de Deforestation par Polygones</a>
@endpush
@push('style')
    <style>
        .table-responsive {
            overflow-x: auto;
        }
    </style>
@endpush
@push('style-lib')
    <link rel="stylesheet" href="{{ asset('assets/fcadmin/css/vendor/datepicker.min.css') }}">
@endpush
@push('script')
<script src="https://polyfill.io/v3/polyfill.min.js?features=default"></script>
 <script async src="https://maps.googleapis.com/maps/api/js?key={{env('GOOGLE_MAPS_KEY')}}" ></script>  
@endpush
@push('script')
    <script>  
    let map;
let infoWindow;
@if(!is_array($pointsPolygon))
var locations = <?php echo $pointsPolygon; ?>;
var total = <?php echo $total; ?>;
@endif

@if(!is_array($pointsPolygonF))
var locationsF = <?php echo $pointsPolygonF; ?>;
var totalF = <?php echo $totalF; ?>;
@endif

@if(!is_array($pointsPolygonZT))
var locationsZT = <?php echo $pointsPolygonZT; ?>;
var totalZT = <?php echo $totalZT; ?>;
@endif

window.onload = function () {
  map = new google.maps.Map(document.getElementById("map"), {
    zoom: 7,
    center: { lat: 6.881703, lng: -5.500461 },
    mapTypeId: "hybrid",
  });
 
// Affichage des waypoints
@if(($fc==null && $zt==null && $pp==null) || $pp==1)
var infowindow = new google.maps.InfoWindow();

    var marker, i;

    for (i = 0; i < total; i++) { 
      marker = new google.maps.Marker({
        position: new google.maps.LatLng(locations[i][2],locations[i][1]),
        map: map, 
      });

      google.maps.event.addListener(marker, 'click', (function(marker, i) {
        return function() {
          infowindow.setContent(locations[i][0]);
          infowindow.open(map, marker);
        }
      })(marker, i));
    } 
@endif

// Afichage Forets Classées
@if(($fc==null && $zt==null && $pp==null) || $fc==1)
  const triangleCoordsF = <?php echo Str::replace('"','',json_encode($seriescoordonatesF)); ?>; 
  const polygonsF = []; 
for (let i = 0; i < totalF; i++) {   

    const polygon = new google.maps.Polygon({
        paths: triangleCoordsF[i],
        strokeColor: "#FFFF00",
        strokeOpacity: 1,
        strokeWeight: 2,
        fillColor: "#1A281A",
        fillOpacity: 1,
        clickable: true
    });

    polygonsF.push(polygon);
 
    google.maps.event.addListener(polygon, 'click', function (event) {
        const infoWindow = new google.maps.InfoWindow({
            content: getInfoWindowContentF(locationsF[i])
        });

        infoWindow.setPosition(event.latLng);
        infoWindow.open(map);
    });

    polygon.setMap(map);
} 
@endif
 // Afichage Zones Tampons
 @if(($fc==null && $zt==null && $pp==null) || $zt==1)
 const triangleCoordsZT = <?php echo Str::replace('"','',json_encode($seriescoordonatesZT)); ?>; 
  const polygonsZT = []; 
for (let i = 0; i < totalZT; i++) {   

    const polygon = new google.maps.Polygon({
        paths: triangleCoordsZT[i],
        strokeColor: "#FFFFFF",
        strokeOpacity: 0.2,
        strokeWeight: 2,
        fillColor: "#FFFFFF",
        fillOpacity: 0.2,
        clickable: false
    });

    polygonsZT.push(polygon); 
    polygon.setMap(map);
}
@endif
} 
function getInfoWindowContent(location) {
        return `${location[0]}`;
    }
    function getInfoWindowContentF(location) {
        return `Region: ${location[3]}<br>Nom: ${location[0]}<br>Latitude: ${location[2]}<br>Longitude: ${location[1]}<br>Superficie: ${location[4]} ha`;
    }
    function getInfoWindowContentZT(location) {
        return `Region: ${location[3]}<br>Nom: ${location[0]}<br>Latitude: ${location[2]}<br>Longitude: ${location[1]}<br>Superficie: ${location[4]} ha`;
    }

function getRandomElement(array) {
    return array[Math.floor(Math.random() * array.length)];
  }


 
$('form select').on('change', function(){
    $(this).closest('form').submit();
});
    </script>
@endpush
