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
Localité:
                        </td>
<td>
    @foreach($communauteSociale->localites as $data)
    {{ $data->localite->nom }}<br>
    @endforeach
                        </td>
</tr>

                    <tr>
                        <td>
                        Bénéfiniaires Membres du Projet
                        </td>
<td>
    @foreach($communauteSociale->beneficiaires as $data)

    {{ $data->producteur->nom }} {{ $data->producteur->prenoms }}<br>
@endforeach
                        </td>
</tr>
                    <tr>
                        <td>
Titre du projet:
                        </td>
<td>
    {{ $communauteSociale->titre_projet }} 
                        </td>
</tr>

                    <tr>
                        <td>
Description du Projet:
                        </td>
<td style="text-wrap: wrap;">
    {{ $communauteSociale->description_projet }} 
                            
                        </td>
</tr>

                    <tr>
                        <td>
Type de projet:
                        </td>
<td>
    {{ $communauteSociale->type_projet }}  
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
    {{ $communauteSociale->niveau_realisation }} 
                        </td>
</tr>
@if($communauteSociale->niveau_realisation !='Non démarré')
 
                        <tr>
                            <td>
                                
Date de démarrage du projet:
                            </td>
<td>
    {{ date('d/m/Y', strtotime($communauteSociale->date_demarrage))}}           
                            </div>
                        </td>
</tr>
 
                        <tr>
                            <td>
Date de fin du projet:
                            </td>
<td>
    {{ date('d/m/Y', strtotime($communauteSociale->date_fin_projet))}} 
                            </div>
                        </td>
</tr>
@endif
<tr>
    <td>
Date de la livraison:
    </td>
<td> 
    {{ date('d/m/Y', strtotime($communauteSociale->date_livraison))}} 
    </td>
</tr>
                    <tr>
                        <td>
Coûts du projet:
                        </td>
<td> 
    {{ number_format($communauteSociale->cout_projet,0,'',' ') }} FCFA
                        
                        </td>
</tr>


                    <tr>
                        <td>
Photos:
                        </td>
<td>
              @if($communauteSociale->photos)
<div class="row">
    @php
    $photos = json_decode($communauteSociale->photos);
    @endphp
    @foreach($photos as $data)
    <div class="col-md-4">
        <img src="{{asset('core/storage/app/'.$data)}}" class="img img-rounded" style="width:200px"/>
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
                           @if($communauteSociale->documents_joints)
<div class="row">
    @php
    $documents_joints = json_decode($communauteSociale->documents_joints);
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
{{ $communauteSociale->commentaires }} 
                        </td>
</tr>
                </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <x-back route="{{ route('manager.communaute.activite.communautaire.index') }}" />
@endpush
 
