@extends('manager.layouts.app')
@section('panel')
    <x-setting-sidebar :activeMenu="$activeSettingMenu" />
    <x-setting-card>
        <x-slot name="header">
            <div class="s-b-n-header" id="tabs">
                <h2 class="mb-0 p-20 f-21 font-weight-normal text-capitalize border-bottom-grey">
                    @lang($pageTitle)</h2>
            </div>
        </x-slot>
        <div class="col-lg-12 col-md-12 ntfcn-tab-content-left w-100 p-4 ">
            <div class="card">
                <div class="card-body">
                    @method('POST')
                    <div class="form-group row">
                        <label class="col-xs-12 col-sm-4">@lang('Select Cooperative')</label>
                        <div class="col-xs-12 col-sm-8">
                            <select class="form-control" disabled>
                                <option value="">{{ __($manager->cooperative->name) }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-xs-12 col-sm-4">@lang('Select section')</label>
                        <div class="col-xs-12 col-sm-8">
                            <select class="form-control" name="section_id" required>
                                @foreach ($sections as $section)
                                    <option value="{{ $section->id }}" @selected(old('section_id'))>
                                        {{ __($section->libelle) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <?php echo Form::label(__('Nom de la localite'), null, ['class' => 'control-label col-xs-12 col-sm-4']); ?>
                        <div class="col-xs-12 col-sm-8">
                            <?php echo Form::text('nom', null, ['placeholder' => __('Nom de la localite'), 'class' => 'form-control', 'required']); ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <?php echo Form::label(__('Type de localite'), null, ['class' => 'control-label col-xs-12 col-sm-4']); ?>
                        <div class="col-xs-12 col-sm-8">
                            <?php echo Form::select('type_localites', ['Ville' => 'Ville', 'Campement' => 'Campement', 'Village' => 'Village'], null, ['placeholder' => __('Selectionner une option'), 'class' => 'form-control', 'required']); ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <?php echo Form::label(__('Sous préfecture'), null, ['class' => 'control-label col-xs-12 col-sm-4']); ?>
                        <div class="col-xs-12 col-sm-8">
                            <?php echo Form::text('sousprefecture', null, ['placeholder' => __('sous prefecture'), 'class' => 'form-control', 'required']); ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <?php echo Form::label(__('Estimation de la population'), null, ['class' => 'control-label col-xs-12 col-sm-4']); ?>
                        <div class="col-xs-12 col-sm-8">
                            <?php echo Form::number('population', null, ['placeholder' => __('nombre'), 'class' => 'form-control', 'min' => '1']); ?>
                        </div>
                    </div>
                    <hr class="panel-wide">

                    <div class="form-group row">
                        <?php echo Form::label(__('Existe-t-il un centre de santé dans la localité ?'), null, ['class' => 'control-label col-xs-12 col-sm-4']); ?>
                        <div class="col-xs-12 col-sm-8">
                            <?php echo Form::select('centresante', ['non' => 'non', 'oui' => 'oui'], null, ['class' => 'form-control centresante', 'required']); ?>
                        </div>
                    </div>
                    <div class="form-group row col-lg-12" id="centreSante">
                        <table class="table table-striped table-bordered">
                            <tbody id="centreSantes">
                                <tr>
                                    <td class="row">
                                        <div class="col-xs-12 col-sm-12 bg-success">
                                            <badge class="btn  btn-outline--warning h-45 btn-sm text-white">@lang('Informations sur le centre de centé 1')
                                            </badge>
                                            </badge>
                                        </div>
                                        <div class="col-xs-12 col-sm-12">
                                            <div class="form-group row">
                                                <input type="text" name="nomcentresantes[]"
                                                    placeholder="Nom du centre de Santé" id="nomcentresantes-1"
                                                    class="form-control" value="{{ old('nomcentresantes') }}">
                                            </div>
                                        </div>
                                        <div class="col-xs-12 col-sm-12">
                                            <div class="form-group row">
                                                <input type="text" name="latitude[]" placeholder="Latitude"
                                                    id="latitude" class="form-control" value="{{ old('latitude') }}">
                                            </div>
                                        </div>
                                        <div class="col-xs-12 col-sm-12">
                                            <div class="form-group row">
                                                <input type="text" name="longitude[]" placeholder="longitude"
                                                    id="longitude" class="form-control" value="{{ old('longitude') }}">
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot style="background: #e3e3e3;">
                                <tr>
                                    <td colspan="3">
                                        <button id="addRowMalSante" type="button" class="btn btn-success btn-sm"><i
                                                class="fa fa-plus"></i></button>
                                    </td>
                                <tr>
                            </tfoot>
                        </table>
                    </div>
                </div>


                {{-- Si non on affiche le champs si dessous --}}
                <div id="nonCentreSante">
                    <div class="form-group row">
                        <?php echo Form::label(__('A combien de km du village se situe le centre de santé le plus proche ?'), null, ['class' => 'control-label col-xs-12 col-sm-4']); ?>
                        <div class="col-xs-12 col-sm-8">
                            <?php echo Form::number('kmCentresante', null, ['placeholder' => __('nombre'), 'class' => 'form-control kmCentresante', 'min' => '0']); ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <?php echo Form::label(__('Nom de ce centre de santé'), null, ['class' => 'control-label col-xs-12 col-sm-4']); ?>
                        <div class="col-xs-12 col-sm-8">
                            <?php echo Form::text('nomCentresante', null, ['placeholder' => __('Nom du centre de Santé'), 'class' => 'form-control nomCentresante']); ?>
                        </div>
                    </div>
                </div>

                {{-- fin de champs si dessous --}}
                <hr class="panel-wide">

                <div class="form-group row">
                    <?php echo Form::label(__('Existe-t-il une ou des école(s) primaire(s) publique(s) dans la localité ?'), null, ['class' => 'control-label col-xs-12 col-sm-4']); ?>
                    <div class="col-xs-12 col-sm-8">
                        <?php echo Form::select('ecole', ['non' => 'non', 'oui' => 'oui'], null, ['class' => 'form-control ecole', 'required']); ?>
                    </div>
                </div>
            </div>

            {{-- Si non afficher le champs si dessous --}}
            <div id="nonEcolePrimaire">
                <div class="form-group row">
                    <?php echo Form::label(__("A combien de km se trouve l'école la plus proche ?"), null, ['class' => 'control-label col-xs-12 col-sm-4']); ?>
                    <div class="col-xs-12 col-sm-8">
                        <?php echo Form::number('kmEcoleproche', null, ['placeholder' => __('nombre'), 'class' => 'form-control kmEcoleproche', 'min' => '0']); ?>
                    </div>
                </div>

                <div class="form-group row">
                    <?php echo Form::label(__('Nom Ecole primaire'), null, ['class' => 'control-label col-xs-12 col-sm-4']); ?>
                    <div class="col-xs-12 col-sm-8">
                        <?php echo Form::text('nomEcoleproche', null, ['placeholder' => 'Nom de l\'école primaire', 'class' => 'form-control nomEcoleproche', 'id' => 'nomEcoleproche']); ?>
                    </div>
                </div>
            </div>
            {{-- fin de champs si dessous --}}

            <div id="nombrecole">
                <div class="form-group row">
                    <?php echo Form::label(__('Citez les 3 Principales Ecoles'), null, ['class' => 'control-label col-xs-12 col-sm-4']); ?>
                </div>

                <div class="form-group row col-lg-12">
                    <table class="table table-striped table-bordered">
                        <tbody id="maladies">
                            <tr>
                                <td class="row">
                                    <div class="col-xs-12 col-sm-12 bg-success">
                                        <badge class="btn  btn-outline--warning h-45 btn-sm text-white">@lang('Informations sur l\'école primaire 1')
                                        </badge>
                                        </badge>
                                    </div>
                                    <div class="col-xs-12 col-sm-12">
                                        <div class="form-group row">
                                            <input type="text" name="nomecolesprimaires[]"
                                                placeholder="Nom de l'école primaire" id="nomecolesprimaires-1"
                                                class="form-control" value="{{ old('nomecolesprimaires') }}">
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-12">
                                        <div class="form-group row">
                                            <input type="text" name="latitude[]" placeholder="Latitude" id="latitude-1"
                                                class="form-control" value="{{ old('latitude') }}">
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-12">
                                        <div class="form-group row">
                                            <input type="text" name="longitude[]" placeholder="longitude"
                                                id="longitude-1" class="form-control" value="{{ old('longitude') }}">
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot style="background: #e3e3e3;">
                            <tr>
                                <td colspan="3">
                                    <button id="addRowMal" type="button" class="btn btn-success btn-sm"><i
                                            class="fa fa-plus"></i></button>
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

                    <select class="form-control select2-multi-select eauxPotables" name="eauPotables[]" multiple
                        id="eauxPotables" required>
                        <option value="">@lang('Selectionner les options')</option>
                        <option value="Pompe Hydraulique Villageoise"
                            {{ in_array('Pompe Hydraulique Villageoise', old('eauPotables', [])) ? 'selected' : '' }}>
                            Pompe Hydraulique Villageoise
                        </option>
                        <option value="SODECI" {{ in_array('SODECI', old('eauPotables', [])) ? 'selected' : '' }}>
                            SODECI
                        </option>
                        <option value="Marigot" {{ in_array('Marigot', old('eauPotables', [])) ? 'selected' : '' }}>
                            Marigot
                        </option>
                        <option value="Puits Individuel"
                            {{ in_array('Puits Individuel', old('eauPotables', [])) ? 'selected' : '' }}>
                            Puits Individuel
                        </option>
                    </select>
                </div>
            </div>

            <div class="form-group row" id="etatpompehydrau">
                <?php echo Form::label(__('Est-il en bon état ?'), null, ['class' => 'control-label col-xs-12 col-sm-4']); ?>
                <div class="col-xs-12 col-sm-8">
                    <?php echo Form::select('etatpompehydrau', [null => '', 'oui' => 'oui', 'non' => 'non'], null, ['class' => 'form-control etatpompehydrau']); ?>
                </div>
            </div>

            <div class="form-group row">
                <?php echo Form::label(__("Existe-t-il l'éclairage public dans la localité ?"), null, ['class' => 'control-label col-xs-12 col-sm-4']); ?>
                <div class="col-xs-12 col-sm-8">
                    <?php echo Form::select('electricite', ['oui' => 'oui', 'non' => 'non'], null, ['class' => 'form-control', 'required']); ?>
                </div>
            </div>
            <hr class="panel-wide">
            <div class="form-group row">
                <?php echo Form::label(__('Existe-t-il un marché dans la localité ?'), null, ['class' => 'control-label col-xs-12 col-sm-4']); ?>
                <div class="col-xs-12 col-sm-8">
                    <?php echo Form::select('marche', ['non' => 'non', 'oui' => 'oui'], null, ['class' => 'form-control marche', 'required']); ?>
                </div>
            </div>

            <div class="form-group row" id="jourmarches">
                <?php echo Form::label(__('Quel est le jour du marché ?'), null, ['class' => 'control-label col-xs-12 col-sm-4']); ?>
                <div class="col-xs-12 col-sm-8">

                    <select class="form-control select2-multi-select sources_energies" name="jourmarche[]" multiple
                        id="jourmarche" required>
                        <option value="">@lang('Selectionner les options')</option>
                        <option value="Lundi" {{ in_array('Lundi', old('jourmarche', [])) ? 'selected' : '' }}>
                            Lundi
                        </option>
                        <option value="Mardi" {{ in_array('Mardi', old('jourmarche', [])) ? 'selected' : '' }}>
                            Mardi
                        </option>
                        <option value="Mercredi" {{ in_array('Mercredi', old('jourmarche', [])) ? 'selected' : '' }}>
                            Mercredi
                        </option>
                        <option value="Jeudi" {{ in_array('Jeudi', old('jourmarche', [])) ? 'selected' : '' }}>
                            Jeudi
                        </option>
                        <option value="vendredi" {{ in_array('vendredi', old('jourmarche', [])) ? 'selected' : '' }}>
                            vendredi
                        </option>
                        <option value="Samedi" {{ in_array('Samedi', old('jourmarche', [])) ? 'selected' : '' }}>
                            Samedi
                        </option>
                        <option value="Dimanche" {{ in_array('Dimanche', old('jourmarche', [])) ? 'selected' : '' }}>
                            Dimanche
                    </select>
                </div>
            </div>
            <div class="form-group row" id="kmmarcheproches">
                <?php echo Form::label(__('A combien de km se trouve le marché le plus proche ?'), null, ['class' => 'control-label col-xs-12 col-sm-4']); ?>
                <div class="col-xs-12 col-sm-8">
                    <?php echo Form::number('kmmarcheproche', null, ['placeholder' => __('Kilomètre du marché le plus proche'), 'class' => 'form-control kmmarcheproche', 'min' => '0']); ?>
                </div>
            </div>
            <div class="form-group row">
                <?php echo Form::label(__('Existe-t-il un endroit public pour le déversement des déchets dans la localité ?'), null, ['class' => 'control-label col-xs-12 col-sm-4']); ?>
                <div class="col-xs-12 col-sm-8">
                    <?php echo Form::select('deversementDechets', ['oui' => 'oui', 'non' => 'non'], null, ['class' => 'form-control', 'required']); ?>
                </div>
            </div>
            <hr class="panel-wide">
            <div class="form-group row">
                <?php echo Form::label(__("Nombre comite de main d'œuvre qu'il y a dans la localité"), null, ['class' => 'control-label col-xs-12 col-sm-4']); ?>
                <div class="col-xs-12 col-sm-8">
                    <?php echo Form::number('comiteMainOeuvre', null, ['placeholder' => __('nombre'), 'class' => 'form-control', 'min' => '0']); ?>
                </div>
            </div>

            <div class="form-group row">
                <?php echo Form::label(__("Nombre d'association de femmes qu'il y a dans la localité"), null, ['class' => 'control-label col-xs-12 col-sm-4']); ?>
                <div class="col-xs-12 col-sm-8">
                    <?php echo Form::number('associationFemmes', null, ['placeholder' => __('nombre'), 'class' => 'form-control', 'min' => '0']); ?>
                </div>
            </div>
            <div class="form-group row">
                <?php echo Form::label(__('Nombre d’association de jeunes qu’il y a dans la localité'), null, ['class' => 'control-label col-xs-12 col-sm-4']); ?>
                <div class="col-xs-12 col-sm-8">
                    <?php echo Form::number('associationJeunes', null, ['placeholder' => __('nombre'), 'class' => 'form-control', 'min' => '0']); ?>
                </div>
            </div>

            <div class="form-group row">
                <?php echo Form::label(__("Nombre d'association de jeunes qu'il y a dans la localité"), null, ['class' => 'control-label col-xs-12 col-sm-4']); ?>
                <div class="col-xs-12 col-sm-8">
                    <?php echo Form::number('associationJeunes', null, ['placeholder' => __('nombre'), 'class' => 'form-control', 'min' => '0']); ?>
                </div>
            </div>
            <hr class="panel-wide">

            <div class="form-group row">
                <?php echo Form::label(__('Prise des coordonnées gps de la localité'), null, ['class' => 'control-label col-xs-12 col-sm-12']); ?>
            </div>
            <div class="form-group row">
                <?php echo Form::label('Longitude', null, ['class' => 'control-label col-xs-12 col-sm-4']); ?>
                <div class="col-xs-12 col-sm-8">
                    <?php echo Form::text('localongitude', null, ['placeholder' => __('longitude Ex : -65'), 'class' => 'form-control']); ?>
                </div>
            </div>
            <div class="form-group row">
                <?php echo Form::label('Latitude', null, ['class' => 'control-label col-xs-12 col-sm-4']); ?>
                <div class="col-xs-12 col-sm-8">
                    <?php echo Form::text('localatitude', null, ['placeholder' => __('latitude Ex : 65'), 'class' => 'form-control']); ?>
                </div>
            </div>

            <div class="form-group">
                <button type="submit" id="save-form" class="btn btn--primary w-100 h-45">
                    @lang('app.save')</button>
            </div>

        </div>
        </div>
        </div>
    </x-setting-card>
@endsection

@push('breadcrumb-plugins')
    <x-back route="{{ route('manager.settings.localite-settings.index') }}" />
@endpush

@push('script')
    <script type="text/javascript">
        $(document).ready(function() {

            var maladiesCount = $("#maladies tr").length + 1;
            $(document).on('click', '#addRowMal', function() {

                //---> Start create table tr
                var html_table = '<tr>';
                html_table +=
                    '<td class="row"><div class="col-xs-12 col-sm-12 bg-success"><badge class="btn btn-warning btn-sm">Nom Ecole Primaire ' +
                    maladiesCount +
                    '</badge></div><div class="col-xs-12 col-sm-12"><div class="form-group"><input placeholder="Nom école primaire" class="form-control" id="nomecolesprimaires-' +
                    maladiesCount +
                    '" name="nomecolesprimaires[]" type="text"></div></div>' +
                    '<div class="col-xs-12 col-sm-12"><div class="form-group row"><input type="text" name="latitude[]" placeholder="Latitude Ex : 14 " id="latitude-' +
                    maladiesCount +
                    '" class="form-control" value="{{ old('latitude') }}"></div></div>' +
                    '<div class="col-xs-12 col-sm-12"><div class="form-group row"><input type="text" name="longitude[]" placeholder="Longitude Ex : -14" id="longitude-' +
                    maladiesCount +
                    '" class="form-control" value="{{ old('longitude') }}"></div></div>' +
                    '<div class="col-xs-12 col-sm-8"><button type="button" id="' +
                    maladiesCount +
                    '" class="removeRowMal btn btn-danger btn-sm"><i class="fa fa-minus"></i></button></div></td>';

                html_table += '</tr>';


                maladiesCount = parseInt(maladiesCount) + 1;
                $('#maladies').append(html_table);

            });

            $(document).on('click', '.removeRowMal', function() {

                var row_id = $(this).attr('id');

                // delete only last row id
                if (row_id == $("#maladies tr").length) {

                    $(this).parents('tr').remove();

                    maladiesCount = parseInt(maladiesCount) - 1;

                }
            });

        });






        $(document).ready(function() {

            var maladiesCount = $("#centreSantes tr").length + 1;
            $(document).on('click', '#addRowMalSante', function() {

                //---> Start create table tr
                var html_table = '<tr>';
                html_table +=
                    '<td class="row"><div class="col-xs-12 col-sm-12 bg-success"><badge class="btn btn-warning btn-sm">Nom Centre de Santé ' +
                    maladiesCount +
                    '</badge></div><div class="col-xs-12 col-sm-12"><div class="form-group"><input placeholder="Nom du centre de santé" class="form-control" id="nomcentresantes-' +
                    maladiesCount +
                    '" name="nomcentresantes[]" type="text"></div></div>' +
                    '<div class="col-xs-12 col-sm-12"><div class="form-group row"><input type="text" name="latitude[]" placeholder="Latitude Ex : 14 " id="latitude-' +
                    maladiesCount +
                    '" class="form-control" value="{{ old('latitude') }}"></div></div>' +
                    '<div class="col-xs-12 col-sm-12"><div class="form-group row"><input type="text" name="longitude[]" placeholder="Longitude Ex : -14" id="longitude-' +
                    maladiesCount +
                    '" class="form-control" value="{{ old('longitude') }}"></div></div>' +
                    '<div class="col-xs-12 col-sm-8"><button type="button" id="' +
                    maladiesCount +
                    '" class="removeRowSante btn btn-danger btn-sm"><i class="fa fa-minus"></i></button></div></td>';

                html_table += '</tr>';


                maladiesCount = parseInt(maladiesCount) + 1;
                $('#centreSantes').append(html_table);

            });

            $(document).on('click', '.removeRowSante', function() {

                var row_id = $(this).attr('id');

                // delete only last row id
                if (row_id == $("#centreSantes tr").length) {

                    $(this).parents('tr').remove();

                    maladiesCount = parseInt(maladiesCount) - 1;

                }
            });

        });


        $('#nombrecole, #etatpompehydrau,#centreSante,#jourmarches').hide();

        $('.marche').change(function() {
            var marche = $('.marche').val();
            if (marche == 'non') {
                $('#kmmarcheproches').show('slow');
                $('#jourmarches').hide('slow');

            } else {
                $('#kmmarcheproches').hide('slow');
                $('.kmmarcheproche').val('');
                $('#jourmarches').show('slow');

            }
        });
        $('.centresante').change(function() {

            var centresante = $('.centresante').val();
            if (centresante == 'non') {
                $('#nonCentreSante').show('slow');
                $('#centreSante').hide('slow');
            } else {
                $('#nonCentreSante').hide('slow');
                $('.kmCentresante').val('');
                $('.nomCentresante').val('');
                $('#centreSante').show('slow');
            }

        });

        $('.ecole').change(function() {
            var ecole = $('.ecole').val();
            if (ecole == 'oui') {
                $('#nombrecole').show('slow');
                $('#nonEcolePrimaire').hide('slow');
                $('.kmEcoleproche').val('');
                $('.nomEcoleproche').val('');
                $('#kmEcoleproche').prop('required', false);
                $('#nomEcoleproche').prop('required', false);

            } else {
                $('#nombrecole').hide('slow');
                $('#nonEcolePrimaire').show('slow');
                $('.kmEcoleproche').val('');
                $('.nomEcoleproche').val('');
                $('#kmEcoleproche').prop('required', true);
                $('#nomEcoleproche').prop('required', true);
            }
        });

        $('.eauxPotables').change(function() {
            var eauxPotables = $('.eauxPotables').find(":selected").map((key, item) => {
                return item.textContent.trim();
            }).get();
            if (eauxPotables.includes("Pompe Hydraulique Villageoise")) {

                $('#etatpompehydrau').show('slow');
                $('.etatpompehydrau').show('slow');

            } else {

                $('#etatpompehydrau').hide('slow');
                $('.etatpompehydrau').show('slow');

            }
        });
        $('#save-form').click(function() {
            var url = "{{ route('manager.settings.localite-settings.store') }}";

            $.easyAjax({
                url: url,
                container: '#editSettings',
                type: "POST",
                disableButton: true,
                blockUI: true,
                redirect: true,
                buttonSelector: "#save-form",
                data: $('#editSettings').serialize(),
                success: function(response) {
                    if (response.status == 'success') {
                        window.location.href = response.redirectUrl;
                    }
                }
            })
        });
    </script>
@endpush
