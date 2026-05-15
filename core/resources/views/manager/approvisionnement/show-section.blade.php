@extends('manager.layouts.app')
@section('panel')
    <div class="row mb-none-30">
        <div class="col-lg-12 mb-30">
            <div class="card">
                <div class="card-body">  
                        <input type="hidden" name="id" value="{{ $approvisionnement->id }}"> 
                         <div class="form-group row">
                                <label class="col-sm-4 control-label">@lang('Coopérative')</label>
                                <div class="col-xs-12 col-sm-8"> 
                                    {{$approvisionnement->section->cooperative->name}}
                                </div>
                            </div> 
                            <div class="form-group row">
                                <label class="col-sm-4 control-label">@lang('Campagne')</label>
                                <div class="col-xs-12 col-sm-8"> 
                                    {{$approvisionnement->campagne->nom}}
                                </div>
                            </div> 
                            <div class="form-group row">
                                <label class="col-sm-4 control-label">@lang('Section')</label>
                                <div class="col-xs-12 col-sm-8"> 
                                    {{$approvisionnement->section->libelle}}
                                </div>
                            </div> 
                        <div class="form-group row">
            {{ Form::label(__("Espèce D'arbres"), null, ['class' => 'col-sm-12 control-label']) }}
    <div class="col-xs-12 col-sm-12">
    <table class="table table-striped table-bordered">
    <thead>
            <tr>
                <th>Variété</th>
                <th>Strate</th>
                <th>Quantité</th>
            </tr>
        </thead>
    <tbody>
   
    <?php

use Illuminate\Support\Arr;
$somme = 0;
 //$filtered = Arr::pluck($approvisionnement->especesSection,'agroespecesarbre_id'); ?>
        @foreach($approvisionnement->especesSection as $data)
        <?php 
       if($data->total ==0 ){
            continue;
        }
        ?>
        @php $somme = $somme + $data->total; @endphp
 <tr>
            <td> 
                {{ $data->agroespecesarbre->nom }} 
            </td>
            <td>
            strate {{ $data->agroespecesarbre->strate}}
            </td>
            <td>
            {{ $data->total }}  
        </td>
        </tr>
        @endforeach
    </tbody>
    <tfoot class="bg bg-info">
        <tr>
        <td colspan="2"><h1>TOTAL</h1></td>
            <td><h1>{{ $somme; }}</h1></td>
        </tr>
    </tfoot>
</table>
</div>
    </div>
    
    <hr class="panel-wide"> 
 
                </div>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <x-back route="{{ route('manager.agro.approvisionnement.section',['id'=>encrypt($approvisionnement->agroapprovisionnement_id)]) }}" />
@endpush

@push('script')
<script type="text/javascript"> 

        $('#localite').change(function() {
            $("#producteur").chained("#localite");
        });

    </script>
@endpush