@extends('manager.layouts.app')
@section('panel')
    <div class="row mb-none-30">
        <div class="col-lg-12 mb-30">
            <div class="card">
                <div class="card-body">
                    {!! Form::model($nonmembre, [
                        'route' => ['manager.communaute.nonmembre.storenonmembre', $nonmembre->id],
                        'method' => 'POST',
                        'class' => 'form-horizontal',
                        'id' => 'flocal',
                        'enctype' => 'multipart/form-data',
                    ]) !!}

                    <input type="hidden" name="id" value="{{ $nonmembre->id }}">

                    <div class="form-group row">
                        <?php echo Form::label(__('Nom'), null, ['class' => 'col-sm-4 control-label required']); ?>
                        <div class="col-xs-12 col-sm-8">
                            <?php echo Form::text('nom', null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => 'Nom du non membre']); ?>
                        </div>
                    </div>
                    <div class="form-group row">
                        <?php echo Form::label(__('Prénom'), null, ['class' => 'col-sm-4 control-label required']); ?>
                        <div class="col-xs-12 col-sm-8">
                            <?php echo Form::text('prenom', null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => 'Prénom du non membre']); ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <?php echo Form::label(__('Sexe'), null, ['class' => 'col-sm-4 control-label']); ?>
                        <div class="col-xs-12 col-sm-8">
                            <?php echo Form::select('sexe', ['' => 'Selectionner une option', 'Homme' => 'Homme', 'Femme' => 'Femme'], null, ['class' => 'form-control', 'required' => 'required']); ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <?php echo Form::label(__('Téléphone'), null, ['class' => 'col-sm-4 control-label required']); ?>
                        <div class="col-xs-12 col-sm-8">
                            <?php echo Form::text('telephone', null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => 'Téléphone du visteur']); ?>
                        </div>
                    </div>
                    <div class="form-group row">
                        <?php echo Form::label(__('Representez vous un producteur ? '), null, ['class' => 'col-sm-4 control-label']); ?>
                        <div class="col-xs-12 col-sm-8">
                            <?php echo Form::select('representer', ['' => 'Selectionner une option', 'non' => 'Non', 'oui' => 'Oui'], null, ['class' => 'form-control representer', 'required' => 'required']); ?>
                        </div>
                    </div>
                    <div id="producteur">
                        <div class="form-group row">
                            <?php echo Form::label(__('Producteur'), null, ['class' => 'col-sm-4 control-label']); ?>
                            <div class="col-xs-12 col-sm-8">
                                <select class="form-control producteur" name="producteur">
                                    <option value="">@lang('Selectionner une option')</option>
                                    @foreach ($producteurs as $producteur)
                                        <option value="{{ $producteur->id }}" @selected($producteur->id == $nonmembre->producteur_id)>
                                            {{ stripslashes($producteur->nom) }} {{ stripslashes($producteur->prenoms) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <?php echo Form::label(__('Type de lien '), null, ['class' => 'col-sm-4 control-label']); ?>
                            <div class="col-xs-12 col-sm-8">
                                <?php echo Form::select('lien', ['' => 'Selectionner une option', 'Conjoint' => 'Conjoint', 'Neveu/Niece' => 'Neveu/Nièce', 'Fils/Fille' => 'Fils/Fille', 'Oncle/Tante' => 'Oncle/Tante', 'Frere/Soeur' => 'Frère/Soeur'], null, ['class' => 'form-control lien', 'required' => 'required']); ?>
                            </div>
                        </div>
                       <?php echo Form::hidden('activite_communautaire_id', $nonmembre->activite_communautaire_id, []); ?>

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
    <x-back route="{{ route('manager.communaute.nonmembre.nonmembre', $nonmembre->activite_communautaire_id) }}" />
@endpush

@push('script')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#producteur,#autre').hide();
            $('.representer').change(function() {
                if ($(this).val() == 'oui') {
                    $('#producteur').show('slow');
                    $('#producteur select[name="producteur"]').prop('required', true);
                    $('#producteur select[name="producteur"]').show('slow');
                    $('#producteur input[name="lien"]').prop('required', true);
                } else {
                    $('#producteur').hide('slow');
                    $('#producteur select[name="producteur"]').prop('required', false);
                    $('#producteur select[name="producteur"]').find('option[value=""]').prop('selected',
                        true);
                    $('#producteur select[name="producteur"]').hide('slow');
                    $('#producteur input[name="lien"]').prop('required', false);
                    $('#producteur input[name="lien"]').val('');
                }
            });
            if ($('.representer').val() == 'oui') {
                $('#producteur').show('slow');
                $('#producteur select[name="producteur"]').prop('required', true);
                $('#producteur select[name="producteur"]').show('slow');
                $('#producteur input[name="lien"]').prop('required', true);
            }
           

        });
    </script>
@endpush
