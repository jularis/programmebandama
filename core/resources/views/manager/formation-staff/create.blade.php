@extends('manager.layouts.app')
@section('panel')
    <div class="row mb-none-30">
        <div class="col-lg-12 mb-30">
            <div class="card">
                <div class="card-body">
                    {!! Form::open([
                        'route' => ['manager.formation-staff.store'],
                        'method' => 'POST',
                        'class' => 'form-horizontal',
                        'id' => 'flocal',
                        'enctype' => 'multipart/form-data',
                    ]) !!}

                    <div class="form-group row">
                        <label class="col-sm-4 control-label">@lang('Les membres présents à la formation')</label>
                        <div class="col-xs-12 col-sm-8">
                            <select class="form-control select2-multi-select" name="user[]" id="user" multiple required>
                                <option value="">@lang('Selectionner une option')</option>
                                @foreach ($staffs as $user)
                                    <option value="{{ $user->id }}" @selected(old('user'))>
                                        {{ $user->lastname }} {{ $user->firstname }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <?php echo Form::label(__('Nom des visiteurs ayant participer à la formation'), null, ['class' => 'col-sm-4 control-label']); ?>
                        <div class="col-xs-12 col-sm-8">
                            <select name="visiteurs[]" id="visiteurs" class="form-control select2-auto-tokenize" multiple>
                                <option value="null" disabled>@lang('Entrer un nom')</option>
                            </select>
                        </div>
                    </div>
                    <hr class="panel-wide">
                    <div class="form-group row">
                        <?php echo Form::label(__('Lieu de la formation'), null, ['class' => 'col-sm-4 control-label']); ?>
                        <div class="col-xs-12 col-sm-8">
                            <?php echo Form::text('lieu_formation', null, ['placeholder' => __('Saisissez le lieu de la formation'), 'class' => 'form-control', 'id' => 'lieu_formations', 'required' => 'required']); ?>
                        </div>
                    </div>
                    <div class="form-group row">
                        <?php echo Form::label(__('Modules de formations'), null, ['class' => 'col-sm-4 control-label']); ?>
                        <div class="col-xs-12 col-sm-8">
                            <select class="form-control select2-multi-select" name="module_formation[]" id="typeformation"
                                multiple required>
                                <option value="">@lang('Selectionner une option')</option>
                                @foreach ($ModuleFormationStaffs as $ModuleFormationStaff)
                                    <option value="{{ $ModuleFormationStaff->id }}"@selected(old('module_formation'))>
                                        {{ $ModuleFormationStaff->nom }}
                                    </option>
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
                                        data-chained="{{ $theme->module_formation_staff_id }}" @selected(old('theme'))>
                                        {{ $theme->nom }} </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <hr class="panel-wide">

                    <div class="form-group row">
                        <?php echo Form::label(__('Entreprise du formateur'), null, ['class' => 'col-sm-4 control-label']); ?>
                        <div class="col-xs-8 col-sm-8 input-group mb-3">
                            <select class="form-control select2-multi-select" name="entreprise_formateur[]"
                                id="entreprise_formateur" required multiple>
                                <option value="">@lang('Selectionner une option')</option>
                                @foreach ($entreprises as $entreprise)
                                    <option value="{{ $entreprise->id }}" @selected(old('entreprise_formateur'))>
                                        {{ $entreprise->nom_entreprise }}</option>
                                @endforeach
                            </select>
                            <button type="button" class="btn btn-outline-secondary border-grey add-entreprise"
                                data-toggle="tooltip" data-original-title="Ajouter un formateur"><i class="las la-plus"></i>
                            </button>
                        </div>

                    </div>

                    <div class="form-group row">
                        <?php echo Form::label(__('Formateur'), null, ['class' => 'col-sm-4 control-label']); ?>
                        <div class="col-xs-12 col-sm-8 input-group mb-3">
                            <select class="form-control select2-multi-select" name="formateur[]" id="formateur" required
                                multiple>
                                <option value="">@lang('Selectionner une option')</option>
                                @foreach ($formateurs as $formateur)
                                    <option value="{{ $formateur->id }}" data-chained="{{ $formateur->entreprise_id }}"
                                        @selected(old('formateur'))>
                                        {{ $formateur->nom_formateur }} {{ $formateur->prenom_formateur }}</option>
                                @endforeach
                            </select>
                            <button type="button" class="btn btn-outline-secondary border-grey add-formateur"
                                data-toggle="tooltip" data-original-title="Ajouter un formateur"><i
                                    class="las la-plus"></i></button>
                        </div>
                    </div>

                    <hr class="panel-wide">
                    <div class="form-group row">
                        <?php echo Form::label(__('Date de Début & Fin de la formation'), null, ['class' => 'col-sm-4 control-label required']); ?>
                        <div class="col-xs-12 col-sm-8">

                            <?php echo Form::text('multi_date', now('Africa/Abidjan')->translatedFormat('Y-m-d'), ['class' => 'form-control', 'id' => 'multi_date', 'required' => 'required']); ?>
                        </div>
                    </div>
                    <div class="form-group row">
                        <?php echo Form::label(__('Durée de la formation'), null, ['class' => 'col-sm-4 control-label required']); ?>
                        <div class="col-xs-12 col-sm-8 bootstrap-timepicker timepicker">
                            <?php echo Form::text('duree_formation', null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => 'Ex : 04:10']); ?>

                        </div>
                    </div>
                    <hr class="panel-wide">
                    <div class="form-group row">
                        <?php echo Form::label(__('Photo de la formation'), null, ['class' => 'col-sm-4 control-label']); ?>
                        <div class="col-xs-12 col-sm-8">
                            <input type="file" name="photo_formation" class="form-control dropify-fr"
                                data-allowed-file-extensions="jpg jpeg png">
                        </div>
                    </div>
                    <div class="form-group row">
                        <?php echo Form::label(__('Rapport de la formation'), null, ['class' => 'col-sm-4 control-label']); ?>
                        <div class="col-xs-12 col-sm-8">
                            <input type="file" name="rapport_formation" class="form-control dropify-fr"
                                data-allowed-file-extensions="pdf docx doc xls xlsx">
                        </div>
                    </div>
                    <div class="form-group row">
                        <?php echo Form::label(__('Observation de la formation'), null, ['class' => 'col-sm-4 control-label']); ?>
                        <div class="col-xs-12 col-sm-8">
                            <?php echo Form::textarea('observation_formation', null, ['class' => 'form-control duree_formation', 'rows' => 4]); ?>
                        </div>
                    </div>
                    <hr class="panel-wide">
                    <input type="hidden" name="multiStartDate" id="multiStartDate">
                    <input type="hidden" name="multiEndDate" id="multiEndDate">
                    <div class="form-group row">
                        <button type="submit" class="btn btn--primary w-100 h-45"> @lang('Envoyer')</button>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    @endsection

    @push('breadcrumb-plugins')
        <x-back route="{{ route('manager.formation-staff.index') }}" />
    @endpush
    @push('style')
        <style>
            #flocal>div:nth-child(9)>div>span {
                width: 94% !important;
            }

            #flocal > div:nth-child(10)>div>span{
                width: 94% !important;
            }
        </style>
    @endpush

    @push('script')
        <link rel="stylesheet" href="{{ asset('assets/vendor/css/daterangepicker.css') }}">
        <script src="{{ asset('assets/vendor/jquery/daterangepicker.min.js') }}"></script>
        <script type="text/javascript">
            $("#producteur").chained("#localite");
            //$("#theme").chained("#typeformation");
            //$("#formateur").chained("#entreprise_formateur");
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

            $('body').on('click', '.add-formateur', function() {
                var url = "{{ route('manager.settings.formateurStaff.index') }}";

                $(MODAL_XL + ' ' + MODAL_HEADING).html('...');
                $.ajaxModal(MODAL_XL, url);
                $(MODAL_XL).modal('show');
            });
            $('body').on('click', '.add-entreprise', function() {
                var url = "{{ route('manager.settings.entrepriseModal.index') }}";

                $(MODAL_XL + ' ' + MODAL_HEADING).html('...');
                $.ajaxModal(MODAL_XL, url);
                $(MODAL_XL).modal('show');
            });


            $(document).ready(function() {

                var optionParTheme = new Object();
                $("#theme option").each(function() {
                    //on assigne les themes à lobjet optionParTheme
                    var curreentArray = optionParTheme[($(this).data('chained'))] ? optionParTheme[($(this)
                        .data('chained'))] : [];
                    curreentArray[$(this).val()] = $(this).text().trim();
                    Object.assign(optionParTheme, {
                        [$(this).data('chained')]: curreentArray
                    });
                    $(this).remove();
                });

                $('#typeformation').change(function() {
                    var typeformation = $(this).val();
                    $("#theme").empty();
                    var optionsHtml2 = "";
                    $(this).find('option:selected').each(function() {
                        console.log($(this).val());
                        optionsHtml2 = updateTheme(optionsHtml2, $(this).val(), optionParTheme);
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

            var optionParFormateur = new Object();
            $("#formateur option").each(function() {
                //on assigne les themes à lobjet optionParTheme
                var curreentArray = optionParFormateur[($(this).data('chained'))] ? optionParFormateur[($(this)
                    .data('chained'))] : [];
                curreentArray[$(this).val()] = $(this).text().trim();
                Object.assign(optionParFormateur, {
                    [$(this).data('chained')]: curreentArray
                });
                console.log(optionParFormateur, $(this).data('chained'),$(this).val());
                $(this).remove();
            });

            $('#entreprise_formateur').change(function() {
                var entreprise_formateur = $(this).val();
                $("#formateur").empty();
                var optionsHtml2 = "";
                $(this).find('option:selected').each(function() {
                    console.log($(this).val(), optionParFormateur);
                    optionsHtml2 = updateFormateur(optionsHtml2, $(this).val(), optionParFormateur);
                })
            });

            function updateFormateur(optionsHtml2, id, optionParFormateur) {
                var optionsHtml = optionsHtml2
                if (id != '') {
                    if(optionParFormateur[id] != null){
                        console.log(optionParFormateur[id], id);
                        optionParFormateur[id].forEach(function(key, element) {
                            optionsHtml += '<option value="' + id + '-' + element + '">' + key + '</option>';
                        });
                        $("#formateur").html(optionsHtml);
                    }
                }
                return optionsHtml;
            }
        </script>
    @endpush
