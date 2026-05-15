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
 <td colspan="2"><h1 style="text-align: center; font-size:36px;"><strong>COMMUNAUTE RESILIENTE - ACTION SOCIALE</strong></h1></td>
</tr>
<tr>
 <td>
Type de projet:
 </td>
<td>
{{ $actionsociale->type_projet}}
 </td>
</tr>

 <tr>
 <td>
Titre du projet:
 </td>
<td>
    {{ $actionsociale->titre_projet }}
 </td>
</tr>

 <tr>
 <td>
Description du Projet:
 </td>
<td>
    {{ $actionsociale->description_projet }}
 </td>
</tr>

 <tr>
 <td>
Bénéficiaires du projet(Localités):
 </td>
<td>
    @foreach($actionsociale->beneficiaires as $data)
    {{ $data->localite->nom }}<br>
    @endforeach
 </td>
</tr>
<tr>
    <td>
   Autres Bénéficiaires du projet:
    </td>
   <td>
       @foreach($actionsociale->autreBeneficiaires as $data)
       {{ $data->libelle }}<br>
       @endforeach
    </td>
   </tr>
 <tr>
 <td>
 Niveau de réalisation:
 </td>
<td>
    {{ $actionsociale->niveau_realisation }} 
 </td>
</tr>

@if($actionsociale->niveau_realisation !='Non démarré')
 
 <tr>
     <td>
Date de démarrage du projet:
     </td>
<td> 
    {{ date('d/m/Y', strtotime($actionsociale->date_demarrage))}}
 </td>
</tr>
 <tr>
     <td>
Date de fin du projet:
     </td>
<td> 
    {{ date('d/m/Y', strtotime($actionsociale->date_fin_projet))}}
 </td>
</tr>
@endif
<tr>
     <td>
Date de livraison:
     </td>
<td> 
    {{ date('d/m/Y', strtotime($actionsociale->date_livraison))}}
 </td>
</tr>
 <tr>
 <td>
Coûts du projet:
 </td>
<td>
    {{ number_format($actionsociale->cout_projet,0,'',' ') }}
 </td>
</tr>

<tr>
<td>
Partenaires impliqués: 
</td>
<td>
<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>Nom du partenaire</th>
            <th>Type de partenariat</th>
            <th>Montant de la contribution (En FCFA)</th>
        </tr>
    </thead>
    <tbody>
        @foreach($actionsociale->partenaires as $data)
          <tr>
              <td>
                {{ $data->partenaire }} 
      </td>
 
              <td>
                {{ $data->type_partenaire }} 
 </td>
 <td>
    {{ number_format($data->montant,0,'',' ') }} 
</td>
  </tr>
                 @endforeach 

         </tbody>
     </table>
 </td>
</tr>

 <tr>
 <td>
Photos:
 </td>
<td>
@if($actionsociale->photos)
<div class="row">
    @php
    $photos = json_decode($actionsociale->photos);
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
@if($actionsociale->documents_joints)
<div class="row">
    @php
    $documents_joints = json_decode($actionsociale->documents_joints);
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
<td>
{{ $actionsociale->commentaires }}
 </td>
</tr> 

                </table>
</body>
</html>