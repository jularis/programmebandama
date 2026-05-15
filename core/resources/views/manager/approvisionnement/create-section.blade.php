@extends('manager.layouts.app')
@section('panel')
<?php
use App\Models\AgroapprovisionnementSectionEspece;
                                        ?>
    <div class="row mb-none-30">
        <div class="col-lg-12 mb-30">
            <div class="card">
                <div class="card-body">
                    {!! Form::open(array('route' => ['manager.agro.approvisionnement.store-section'],'method'=>'POST','class'=>'form-horizontal', 'id'=>'flocal', 'enctype'=>'multipart/form-data')) !!}

                    <input type="hidden" name="agroapprovisionnement" value="{{ decrypt(request()->id)}}"/>
                    <div class="form-group row">
                                <label class="col-sm-4 control-label">@lang('Coopérative')</label>
                                <div class="col-xs-12 col-sm-8">

                                {!! Form::text('cooperative', auth()->user()->cooperative->name, array('placeholder' => __('Localite'),'class' => 'form-control', 'readonly')) !!}
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-4 control-label">@lang('Section')</label>
                                <div class="col-xs-12 col-sm-8">
                                <select class="form-control" name="section" id="section" required>
                                    <option value="">@lang('Selectionner une option')</option>
                                    @foreach($sections as $section)
                                    <?php

                                        // if(in_array($section->id,$dataSection)){
                                        //     continue;
                                        // }
                                        ?>
                                        <option value="{{ $section->id }}" @selected(old('section'))>
                                            {{ $section->libelle }}</option>
                                    @endforeach
                                </select>
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
                <th>Quantité Initiale</th>
                <th>Quantité</th>
            </tr>
        </thead>
    <tbody>
        @foreach($especesarbres as $data)
        <?php
        if(!in_array($data->id,$dataEspece)){
            continue;
        }
        $somme = AgroapprovisionnementSectionEspece::joinRelationship('agroapprovisionnementSection')->where([['agroapprovisionnement_id',decrypt(request()->id)],['agroespecesarbre_id',$data->id]])->sum('agroapprovisionnement_section_especes.total');
        if($dataQuantite[$data->id]<=$somme){
            continue;
        }
        ?>
 <tr>
            <td>
            {!! Form::hidden('especesarbre[]', $data->id, array()) !!}
            {{ $data->nom }}
            </td>
            <td>
            strate {{ $data->strate}}
            </td>
            <td>
            <span class="badge badge-info">{{ $dataQuantite[$data->id]-$somme}}</span>
            </td>
            <td>
            {!! Form::number('quantite[]', null, array('placeholder' => __('Qté'),'class' => 'form-control', 'min'=>'0','max'=>$dataQuantite[$data->id]-$somme)) !!}
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

                        <div class="form-group row">
                            <button type="submit" class="btn btn--primary w-100 h-45"> @lang('Envoyer')</button>
                        </div>
                        {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>


@endsection

@push('breadcrumb-plugins')
    <x-back route="{{ route('manager.agro.approvisionnement.section',['id'=>request()->id]) }}" />
@endpush

@push('script')
<script type="text/javascript">
    $("#producteur").chained("#localite");
 </script>
@endpush
