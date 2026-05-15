<!doctype html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Details Action Sociale</title>
<style>
 table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
}

table, th, td {
    border: 1px solid black;
}

th, td {
    padding: 10px;
    text-align: left;
}

th {
    background-color: #f2f2f2;
}

/* Style for odd rows */
tr:nth-child(odd) {
    background-color: #e6e6e6;
}

/* Add additional styling as needed */

</style>
</head>
<body>
<table class="table table-bordered table-striped"> 
<tr>
 <td colspan="2"><h1 style="text-align: center; font-size:36px;"><strong>COMMUNAUTE RESILIENTE - ACTIVITES COMMUNAUTAIRES</strong></h1></td>
</tr>
                <tr>
                        <td>
Localité:
                        </td>
<td>
    @foreach($activite->localites as $data)
    {{ $data->localite->nom }}<br>
    @endforeach
                        </td>
</tr>

                    <tr>
                        <td>
                        Bénéfiniaires Membres du Projet
                        </td>
<td>
    @foreach($activite->beneficiaires as $data)

    {{ $data->producteur->nom }} {{ $data->producteur->prenoms }}<br>
@endforeach
                        </td>
</tr>
                    <tr>
                        <td>
Titre du projet:
                        </td>
<td>
    {{ $activite->titre_projet }} 
                        </td>
</tr>

                    <tr>
                        <td>
Description du Projet:
                        </td>
<td style="text-wrap: wrap;">
    {{ $activite->description_projet }} 
                            
                        </td>
</tr>

                    <tr>
                        <td>
Type de projet:
                        </td>
<td>
    {{ $activite->type_projet }}  
                        </td>
</tr>


 
                        <td>
Bénéficiaires Non Membres du Projet:
                        </td>
<td> 
                        </td>
</tr> 

 
                    <tr>
                        <td>
Niveau de réalisation:
                        </td>
<td>
    {{ $activite->niveau_realisation }} 
                        </td>
</tr>
@if($activite->niveau_realisation !='Non démarré')
 
                        <tr>
                            <td>
                                
Date de démarrage du projet:
                            </td>
<td>
    {{ date('d/m/Y', strtotime($activite->date_demarrage))}}           
                            </div>
                        </td>
</tr>
 
                        <tr>
                            <td>
Date de fin du projet:
                            </td>
<td>
    {{ date('d/m/Y', strtotime($activite->date_fin_projet))}} 
                            </div>
                        </td>
</tr>
@endif
<tr>
    <td>
Date de la livraison:
    </td>
<td> 
    {{ date('d/m/Y', strtotime($activite->date_livraison))}} 
    </td>
</tr>
                    <tr>
                        <td>
Coûts du projet:
                        </td>
<td> 
    {{ number_format($activite->cout_projet,0,'',' ') }} FCFA
                        
                        </td>
</tr>


                    <tr>
                        <td>
Photos:
                        </td>
<td>
              @if($activite->photos)
<div class="row">
    @php
    $photos = json_decode($activite->photos);
    @endphp
    @foreach($photos as $data)
    <div class="col-md-12">
        <img src="{{asset('core/storage/app/'.$data)}}" class="img img-rounded" style="width: 400px;"/>
    </div><br>
    @endforeach
</div>
     
@endif
              
                        </td>
</tr>
                    <tr>
                        <td>
 Documents joints:
                        </td>
<td>
                           @if($activite->documents_joints)
<div class="row">
    @php
    $documents_joints = json_decode($activite->documents_joints);
    @endphp
    @foreach($documents_joints as $data)
    <div class="col-md-12">
    <a href="{{asset('core/storage/app/'.$data)}}" target="_blank"> {{ Str::afterLast($data,"/")  }}</a>
    </div>
    @endforeach
</div>
     
@endif 
                      
                        </td>
</tr>

                    <tr>
                        <td>
Commentaires:
                        </td>
<td style="text-wrap: wrap;">
{{ $activite->commentaires }} 
                        </td>
</tr>
                </table>
</body>
</html>