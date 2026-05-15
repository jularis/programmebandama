<div class="modal-header">
    <h5 class="modal-title">{{$pageTitle}}</h5>
    <button type="button" class="close" data-bs-dismiss="modal" aria-hidden="true">×</button>
</div>
<div class="modal-body">
    <div class="portlet-body">
        <x-form id="createFormateur" method="POST" class="ajax-form">
        <div class="form-group row">
              
                    <label for="entreprise_id" class="control-label col-sm-4">@lang('Entreprise')</label>
                    <div class="col-xs-12 col-sm-8">
                   <select class="form-control" name="entreprise_id" id="entreprise_id" required>
                        <option value="">@lang('Choisir une entreprise')</option>
                        @foreach($entreprises as $entreprise)
                            <option value="{{ $entreprise->id }}">{{ $entreprise->nom_entreprise }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
                    <div class="form-group row">
        <?php echo Form::label(__('Nom du transporteur'), null, ['class' => 'col-sm-4 control-label required']); ?>
        <div class="col-xs-12 col-sm-8">
        <?php echo Form::text('nom', null, array('placeholder' => __('Nom du transporteur'),'class' => 'form-control', 'required')); ?>
        </div>
    </div>

        <div class="form-group row">
        <?php echo Form::label(__('Prenoms du transporteur'), null, ['class' => 'col-sm-4 control-label required']); ?>
        <div class="col-xs-12 col-sm-8">
        <?php echo Form::text('prenoms', null, array('placeholder' => __('Prenoms du transporteur'),'class' => 'form-control', 'required')); ?>
        </div>
    </div>


        <div class="form-group row">
            <?php echo Form::label(__('Genre'), null, ['class' => 'col-sm-4 control-label']); ?>
            <div class="col-xs-12 col-sm-8">
            <?php echo Form::select('sexe', ['Homme' => __('Homme'), 'Femme' => __('Femme')], null,array('class' => 'form-control', 'required')); ?>

        </div>
    </div>

        <div class="form-group row">
        <?php echo Form::label(__('Date de naissance'), null, ['class' => 'col-sm-4 control-label required']); ?>
        <div class="col-xs-12 col-sm-8">
             <?php echo Form::date('date_naiss', null,array('class' => 'form-control', 'id'=>'datenais', 'required') ); ?>
        </div>
    </div>

        <div class="form-group row">
            <?php echo Form::label(__('Numero de téléphone 1'), null, ['class' => 'col-sm-4 control-label required']); ?>
            <div class="col-xs-12 col-sm-8">
                <?php echo Form::text('phone1',  null, array('class' => 'form-control phone', 'required')); ?>
        </div>
    </div>

        <div class="form-group row">
            <?php echo Form::label(__('Numero de téléphone 2'), null, ['class' => 'col-sm-4 control-label']); ?>
            <div class="col-xs-12 col-sm-8">
                 <?php echo Form::text('phone2',  null, array('class' => 'form-control phone')); ?>
        </div>
    </div>

    <div class="form-group row">
        <?php echo Form::label(__('Nationalité'), null, ['class' => 'col-sm-4 control-label required']); ?>
        <div class="col-xs-12 col-sm-8">
        <select name="nationalite" id="nationalite" class="form-control nationalite select-picker" data-live-search="true" required> 
        <option value="">@lang('Selectionner une option')</option>
                                                   @foreach ($countries as $item)
                                <option data-tokens="{{ $item->iso3 }}" data-phonecode = "{{$item->phonecode}}"
                                    data-content="<span class='flag-icon flag-icon-{{ strtolower($item->iso) }} flag-icon-squared'></span> {{ $item->nicename }}"
                                    value="{{ $item->nicename }}">{{ $item->nicename }}</option>
                            @endforeach
                                                </select>
        </div>
    </div>
    <div class="form-group row">
        <?php echo Form::label(__('Niveau d\'étude'), null, ['class' => 'col-sm-4 control-label']); ?>
        <div class="col-xs-12 col-sm-8">
        <?php echo Form::select('niveau_etude', ['Primaire' => 'Primaire', 'Collège (6e à 3ème)' => 'Collège (6e à 3ème)', 'Lycée (2nde à Tle)' => 'Lycée (2nde à Tle)', 'Superieur (BAC et Plus)' => 'Superieur (BAC et Plus)', 'Aucun' => 'Aucun'], null, ['placeholder' => __('Selectionner une option'), 'class' => 'form-control', 'required']); ?>
        </div>
    </div>

        <div class="form-group row">
        <?php echo Form::label(__('Type de pièces'), null, ['class' => 'col-sm-4 control-label required']); ?>
        <div class="col-xs-12 col-sm-8">
        <?php echo Form::select('type_piece', ['CNI' => 'CNI', 'Carte Consulaire' => 'Carte Consulaire', 'Passeport' => 'Passeport', 'Attestation' => 'Attestation', 'Extrait de naissance' => 'Extrait de naissance', 'Permis de conduire' => 'Permis de conduire', 'CMU' => 'CMU','Pas Disponible' => 'Pas Disponible'], null, ['placeholder' => __('Selectionner une option'), 'class' => 'form-control', 'required']); ?> 
        </div>
    </div>

        <div class="form-group row">
        <?php echo Form::label(__('N° de la pièce d\'identité'), null, ['class' => 'col-sm-4 control-label required']); ?>
        <div class="col-xs-12 col-sm-8">
          <?php echo Form::text('num_piece', null, array('placeholder' => '','class' => 'form-control','required')); ?>
        </div>
    </div>
    <div class="form-group row">
        <?php echo Form::label(__('N° du permis de conduire'), null, ['class' => 'col-sm-4 control-label required']); ?>
        <div class="col-xs-12 col-sm-8">
          <?php echo Form::text('num_permis', null, array('placeholder' => '','class' => 'form-control','required')); ?>
        </div>
    </div>

    <hr class="panel-wide">

    <div class="form-group row">

                     <?php echo Form::label(__('Photo du transporteur'), null, ['class' => 'col-sm-4 control-label']); ?>
                     <div class="col-xs-12 col-sm-8">
                           <input type="file" name="photo" accept="image/*" class="form-control dropify-fr" data-max-file-size="2M" data-msg-required="Choisissez une image sur votre disque" id="image">
                 </div>
     </div>
     <div class="form-group row">

                     <?php echo Form::label(__('Pièce d\'identité'), null, ['class' => 'col-sm-4 control-label']); ?>
                     <div class="col-xs-12 col-sm-8">
                           <input type="file" name="photo_piece_identite" accept="image/*" class="form-control dropify-fr" data-max-file-size="2M" data-msg-required="Choisissez une image sur votre disque" id="piece">
                 </div>
     </div>
     <div class="form-group row">

                     <?php echo Form::label(__('Permis de conduire'), null, ['class' => 'col-sm-4 control-label']); ?>
                     <div class="col-xs-12 col-sm-8">
                           <input type="file" name="photo_permis_conduire" accept="image/*" class="form-control dropify-fr" data-max-file-size="2M" data-msg-required="Choisissez une image sur votre disque" id="permis">
                 </div>
     </div>

                    </div> 
        </x-form>

    </div>
</div>
<div class="modal-footer">
    <x-forms.button-cancel data-bs-dismiss="modal" class="border-0 mr-3">@lang('app.cancel')</x-forms.button-cancel>
    <x-forms.button-primary id="save-formateur-staff" icon="check">@lang('app.save')</x-forms.button-primary>
</div>


<script>
    $(document).ready(function() {
        setTimeout(function() {
            $('[data-toggle="popover"]').popover();
        }, 500);
    });

    $('#save-formateur-staff').click(function() {
        $.easyAjax({
            container: '#createFormateur',
            type: "POST",
            disableButton: true,
            blockUI: true,
            buttonSelector: "#save-formateur-staff",
            errorPosition: 'inline',
            url: "{{ route('manager.settings.transporteurModal.store') }}",
            data: $('#createFormateur').serialize(),
            success: function(response) {
                if (response.status == 'success') {
                    if (response.page_reload == 'true') {
                        window.location.reload();
                    } else {
                        $('#documentad_type_id').html(response.data);
                        $(MODAL_XL).modal('hide');
                    }
                }
            }
        })
    });
</script>
