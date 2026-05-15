@extends('manager.layouts.app')
@section('panel')
    <?php use Carbon\Carbon; ?>
    <div class="row mb-none-30">
        <div class="col-lg-12 mb-30">
            <div class="card">
                <div class="card-body">
                    {!! Form::model($formation, [
                        'method' => 'POST',
                        'route' => ['manager.suivi.formation.store', $formation->id],
                        'class' => 'form-horizontal',
                        'id' => 'flocal',
                        'enctype' => 'multipart/form-data',
                    ]) !!}
                    <input type="hidden" name="id" value="{{ $formation->id }}">

                    <div class="form-group row">
                        <label class="col-sm-4 control-label">@lang('Selectionner une localite')</label>
                        <div class="col-xs-12 col-sm-8">
                            <select class="form-control" name="localite" id="localite" required>
                                <option value="">@lang('Selectionner une option')</option>
                                @foreach ($localites as $localite)
                                    <option value="{{ $localite->id }}" @selected($localite->id == $formation->localite_id)>
                                        {{ $localite->nom }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 control-label">@lang('Selectionner un producteur')</label>
                        <div class="col-xs-12 col-sm-8">
                            <select class="form-control select2-multi-select" name="producteur[]" id="producteur" multiple
                                required>
                                <option value="">@lang('Selectionner une option')</option>
                                @foreach ($producteurs as $producteur)
                                    <option value="{{ $producteur->id }}" data-chained="{{ $producteur->localite->id }}"
                                        @selected(in_array($producteur->id, $producteursSelected))>
                                        {{ stripslashes($producteur->nom) }} {{ stripslashes($producteur->prenoms) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <?php echo Form::label(__('Type de la formation'), null, ['class' => 'col-sm-4 control-label']); ?>
                        <div class="col-xs-12 col-sm-8">
                            <?php echo Form::select('formation_type', ['Cible' => 'Ciblé', 'Groupe' => 'Groupé'], null, ['placeholder' => __('Selectionner une option'), 'class' => 'form-control', 'id' => 'formation_type', 'required' => 'required']); ?>
                        </div>
                    </div>

                    <hr class="panel-wide">
                    <div class="form-group row">
                        <?php echo Form::label(__('Lieu de la formation'), null, ['class' => 'col-sm-4 control-label']); ?>
                        <div class="col-xs-12 col-sm-8">
                            <?php echo Form::select('lieu_formation', ['Dans le ménage' => 'Dans le ménage', 'Place Publique' => 'Place Publique', 'Champs Ecole' => 'Champs Ecole'], $formation->lieu_formation, ['placeholder' => __('Selectionner une option'), 'class' => 'form-control', 'id' => 'lieu_formations', 'required' => 'required']); ?>
                        </div>
                    </div>
                    <div class="form-group row">
                        <?php echo Form::label(__('Module de la formation'), null, ['class' => 'col-sm-4 control-label']); ?>
                        <div class="col-xs-12 col-sm-8">
                            <select class="form-control select2-multi-select" name="type_formation[]" id="typeformation"
                                multiple required>
                                <option value="">@lang('Selectionner une option')</option>
                                @foreach ($typeformations as $type)
                                    <option value="{{ $type->id }}" @selected(in_array($type->id, $modules))>
                                        {{ $type->nom }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <?php echo Form::label(__('Thème de la formation'), null, ['class' => 'col-sm-4 control-label']); ?>
                        <div class="col-xs-12 col-sm-8">
                            <select class="form-control select2-multi-select" name="theme[]" id="theme" multiple
                                required>
                                <option value="">@lang('Selectionner une option')</option>
                                @foreach ($themes as $theme)
                                    <option value="{{ $theme->id }}"
                                        data-chained="{{ $theme->type_formation_id ?? '' }}">
                                        {{ $theme->nom }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <?php echo Form::label(__('Sous-thème de la formation'), null, ['class' => 'col-sm-4 control-label']); ?>
                        <div class="col-xs-12 col-sm-8">
                            <select class="form-control select2-multi-select" name="sous_theme[]" id="sous_theme" multiple
                                required>
                                <option value="">@lang('Selectionner une option')</option>
                                @foreach ($sousthemes as $soustheme)
                                    <option value="{{ $soustheme->id }}"
                                        data-chained="{{ $soustheme->theme_formation_id ?? '' }}">
                                        {{ $soustheme->nom }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <hr class="panel-wide">

                    <div class="form-group row">
                        <?php echo Form::label(__('Cette formation a t-elle était dispensée par un formateur externe ?'), null, ['class' => 'col-sm-4 control-label']); ?>
                        <div class="col-xs-12 col-sm-8">
                            <?php echo Form::select('formateur_externe', ['' => 'Selectionner une option', 'non' => __('non'), 'oui' => __('oui')], $formation->formateur_externe, ['class' => 'form-control formateur_externe']); ?>
                        </div>
                    </div>

                    <div class="form-group row" id="formateurInterne">
                        <?php echo Form::label(__('Staff ayant dispensé la formation'), null, ['class' => 'col-sm-4 control-label']); ?>
                        <div class="col-xs-12 col-sm-8">
                            <select class="form-control" name="staff" id="staff" required>
                                <option value="">@lang('Selectionner une option')</option>
                                @foreach ($staffs as $staff)
                                    <option value="{{ $staff->id }}" {{ $staff->id == $formation->user_id ? 'selected' : '' }}>
                                        {{ $staff->lastname }} {{ $staff->firstname }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div id="formateurExterne">
                        <div class="form-group row">
                            <?php echo Form::label(__('Entreprise du formateur'), null, ['class' => 'col-sm-4 control-label']); ?>
                            <div class="col-xs-8 col-sm-8 input-group mb-3">
                                <select class="form-control select2-multi-select" name="entreprise_formateur[]"
                                    id="entreprise_formateur" multiple>
                                    <option value="">@lang('Selectionner une option')</option>
                                    @foreach ($entreprises as $entreprise)
                                        <option value="{{ $entreprise->id }}" @selected(in_array($entreprise->id, $entreprisess))>
                                        {{ $entreprise->nom_entreprise }}</option>
                                    @endforeach
                                </select>
                                {{-- <button type="button" class="btn btn-outline-secondary border-grey add-entreprise"
                                    data-toggle="tooltip" data-original-title="Ajouter un formateur"><i
                                        class="las la-plus"></i>
                                </button> --}}
                            </div>
                        </div>
                        <div class="form-group row">
                            <?php echo Form::label(__('Formateur'), null, ['class' => 'col-sm-4 control-label']); ?>
                            <div class="col-xs-12 col-sm-8 input-group mb-3">
                                <select class="form-control select2-multi-select" name="formateur[]" id="formateur"
                                    multiple>
                                    <option value="">@lang('Selectionner une option')</option>
                                    @foreach ($formateurs as $formateur)
                                        <option value="{{ $formateur->id }}"
                                            data-chained="{{ $formateur->entreprise_id }}" @selected(old('formateur'))>
                                            {{ $formateur->nom_formateur }} {{ $formateur->prenom_formateur }}</option>
                                    @endforeach
                                </select>
                                {{-- <button type="button" class="btn btn-outline-secondary border-grey add-formateur"
                                    data-toggle="tooltip" data-original-title="Ajouter un formateur"><i
                                        class="las la-plus"></i></button> --}}
                            </div>
                        </div>
                    </div>
                    <hr class="panel-wide">
                    <div class="form-group row">
                        <?php echo Form::label(__('Date de Début & Fin de la formation'), null, ['class' => 'col-sm-4 control-label required']); ?>
                        <div class="col-xs-12 col-sm-8">
                            <?php
                            $datedebut = Carbon::parse($formation->date_debut_formation)->format('m/d/Y');
                            $datefin = Carbon::parse($formation->date_fin_formation)->format('m/d/Y');
                            
                            ?>
                            <?php echo Form::text('multi_date', $datedebut . ' - ' . $datefin, ['class' => 'form-control', 'id' => 'multi_date', 'required' => 'required']); ?>
                        </div>
                    </div>
                    <div class="form-group row">
                        <?php echo Form::label(__('Durée de la formation'), null, ['class' => 'col-sm-4 control-label required']); ?>
                        <div class="col-xs-12 col-sm-8 bootstrap-timepicker timepicker">
                            <?php echo Form::text('duree_formation', $formation->duree_formation, ['class' => 'form-control', 'required' => 'required', 'placeholder' => 'Ex : 04:10']); ?>

                        </div>
                    </div>
                    <div class="form-group row">
                        <?php echo Form::label(__('Observation de la formation'), null, ['class' => 'col-sm-4 control-label required']); ?>
                        <div class="col-xs-12 col-sm-8">
                            <?php echo Form::textarea('observation_formation', null, ['class' => 'form-control duree_formation', 'rows' => 4]); ?>
                        </div>
                    </div>
                    <div class="form-group row">
                        <?php echo Form::label(__('Photo de la formation'), null, ['class' => 'col-sm-4 control-label']); ?>
                        <div class="col-xs-12 col-sm-8">
                            <input type="file" name="photo_formation" class="form-control dropify-fr"
                                data-default-file="{{ asset('core/storage/app/' . $formation->photo_formation) }}">
                        </div>
                    </div>
                    <div class="form-group row">
                        <?php echo Form::label(__('Photo de la fiche de présence'), null, ['class' => 'col-sm-4 control-label']); ?>
                        <div class="col-xs-12 col-sm-8">
                            <input type="file" name="photo_docListePresence" class="form-control dropify-fr"
                                data-default-file="{{ asset('core/storage/app/' . $formation->photo_docListePresence) }}">
                        </div>
                    </div>
                    <div class="form-group row">
                        <?php echo Form::label(__('Document liste de présence'), null, ['class' => 'col-sm-4 control-label']); ?>
                        <div class="col-xs-12 col-sm-8">
                            <input type="file" name="docListePresence" class="form-control dropify-fr"
                                data-default-file="{{ asset('core/storage/app/' . $formation->docListePresence) }}"
                                data-allowed-file-extensions="pdf docx doc xls xlsx">
                        </div>
                    </div>

                    <div class="form-group row">
                        <?php echo Form::label(__('Rapport de la formation'), null, ['class' => 'col-sm-4 control-label']); ?>
                        <div class="col-xs-12 col-sm-8">
                            <input type="file" name="rapport_formation" class="form-control dropify-fr"
                                data-default-file="{{ asset('core/storage/app/' . $formation->rapport_formation) }}"
                                data-allowed-file-extensions="pdf docx doc xls xlsx">
                        </div>
                    </div>

                    <hr class="panel-wide">
                    <input type="hidden" name="multiStartDate" id="multiStartDate"
                        value="{{ Carbon::parse($formation->date_debut_formation)->format('Y-m-d') }}">
                    <input type="hidden" name="multiEndDate" id="multiEndDate"
                        value="{{ Carbon::parse($formation->date_fin_formation)->format('Y-m-d') }}">

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
    <x-back route="{{ route('manager.suivi.formation.index') }}" />
@endpush

@push('script')
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/daterangepicker.css') }}">
    <script src="{{ asset('assets/vendor/jquery/daterangepicker.min.js') }}"></script>
    <script type="text/javascript">
        $('#formateurInterne,#formateurExterne').hide();
        $('.formateur_externe').change(function() {
            if ($(this).val() == 'oui') {
                $('#formateurInterne').hide('slow');
                $('#formateurExterne').show('slow');
                $('#staff').prop('selectedIndex', 0);
                $('#staff').prop('required', false);
                $('#entreprise_formateur').prop('required', true);
            } else {
                $('#formateurInterne').show('slow');
                $('#formateurExterne').hide('slow');
                $('#entreprise_formateur').val('').trigger('change');
                $('#formateur').val('').trigger('change');
                $('#staff').prop('required', true);
                $('#entreprise_formateur').prop('required', false);
            }
        });
        if ($('.formateur_externe').val() == 'oui') {
            $('#formateurInterne').hide();
            $('#formateurExterne').show();
            $('#staff').prop('selectedIndex', 0);
            $('#staff').prop('required', false);
            $('#entreprise_formateur').prop('required', true);
        } else {
            $('#formateurInterne').show();
            $('#formateurExterne').hide();
            $('#entreprise_formateur').val('').trigger('change');
            $('#formateur').val('').trigger('change');
            $('#staff').prop('required', true);
            $('#entreprise_formateur').prop('required', false);
        }


        $('#duree_formation').timepicker({
            showMeridian: (false)
        });
        $('#multi_date').daterangepicker({
            linkedCalendars: false,
            multidate: true,
            todayHighlight: true,
            format: 'yyyy-mm-d'
        });
        $('#multi_date').change(function() {
            var dates = $(this).val();

            var startDate = moment(new Date(dates.split(' - ')[0]));
            var endDate = moment(new Date(dates.split(' - ')[1]));
            var totalDays = endDate.diff(startDate, 'days') + 1;

            startDate = startDate.format('YYYY-MM-DD');

            endDate = endDate.format('YYYY-MM-DD');

            var multiDate = [];
            multiDate = [startDate, endDate];
            $('#multi_date').val(multiDate);

            $('#multiStartDate').val(startDate);
            $('#multiEndDate').val(endDate);
            $('.date-range-days').html(totalDays + ' Jours sélectionnés');
        });

        $(document).ready(function() {
            var themesSelected = "{{ implode(',', $themesSelected) }}";
            var sousThemesSelected = "{{ implode(',', $sousThemesSelected) }}";
            //idée de ce bloque de code c'est de remplire l'objet optionParTheme avec les themes provenant de la base de données
            var optionParTheme = new Object();
            $("#theme option").each(function() {
                var curreentArray = optionParTheme[($(this).data('chained'))] ? optionParTheme[($(this)
                    .data('chained'))] : [];
                curreentArray[$(this).val()] = $(this).text().trim();
                Object.assign(optionParTheme, {
                    [$(this).data('chained')]: curreentArray
                });
                if (themesSelected.split(',').includes($(this).val()) && themesSelected != "") {
                    $(this).val($(this).data('chained') + "-" + $(this).val());
                    $(this).attr('selected', 'selected');
                } else $(this).remove();
            });
            console.log(optionParTheme);

            var optionParSousTheme = new Object();
            $("#sous_theme option").each(function() {
                var curreentArray = optionParSousTheme[($(this).data('chained'))] ? optionParSousTheme[($(
                        this)
                    .data('chained'))] : [];
                curreentArray[$(this).val()] = $(this).text().trim();
                Object.assign(optionParSousTheme, {
                    [$(this).data('chained')]: curreentArray
                });
                if (sousThemesSelected.split(',').includes($(this).val()) && sousThemesSelected != "") {
                    $(this).val($(this).data('chained') + "-" + $(this).val());
                    $(this).attr('selected', 'selected');
                } else $(this).remove();
            });
            console.log(optionParSousTheme);

            $('#typeformation').change(function() {
                var typeformation = $(this).val();
                $("#theme").empty();
                $("#sous_theme").empty();
                var optionsHtml2 = "";
                window.optionSousTheme = "";
                $(this).find('option:selected').each(function() {
                    //console.log($(this).val());
                    optionsHtml2 = updateTheme(optionsHtml2, $(this).val(), optionParTheme,
                        optionParSousTheme);
                })
            });

            $('#theme').change(function() {
                $("#sous_theme").empty();
                window.optionSousTheme = "";
                $(this).find('option:selected').each(function() {
                    //console.log($(this).val());
                    window.optionSousTheme = updateSousTheme(window.optionSousTheme, $(this).val()
                        .split("-")[1], optionParSousTheme);
                })
            });
        });

        function updateTheme(optionsHtml2, id, optionParTheme, optionParSousTheme) {
            var optionsHtml = optionsHtml2
            if (id != '') {
                optionParTheme[id].forEach(function(key, element) {
                    optionsHtml += '<option value="' + id + '-' + element + '">' + key + '</option>';
                    window.optionSousTheme = updateSousTheme(window.optionSousTheme, element, optionParSousTheme);
                });
                $("#theme").html(optionsHtml);
            }
            return optionsHtml;
        }

        function updateSousTheme(optionsHtml2, id, optionParSousTheme) {
            var optionsHtml = optionsHtml2
            if (id != '' && optionParSousTheme[id] != undefined) {
                optionParSousTheme[id].forEach(function(key, element) {
                    optionsHtml += '<option value="' + id + '-' + element + '">' + key + '</option>';
                });
                $("#sous_theme").html(optionsHtml);
            }
            return optionsHtml;
        }

        $(document).ready(function() {

            var formateurSelected = "{{ implode(',', $formateurSelected) }}";
            //idée de ce bloque de code c'est de remplire l'objet optionParTheme avec les themes provenant de la base de données
            var optionParFormateur = new Object();
            $("#formateur option").each(function() {
                var curreentArray = optionParFormateur[($(this).data('chained'))] ? optionParFormateur[($(
                        this)
                    .data('chained'))] : [];
                curreentArray[$(this).val()] = $(this).text().trim();
                Object.assign(optionParFormateur, {
                    [$(this).data('chained')]: curreentArray
                });
                console.log($(this).val() + " option");

                if (formateurSelected.split(',').includes($(this).val()) && formateurSelected != "") {
                    $(this).val($(this).data('chained') + "-" + $(this).val());
                    $(this).attr('selected', 'selected');
                } else $(this).remove();
            });

            console.log(optionParFormateur);
            $('#entreprise_formateur').change(function() {
                var typeformation = $(this).val();
                $("#formateur").empty();
                var optionsHtml2 = "";
                $(this).find('option:selected').each(function() {
                    //console.log($(this).val());
                    optionsHtml2 = updateFormateur(optionsHtml2, $(this).val(), optionParFormateur);
                })
            });
        });


        var producteursSelected = "{{ implode(',', $producteursSelected) }}";
        var optionParProducteur = new Object();
        var varLocaliteId = $("#localite option:selected").get(0).value;
        $("#producteur option").each(function() {
            var currentArray = optionParProducteur[($(this).data('chained'))] ? optionParProducteur[($(
                    this)
                .data('chained'))] : [];
            currentArray[$(this).val()] = $(this).text().trim();
            Object.assign(optionParProducteur, {
                [$(this).data('chained')]: currentArray
            });

            if (varLocaliteId != $(this).data('chained')) $(this).remove();

            if (producteursSelected.split(',').includes($(this).val()) && producteursSelected != "") {
                $(this).val($(this).val());
                $(this).attr('selected', 'selected');
            }
        });
        console.log(optionParProducteur);

        $('#localite').change(function() {
            var localite = $(this).val();
            $("#producteur").empty();
            var optionsHtml2 = "";
            $(this).find('option:selected').each(function() {
                optionsHtml2 = updateProducteur(optionsHtml2, $(this).val(),
                    optionParProducteur);
            })
        });

        function updateFormateur(optionsHtml2, id, optionParFormateur) {
            var optionsHtml = optionsHtml2
            if (id != '') {
                optionParFormateur[id].forEach(function(key, element) {
                    optionsHtml += '<option value="' + id + '-' + element + '">' + key + '</option>';
                });
                $("#formateur").html(optionsHtml);
            }
            return optionsHtml;
        }

        function updateProducteur(optionsHtml2, id, optionParProducteur) {
            var optionsHtml = optionsHtml2
            if (id != '') {
                optionParProducteur[id].forEach(function(key, element) {
                    optionsHtml += '<option value="' + element + '">' + key + '</option>';
                });
                $("#producteur").html(optionsHtml);
            }
            return optionsHtml;
        }
    </script>
@endpush
