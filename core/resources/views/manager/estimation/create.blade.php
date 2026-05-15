@extends('manager.layouts.app')
@section('panel')
    <div class="row mb-none-30">
        <div class="col-lg-12 mb-30">
            <div class="card">
                <div class="card-body"> 
                    {!! Form::open(array('route' => ['manager.traca.estimation.store'],'method'=>'POST','class'=>'form-horizontal', 'id'=>'flocal', 'enctype'=>'multipart/form-data')) !!} 
                        
                            <div class="form-group row">
                                <label class="col-sm-4 control-label">@lang('Localite')</label>
                                <div class="col-xs-12 col-sm-8">
                                <select class="form-control" name="localite" id="localite" required>
                                    <option value="">@lang('Selectionner une option')</option>
                                    @foreach($localites as $localite)
                                        <option value="{{ $localite->id }}" @selected(old('localite'))>
                                            {{ $localite->nom }}</option>
                                    @endforeach
                                </select>
                                </div>
                            </div>  
                       
                            <div class="form-group row">
                                <label class="col-sm-4 control-label">@lang('Producteur')</label>
                                <div class="col-xs-12 col-sm-8">
                                <select class="form-control" name="producteur" id="producteur" required>
                                    <option value="">@lang('Selectionner une option')</option>
                                    @foreach($producteurs as $producteur)
                                        <option value="{{ $producteur->id }}" data-chained="{{ $producteur->localite->id }}" @selected(old('producteur'))>
                                            {{ stripslashes($producteur->nom) }} {{ stripslashes($producteur->prenoms) }}</option>
                                    @endforeach
                                </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-4 control-label">@lang('Parcelle')</label>
                                <div class="col-xs-12 col-sm-8">
                                <select class="form-control" name="parcelle" id="parcelle" onchange="getSuperficie()" required>
                                    <option value="">@lang('Selectionner une option')</option>
                                    @foreach($parcelles as $parcelle)
                                        <option value="{{ $parcelle->id }}" data-superficie="{{ $parcelle->superficie }}" data-chained="{{ $parcelle->producteur->id }}" @selected(old('parcelle'))>
                                           {{ __('Parcelle')}} {{ $parcelle->codeParc }}</option>
                                    @endforeach
                                </select>
                                </div>
                            </div>
                            <input type="hidden" name="superficie" value="0" id="superficie">
    <div class="form-group row">
        <?php echo Form::label(__('Campagne'), null, ['class' => 'col-sm-4 control-label required']); ?>
        <div class="col-xs-12 col-sm-8">
             <?php echo Form::select('campagne', $campagnes, null, array('class' => 'form-control campagnes', 'id'=>'campagnes','required'=>'required')); ?>
        </div>
    </div>
    <div class="form-group row">
        <?php echo Form::label(__('Quel type d\'estimation réalisez-vous?'), null, ['class' => 'col-sm-4 control-label required']); ?>
        <div class="col-xs-12 col-sm-8">
        <select class="form-control typeEstimation" name="typeEstimation" id="typeEstimation" required> 
                                    @foreach($typeEstimation as $type)
                                        <option value="{{ $type }}"  @selected(old('typeEstimation'))>
                                            {{ $type }}</option>
                                    @endforeach
                                </select> 
        </div>
    </div>
    <hr class="panel-wide">
    <div class="form-group row determe">
    <table border="1" class="table-bordered table-striped table-responsive" id="myTable">
  <tr>
    <td align="center" valign="middle"><strong>@lang("Repartition des Carrés(3 carrés de 10 m de coté chacun)")</strong></td>
    <td align="center" valign="middle"><strong>@lang("Nombre d'arbres compté Carré A")</strong></td>
    <td align="center" valign="middle"><strong>@lang("Nombre d'arbres compté Carré B")</strong></td>
    <td align="center" valign="middle"><strong>@lang("Nombre d'arbres compté Carré C")</strong></td>
    <td align="center" valign="middle"><strong>@lang("Nombre total d'arbres")</strong></td>
    <td colspan="2" align="center" valign="middle"><strong>@lang("V=Nombre total d'arbres x Coefficient")</strong></td>
    <td align="center" valign="middle"><strong>@lang("Volume Moyen")</strong></td>
    <td align="center" valign="middle"><strong>@lang("Calcul de l'estimation")</strong></td>
  </tr>
  <tr>
    <td>@lang("Sup à 20 Cabosses")</td>
    <td>

      <input type="number" name="EA1" value="{{ old('EA1') }}" id="EA1" style="width: 60px;" min="1" />
    </td>
    <td>
      <input type="number" name="EB1" value="{{ old('EB1') }}" id="EB1" style="width: 60px;"  min="1" />
    </td>
    <td><input type="number" name="EC1" value="{{ old('EC1') }}" id="EC1" style="width: 60px;" /></td>
    <td><input name="T1" value="{{ old('T1') }}" type="number" id="T1" readonly="readonly" style="width: 60px;"  min="1" /></td>
    <td>1</td>
    <td>V1
      <input name="V1" value="{{ old('V1') }}" type="number" id="V1" readonly="readonly" style="width: 60px;"  min="1" /></td>
    <td>
    <input name="VM1" value="{{ old('VM1') }}" type="number" id="VM1" readonly="readonly" style="width: 60px;" min="1" />T1=V1:3</td>
    <td>
    <input name="Q" value="{{ old('Q') }}" type="number" id="Q" readonly="readonly" style="width: 60px;" min="1" /><br>@lang("Rendement des 3 carrés A, B, C")<br>Q=T1+T2+T3</td>
  </tr>
  <tr>
    <td>@lang("De 11 à 20 Cabosses")</td>
    <td>
      <input type="number" name="EA2" value="{{ old('Q') }}"  id="EA2" style="width: 60px;" min="1" />
    </td>
    <td>
    <input type="number" name="EB2" value="{{ old('EB2') }}"  id="EB2" style="width: 60px;" min="1" /></td>
    <td><input type="number" name="EC2" value="{{ old('EC2') }}" id="EC2" style="width: 60px;" min="1" /></td>
    <td><input name="T2" value="{{ old('T2') }}" type="number" id="T2" readonly="readonly" style="width: 60px;" min="1" /></td>
    <td>0.6</td>
    <td>V2
      <input name="V2" type="number" value="{{ old('V2') }}"  id="V2" readonly="readonly" style="width: 60px;" min="1" /></td>
    <td>
    <input name="VM2" type="number" value="{{ old('VM2') }}"  id="VM2" readonly="readonly" style="width: 60px;" min="1" />T2=V1:3</td>
    <td><input name="RT" value="{{ old('RT') }}"  type="number" id="RT" readonly="readonly" style="width: 60px;" min="1" /><br>@lang("Rendement théorique")<br>RT=Q*100</td>
  </tr>
  <tr>
    <td>@lang("De 0 à 10 Cabosses")</td>
    <td>
      <input type="number" name="EA3" value="{{ old('EA3') }}"  id="EA3" style="width: 60px;" min="1" />
    </td>
    <td><input type="number" name="EB3" value="{{ old('EB3') }}"  id="EB3" style="width: 60px;" min="1" /></td>
    <td><input type="number" name="EC3" value="{{ old('EC3') }}"  id="EC3" style="width: 60px;" min="1" /></td>
    <td><input name="T3" value="{{ old('T3') }}"  type="number" id="T3" readonly="readonly" style="width: 60px;" min="1" /></td>
    <td>0.2</td>
    <td>V3
      <input name="V3" value="{{ old('V3') }}"  type="number" id="V3" readonly="readonly" style="width: 60px;" min="1" /></td>
    <td>
    <input name="VM3" value="{{ old('VM3') }}"  type="number" id="VM3" readonly="readonly" style="width: 60px;" min="1" />T3=V1:3</td>
    <td>
    <input name="ajustement" value="{{ old('ajustement') }}"  type="number" id="ajustement" style="width: 60px;" min="-20" max="20" />%<br>@lang("Pourcentage d'ajustement")</td>
  </tr>
</table>

</table>
 
            </div>

<p></p>
<p></p>
<div class="form-group row"> 
            {{ Form::label(__("Rendement Final(RT + RT * % Ajustement)"), null, ['class' => 'col-sm-4 control-label']) }}
            <div class="col-xs-12 col-sm-8">
           
            <div class="input-group">
            <?php echo Form::number('RF', null,array('class' => 'form-control','id'=>'RF', 'required', 'style'=>'width: 300px;') ); ?>
                            <div class="input-group-append">
                                <span class="input-group-text" style="height: 45px;border-radius: 0;">Kg/Ha</span>
                            </div>
                        </div>
        </div>
        </div>
<div class="form-group row"> 
            {{ Form::label(__("Superficie"), null, ['class' => 'col-sm-4 control-label']) }}
            <div class="col-xs-12 col-sm-8">
            
            <div class="input-group">
            <?php echo Form::number('superf', null,array('class' => 'form-control','id'=>'superf', 'readonly','style'=>'width: 300px;') ); ?>
                            <div class="input-group-append">
                                <span class="input-group-text" style="height: 45px;border-radius: 0;">Ha</span>
                            </div>
                        </div>
        </div>
        </div>

<div class="form-group row"> 
            {{ Form::label(__("Recolte Estimée(RF * Superficie)"), null, ['class' => 'col-sm-4 control-label']) }}
            <div class="col-xs-12 col-sm-8">
            
            <div class="input-group">
            <?php echo Form::number('EsP', null,array('class' => 'form-control','id'=>'EsP', 'required', 'readonly','style'=>'width: 300px;') ); ?>
                            <div class="input-group-append">
                                <span class="input-group-text" style="height: 45px;border-radius: 0;">Kg</span>
                            </div>
                        </div>
        </div>
        </div>

<hr class="panel-wide">

        <div class="form-group row">
            {{ Form::label(__("Date d'estimation"), null, ['class' => 'col-sm-4 control-label required']) }}
            <div class="col-xs-12 col-sm-8">
            <?php echo Form::date('date_estimation', null,array('class' => 'form-control dateactivite required','required'=>'required') ); ?>
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
@push('style')
<style type="text/css">
    input:not([type="radio"]), textarea {
    padding: 0px;
    }
    #myTable input {
    border-color: var(--main-color);
    box-shadow: 0 3px 9px rgba(50, 50, 9, 0.05), 3px 4px 8px rgba(115, 103, 240, 0.1);
}
    </style>
@endpush
@push('script')
<script type="text/javascript">
    function getSuperficie() {
        let superficie = $("#parcelle").find(':selected').data('superficie'); 
        $('#superficie').val(superficie); 
    }
    $("#parcelle").chained("#producteur");
    $("#producteur").chained("#localite"); 
    $('.determe').show();
    $('#EA1').prop('required', true);
                  $('#EA2').prop('required', true);
                  $('#EA3').prop('required', true);

                  $('#EB1').prop('required', true);
                  $('#EB2').prop('required', true);
                  $('#EB3').prop('required', true);
                  
                  $('#EC1').prop('required', true);
                  $('#EC2').prop('required', true);
                  $('#EC3').prop('required', true);
                  $('#ajustement').prop('required', true);
                  $('#RF').prop('readonly', true); 
    $('.estimationProd').hide();
    $('#typeEstimation').change(function() {
                var typeEstimation = $('#typeEstimation').val();
               
                if (typeEstimation == 'Rendement estimé') {  
                    $('.determe').hide('slow'); 
                    $('.estimationProd').show('slow');
                    $('#RF').prop('readonly', false); 
                    
                  $('#EA1').val('');
                  $('#EA2').val('');
                  $('#EA3').val('');

                  $('#EB1').val('');
                  $('#EB2').val('');
                  $('#EB3').val('');
                  
                  $('#EC1').val('');
                  $('#EC2').val('');
                  $('#EC3').val('');
                  $('#ajustement').val('');
                  $('#EA1').prop('required', false);
                  $('#EA2').prop('required', false);
                  $('#EA3').prop('required', false);

                  $('#EB1').prop('required', false);
                  $('#EB2').prop('required', false);
                  $('#EB3').prop('required', false);
                  
                  $('#EC1').prop('required', false);
                  $('#EC2').prop('required', false);
                  $('#EC3').prop('required', false);
                   $('#ajustement').prop('required', false);
                } else {
                  $('.determe').show('slow'); 
                  $('.estimationProd').hide('slow');
                 $('#RF').prop('readonly', true); 
                  $('.estimationProd').val(''); 
                  $('#EA1').prop('required', true);
                  $('#EA2').prop('required', true);
                  $('#EA3').prop('required', true);

                  $('#EB1').prop('required', true);
                  $('#EB2').prop('required', true);
                  $('#EB3').prop('required', true);
                  
                  $('#EC1').prop('required', true);
                  $('#EC2').prop('required', true);
                  $('#EC3').prop('required', true);
                  $('#ajustement').prop('required', true);
                }
            });
    $('#EA1,#EA2,#EA3,#EB1,#EB2,#EB3,#EC1,#EC2,#EC3,#ajustement').keyup(function(){
    var EA1= $('#EA1').val();
    var EA2= $('#EA2').val();
    var EA3= $('#EA3').val();

    var EB1= $('#EB1').val();
    var EB2= $('#EB2').val();
    var EB3= $('#EB3').val();

    var EC1= $('#EC1').val();
    var EC2= $('#EC2').val();
    var EC3= $('#EC3').val();
    var coefV1=1;
    var coefV2=0.6;
    var coefV3=0.2;
    var supT=$('#superficie').val(); 
    $('#superf').val($('#superficie').val());
if(EA1 && EB1 && EC1){
   $('#T1').val(parseInt(EA1)+parseInt(EB1)+parseInt(EC1));
}
if(EA2 && EB2 && EC2){
   $('#T2').val(parseInt(EA2)+parseInt(EB2)+parseInt(EC2));
}
if(EA3 && EB3 && EC3){
   $('#T3').val(parseInt(EA3)+parseInt(EB3)+parseInt(EC3));
}

if($('#T1').val() && $('#T2').val() && $('#T3').val())
{
  var T1 = parseFloat($('#T1').val())*1;
  var T2 = parseFloat($('#T2').val())*0.6;
  var T3 = parseFloat($('#T3').val())*0.2;
  $('#V1').val(T1.toFixed(2));
  $('#V2').val(T2.toFixed(2));
  $('#V3').val(T3.toFixed(2));
}
if($('#V1').val() && $('#V2').val() && $('#V3').val())
{
  var V1 = parseFloat($('#V1').val())/3;
  var V2 = parseFloat($('#V2').val())/3;
  var V3 = parseFloat($('#V3').val())/3;
  $('#VM1').val(V1.toFixed(2));
  $('#VM2').val(V2.toFixed(2));
  $('#VM3').val(V3.toFixed(2));
}
if($('#VM1').val() && $('#VM2').val() && $('#VM3').val())
{
  var VM1 = parseFloat($('#VM1').val());
  var VM2 = parseFloat($('#VM2').val());
  var VM3 = parseFloat($('#VM3').val());
  var VT = parseFloat(VM1)+parseFloat(VM2)+parseFloat(VM3);
  $('#Q').val(VT.toFixed(2));
}

if($('#Q').val()){
  var Q = parseFloat($('#Q').val())*100;
  $('#RT').val(Q.toFixed(2));
}
if($('#ajustement').val()){
  var RF = parseFloat($('#RT').val()) + parseFloat($('#RT').val())*(parseFloat($('#ajustement').val())/100); 
  $('#RF').val(RF.toFixed(2));
}
if($('#RF').val()){
  var EsP = parseFloat($('#RF').val())*supT;
  $('#EsP').val(EsP.toFixed(2));
}
});

 $('#RF').keyup(function(){
  $('#superf').val($('#superficie').val());
  var EsP = parseFloat($('#RF').val())*$('#superficie').val();
  $('#EsP').val(EsP.toFixed(2));
 });
 </script>
@endpush
<style>
    #myTable td{
    font-size: 0.8125rem;
    color: #5b6e88; 
    font-weight: 500;
    padding: 15px 25px;
    vertical-align: middle;  
    border: 1px solid #f4f4f4;
}
</style>
 