@extends('manager.layouts.app')
@section('panel')
<?php
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
?>
    <div class="row">
        <div class="col-lg-12">
        <div class="card b-radius--10 mb-3">
                <div class="card-body">
                    <form action="">
                        <div class="d-flex flex-wrap gap-4">
                            <input type="hidden" name="table" value="parcelles" />
                            <div class="flex-grow-1">
                                <label>@lang('Section')</label>
                                <select name="section" class="form-control select2-basic" id="section">
                                    <option value="">@lang('Toutes')</option>
                                    @foreach ($sections as $local)
                                        <option value="{{ $local->id }}" {{ request()->section == $local->id ? 'selected' : '' }}>{{ $local->libelle }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="flex-grow-1">
                                <label>@lang('Localité')</label>
                                <select name="localite" class="form-control select2-basic" id="localite">
                                    <option value="">@lang('Toutes')</option>
                                    @foreach ($localites as $local)
                                        <option value="{{ $local->id }}" data-chained="{{ $local->section_id }}" {{ request()->localite == $local->id ? 'selected' : '' }}>{{ $local->nom }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="flex-grow-1">
                                <label>@lang('Producteur')</label>
                                <select name="producteur" class="form-control select2-basic" id="producteur">
                                    <option value="">@lang('Tous')</option>
                                    @foreach ($producteurs as $local)
                                        <option value="{{ $local->id }}" data-chained="{{ $local->localite_id }}" {{ request()->producteur == $local->id ? 'selected' : '' }}>{{ $local->nom }} {{ $local->prenoms }} ({{ $local->codeProd }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="flex-grow-1">
                                <label>@lang('Date')</label>
                                <input name="date" type="text" class="dates form-control"
                                    placeholder="@lang('Date de début - Date de fin')" autocomplete="off" value="{{ request()->date }}">
                            </div>
                            <div class="flex-grow-1 align-self-end">
                                <button class="btn btn--primary w-100 h-45"><i class="fas fa-filter"></i>
                                    @lang('Filter')</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
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
$seriescoord= $pointsPol = $pointsWay=  array();
$seriescoordonates= $nombreTotal = $pointsPolygon = $pointsWaypoints = array();
$a=1;

if(isset($parcelles) && count($parcelles)){

    $total = count($parcelles);

    foreach($cooperatives as $coop){

        $nb = 0;
    foreach($parcelles as $data) {

        if($data->latitude==0 || $data->latitude==null || $data->latitude==1){
            continue;
        }
        if (isset($data->producteur) && isset($data->producteur->localite) && isset($data->producteur->localite->section)) {
            if(!isset($data->producteur->localite->section->cooperative_id)) {
                continue;
            }else{
                if($coop->id !=$data->producteur->localite->section->cooperative_id) {
                 continue;
                }
             }
        }
        $polygonCoordinates = "";
        $proprietaire = "";
        $pointsCoordinates = "";

        if($data->waypoints !=null)
        {

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

            $pointsCoordinates = "['".$proprietaire."',".$long.",".$lat."]";
     $polygon ='';

     $dataArray = explode(",", $data->waypoints);
     $outputString = "0 ";
    for ($i = 0; $i < count($dataArray); $i += 3) {
        if (isset($dataArray[$i]) && isset($dataArray[$i + 1]) && isset($dataArray[$i + 2])) {
            $outputString .= trim($dataArray[$i]) . "," . trim($dataArray[$i + 1]) . "," . trim($dataArray[$i + 2]) . " ";
        }
    }
    $coords = rtrim($outputString, ", ");
    $coords = explode(" ", $coords);


         $nombre = count($coords);
         $i=0;
        foreach($coords as $data2) {

                $i++;
                $coords2 = explode(',', $data2);
                if(count($coords2)==3)
                    {

                        $coordo1 = isset($coords2[1]) ? $coords2[1] : null;
                        $coordo2 = isset($coords2[0]) ? $coords2[0] : null;
                        if($i==$nombre){
                            $polygon .='{ lat: ' . $coordo1 . ', lng: ' . $coordo2 . ' }';
                        }else{
                            $polygon .='{ lat: ' . $coordo1 . ', lng: ' . $coordo2 . ' },';
                        }
                    }

        }

        $polygonCoordinates ='['.$polygon.']';
         $nb++;
        }
        $seriescoord[]= $polygonCoordinates;
        $pointsPol[] = "['".$proprietaire."']";
        $pointsWay[] = $pointsCoordinates;

    }
     $nombreTotal[$coop->id] = $nb;
     $seriescoordonates[$coop->id] = $seriescoord;
     $pointsWaypoints[$coop->id] = $pointsWay;
     $pointsPolygon[$coop->id] = $pointsPol;
     $seriescoord = $pointsPol = $pointsWay = array();

}


}

?>
    <x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')
    <x-back route="{{ route('manager.traca.parcelle.index') }}" />
    <a href="{{ route('manager.traca.parcelle.mapping') }}" class="btn  btn-outline--primary h-45"><i
            class="las la-map-marker"></i> Mapping Waypoints</a>
            <a href="{{ route('manager.traca.parcelle.mapping',['download' => 'kml','table'=>request()->table,'section'=>request()->section,'localite'=>request()->localite,'producteur'=>request()->producteur]) }}" class="btn  btn-outline--warning h-45"><i
                class="las la-download"></i> @lang('Télécharger KML')</a>
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
//var locationsWaypoints = <?php //echo $pointsWaypoints; ?>;

window.onload = function () {
  map = new google.maps.Map(document.getElementById("map"), {
    zoom: 8,
    center: { lat: 6.8817026, lng: -5.5004615 },
    mapTypeId: "terrain",
  });
  @if(isset($parcelles) && count($parcelles))
  // Define the LatLng coordinates for the polygon.
@foreach($cooperatives as $coopera)

var locations<?php echo $coopera->id; ?> = <?php echo Str::replace('"','',json_encode($pointsPolygon[$coopera->id])); ?>;
  var total = <?php echo $nombreTotal[$coopera->id]; ?>;
  const triangleCoords<?php echo $coopera->id; ?> = <?php echo Str::replace('"','',json_encode($seriescoordonates[$coopera->id])); ?>;
  const polygons<?php echo $coopera->id; ?> = [];
// Construct polygons
for (let i = 0; i < total; i++) {

    const polygon = new google.maps.Polygon({
        paths: triangleCoords<?php echo $coopera->id; ?>[i],
        strokeColor: "#DD160B",
        strokeOpacity: 0.8,
        strokeWeight: 3,
        fillColor: "#DD160B",
        fillOpacity: 0.35,
    });

    polygons<?php echo $coopera->id; ?>.push(polygon);

    const infoWindow = new google.maps.InfoWindow({
        content: getInfoWindowContent(locations<?php echo $coopera->id; ?>[i]),
    });

    google.maps.event.addListener(polygon, 'click', function (event) {
        infoWindow.setPosition(event.latLng);
        infoWindow.open(map);
    });

    google.maps.event.addListener(polygon, 'mouseout', function () {
        infoWindow.close();
    });


    polygon.setMap(map);

}
// Calcul du centre du polygone
const bounds = new google.maps.LatLngBounds();
  for (let i = 0; i < total; i++) {
    triangleCoords<?php echo $coopera->id; ?>[i].forEach((coord) => {
      bounds.extend(new google.maps.LatLng(coord.lat, coord.lng));
    });
  }
  map.fitBounds(bounds);

@endforeach

@endif

}
function getInfoWindowContent(location) {
        return `${location[0]}`;
    }

$('form select').on('change', function(){
    $(this).closest('form').submit();
});
    </script>
@endpush
