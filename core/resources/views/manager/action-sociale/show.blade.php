@extends('manager.layouts.app')
@section('panel')
<style type="text/css">
table td:last-child {
    text-align: left !important;
}
</style>
    <div class="row mb-none-30">
        <div class="col-lg-12 mb-30">
            <div class="card">
                <div class="card-body">
                <table class="table table-bordered table-striped"> 
<tr>
 <td>
Type de projet:
 </td>
<td>
{{ $actionSociale->type_projet}}
 </td>
</tr>

 <tr>
 <td>
Titre du projet:
 </td>
<td>
    {{ $actionSociale->titre_projet }}
 </td>
</tr>

 <tr>
 <td>
Description du Projet:
 </td>
<td>
    {{ $actionSociale->description_projet }}
 </td>
</tr>

 <tr>
 <td>
Bénéficiaires du projet(Localités):
 </td>
<td>
    @foreach($actionSociale->beneficiaires as $data)
    {{ $data->localite->nom }}<br>
    @endforeach
 </td>
</tr>
<tr>
    <td>
   Autres Bénéficiaires du projet:
    </td>
   <td>
       @foreach($actionSociale->autreBeneficiaires as $data)
       {{ $data->libelle }}<br>
       @endforeach
    </td>
   </tr>
 <tr>
 <td>
 Niveau de réalisation:
 </td>
<td>
    {{ $actionSociale->niveau_realisation }} 
 </td>
</tr>

@if($actionSociale->niveau_realisation !='Non démarré')
 
 <tr>
     <td>
Date de démarrage du projet:
     </td>
<td> 
    {{ date('d/m/Y', strtotime($actionSociale->date_demarrage))}}
 </td>
</tr>
 <tr>
     <td>
Date de fin du projet:
     </td>
<td> 
    {{ date('d/m/Y', strtotime($actionSociale->date_fin_projet))}}
 </td>
</tr>
@endif
<tr>
     <td>
Date de livraison:
     </td>
<td> 
    {{ date('d/m/Y', strtotime($actionSociale->date_livraison))}}
 </td>
</tr>
 <tr>
 <td>
Coûts du projet:
 </td>
<td>
    {{ number_format($actionSociale->cout_projet,0,'',' ') }} FCFA
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
        @foreach($actionSociale->partenaires as $data)
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
@if($actionSociale->photos)
<div class="row">
    @php
    $photos = json_decode($actionSociale->photos);
    @endphp
    @foreach($photos as $data)
    <div class="col-md-4">
        <img src="{{asset('core/storage/app/'.$data)}}" class="img img-rounded"/>
    </div>
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
@if($actionSociale->documents_joints)
<div class="row">
    @php
    $documents_joints = json_decode($actionSociale->documents_joints);
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
{{ $actionSociale->commentaires }}
 </td>
</tr> 

                </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <x-back route="{{ route('manager.communaute.action.sociale.index') }}" />
@endpush
 