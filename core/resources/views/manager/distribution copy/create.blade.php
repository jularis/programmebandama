@extends('manager.layouts.app')
@section('panel')
    <div class="row mb-none-30">
        <div class="col-lg-12 mb-30">
            <div class="card">
                <div class="card-body"> 
                    {!! Form::open(array('route' => ['manager.agro.distribution.store'],'method'=>'POST','class'=>'form-horizontal', 'id'=>'flocal', 'enctype'=>'multipart/form-data')) !!} 
                        
                            <div class="form-group row">
                                <label class="col-sm-4 control-label">@lang('Selectionner une localite')</label>
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
                                <label class="col-sm-4 control-label">@lang('Selectionner un producteur')</label>
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
     if(quantite>max){
        $('#qte-'+k).val(0); 
        } 
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
            let maxparc = $('.st-'+i).attr('parc-'+i); 
            console.log(maxparc);
            $('.st-'+i).each(function() {
        var nb = $(this).val(); 
        soustotal = parseInt(soustotal) + parseInt(nb);  
                if(soustotal>maxparc){ 
                    $('#qte-'+k).val(0);  
                }
            });
            if(soustotal<=maxparc){ 
                $('#soustotal-'+i).val(soustotal); 
                }
            
        }  
    
    $("#qtelivre").attr({
              "max" : total,       
              "min" : 1      
            });
}

    $("#producteur").chained("#localite");
    
 </script>
@endpush