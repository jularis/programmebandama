@extends('admin.layouts.app')
@section('panel')
<?php
use Illuminate\Support\Str; 
?>
    <div class="row">
        <div class="col-lg-12">
            <div class="card b-radius--10 mb-3">
                <div class="card-body">
                    <form action="" id="">
                        <div class="d-flex flex-wrap gap-4">
                            <input type="hidden" name="table" value="parcelles" />
                            <div class="flex-grow-1">
                                <label>@lang('Cooperative')</label>
                                <select name="cooperative" class="form-control select2-basic" id="cooperative">
                                    <option value="">@lang('Toutes')</option>
                                    @foreach ($cooperatives as $local)
                                        <option value="{{ $local->id }}" {{ request()->cooperative == $local->id ? 'selected' : '' }}>{{ $local->name }}</option>
                                    @endforeach
                                </select>
                            </div>
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
                                        <option value="{{ $local->id }}" {{ request()->localite == $local->id ? 'selected' : '' }}>{{ $local->nom }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="flex-grow-1">
                                <label>@lang('Producteur')</label>
                                <select name="producteur" class="form-control select2-basic" id="producteur">
                                    <option value="">@lang('Tous')</option>
                                    @foreach ($producteurs as $local)
                                        <option value="{{ $local->id }}" {{ request()->producteur == $local->id ? 'selected' : '' }}>{{ $local->nom }} {{ $local->prenoms }} ({{ $local->codeProd }})</option>
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
                    <div class="table-responsive--sm table-responsive" id="googleMap" style="height: 800px;">
                         
                    </div>
                </div>
                
            </div>
        </div>
    </div>
     
    <x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')  
    <a href="{{ route('admin.traca.parcelle.mapping.polygone') }}" class="btn  btn-outline--primary h-45"><i
            class="las la-map-marker"></i> Mapping Polygone</a>
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

@endpush
@push('script') 
<script type="text/javascript"> 
var lgt='-5.5004615';
    var ltt='6.8817026';
    var z=8; 
    <?php
    $nombreTotal = array();
     
    foreach($cooperatives as $coop)
    {
        $nb = 0;
        
        ?>
    var locations<?php echo $coop->id; ?> = [    <?php
  
$i=1;
foreach($parcelles as  $data) {
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
 ?>
  ['<?php echo $proprietaire; ?>', <?php echo $long; ?>, <?php echo $lat; ?>, 7],
  
 <?php
//   if($total>$i){echo ',';}
 $i++;
 $nb++;
}
$nombreTotal[$coop->id] = $nb; 
?>
];
<?php
}
 
?>

    var map = new google.maps.Map(document.getElementById('googleMap'), {
      zoom: z,
      center: new google.maps.LatLng(ltt,lgt), 
      mapTypeId: google.maps.MapTypeId.TERRAIN
    });

    var infowindow = new google.maps.InfoWindow();

    var marker, i;
    @foreach($cooperatives as $coopera)

var total = <?php echo $nombreTotal[$coopera->id]; ?>;
var svgIcon = {
            path: "M8 0C3.58 0 0 3.58 0 8s8 16 8 16 8-12.92 8-16-3.58-8-8-8zm0 11c-1.11 0-2-.89-2-2s.89-2 2-2 2 .89 2 2-.89 2-2 2z",
            fillColor: "<?php echo $coopera->color; ?>",
            fillOpacity: 0.6,
            strokeWeight: 1,
            strokeColor: "#FFFFFF", // Changer cette couleur pour le contour
            strokeOpacity: 1,
            strokeWeight: 1,
            scale: 1 // Ajustez la taille du SVG selon vos besoins
        };
    for (i = 0; i < total; i++) { 

// Créer une icône personnalisée avec la couleur spécifiée 
      marker = new google.maps.Marker({
        position: new google.maps.LatLng(locations<?php echo $coopera->id; ?>[i][2],locations<?php echo $coopera->id; ?>[i][1]),
        map: map, 
        icon: svgIcon
      });

      google.maps.event.addListener(marker, 'click', (function(marker, i) {
        return function() {
          infowindow.setContent(locations<?php echo $coopera->id; ?>[i][0]);
          infowindow.open(map, marker);
        }
      })(marker, i));
    } 
    @endforeach
$('form select').on('change', function(){
    $(this).closest('form').submit();
});
    </script>
@endpush
