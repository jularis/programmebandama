@extends('admin.layouts.app')
@section('panel')
    <div class="row mb-none-30">
        <div class="col-lg-12 mb-30">
            <div class="card">
                <div class="card-body"> 
                    {!! Form::open(array('route' => ['admin.cooperative.localite.store'],'method'=>'POST','class'=>'form-horizontal', 'id'=>'flocal', 'enctype'=>'multipart/form-data')) !!} 
                         
                            <div class="form-group row">
                                <label class="col-xs-12 col-sm-4">@lang('Select Cooperative')</label>
                            <div class="col-xs-12 col-sm-8">
                                <select class="form-control" name="cooperative" required>
                                    <option value="">@lang('Selectionner une option')</option>
                                    @foreach($cooperatives as $cooperative)
                                        <option value="{{ $cooperative->id }}" @selected(old('cooperative'))>
                                            {{ __($cooperative->name) }}</option>
                                    @endforeach
                                </select>
                            </div>  
                        </div> 
<div class="form-group row">
        <?php echo Form::label(__('Nom de la localite'), null, ['class' => 'control-label col-xs-12 col-sm-4']); ?>
        <div class="col-xs-12 col-sm-8">
        <?php echo Form::text('nom', null, array('placeholder' => __('Nom de la localite'),'class' => 'form-control', 'required')); ?>
        </div>
</div>  
                         
        <div class="form-group row"> 
            <?php echo Form::label(__('Type de localite'), null, ['class' => 'control-label col-xs-12 col-sm-4']); ?>
            <div class="col-xs-12 col-sm-8">
                 <?php echo Form::select('type_localites',['Ville'=>'Ville','Campement'=>'Campement','Village'=>'Village'], null, ['placeholder' => __('Selectionner une option'),'class' => 'form-control', 'required']); ?>
        </div>
        </div> 

        <div class="form-group row">
            <?php echo Form::label(__('Sous préfecture'), null, ['class' => 'control-label col-xs-12 col-sm-4']); ?>
            <div class="col-xs-12 col-sm-8">
                 <?php echo  Form::text('sousprefecture', null, array('placeholder' => __('sous prefecture'),'class' => 'form-control', 'required')); ?>
        </div>
        </div> 
       
        <div class="form-group row">
            <?php echo Form::label(__('Estimation de la population'), null, ['class' => 'control-label col-xs-12 col-sm-4']); ?>
            <div class="col-xs-12 col-sm-8">
                 <?php echo Form::number('population',  null, array('placeholder' => __('nombre'),'class' => 'form-control','min'=>'1')); ?>
        </div>
        </div>
    <hr class="panel-wide">
   
                      
                      
        <div class="form-group row">
            <?php echo Form::label(__('Existe-t-il un centre de santé dans la localité ?'), null, ['class' => 'control-label col-xs-12 col-sm-4']); ?>
            <div class="col-xs-12 col-sm-8">
                  <?php echo Form::select('centresante', ['oui' => 'oui', 'non' => 'non'], null, [ 'class' => 'form-control centresante', 'required']); ?>
        </div>
        </div> 
 
    <div class="form-group row" id="kmCentresante"> 
             <?php echo Form::label(__('A combien de km du village se situe le centre de santé le plus proche ?'), null, ['class' => 'control-label col-xs-12 col-sm-4']); ?>
             <div class="col-xs-12 col-sm-8">
             <?php echo Form::number('kmCentresante',  null, array('placeholder' => __('nombre'),'class' => 'form-control kmCentresante','min'=>'0.1')); ?>
         </div> 
         </div> 
         <div class="form-group row">
            <?php echo Form::label(__('Type de centre de santé'), null, ['class' => 'control-label col-xs-12 col-sm-4']); ?>
            <div class="col-xs-12 col-sm-8">
                  <?php echo Form::select('typecentre', ['Publique' => 'publique', 'Prive' => 'prive'], null, ['class' => 'form-control typecentre']); ?>
        </div>
        </div> 

        <div class="form-group row">
            <?php echo Form::label(__('Nom de ce centre de santé'), null, ['class' => 'control-label col-xs-12 col-sm-4']); ?>
            <div class="col-xs-12 col-sm-8">
                 <?php echo  Form::text('nomCentresante', null, array('placeholder' => __('nom_centre'),'class' => 'form-control')); ?>
        </div>
        </div>
<hr class="panel-wide">

        <div class="form-group row">
            <?php echo Form::label(__('Existe-t-il une ou des école(s) primaire(s) publique(s) dans la localité ?'), null, ['class' => 'control-label col-xs-12 col-sm-4']); ?>
            <div class="col-xs-12 col-sm-8">
                   <?php echo Form::select('ecole', ['non' => 'non','oui' => 'oui'], null,array('class' => 'form-control ecole', 'required')); ?>
        </div> 
        </div> 

<div class="form-group row kmEcoleproche">
    <?php echo Form::label(__("A combien de km se trouve l'école la plus proche ?"), null, ['class' => 'control-label col-xs-12 col-sm-4']); ?> 
    <div class="col-xs-12 col-sm-8">
    <?php echo Form::number('kmEcoleproche',  null, array('placeholder' => __('nombre'),'class' => 'form-control','min'=>'0.1')); ?>
</div>
</div> 

<div class="form-group row nomEcoleproche">
    <?php echo Form::label(__('Donne le nom de cette école'), null, ['class' => 'control-label col-xs-12 col-sm-4']); ?>
    <div class="col-xs-12 col-sm-8">       
    <?php echo  Form::text('nomEcoleproche', null, array('placeholder' => '...','class' => 'form-control')); ?>
</div> 
</div>

    <div  id="nombrecole">
        <div class="form-group row">
            <?php echo Form::label(__("Combien d'école(s) primaire(s)?"), null, ['class' => 'control-label col-xs-12 col-sm-4']); ?>
            <div class="col-xs-12 col-sm-8">
            <?php echo  Form::number('nombrecole', null, array('placeholder' => __('nombre'),'class' => 'form-control nombrecole','min'=>'1')); ?>
        </div>
        </div>

    <div class="form-group row col-lg-12">
            <?php echo Form::label(__("Combien d'école(s) primaire(s)?"), null, ['class' => 'control-label col-xs-12 col-sm-4']); ?>
            
            <table class="table table-striped table-bordered">
    <tbody id="maladies">

 <tr>
            <td class="col-xs-12 col-sm-8">
            <div class="col-xs-12 col-sm-12 bg-success"><badge  class="btn btn-warning btn-sm">@lang('Donne le nom de cette école')</badge></div>
            <div class="col-xs-12 col-sm-12">
        <div class="form-group col-lg-12">
            <input type="text" name="nomecolesprimaires[]" placeholder="..." id="nomecolesprimaires-1" class="form-control" value="{{ old('nomecolesprimaires') }}" >
        </div>
        </div>

        </td>
        </tr>

    </tbody>
    <tfoot style="background: #e3e3e3;">
      <tr>

        <td colspan="3">
        <button id="addRowMal" type="button" class="btn btn-success btn-sm"><i class="fa fa-plus"></i></button>
</td>
<tr>
    </tfoot>
</table>
        </div> 
    </div>

     
    <hr class="panel-wide">
 
        <div class="form-group row">
            <?php echo Form::label(__("Quelle est la source d'eau potable dans la localité ?"), null, ['class' => 'control-label col-xs-12 col-sm-4']); ?>
            <div class="col-xs-12 col-sm-8">
            <?php echo Form::select('sources_eaux',['Pompe Hydraulique Villageoise'=>'Pompe Hydraulique Villageoise',
'SODECI'=>'SODECI',
'Marigot'=>'Marigot',
'Puits Individuel'=>'Puits Individuel'], null,  array('placeholder' => __('selectionner une option'),'class' => 'form-control sourceeau', 'required')); ?>
        </div>
        </div>

        <div class="form-group row" id="etatpompehydrau">
        <?php echo Form::label(__('Est-il en bon état ?'), null, ['class' => 'control-label col-xs-12 col-sm-4']); ?>
        <div class="col-xs-12 col-sm-8">    
        <?php echo Form::select('etatpompehydrau', ['oui' => 'oui', 'non' => 'non'], null, array('class' => 'form-control etatpompehydrau')); ?>
        </div>
        </div>

        <div class="form-group row">
            <?php echo Form::label(__("Existe-t-il l'éclairage public dans la localité ?"), null, ['class' => 'control-label col-xs-12 col-sm-4']); ?>
            <div class="col-xs-12 col-sm-8">
                  <?php echo Form::select('electricite', ['oui' => 'oui', 'non' => 'non'], null,array('class' => 'form-control', 'required')); ?>
        </div>
        </div>
    <hr class="panel-wide">
    <div class="form-group row">
            <?php echo Form::label(__('Existe-t-il un marché dans la localité ?'), null, ['class' => 'control-label col-xs-12 col-sm-4']); ?>
            <div class="col-xs-12 col-sm-8">
            <?php echo Form::select('marche', ['oui' => 'oui', 'non' => 'non'], null,array('class' => 'form-control marche', 'required')); ?>
        </div>
        </div>
    <div class="form-group row">
            <?php echo Form::label(__('Quel est le jour du marché ?'), null, ['class' => 'control-label col-xs-12 col-sm-4']); ?> 
            <div class="col-xs-12 col-sm-8">
            <?php echo Form::select('jourmarche', ['Lundi' =>__('lundi'), 'Mardi' => __('mardi'),'Mercredi' => __('mercredi'),'Jeudi' => __('jeudi'),'Vendredi' => __('vendredi'),'Samedi' =>__('samedi'),'Dimanche' => __('dimanche')], null,array('class' => 'form-control marche')); ?>
        </div>
        </div>
    <div class="form-group row" id="kmmarcheproche">
            <?php echo Form::label(__('A combien de km se trouve le marché le plus proche ?'), null, ['class' => 'control-label col-xs-12 col-sm-4']); ?>
            <div class="col-xs-12 col-sm-8">
            <?php echo  Form::number('kmmarcheproche', null, array('placeholder' => __('nom_marche'),'class' => 'form-control kmmarcheproche','min'=>'0.1')); ?>
        </div>
        </div>
        <div class="form-group row">
            <?php echo Form::label(__('Existe-t-il un endroit public pour le déversement des déchets dans la localité ?'), null, ['class' => 'control-label col-xs-12 col-sm-4']); ?>
            <div class="col-xs-12 col-sm-8">
            <?php echo Form::select('deversementDechets', ['oui' => 'oui', 'non' => 'non'], null,array('class' => 'form-control', 'required')); ?>
        </div>
        </div>
    <hr class="panel-wide">
        <div class="form-group row">
            <?php echo Form::label(__("Nombre comite de main d'œuvre qu'il y a dans la localité"), null, ['class' => 'control-label col-xs-12 col-sm-4']); ?>
            <div class="col-xs-12 col-sm-8">
            <?php echo  Form::number('comiteMainOeuvre', null, array('placeholder' => __('nombre'),'class' => 'form-control','min'=>'0')); ?>
        </div>
        </div>

        <div class="form-group row">
            <?php echo Form::label(__("Nombre d'association de femmes qu'il y a dans la localité"), null, ['class' => 'control-label col-xs-12 col-sm-4']); ?>
            <div class="col-xs-12 col-sm-8">
                 <?php echo  Form::number('associationFemmes', null, array('placeholder' => __('nombre'),'class' => 'form-control','min'=>'0')); ?>
        </div>
        </div>

        <div class="form-group row">
            <?php echo Form::label(__("Nombre d'association de jeunes qu'il y a dans la localité"), null, ['class' => 'control-label col-xs-12 col-sm-4']); ?>
            <div class="col-xs-12 col-sm-8">
                <?php echo  Form::number('associationJeunes', null, array('placeholder' => __('nombre'),'class' => 'form-control','min'=>'0')); ?>
        </div>
    </div>
    <hr class="panel-wide">
    
    <div class="form-group row">
            <?php echo Form::label(__("Prise des coordonnées gps de la localité"), null, ['class' => 'control-label col-xs-12 col-sm-12']); ?> 
    </div>
            <div class="form-group row">
            <?php echo Form::label('Longitude', null, ['class' => 'control-label col-xs-12 col-sm-4']); ?>
            <div class="col-xs-12 col-sm-8">
            <?php echo  Form::text('localongitude', null, array('placeholder' =>__('longitude'),'class' => 'form-control')); ?>
        </div> 
        </div>
        <div class="form-group row">
        <?php echo Form::label('Latitude', null, ['class' => 'control-label col-xs-12 col-sm-4']); ?>
        <div class="col-xs-12 col-sm-8">
        <?php echo  Form::text('localatitude', null, array('placeholder' => __('latitude'),'class' => 'form-control')); ?>
        </div>
</div>

                        <div class="form-group">
                            <button type="submit" class="btn btn--primary w-100 h-45"> @lang('Envoyer')</button>
                        </div>
                        {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>


@endsection

@push('breadcrumb-plugins')
    <x-back route="{{ route('admin.cooperative.localite.index') }}" />
@endpush

@push('script')
<script type="text/javascript">
$(document).ready(function () {

         var maladiesCount = $("#maladies tr").length + 1;
         $(document).on('click', '#addRowMal', function(){

           //---> Start create table tr
           var html_table = '<tr>';
           html_table +='<td class="row"><div class="col-xs-12 col-sm-12 bg-success"><badge class="btn btn-warning btn-sm">Nom Ecole Primaire ' + maladiesCount + '</badge></div><div class="col-xs-12 col-sm-12"><div class="form-group"><input placeholder="..." class="form-control" id="nomecolesprimaires-' + maladiesCount + '" name="nomecolesprimaires[]" type="text"></div></div><div class="col-xs-12 col-sm-8"><button type="button" id="' + maladiesCount + '" class="removeRowMal btn btn-danger btn-sm"><i class="fa fa-minus"></i></button></div></td>';

           html_table += '</tr>';
           //---> End create table tr

           maladiesCount = parseInt(maladiesCount) + 1;
           $('#maladies').append(html_table);

         });

           $(document).on('click', '.removeRowMal', function(){

           var row_id = $(this).attr('id');

           // delete only last row id
           if (row_id == $("#maladies tr").length) {

             $(this).parents('tr').remove();

             maladiesCount = parseInt(maladiesCount) - 1;

           }
         });

     });


$('#kmCentresante,#nombrecole, #etatpompehydrau,#kmmarcheproche').hide();
$('.marche').change(function(){
var marche= $('.marche').val();
  if(marche=='non')
  {
   $('#kmmarcheproche').show('slow');
  }
  else{
   $('#kmmarcheproche').hide('slow');
  }
});
$('.centresante').change(function(){
var centresante= $('.centresante').val();
  if(centresante=='non')
  {
   $('#kmCentresante').show('slow');
  }
  else{
   $('#kmCentresante').hide('slow');
  }
});

$('.ecole').change(function(){
var ecole= $('.ecole').val();
  if(ecole=='oui')
  {
   $('#nombrecole').show('slow');
   $('#kmEcoleproche').hide('slow');
  }
  else{
   $('#kmEcoleproche').show('slow');
   $('#nombrecole').hide('slow');
  }
});

$('.sourceeau').change(function(){
var sourceeau= $('.sourceeau').val();
  if(sourceeau=='Pompe Hydraulique Villageoise')
  {
   $('#etatpompehydrau').show('slow');
  }
  else{
   $('#etatpompehydrau').hide('slow');
  }
});
</script>
@endpush