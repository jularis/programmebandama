@extends('manager.layouts.app')
@section('panel')
    <div class="row mb-none-30">
        <div class="col-lg-12 mb-30">
            <div class="card">
                <div class="card-body"> 
         {!! Form::model($approvisionnement, ['method' => 'POST','route' => ['manager.agro.approvisionnement.store', $approvisionnement->id],'class'=>'form-horizontal', 'id'=>'flocal', 'enctype'=>'multipart/form-data']) !!}
                        <input type="hidden" name="id" value="{{ $approvisionnement->id }}"> 
                         <div class="form-group row">
                                <label class="col-sm-4 control-label">@lang('Coopérative')</label>
                                <div class="col-xs-12 col-sm-8">
                                
                                {!! Form::text('cooperative', $approvisionnement->cooperative->name, array('placeholder' => __('Localite'),'class' => 'form-control', 'readonly')) !!}
                                </div>
                            </div> 
                        <div class="form-group row">
            {{ Form::label(__("Espèce D'arbres"), null, ['class' => 'col-sm-4 control-label']) }}
    <div class="col-xs-12 col-sm-8">
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

 $filtered = Arr::pluck($approvisionnement->especes,'agroespecesarbre_id'); ?>
        @foreach($especesarbres as $data)
        
 <tr>
            <td> 
            {!! Form::hidden('especesarbre[]', $data->id, array()) !!}
            {!! Form::text('nom', $data->nom, array('class' => 'form-control','readonly')) !!}
            </td>
            <td>
            strate {{ $data->strate}}
            </td>
            <td>
                
            <?php
            $quantite=null;
            foreach($approvisionnement->especes as $espece)
            {
                if($data->id==$espece->agroespecesarbre_id){
                    $quantite = $espece->total;
                }
            }
            ?>
            {!! Form::number('quantite[]', $quantite, array('placeholder' => __('Qté'),'class' => 'form-control', 'min'=>'0')) !!}
 
        </td>
        </tr>
        @endforeach
    </tbody>

</table>
</div>
    </div>
    
    <hr class="panel-wide">

    <div class="form-group row">

                     <?php echo Form::label(__('Bon de Livraison'), null, ['class' => 'col-sm-4 control-label']); ?>
                     <div class="col-xs-12 col-sm-8">
                           <input type="file" name="bon_livraison" class="form-control dropify-fr">
                 </div>
     </div> 
<hr class="panel-wide">


                        <div class="form-group">
                            <button type="submit" class="btn btn--primary btn-block h-45 w-100">@lang('Envoyer')</button>
                        </div>
                        {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <x-back route="{{ route('manager.agro.approvisionnement.index') }}" />
@endpush

@push('script')
<script type="text/javascript"> 

        $('#localite').change(function() {
            $("#producteur").chained("#localite");
        });

    </script>
@endpush