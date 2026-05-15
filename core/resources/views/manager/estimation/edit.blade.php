@extends('manager.layouts.app')
@section('panel')
    <div class="row mb-none-30">
        <div class="col-lg-12 mb-30">
            <div class="card">
                <div class="card-body">
                    {!! Form::model($estimation, [
                        'method' => 'POST',
                        'route' => ['manager.traca.estimation.store', $estimation->id],
                        'class' => 'form-horizontal',
                        'id' => 'flocal',
                        'enctype' => 'multipart/form-data',
                    ]) !!}
                    <input type="hidden" name="id" value="{{ $estimation->id }}">

                    <div class="form-group row">
                                <label class="col-sm-4 control-label">@lang('Localite')</label>
                                <div class="col-xs-12 col-sm-8">
                                {{ $estimation->parcelle->producteur->localite->nom }}
                                </div>
                            </div>  
                       
                            <div class="form-group row">
                                <label class="col-sm-4 control-label">@lang('Producteur')</label>
                                <div class="col-xs-12 col-sm-8">
                                {{ $estimation->parcelle->producteur->nom }} {{ $estimation->parcelle->producteur->prenoms }}({{$estimation->parcelle->producteur->codeProd}})
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-4 control-label">@lang('Parcelle')</label>
                                <div class="col-xs-12 col-sm-8">
                                {{ __('Parcelle')}} {{ $estimation->parcelle->codeParc }}
                                </div>
                            </div>
                            <input type="hidden" name="parcelle" value="{{$estimation->parcelle_id}}" id="parcelle">
                            <input type="hidden" name="superficie" value="{{$estimation->parcelle->superficie}}" id="superficie">
                            <input type="hidden" name="campagne" value="{{$estimation->campagne_id}}" id="superficie">
    <div class="form-group row">
        <?php echo Form::label(__('Campagne'), null, ['class' => 'col-sm-4 control-label required']); ?>
        <div class="col-xs-12 col-sm-8">
        {{ $estimation->campagne->nom }}
        </div>
    </div>

    <hr class="panel-wide">
    <div class="form-group row determe">
    <table border="1" class="table-bordered table-striped table-responsive" id="myTable">
  <tr>
    <td align="center" valign="middle"><strong>@lang("Repartition des Carrés(3 carrés de 20 m de coté chacun)")</strong></td>
    <td align="center" valign="middle"><strong>@lang("Nombre d'arbres compté Carré A")</strong></td>
    <td align="center" valign="middle"><strong>@lang("Nombre d'arbres compté Carré B")</strong></td>
    <td align="center" valign="middle"><strong>@lang("Nombre d'arbres compté Carré C")</strong></td>
    <td align="center" valign="middle"><strong>@lang("Nombre total d'arbres")</strong></td>
    <td colspan="2" align="center" valign="middle"><strong>@lang("V=Nombre total d'arbres x Coefficient")</strong></td>
    <td align="center" valign="middle"><strong>@lang("Volume Moyen")</strong></td>
    <td align="center" valign="middle"><strong>@lang("Calcul de l'estimation")</strong></td>
  </tr>
  <tr>
    <td>@lang("Sup à 20 Cab")</td>
    <td>

      <input type="number" name="EA1" value="{{ $estimation->EA1 }}" id="EA1" style="width: 140px;" />
    </td>
    <td>
      <input type="number" name="EB1" value="{{ $estimation->EB1 }}" id="EB1" style="width: 140px;"  />
    </td>
    <td><input type="number" name="EC1" value="{{ $estimation->EC1 }}" id="EC1" style="width: 140px;" /></td>
    <td><input name="T1" value="{{ $estimation->T1 }}" type="number" id="T1" readonly="readonly" style="width: 140px;"  /></td>
    <td>1</td>
    <td>V1
      <input name="V1" value="{{ $estimation->V1 }}" type="number" id="V1" readonly="readonly" style="width: 140px;"  /></td>
    <td>
    <input name="VM1" value="{{ $estimation->VM1 }}" type="number" id="VM1" readonly="readonly" style="width: 140px;" />T1=V1:3</td>
    <td>
    <input name="Q" value="{{ $estimation->Q }}" type="number" id="Q" readonly="readonly" style="width: 140px;" /><br>@lang("Rendement des 3 carrés A, B, C")<br>Q=T1+T2+T3</td>
  </tr>
  <tr>
    <td>@lang("De 11 à 20 Cab")</td>
    <td>
      <input type="number" name="EA2" value="{{ $estimation->Q }}"  id="EA2" style="width: 140px;" />
    </td>
    <td>
    <input type="number" name="EB2" value="{{ $estimation->EB2 }}"  id="EB2" style="width: 140px;" /></td>
    <td><input type="number" name="EC2" value="{{ $estimation->EC2 }}" id="EC2" style="width: 140px;" /></td>
    <td><input name="T2" value="{{ $estimation->T2 }}" type="number" id="T2" readonly="readonly" style="width: 140px;" /></td>
    <td>0.6</td>
    <td>V2
      <input name="V2" type="number" value="{{ $estimation->V2 }}"  id="V2" readonly="readonly" style="width: 140px;" /></td>
    <td>
    <input name="VM2" type="number" value="{{ $estimation->VM2 }}"  id="VM2" readonly="readonly" style="width: 140px;" />T2=V1:3</td>
    <td><input name="RF" value="{{ $estimation->RF }}"  type="number" id="RF" readonly="readonly" style="width: 140px;" /><br>@lang("Rendement final")<br>RF=Q*25</td>
  </tr>
  <tr>
    <td>@lang("De 0 à 10 Cab")</td>
    <td>
      <input type="number" name="EA3" value="{{ $estimation->EA3 }}"  id="EA3" style="width: 140px;" />
    </td>
    <td><input type="number" name="EB3" value="{{ $estimation->EB3 }}"  id="EB3" style="width: 140px;" /></td>
    <td><input type="number" name="EC3" value="{{ $estimation->EC3 }}"  id="EC3" style="width: 140px;" /></td>
    <td><input name="T3" value="{{ $estimation->T3 }}"  type="number" id="T3" readonly="readonly" style="width: 140px;" /></td>
    <td>0.2</td>
    <td>V3
      <input name="V3" value="{{ $estimation->V3 }}"  type="number" id="V3" readonly="readonly" style="width: 140px;" /></td>
    <td>
    <input name="VM3" value="{{ $estimation->VM3 }}"  type="number" id="VM3" readonly="readonly" style="width: 140px;" />T3=V1:3</td>
    <td>
    <input name="EsP" value="{{ $estimation->EsP }}"  type="number" id="EsP" style="width: 140px;" /><br>@lang("Estimation de production")<br>@lang("Q * Superficie")</td>
  </tr>
</table>

</table>
            </div>

<hr class="panel-wide">

        <div class="form-group row">
            {{ Form::label(__("Date d'estimation"), null, ['class' => 'col-sm-4 control-label required']) }}
            <div class="col-xs-12 col-sm-8">
            <?php echo Form::date('date_estimation', $estimation->date_estimation,array('class' => 'form-control dateactivite required','required'=>'required') ); ?>
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
    <x-back route="{{ route('manager.traca.estimation.index') }}" />
@endpush

@push('script')
    <script type="text/javascript">
        $('#localite').change(function() {
            $("#producteur").chained("#localite");
        });
    </script>
@endpush
