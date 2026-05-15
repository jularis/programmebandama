@extends('manager.layouts.app')
@section('panel')
    <?php use Carbon\Carbon; ?>
    <div class="row mb-none-30">
        <div class="col-lg-12 mb-30">
            <div class="card">
                <div class="card-body">
                    {!! Form::model($formation, [
                        'method' => 'POST',
                        'route' => ['manager.formation-staff.store', $formation->id],
                        'class' => 'form-horizontal',
                        'id' => 'flocal',
                        'enctype' => 'multipart/form-data',
                    ]) !!}
                    <input type="hidden" name="id" value="{{ $formation->id }}">


                    <div class="form-group row">
                        <label class="col-sm-4 control-label">@lang('Selectionner un membre')</label>
                        <div class="col-xs-12 col-sm-8">
                            <select class="form-control select2-multi-select" name="user[]" id="user" multiple
                                required>
                                <option value="">@lang('Selectionner une option')</option>
                                @foreach ($staffs as $user)
                                    <option value="{{ $user->id }}" @selected(in_array($user->id, $dataUser))>
                                        {{ $user->lastname }} {{ $user->firstname }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <?php echo Form::label(__('Nom des visiteurs ayant participer à la formation'), null, ['class' => 'col-sm-4 control-label']); ?>
                        <div class="col-xs-12 col-sm-8">
                            <select name="visiteurs[]" id="visiteurs" class="form-control select2-auto-tokenize" multiple>
                                @if (@$visiteurStaff->count())
                                    @foreach ($visiteurStaff as $visiteur)
                                        <option value="{{ $visiteur->visiteur }}" selected>{{ __($visiteur->visiteur) }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    <hr class="panel-wide">
                    <div class="form-group row">
                        <?php echo Form::label(__('Lieu de la formation'), null, ['class' => 'col-sm-4 control-label']); ?>
                        <div class="col-xs-12 col-sm-8">
                            <?php echo Form::text('lieu_formation', $formation->lieu_formation, ['placeholder' => __('Selectionner une option'), 'class' => 'form-control', 'id' => 'lieu_formations', 'required' => 'required']); ?>
                        </div>
                    </div>
                    <div class="form-group row">
                        <?php echo Form::label(__('Modules de formations'), null, ['class' => 'col-sm-4 control-label']); ?>
                        <div class="col-xs-12 col-sm-8">
                            <select class="form-control select2-multi-select" name="module_formation[]" id="typeformation"
                                multiple required>
                                <option value="">@lang('Selectionner une option')</option>
                                @foreach ($moduleFormationStaffs as $module)
                                    <option value="{{ $module->id }}" @selected(in_array($module->id, $modules))>
                                        {{ $module->nom }}</option>
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
                                        data-chained="{{ $theme->module_formation_staff_id ?? '' }}">
                                        {{ $theme->nom }} </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <hr class="panel-wide">
                    <div class="form-group row">
                        <?php echo Form::label(__('Entreprise du formateur'), null, ['class' => 'col-sm-4 control-label']); ?>
                        <div class="col-xs-12 col-sm-8">
                            <select class="form-control select2-multi-select" name="entreprise_formateur[]"
                                id="entreprise_formateur" required multiple>
                                <option value="">@lang('Selectionner une option')</option>
                                @foreach ($entreprises as $entreprise)
                                    <option value="{{ $entreprise->id }}" @selected(in_array($entreprise->id, $entreprisess))>
                                        {{ $entreprise->nom_entreprise }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <?php echo Form::label(__('Formateur'), null, ['class' => 'col-sm-4 control-label']); ?>
                        <div class="col-xs-12 col-sm-8">
                            <select class="form-control select2-multi-select" name="formateur[]" id="formateur" required
                                multiple>
                                <option value="">@lang('Selectionner une option')</option>
                                @foreach ($formateurs as $formateur)
                                    <option value="{{ $formateur->id }}"
                                        data-chained="{{ $formateur->entreprise_id ?? '' }}">
                                        {{ $formateur->nom_formateur }} {{ $formateur->prenom_formateur }}</option>
                                @endforeach
                            </select>
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
                    <hr class="panel-wide">
                    <div class="form-group row">
                        <?php echo Form::label(__('Photo de la formation'), null, ['class' => 'col-sm-4 control-label']); ?>
                        <div class="col-xs-12 col-sm-8">
                            <input type="file" name="photo_formation" class="form-control dropify-fr"
                                data-default-file="{{ asset('core/storage/app/' . $formation->photo_formation) }}"
                                data-allowed-file-extensions="jpg jpeg png">
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
                    <div class="form-group row">
                        <?php echo Form::label(__('Observation de la formation'), null, ['class' => 'col-sm-4 control-label']); ?>
                        <div class="col-xs-12 col-sm-8">
                            <?php echo Form::textarea('observation_formation', $formation->observation_formation, ['class' => 'form-control duree_formation', 'rows' => 4]); ?>
                        </div>
                    </div>
                    <input type="hidden" name="multiStartDate" id="multiStartDate"
                        value="{{ Carbon::parse($formation->date_debut_formation)->format('Y-m-d') }}">
                    <input type="hidden" name="multiEndDate" id="multiEndDate"
                        value="{{ Carbon::parse($formation->date_fin_formation)->format('Y-m-d') }}">
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
    <x-back route="{{ route('manager.formation-staff.index') }}" />
@endpush

@push('script')
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/daterangepicker.css') }}">
    <script src="{{ asset('assets/vendor/jquery/daterangepicker.min.js') }}"></script>
    <script type="text/javascript">
        $("#producteur").chained("#localite");
        
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
        })


        $(document).ready(function() {
            var themesSelected = "{{ implode(',', $themesSelected) }}";
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

            $('#typeformation').change(function() {
                var typeformation = $(this).val();
                $("#theme").empty();
                var optionsHtml2 = "";
                $(this).find('option:selected').each(function() {
                    //console.log($(this).val());
                    optionsHtml2 = updateTheme(optionsHtml2, $(this).val(), optionParTheme);
                })
            });


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
                console.log($(this).val()+" option");

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

        function updateTheme(optionsHtml2, id, optionParTheme) {
            var optionsHtml = optionsHtml2
            if (id != '') {
                optionParTheme[id].forEach(function(key, element) {
                    optionsHtml += '<option value="' + id + '-' + element + '">' + key + '</option>';
                });
                $("#theme").html(optionsHtml);
            }
            return optionsHtml;
        }

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
    </script>
@endpush
