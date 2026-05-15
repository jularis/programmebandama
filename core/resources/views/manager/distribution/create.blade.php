@extends('manager.layouts.app')
@section('panel')
    <div class="row mb-none-30">
        <div class="col-lg-12 mb-30">
            <div class="card">
                <div class="card-body">
                    {!! Form::open(array('route' => ['manager.agro.distribution.store'],'method'=>'POST','class'=>'form-horizontal', 'id'=>'flocal', 'enctype'=>'multipart/form-data','style'=>'margin-bottom:200px;')) !!}

                    <div class="form-group row">
                                <label class="col-sm-4 control-label">@lang('Selectionner une section')</label>
                                <div class="col-xs-12 col-sm-8">
                                <select class="form-control" name="section" id="section" required>
                                    <option value="">@lang('Selectionner une option')</option>
                                    @foreach($sections as $section)
                                        <option value="{{ $section->id }}" @selected(old('section'))>
                                            {{ $section->libelle }}</option>
                                    @endforeach
                                </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-4 control-label">@lang('Selectionner une localite')</label>
                                <div class="col-xs-12 col-sm-8">
                                <select class="form-control" name="localite" id="localite" required>
                                    <option value="">@lang('Selectionner une option')</option>
                                    @foreach($localites as $localite)
                                        <option value="{{ $localite->id }}" data-chained="{{ $localite->section_id }}" @selected(old('localite'))>
                                            {{ $localite->nom }}</option>
                                    @endforeach
                                </select>
                                </div>
                            </div>

                            <div class="form-group row">

                                <label class="col-sm-4 control-label">@lang('Selectionner un producteur')</label>
                                <div class="col-xs-12 col-sm-8">
                                <select class="form-control" name="producteur" id="producteur" required>
                                    <option value="">@lang('Selectionner une option')</option>
                                    @foreach($producteurs as $data)
                                    <?php

                                    if(in_array($data->producteur->id,$producteurDistri)){
                                        continue;
                                    }
                                    ?>
                                        <option value="{{ $data->producteur->id }}" data-chained="{{ $data->producteur->localite->id }}" @selected(old('producteur'))>
                                            {{ stripslashes($data->producteur->nom) }} {{ stripslashes($data->producteur->prenoms) }}</option>
                                    @endforeach
                                </select>
                                </div>
                            </div>
     <hr class="panel-wide">

     <div class="form-group">
     <?php echo Form::label(__('ESPECES D\'ARBRE'), null, ['class' => 'col-sm-12 control-label', 'style'=>'font-weight:bold;font-size:20px;']); ?>
     <div class="col-xs-12 col-sm-12">
    <table class="table table-striped table-bordered">
    <tbody id="listeespece" style="text-align: center;">

    </tbody>
     </table>
     </div>
     </div>
    <hr class="panel-wide">
    <!-- <div style="
    position: fixed;
    bottom: 0px;
    left: 270px;
    width: 78%;
    overflow: hidden;
    background: #e9ecef;
"> -->
    <div class="form-group row">
        <?php echo Form::label(__('QUANTITE DEMANDEE'), null, ['class' => 'col-sm-4 control-label', 'style'=>'font-weight:bold;font-size:20px;']); ?>
        <div class="col-xs-12 col-sm-8">
        <input type="number" name="total" id="total"  class="form-control" readonly style="font-weight:bold; font-size:20px;" />
        </div>
    </div>
    <div class="form-group row">
        <?php echo Form::label(__('QUANTITE LIVREE'), null, ['class' => 'col-sm-4 control-label required', 'style'=>'font-weight:bold;font-size:20px;']); ?>
        <div class="col-xs-12 col-sm-8">
        <input type="number" name="qtelivre" id="qtelivre" class="form-control" readonly style="color:#FF0000; font-weight:bold; font-size:20px;" />
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
    <x-back route="{{ route('manager.agro.distribution.index') }}" />
@endpush

@push('script')
<script type="text/javascript">
    $('#producteur').change(function(){

var urlsend='{{ route("manager.agro.distribution.getAgroParcellesArbres") }}';

  $.ajax({
            type:'POST',
            url: urlsend,
            data: $('#flocal').serialize(),
            success:function(html){
            $('#listeespece').html(html.tableau);
            $("#total").val(html.total);
            $("#qtelivre").val(0);

            }

        });
});

$('#flocal').change('keyup change blur',function() {
  var total= $('#total').val();
});
function getQuantite(id,k,s)
  {
    update_amounts(id,k,s);
  }

function update_amounts(id,k,s)
{
    let total= $('#total').val();
    var sum= 0;
    let qtelivre = parseInt($('#qtelivre').val());
    let max = $('.quantity-'+id).attr('max');

    let quantite = 0;
    $('.quantity-'+id).each(function() {
    var qty = $(this).val();
     quantite = parseInt(quantite) + parseInt(qty);
    //  if(quantite>max){
    //     $('#qte-'+k).val(0);
    //     }
    });

    $('.totaux').each(function() {
        var nb = $(this).val();
            sum = parseInt(sum) + parseInt(nb);
    });

    if(sum > total){
        $('#qte-'+k).val(0);
        $('.totaux').each(function() {
        var nb = $(this).val();
            sum = parseInt(sum) + parseInt(nb);
             });
        }else{
            $('#qtelivre').val(sum);
        }
        for(let i = 1; i < 6; i++) {
            var soustotal = 0;

            $('.st-'+i).each(function() {
        var nb = $(this).val();
        soustotal = parseInt(soustotal) + parseInt(nb);

            });
            $('#soustotal-'+i).val(soustotal);

        }

    $("#qtelivre").attr({
              "max" : total,
              "min" : 1
            });
}
$("#localite").chained("#section");
    $("#producteur").chained("#localite");

 </script>
@endpush
