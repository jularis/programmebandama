@extends('manager.layouts.app')
@section('panel')
    <div class="row mb-none-30">
        <div class="col-lg-12 mb-30">
            <div class="card">
                <div class="card-body">
                    {!! Form::model($parcelle, [
                        'method' => 'POST',
                        'route' => ['manager.traca.parcelle.store', $parcelle->id],
                        'class' => 'form-horizontal',
                        'id' => 'flocal',
                        'enctype' => 'multipart/form-data',
                    ]) !!}
                    <input type="hidden" name="id" value="{{ $parcelle->id }}">

                    <div class="form-group row">
                        <label class="col-sm-4 control-label">@lang('Selectionner une section')</label>
                        <div class="col-xs-12 col-sm-8">
                            <select class="form-control" name="section" id="section" required>
                                <option value="">@lang('Selectionner une option')</option>
                                @foreach ($sections as $section)
                                    <option value="{{ $section->id }}" @selected($section->id == $parcelle->producteur->localite->section->id)>
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
                                @foreach ($localites as $localite)
                                    <option value="{{ $localite->id }}" data-chained="{{ $localite->section->id }}"
                                        @selected($localite->id == $parcelle->producteur->localite->id)>
                                        {{ $localite->nom }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 control-label">@lang('Selectionner un producteur')</label>
                        <div class="col-xs-12 col-sm-8">
                            <select class="form-control" name="producteur_id" id="producteur_id" required>
                                <option value="">@lang('Selectionner une option')</option>
                                @foreach ($producteurs as $producteur)
                                    <option value="{{ $producteur->id }}" data-chained="{{ $producteur->localite->id }}"
                                        @selected($producteur->id == $parcelle->producteur->id)>
                                        {{ stripslashes($producteur->nom) }} {{ stripslashes($producteur->prenoms) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <?php echo Form::label(__('Type de déclaration superficie'), null, ['class' => 'col-sm-4 control-label required']); ?>
                        <div class="col-xs-12 col-sm-8">
                            <?php echo Form::select('typedeclaration', ['' => 'Selectionner une option', 'Verbale' => __('Verbale'), 'GPS' => __('GPS')], null, ['class' => 'form-control typedeclaration', 'id' => 'typedeclaration', 'required']); ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        {{ Form::label(__('Quelle est l\'année de création de la parcelle'), null, ['class' => 'col-sm-4 control-label']) }}
                        <div class="col-xs-12 col-sm-8">
                            <?php echo Form::number('anneeCreation', null, ['placeholder' => 'Année de création', 'class' => 'form-control', 'id' => 'anneeCreation', 'required']); ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        {{ Form::label(__('Quel est l\'âge moyen des cacaoyers ?'), null, ['class' => 'col-sm-4 control-label']) }}
                        <div class="col-xs-12 col-sm-8">
                            <?php echo Form::number('ageMoyenCacao', null, ['placeholder' => 'Age moyen des cacaoyers', 'class' => 'form-control', 'id' => 'ageMoyenCacao', 'required']); ?>
                        </div>
                    </div>


                    <div class="form-group row">
                        {{ Form::label(__('Est ce que la parcelle a été régenerer ?'), null, ['class' => 'col-sm-4 control-label']) }}
                        <div class="col-xs-12 col-sm-8">
                            <?php echo Form::select('parcelleRegenerer', ['non' => __('non'), 'oui' => __('oui')], null, ['class' => 'form-control parcelleRegenerer', 'required']); ?>
                        </div>
                    </div>
                    <div id="anneeRegenerers">
                        <div class="form-group row">
                            {{ Form::label(__('Année de régéneration'), null, ['class' => 'col-sm-4 control-label']) }}
                            <div class="col-xs-12 col-sm-8">
                                <?php echo Form::number('anneeRegenerer', null, ['id' => 'anneeRegenerer', 'placeholder' => 'Année de régéneration', 'class' => 'form-control anneeRegenerer']); ?>
                            </div>
                        </div>

                        <div class="form-group row">
                            {{ Form::label(__('Superficie concerné'), null, ['class' => 'col-sm-4 control-label']) }}
                            <div class="col-xs-12 col-sm-8">
                                <?php echo Form::number('superficieConcerne', null, ['id' => 'superficieConcerne', 'placeholder' => 'Superficie concernée', 'class' => 'form-control superficieConcerne']); ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row mt-3">
                        <label class="col-sm-4 control-label">@lang('Quelles sont les variétés de culture ?')</label>
                        <div class="col-xs-12 col-sm-8">
                            <select class="form-control select-picker selectAll varietes" name="varietes[]" multiple
                                required>
                                <option value="">@lang('Selectionner le/les variétés')</option>
                                <option value="CNRA"
                                    {{ in_array('CNRA', old('varietes', $parcelle->varietes->pluck('variete')->toArray())) ? 'selected' : '' }}>
                                    @lang('CNRA')</option>
                                <option value="Tout venant"
                                    {{ in_array('Tout venant', old('varietes', $parcelle->varietes->pluck('variete')->toArray())) ? 'selected' : '' }}>
                                    @lang('Tout venant')</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        {{ Form::label(__('Quel type de documents  possèdes-tu ?'), null, ['class' => 'col-sm-4 control-label']) }}
                        <div class="col-xs-12 col-sm-8">
                            <?php echo Form::select('typeDoc', ['' => 'Selectionner une option', 'Attestation de plantation' => 'Attestation de plantation', 'Attestation coutumières' => 'Attestation coutumières', 'Cadastre' => 'Cadastre', 'Certificat foncier' => 'Certificat foncier', 'Contrat agraire' => 'Contrat agraire', 'Aucun document' => 'Aucun document'], null, ['class' => 'form-control', 'required']); ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        {{ Form::label(__('Ya-t-il un cour ou plan d’eau dans la parcelle ?'), null, ['class' => 'col-sm-4 control-label']) }}
                        <div class="col-xs-12 col-sm-8">
                            <?php echo Form::select('presenceCourDeau', ['non' => __('non'), 'oui' => __('oui')], null, ['class' => 'form-control presenceCourDeau', 'required']); ?>
                        </div>
                    </div>
                    <div id="courDeaus">
                        <div class="form-group row">
                            {{ Form::label(__('Quel est le cour ou plan d\'eau'), null, ['class' => 'col-sm-4 control-label']) }}
                            <div class="col-xs-12 col-sm-8">
                                <?php echo Form::select('courDeau', ['Bas-fond' => 'Bas-fond', 'Marigot' => 'Marigot', 'Rivière' => 'Rivière', 'Source d’eau' => 'Source d’eau', 'Puit' => 'Puit', 'Autre' => 'Autre'], null, ['id' => 'courDeau', 'class' => 'form-control courDeau']); ?>
                            </div>
                        </div>
                        <div class="form-group row">
                            {{ Form::label(__('Est ce qu\'il existe des mésures de protection ?'), null, ['class' => 'col-sm-4 control-label']) }}
                            <div class="col-xs-12 col-sm-8">
                                <?php echo Form::select('existeMesureProtection', ['non' => __('non'), 'oui' => __('oui')], null, ['class' => 'form-control existeMesureProtection', 'required']); ?>
                            </div>
                        </div>
                        <div class="form-group row" id="protection">
                            <label class="col-sm-4 control-label">@lang('Sélectionner les protections')</label>
                            <div class="col-xs-12 col-sm-8">
                                <select class="form-control select2-multi-select protections" name="protection[]" multiple>
                                    <option value="">@lang('Selectionner les protections')</option>
                                    <option value="barriere de végétation"
                                        {{ in_array('barriere de végétation', $protections) ? 'selected' : '' }}>
                                        Barrière de végétation</option>
                                    <option value="zone tampon"
                                        {{ in_array('zone tampon', $protections) ? 'selected' : '' }}>
                                        Zone tampon
                                    </option>
                                    <option value="autre" {{ in_array('autre', $protections) ? 'selected' : '' }}>
                                        Autre</option>
                                </select>
                            </div>
                        </div>

                    </div>

                    <div class="form-group row" id="autreCourDeaus">
                        <?php echo Form::label(__('Autre cour ou plan d\'eau '), null, ['class' => 'col-sm-4 control-label']); ?>
                        <div class="col-xs-12 col-sm-8">
                            <?php echo Form::text('autreCourDeau', null, ['class' => 'form-control autreCourDeau', 'placeholder' => 'Autre Cour ou Plan d\'eau']); ?>
                        </div>
                    </div>


                    <div class="form-group row" id="autreProtections">
                        <?php echo Form::label(__('Autre Protection'), null, ['class' => 'col-sm-4 control-label']); ?>
                        <div class="col-xs-12 col-sm-8">
                            <?php echo Form::text('autreProtection', null, ['class' => 'form-control autreProtection', 'placeholder' => 'Autre Protection', 'id' => 'autreProtection']); ?>
                        </div>
                    </div>
                    <div class="form-group row">
                        {{ Form::label(__('Ya-t-il une pente dans la Parcelle ?'), null, ['class' => 'col-sm-4 control-label']) }}
                        <div class="col-xs-12 col-sm-8">
                            <?php echo Form::select('existePente', ['non' => __('non'), 'oui' => __('oui')], null, ['class' => 'form-control existePente', 'required']); ?>
                        </div>
                    </div>
                    <div class="form-group row" id="niveauPentes">
                        {{ Form::label(__('Quel est le niveau de la pente?'), null, ['class' => 'col-sm-4 control-label']) }}
                        <div class="col-xs-12 col-sm-8">
                            <?php echo Form::select('niveauPente', ['Douce' => 'Douce', 'Moyenne' => 'Moyenne', 'Forte' => 'Forte'], null, ['id' => 'niveauPente', 'class' => 'form-control niveauPente']); ?>
                        </div>
                    </div>
                    <div class="form-group row">
                        {{ Form::label(__('Présence de signe d\'érosion?'), null, ['class' => 'col-sm-4 control-label']) }}
                        <div class="col-xs-12 col-sm-8">
                            <?php echo Form::select('erosion', ['non' => __('non'), 'oui' => __('oui')], null, ['class' => 'form-control', 'required']); ?>
                        </div>
                    </div>
                    <div class="row mb-30">
                        <div class="col-lg-12">
                            <div class="card border--primary mt-3">
                                <h5 class="card-header bg--primary text-white">@lang('Quels sont les arbres à Ombrages observés ?')
                                    <button type="button" class="btn btn-sm btn-outline-light float-end addUserData"><i
                                            class="la la-fw la-plus"></i>@lang('Ajouter un arbre d\'ombrage')
                                    </button>
                                </h5>
                                <div class="card-body">
                                    <div class="row" id="addedField">
                                        <?php $i = 0; ?>
                                        @foreach ($agroespeceabreParcelle as $item)
                                            <div class="row single-item gy-2">
                                                <div class="col-md-8">
                                                    <select class="form-control selected_type"
                                                        name="items[{{ $loop->index }}][arbre]"
                                                        id='producteur-<?php echo $i; ?>'
                                                        onchange=getParcelle(<?php echo $i; ?>) required>
                                                        <option disabled selected value="">@lang('Abres d\'ombrages')
                                                        </option>
                                                        @foreach ($arbres as $arbre)
                                                            <option value="{{ $arbre->id }}"
                                                                @selected($item->agroespeceabre_id == $arbre->id)>
                                                                {{ __($arbre->nom) }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="col-md-3">
                                                    <div class="input-group mb-3">
                                                        <input type="number" class="form-control nombre"
                                                            value="{{ $item['nombre'] }}"
                                                            name="items[{{ $loop->index }}][nombre]" required>
                                                        <span class="input-group-text unit"><i
                                                                class="las la-balance-scale"></i></span>
                                                    </div>
                                                </div>

                                                <div class="col-md-1">
                                                    <button class="btn btn--danger w-100 removeBtn w-100 h-45"
                                                        type="button">
                                                        <i class="fa fa-times"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <?php $i++; ?>
                                        @endforeach

                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-12">
                        <table class="table table-striped table-bordered">
                            <tbody id="insectesParasites_area">
                                @if ($autreAgroespecesarbreParcelle)
                                    @foreach ($autreAgroespecesarbreParcelle as $key => $item)
                                        <tr>
                                            <td class="row">
                                                <div class="col-xs-12 col-sm-12 bg-success">
                                                    <badge class="btn  btn-outline--warning h-45 btn-sm text-white">
                                                        @lang('Autres arbres à ombrages ne figurant pas dans la liste précedente')
                                                    </badge>
                                                </div>
                                                <div class="col-xs-12 col-sm-4">
                                                    <div class="form-group row">
                                                        {{ Form::label(__('Nom'), null, ['class' => 'control-label']) }}
                                                        <input type="text"
                                                            name="arbreStrate[{{ $key }}][nom]"
                                                            id="nom-{{ $key }}" class="form-control"
                                                            value="{{ $item['nom'] }}"
                                                            placeholder="Nom de l'arbre à ombrage">
                                                        <input type="hidden" name="arbreStrate[{{ $key }}][id]"
                                                            value="{{ $item['id'] }}">
                                                    </div>
                                                </div>
                                                <div class="col-xs-12 col-sm-4">
                                                    <div class="form-group row">
                                                        <label>Strate</label> <select class="form-control strate"
                                                            name="arbreStrate[{{ $key }}][strate]"
                                                            id="strate-{{ $key }}">
                                                            <option value="">Selectionne une strate</option>
                                                            <option value="1" @selected($item['strate'] == 1)>Strate 1
                                                            </option>
                                                            <option value="2" @selected($item['strate'] == 2)>Strate 2
                                                            </option>
                                                            <option value="3" @selected($item['strate'] == 3)>Strate 3
                                                            </option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-xs-12 col-sm-4">
                                                    <div
                                                        class="form-group
                                                    row">
                                                        {{ Form::label(__('Nombre'), null, ['class' => 'control-label']) }}
                                                        <input type="number"
                                                            name="arbreStrate[{{ $key }}][nombre]"
                                                            id="qte-{{ $key }}" class="form-control"
                                                            value="{{ $item['nombre'] }}"
                                                            placeholder="Saisissez le nombre d' arbre observé">
                                                    </div>
                                                </div>
                                                <div class="col-xs-12 col-sm-8">
                                                    <button type="button" id="{{ $loop->index}}"
                                                        class="removeRowinsectesParasites btn btn-danger btn-sm"><i
                                                            class="fa fa-minus"></i></button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                            <tfoot style="background: #e3e3e3;">
                                <tr>

                                    <td colspan="3">
                                        <button id="addRowinsectesParasites" type="button"
                                            class="btn btn-success btn-sm"><i class="fa fa-plus"></i></button>
                                    </td>
                                <tr>
                            </tfoot>
                        </table>
                    </div>

                    <hr class="panel-wide">
                    <div class="form-group row">
                        {{ Form::label(__('Information GPS de la parcelle'), null, ['class' => 'col-sm-4 control-label']) }}
                        <div class="col-xs-12 col-sm-12">
                            <table class="table table-bordered">
                                <tbody id="product_area">

                                    <tr>
                                        <td class="row">

                                            <div class="col-xs-12 col-sm-12">
                                                <div class="form-group row">
                                                    {{ Form::label(__('Superficie'), null, ['class' => 'col-sm-4 control-label required']) }}
                                                    {!! Form::text('superficie', null, [
                                                        'placeholder' => __('Superficie'),
                                                        'class' => 'form-control superficie',
                                                        'id' => 'superficie-1',
                                                        'required',
                                                    ]) !!}

                                                </div>
                                            </div>

                                            <div class="col-xs-12 col-sm-12">
                                                <div class="form-group row">
                                                    {{ Form::label(__('Latitude'), null, ['class' => 'col-sm-4 control-label']) }}
                                                    {!! Form::text('latitude', null, [
                                                        'placeholder' => __('Latitude Ex : 0.5'),
                                                        'class' => 'form-control',
                                                        'id' => 'latitude-1',
                                                    ]) !!}

                                                </div>
                                            </div>
                                            <div class="col-xs-12 col-sm-12">
                                                <div class="form-group row">
                                                    {{ Form::label(__('Longitude'), null, ['class' => 'col-sm-4 control-label']) }}
                                                    {!! Form::text('longitude', null, [
                                                        'placeholder' => __('Longitude Ex : -0.5'),
                                                        'class' => 'form-control',
                                                        'id' => 'longitude-1',
                                                    ]) !!}
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                {{ Form::label(__(''), null, ['class' => 'control-label col-sm-4']) }}
                                                <div class="col-xs-12 col-sm-8 col-md-8">
                                                    <p id="status"></p>
                                                    <a href="javascript:void(0)" id="find-me"
                                                        class="btn btn--info">Obtenir les coordonnées GPS</a>
                                                </div>
                                            </div>
                                            <div class="col-xs-12 col-sm-12">
                                                <div class="form-group row">
                                                    {{ Form::label(__('Nombre de Cacao moyen / parcelle'), null, ['class' => 'col-sm-4 control-label']) }}
                                                    {!! Form::number('nbCacaoParHectare', null, [
                                                        'placeholder' => __('Nombre de Cacao moyen par parcelle / Hectare'),
                                                        'class' => 'form-control',
                                                        'id' => 'nbCacaoParHectare-1',
                                                    ]) !!}
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <hr class="panel-wide">

                    <div class="form-group row">

                        <?php echo Form::label(__('Fichier KML ou GPX existant'), null, ['class' => 'col-sm-4 control-label']); ?>
                        <div class="col-xs-12 col-sm-8">
                            <input type="file" name="fichier_kml_gpx" class="form-control dropify-fr">
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
    <x-back route="{{ route('manager.traca.parcelle.index') }}" />
@endpush

@push('script')
    <script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_KEY') }}"></script>
    <script type="text/javascript">
        $("#localite").chained("#section");
        $("#producteur_id").chained("#localite");
    </script>

    <script type="text/javascript">
        $(document).ready(function() {
            $('#anneeRegenerers,#courDeaus,#protection,#niveauPentes,#autreCourDeaus,#autreProtections').hide();

            $('.parcelleRegenerer').change(function() {
                var parcelleRegenerer = $('.parcelleRegenerer').val();
                if (parcelleRegenerer == 'oui') {
                    $('#anneeRegenerers').show('slow');
                    $('.anneeRegenerer').show('slow');
                    $('.superficieConcerne').show('slow');
                    $('#anneeRegenerer').prop('required', true);
                    $('#superficieConcerne').prop('required', true);
                } else {
                    $('#anneeRegenerers').hide('slow');
                    $('.anneeRegenerer').val('');
                    $('.superficieConcerne').val('');
                    $('#anneeRegenerer').prop('required', false);
                    $('#superficieConcerne').prop('required', false);
                }
            });
            if ($('.parcelleRegenerer').val() == 'oui') {
                $('#anneeRegenerers').show('slow');
                $('.anneeRegenerer').show('slow');
                $('.superficieConcerne').show('slow');
                $('#anneeRegenerer').prop('required', true);
                $('#superficieConcerne').prop('required', true);
            } else {
                $('#anneeRegenerers').hide('slow');
                $('.anneeRegenerer').val('');
                $('.superficieConcerne').val('');
                $('#anneeRegenerer').prop('required', false);
                $('#superficieConcerne').prop('required', false);
            }

            $('.presenceCourDeau').change(function() {
                var presenceCourDeau = $('.presenceCourDeau').val();
                if (presenceCourDeau == 'oui') {
                    $('#courDeaus').show('slow');
                    $('.courDeau').show('slow');
                    $('#courDeau').prop('required', true);

                } else {
                    $('#courDeaus').hide('slow');
                    $('.courDeau').hide('slow');
                    $('.courDeau').val('');
                    $('#courDeau').prop('required', false);
                }
            });
            if ($('.presenceCourDeau').val() == 'oui') {
                $('#courDeaus').show('slow');
                $('.courDeau').show('slow');
                $('#courDeau').prop('required', true);

            } else {
                $('#courDeaus').hide('slow');
                $('.courDeau').hide('slow');
                $('.courDeau').val('');
                $('#courDeau').prop('required', false);
            }
            $('.courDeau').change(function() {
                var courDeau = $('.courDeau').val();
                if (courDeau == 'Autre') {
                    $('#autreCourDeaus').show('slow');
                    $('.autreCourDeau').show('slow');
                    $('#autreCourDeau').prop('required', true);

                } else {
                    $('#autreCourDeaus').hide('slow');
                    $('.autreCourDeau').hide('slow');
                    $('.autreCourDeau').val('');
                    $('#autreCourDeau').prop('required', false);
                }
            });
            if ($('.courDeau').val() == 'Autre') {
                $('#autreCourDeaus').show('slow');
                $('.autreCourDeau').show('slow');
                $('#autreCourDeau').prop('required', true);

            } else {
                $('#autreCourDeaus').hide('slow');
                $('.autreCourDeau').hide('slow');
                $('.autreCourDeau').val('');
                $('#autreCourDeau').prop('required', false);
            }
            $('.existePente').change(function() {
                var existPente = $('.existePente').val();
                if (existPente == 'oui') {
                    $('#niveauPentes').show('slow');
                    $('.niveauPente').show('slow');
                    $('#niveauPente').prop('required', true);

                } else {
                    $('#niveauPentes').hide('slow');
                    $('.niveauPente').hide('slow');
                    $('.niveauPente').val('');
                    $('#niveauPente').prop('required', false);
                }
            });
            if ($('.existePente').val() == 'oui') {
                $('#niveauPentes').show('slow');
                $('.niveauPente').show('slow');
                $('#niveauPente').prop('required', true);

            } else {
                $('#niveauPentes').hide('slow');
                $('.niveauPente').hide('slow');
                $('.niveauPente').val('');
                $('#niveauPente').prop('required', false);
            }
            $('.existeMesureProtection').change(function() {
                var existeMesureProtection = $('.existeMesureProtection').val();
                if (existeMesureProtection == 'oui') {
                    $('#protection').show('slow');
                    $('select[name="protection[]"]').prop('required', true);

                } else {
                    $('#protection').hide('slow');
                    $('select[name="protection[]"]').prop('required', false);
                }
            });
            var insectesParasitesCount = $("#insectesParasites_area tr").length;
            $(document).on('click', '#addRowinsectesParasites', function() {

                var html_table = '<tr>';
                html_table +=
                    '<td class="row"><div class="col-xs-12 col-sm-12 bg-success"><badge class="btn  btn-outline--warning h-45 btn-sm text-white">Autres arbres à ombrages ne figurant pas dans la liste précedente ' +
                    insectesParasitesCount +
                    '</badge></div><div class="col-xs-12 col-sm-4"><div class="form-group"><label class="">Nom</label><input class="form-control" id="nom-' +
                    insectesParasitesCount +
                    '" name="arbreStrate[' + insectesParasitesCount +
                    '][nom]"></div></div><div class="col-xs-12 col-sm-4"><div class="form-group"><label class="">Strate</label><select name="arbreStrate[' +
                    insectesParasitesCount +
                    '][strate]" class="form-control" id="strate-' +
                    insectesParasitesCount +
                    '" ><option value="">Selectionner une strate</option><option value="1">Strate 1</option><option value="2">Strate 2</option><option value="3">Strate 3</option></select></div></div><div class="col-xs-12 col-sm-4"><div class="form-group"><label class="">Nombre</label><input type="number" class="form-control" placeholder="Saisissez le nombre d\' arbre observé" name="arbreStrate[' +
                    insectesParasitesCount +
                    '][qte]"></div></div><div class="col-xs-12 col-sm-8"><button type="button" id="' +
                    insectesParasitesCount +
                    '" class="removeRowinsectesParasites btn btn-danger btn-sm"><i class="fa fa-minus"></i></button></div></td>';

                html_table += '</tr>';
                //---> End create table tr

                insectesParasitesCount = parseInt(insectesParasitesCount) + 1;
                $('#insectesParasites_area').append(html_table);

            });
            $(document).on('click', '.removeRowinsectesParasites', function() {
                var row_id = $(this).attr('id');
                if (row_id == $("#insectesParasites_area tr").length - 1) {
                    $(this).parents('tr').remove();
                    insectesParasitesCount = parseInt(insectesParasitesCount) - 1;
                }
            });
            if ($('.existeMesureProtection').val() == 'oui') {
                $('#protection').show('slow');
                $('select[name="protection[]"]').prop('required', true);

            } else {
                $('#protection').hide('slow');
                $('select[name="protection[]"]').prop('required', false);
            }

            $('.protections').change(function() {
                var protections = $('.protections').find(":selected").map((key, item) => {
                    return item.textContent.trim();
                }).get();
                if (protections.includes("Autre")) {
                    $('#autreProtections').show('slow');
                    $('.autreProtection').show('slow');
                    $('#autreProtection').prop('required', true);

                } else {
                    $('#autreProtections').hide('slow');
                    $('.autreProtection').hide('slow');
                    $('.autreProtection').val('');
                    $('#autreProtection').prop('required', false);
                }
            });
            if ($('.protections').find(":selected").map((key, item) => {
                    return item.textContent.trim();
                }).get().includes("Autre")) {
                $('#autreProtections').show('slow');
                $('.autreProtection').show('slow');
                $('#autreProtection').prop('required', true);

            } else {
                $('#autreProtections').hide('slow');
                $('.autreProtection').hide('slow');
                $('.autreProtection').val('');
                $('#autreProtection').prop('required', false);
            }

        });
    </script>

    <script>
        "use strict";

        (function($) {


            $('.addUserData').on('click', function() {

                let count = $("#addedField select").length;
                let length = $("#addedField").find('.single-item').length;

                let html = `
            <div class="row single-item gy-2">
                <div class="col-md-8">
                    <select class="form-control selected_type" name="items[${length}][arbre]" required id='arbre-${length}')>
                        <option disabled selected value="">@lang('Arbres d\'ombrages')</option>
                        @foreach ($arbres as $arbre)
                            <option value="{{ $arbre->id }}"  >{{ __($arbre->nom) }} </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-3">
                    <div class="input-group mb-3">
                        <input type="number" class="form-control quantity" placeholder="@lang('Nombre')"  name="items[${length}][nombre]"  required>
                        <span class="input-group-text unit"><i class="las la-balance-scale"></i></span>
                    </div>
                </div>
                <div class="col-md-1">
                    <button class="btn btn--danger w-100 removeBtn w-100 h-45" type="button">
                        <i class="fa fa-times"></i>
                    </button>
                </div>
                <br><hr class="panel-wide">
            </div>`;
                $('#addedField').append(html)
            });

            $('#addedField').on('change', '.selected_type', function(e) {
                let unit = $(this).find('option:selected').data('unit');
                let parent = $(this).closest('.single-item');
                $(parent).find('.quantity').attr('disabled', false);
                $(parent).find('.unit').html(`${unit || '<i class="las la-balance-scale"></i>'}`);
            });

            $('#addedField').on('click', '.removeBtn', function(e) {
                $(this).closest('.single-item').remove();
            });

        })(jQuery);

        function geoFindMe() {
            const status = document.querySelector("#status");

            function success(position) {
                const latitude = position.coords.latitude;
                const longitude = position.coords.longitude;

                $('input[name=longitude]').val(longitude);
                $('input[name=latitude]').val(latitude);
                $("input[name=longitude], input[name=latitude]").attr({
                    "readonly": 'readonly'
                })
            }

            function error() {
                status.textContent = "Unable to retrieve your location";
            }
            if (!navigator.geolocation) {
                status.textContent = "Geolocation is not supported by your browser";
            } else {
                // status.textContent = "Locating…";
                navigator.geolocation.getCurrentPosition(success, error);
            }
        }
        document.querySelector("#find-me").addEventListener("click", geoFindMe);
    </script>
@endpush
