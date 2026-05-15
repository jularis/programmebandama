@extends('manager.layouts.app')
@section('panel')
    <div class="row mb-none-30">
        <div class="col-lg-12 mb-30">
            <div class="card">
                <div class="card-body"> 
         {!! Form::model($distribution, ['method' => 'POST','route' => ['manager.agro.distribution.update', $distribution->id],'class'=>'form-horizontal', 'id'=>'flocal', 'enctype'=>'multipart/form-data']) !!}
                        <input type="hidden" name="id" value="{{ $distribution->id }}"> 
                        <div class="form-group row">
                                <label class="col-sm-4 control-label" style="font-weight:bold;font-size:20px;">@lang('Producteur')</label>
                                <div class="col-xs-12 col-sm-8">  
                                {!! Form::text('producteurs', $distribution->producteur->nom.' '.$distribution->producteur->prenoms, array('placeholder' => __('Producteur'),'class' => 'form-control', 'readonly')) !!}
                                 
                                </div>
                            </div>
                        <div class="form-group">
     <?php echo Form::label(__('ESPECES D\'ARBRE'), null, ['class' => 'col-sm-12 control-label', 'style'=>'font-weight:bold;font-size:20px;']); ?>
     <div class="col-xs-12 col-sm-12">
    <table class="table table-striped table-bordered">
    <tbody id="listeespece">
    <?php echo $results; ?>
    </tbody>
     </table>
     </div>
     </div>
    <hr class="panel-wide">
    <div class="form-group row">
        <?php echo Form::label(__('QUANTITE DEMANDEE'), null, ['class' => 'col-sm-4 control-label', 'style'=>'font-weight:bold;font-size:20px;']); ?>
        <div class="col-xs-12 col-sm-8">
        <input type="number" name="total" id="total" value="{{ $total }}" class="form-control" readonly style="font-weight:bold; font-size:20px;" />
        </div>
    </div>
    <div class="form-group row">
        <?php echo Form::label(__('QUANTITE LIVREE'), null, ['class' => 'col-sm-4 control-label required', 'style'=>'font-weight:bold;font-size:20px;']); ?>
        <div class="col-xs-12 col-sm-8">
        <input type="number" name="qtelivre" id="qtelivre" class="form-control" value="{{ $somme }}" readonly style="color:#FF0000; font-weight:bold; font-size:20px;" />
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
    </script>
@endpush