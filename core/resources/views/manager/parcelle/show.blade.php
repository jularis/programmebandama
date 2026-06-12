@extends('manager.layouts.app')
@section('panel')
    <div class="row mb-none-30">
        <div class="col-lg-12 mb-30">
            <div class="card">
                <div class="card-body">
                    <table class="table table-striped table-bordered">

                        <tr>
                            <td>Section</td>
                            <td>
                                {{ @$section->libelle }}
                            </td>
                        </tr>

                        <tr>
                            <td>Localité</td>
                            <td>
                                {{ @$localite->nom }}

                            </td>
                        </tr>

                        <tr>
                            <td>Producteur</td>
                            <td>{{ @stripslashes($producteur->nom) }} {{ @stripslashes($producteur->prenoms) }}</td>
                        </tr>
                        <tr>
                            <td>Type de déclaration superficie</td>
                            <td>
                                {{ @$parcelle->typedeclaration }}
                            </td>
                        </tr>
                        <tr>
                            <td>Année de création de la parcelle </td>
                            <td>
                                {{ @$parcelle->anneeCreation }}
                            </td>
                        </tr>

                        <tr>
                            <td>L'âge moyen des cacaoyers</td>
                            <td>
                                {{ @$parcelle->ageMoyenCacao }}
                            </td>
                        </tr>
                        <tr>
                            <td>Est ce que la parcelle a été régenerée ?</td>
                            <td>
                                {{ @$parcelle->parcelleRegenerer }}
                            </td>
                        </tr>
                        @if (@$parcelle->parcelleRegenerer == 'oui')
                            <tr>
                                <td>Année de régénération</td>
                                <td>
                                    {{ @$parcelle->anneeRegenerer }}
                                </td>
                            </tr>
                            <tr>
                                <td>Superficie concernée</td>
                                <td>
                                    {{ @$parcelle->superficieConcerne }}
                                </td>
                            </tr>
                        @endif
                        @if(@$parcelle->varietes)
                        <tr>
                            <td>Quelles sont les variétés de culture ?</td>
                            <td>
                                {{ implode(' ,', @$parcelle->varietes->pluck('variete')->toArray()) }}
                            </td>
                        @endif

                        <tr>
                            <td>Quel type de Document possèdes-tu ?</td>
                            <td>
                                {{ @$parcelle->typeDoc }}
                            </td>
                        </tr>
                        <tr>
                            <td>Ya-t-il Un Cour Ou Plan D’eau Dans La Parcelle ?</td>
                            <td>
                                {{ @$parcelle->presenceCourDeau }}
                            </td>
                        </tr>
                        @if (@$parcelle->presenceCourDeau == 'oui')
                            <tr>
                                <td>Quel est le cour ou plan d'eau</td>
                                <td>
                                    {{ @$parcelle->courDeau }}
                                </td>
                            </tr>
                        @endif
                        <tr>
                            <td>Est Ce qu'il existe des mésures de protection ?</td>
                            <td>
                                {{ @$parcelle->existeMesureProtection }}
                            </td>
                        </tr>
                        @if (@$parcelle->existeMesureProtection == 'oui')
                            <tr>
                                <td>Quelles sont les mesures de protection</td>
                                <td>{{ implode(' ,', @$parcelle->parcelleTypeProtections->pluck('typeProtection')->toArray()) }}
                                </td>
                            </tr>
                        @endif
                        <tr>
                            <td>Ya-t-il une pente dans la parcelle ?</td>
                            <td>
                                {{ @$parcelle->existePente }}
                            </td>
                        </tr>
                        @if (@$parcelle->existePente == 'oui')
                            <tr>
                                <td>Quel est le niveau de la pente ?</td>
                                <td>
                                    {{ @$parcelle->niveauPente }}
                                </td>
                            </tr>
                        @endif
                        <tr>
                            <td>Présence de signe d'érosion ?</td>
                            <td>
                                {{ @$parcelle->erosion }}
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" style="text-align: center;">Quels sont les arbres à Ombrages observés ?</td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Arbre</th>
                                            <th>Nombre</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($arbres as $item)
                                            <tr>
                                                <td>{{ $item->agroespeceabre->nom }}</td>
                                                <td>{{ $item->nombre }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" style="text-align: center;">Information GPS de la parcelle</td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Superficie</th>
                                            <th>Latitude</th>
                                            <th>Longitude</th>
                                            {{-- <th>Nombre de cacao moyen / parcelle</th> --}}
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>{{@$parcelle->superficie}}</td>
                                            <td>{{@$parcelle->latitude}}</td>
                                            <td>{{@$parcelle->longitude}}</td>
                                            {{-- <td>{{@$parcelle->superficie}}</td> --}}
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2"> 
                            <div class="table-responsive--sm table-responsive" id="map" style="height: 500px;">
                         
                         </div>
                            </td> 
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
 
    <?php
    use Illuminate\Support\Str;
    use Illuminate\Support\Arr; 
    if($parcelle->waypoints !=null)
    {
    $arrData = '';
    $newCoord = '';
    $lat = '';
    $long = '';
    $total = 0;
    $mappingparcellle ='';
    $seriescoord= $pointsPol = $pointsWay=  array();
    $seriescoordonates= $nombreTotal = $pointsPolygon = $pointsWaypoints = array();
    $a=1;
   
    if(isset($parcelle)){
     
        
        foreach($cooperatives as $coop){
            
            $nb = 0; 
    
            if($parcelle->latitude==0 || $parcelle->latitude==null || $parcelle->latitude==1){
                continue;
            }
            if (isset($parcelle->producteur) && isset($parcelle->producteur->localite) && isset($parcelle->producteur->localite->section)) {
                if(!isset($parcelle->producteur->localite->section->cooperative_id)) {
                    continue;
                }else{
                    if($coop->id !=$parcelle->producteur->localite->section->cooperative_id) {
                     continue;
                    }
                 }
            } 
          
            if($parcelle->waypoints !=null)
            {
                 
                $lat = isset($parcelle->latitude) ? htmlentities($parcelle->latitude, ENT_QUOTES | ENT_IGNORE, "UTF-8") : 'Non Disponible';
                $long= isset($parcelle->longitude) ? htmlentities($parcelle->longitude, ENT_QUOTES | ENT_IGNORE, "UTF-8") : 'Non Disponible'; 
                $producteur = isset($parcelle->producteur->nom) ? htmlentities($parcelle->producteur->nom, ENT_QUOTES | ENT_IGNORE, "UTF-8").' '.htmlentities($parcelle->producteur->prenoms, ENT_QUOTES | ENT_IGNORE, "UTF-8") : 'Non Disponible';
                $code= isset($parcelle->producteur->codeProd) ? htmlentities($parcelle->producteur->codeProd, ENT_QUOTES | ENT_IGNORE, "UTF-8") : 'Non defini';
                $codeparcelle = isset($parcelle->codeParc) ? htmlentities($parcelle->codeParc, ENT_QUOTES | ENT_IGNORE, "UTF-8") : 'Non Disponible';
                $localite=isset($parcelle->producteur->localite->nom) ? htmlentities($parcelle->producteur->localite->nom, ENT_QUOTES | ENT_IGNORE, "UTF-8") : 'Non Disponible';
                $section=isset($parcelle->producteur->localite->section->libelle) ? htmlentities($parcelle->producteur->localite->section->libelle, ENT_QUOTES | ENT_IGNORE, "UTF-8") : 'Non Disponible';
                $cooperative=isset($parcelle->producteur->localite->section->cooperative->name) ? htmlentities($parcelle->producteur->localite->section->cooperative->name, ENT_QUOTES | ENT_IGNORE, "UTF-8") : 'Non Disponible';
                $annee = isset($parcelle->anneeCreation) ? htmlentities($parcelle->anneeCreation, ENT_QUOTES | ENT_IGNORE, "UTF-8") : 'Non Disponible';
                $culture= isset($parcelle->culture) ? htmlentities($parcelle->culture, ENT_QUOTES | ENT_IGNORE, "UTF-8") : 'Non Disponible';
                $superficie= isset($parcelle->superficie) ? htmlentities($parcelle->superficie, ENT_QUOTES | ENT_IGNORE, "UTF-8") : 'Non Disponible';
                $proprietaire = 'Coopérative:'. $cooperative.'<br>Section:'. $section.'<br>Localite:'. $localite.'<br>Producteur : '.$producteur.'<br>Code producteur:'. $code.'<br>Code Parcelle:'. $codeparcelle.'<br>Année creation:'. $annee.'<br>Latitude:'. $lat.'<br>Longitude:'. $long.'<br>Superficie:'. $superficie.' ha';
               
                $pointsCoordinates = "['".$proprietaire."',".$long.",".$lat."]";
         $polygon =''; 
       
         $dataArray = explode(",", $parcelle->waypoints); 
         // Initialise la chaîne de sortie
         $outputString = "0 "; 
          
         // Parcours chaque élément et ajoute le format souhaité
         for ($i = 0; $i < count($dataArray); $i += 3) {
            if(isset($dataArray[$i]) && isset($dataArray[$i + 1]) && isset($dataArray[$i + 2]))
            {
                $outputString .= trim($dataArray[$i]) . "," . trim($dataArray[$i + 1]) . "," . trim($dataArray[$i + 2]) . " ";
            }
            
        }
        
        $coords = rtrim($outputString, ", ");
        
        //  $coords = Str::replace(",0,",",0 ", $parcelle->waypoints);
         
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
                            $polygon .='[' . $coordo1 . ',' . $coordo2 . ']';
                        }else{
                            $polygon .='[' . $coordo1 . ',' . $coordo2 . '],';
                        }
                    }
                      
                
            }
            
            $polygonCoordinates ='['.$polygon.']';
             $nb++;
            }
            $seriescoord[]= $polygonCoordinates;
            $pointsPol[] = "['".$proprietaire."']";
            $pointsWay[] = $pointsCoordinates; 
             
         $nombreTotal[$coop->id] = $nb; 
         $seriescoordonates[$coop->id] = $seriescoord; 
         $pointsWaypoints[$coop->id] = $pointsWay;
         $pointsPolygon[$coop->id] = $pointsPol;
         $seriescoord = $pointsPol = $pointsWay = array();
        
    }
        
    
    } 
}
    ?>

 
@endsection

@push('breadcrumb-plugins')
    <x-back route="{{ route('manager.traca.parcelle.index') }}" />
@endpush
@if($parcelle->waypoints !=null)
@push('style-lib')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@endpush
@push('script')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
@endpush
@push('script')
    <script>
    var map = L.map('map').setView([6.8817026, -5.5004615], 10);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        maxZoom: 19
    }).addTo(map);

    @foreach($cooperatives as $coopera)

    var locations<?php echo $coopera->id; ?> = <?php echo Str::replace('"','',json_encode($pointsPolygon[$coopera->id])); ?>;
    var total<?php echo $coopera->id; ?> = <?php echo $nombreTotal[$coopera->id]; ?>;
    const triangleCoords<?php echo $coopera->id; ?> = <?php echo Str::replace('"','',json_encode($seriescoordonates[$coopera->id])); ?>;
    var polygonsGroup<?php echo $coopera->id; ?> = [];

    for (let i = 0; i < total<?php echo $coopera->id; ?>; i++) {
        if (!triangleCoords<?php echo $coopera->id; ?>[i] || triangleCoords<?php echo $coopera->id; ?>[i].length === 0) continue;

        var polygon<?php echo $coopera->id; ?> = L.polygon(triangleCoords<?php echo $coopera->id; ?>[i], {
            color: '<?php echo $coopera->color; ?>',
            fillColor: '<?php echo $coopera->color; ?>',
            fillOpacity: 0.35,
            weight: 3,
            opacity: 0.8
        }).addTo(map);

        polygon<?php echo $coopera->id; ?>.bindPopup(locations<?php echo $coopera->id; ?>[i][0]);
        polygonsGroup<?php echo $coopera->id; ?>.push(polygon<?php echo $coopera->id; ?>);
    }

    if (polygonsGroup<?php echo $coopera->id; ?>.length > 0) {
        var group<?php echo $coopera->id; ?> = L.featureGroup(polygonsGroup<?php echo $coopera->id; ?>);
        map.fitBounds(group<?php echo $coopera->id; ?>.getBounds());
    }

    @endforeach

    </script>
@endpush

@endif