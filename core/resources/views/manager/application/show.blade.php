@extends('manager.layouts.app')
@section('panel')
    <div class="row mb-none-30">
        <div class="col-lg-12 mb-30">
            <div class="card">
                <div class="card-body">
                    {!! Form::model($application, [
                        'method' => 'POST',
                        'route' => ['manager.suivi.application.store', $application->id],
                        'class' => 'form-horizontal',
                        'id' => 'flocal',
                        'enctype' => 'multipart/form-data',
                    ]) !!}
                    <input type="hidden" name="id" value="{{ $application->id }}">

                    <div class="form-group row">
                        <?php echo Form::label(__('Campagne'), null, ['class' => 'col-sm-4 control-label required']); ?>
                        <div class="col-xs-12 col-sm-8">
                            <?php echo Form::select('campagne_id', $campagnes, null, ['class' => 'form-control campagnes', 'id' => 'campagnes', 'required' => 'required','disabled']); ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-4 control-label">@lang('Section')</label>
                        <div class="col-xs-12 col-sm-8">
                            <select disabled class="form-control" name="section" id="section" required>
                                <option value="">@lang('Selectionner une option')</option>
                                @foreach ($sections as $section)
                                    <option value="{{ $section->id }}" @selected($section->id == $application->parcelle->producteur->localite->section->id)>
                                        {{ $section->libelle }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-4 control-label">@lang('Localite')</label>
                        <div class="col-xs-12 col-sm-8">
                            <select disabled class="form-control" name="localite" id="localite" required>
                                <option value="">@lang('Selectionner une option')</option>
                                @foreach ($localites as $localite)
                                    <option value="{{ $localite->id }}" @selected($localite->id == $application->parcelle->producteur->localite->id)
                                        data-chained="{{ $localite->section->id }}">
                                        {{ $localite->nom }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-4 control-label">@lang('Producteur')</label>
                        <div class="col-xs-12 col-sm-8">
                            <select disabled class="form-control" name="producteur" id="producteur" required>
                                <option value="">@lang('Selectionner une option')</option>
                                @foreach ($producteurs as $producteur)
                                    <option value="{{ $producteur->id }}" @selected($producteur->id == $application->parcelle->producteur->id)
                                        data-chained="{{ $producteur->localite->id }}">
                                        {{ stripslashes($producteur->nom) }} {{ stripslashes($producteur->prenoms) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 control-label">@lang('Parcelle')</label>
                        <div class="col-xs-12 col-sm-8">
                            <select disabled class="form-control" name="parcelle_id" id="parcelle" onchange="getSuperficie()"
                                required>
                                <option value="">@lang('Selectionner une option')</option>
                                @foreach ($parcelles as $parcelle)
                                    <option value="{{ $parcelle->id }}" @selected($parcelle->id == $application->parcelle->id)
                                        data-chained="{{ $parcelle->producteur->id }}">
                                        {{ __('Parcelle') }} {{ $parcelle->codeParc }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <?php echo Form::label(__('Qui a réalisé l\'application ?'), null, ['class' => 'col-sm-4 control-label']); ?>
                        <div class="col-xs-12 col-sm-8">
                            <select disabled class="form-control personneApplication" name="personneApplication" id="application"
                                required>
                                <option value="">@lang('Selectionner une option')</option>
                                <option value="Producteur" @if ($application->personneApplication == 'Producteur') selected @endif>
                                    @lang('Producteur')</option>
                                <option value="Applicateur coop" @if ($application->personneApplication == 'Applicateur coop') selected @endif>
                                    @lang('Applicateur coop')</option>
                                <option value="Independant" @if ($application->personneApplication == 'Independant') selected @endif>
                                    @lang('Independant')</option>
                            </select>
                        </div>
                    </div>
                    <div id="infosIndependant">
                        <div class="form-group row">
                            <?php echo Form::label(__('A-t-il suivi une formation ?'), null, ['class' => 'col-sm-4 control-label']); ?>
                            <div class="col-xs-12 col-sm-8">
                                <select class="form-control suiviFormation" name="suiviFormation" id="suiviFormation"
                                    required>
                                    <option value="">@lang('Selectionner une option')</option>
                                    <option value="oui" @if ($application->suiviFormation == 'oui') selected @endif>
                                        @lang('Oui')</option>
                                    <option value="non" @if ($application->suiviFormation == 'non') selected @endif>
                                        @lang('Non')</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <?php echo Form::label(__('A-t-il une attestation ?'), null, ['class' => 'col-sm-4 control-label']); ?>
                            <div class="col-xs-12 col-sm-8">
                                <select class="form-control attestion" name="attestion" id="attestion">
                                    <option value="">@lang('Selectionner une option')</option>
                                    <option value="oui" @if ($application->attestion == 'oui') selected @endif>
                                        @lang('Oui')</option>
                                    <option value="non" @if ($application->attestion == 'non') selected @endif>
                                        @lang('Non')</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <?php echo Form::label(__('A-t-il fait un bilan de santé ?'), null, ['class' => 'col-sm-4 control-label']); ?>
                            <div class="col-xs-12 col-sm-8">
                                <select class="form-control bilanSante" name="bilanSante" id="bilanSante">
                                    <option value="">@lang('Selectionner une option')</option>
                                    <option value="oui" @if ($application->bilanSante == 'oui') selected @endif>
                                        @lang('Oui')</option>
                                    <option value="non" @if ($application->bilanSante == 'non') selected @endif>
                                        @lang('Non')</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <?php echo Form::label(__('possede t-il un EPI ?'), null, ['class' => 'col-sm-4 control-label']); ?>
                            <div class="col-xs-12 col-sm-8">
                                <select class="form-control independantEpi" name="independantEpi" id="independantEpi">
                                    <option value="">@lang('Selectionner une option')</option>
                                    <option value="oui" @if ($application->independantEpi == 'oui') selected @endif>
                                        @lang('Oui')</option>
                                    <option value="non" @if ($application->independantEpi == 'non') selected @endif>
                                        @lang('Non')</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row" id="etatEpis">
                            <?php echo Form::label(__('Est-il en bon état ?'), null, ['class' => 'col-sm-4 control-label']); ?>
                            <div class="col-xs-12 col-sm-8">
                                <select class="form-control etatEpi" name="etatEpi" id="etatEpi">
                                    <option value="">@lang('Selectionner une option')</option>
                                    <option value="oui" @if ($application->etatEpi == 'oui') selected @endif>
                                        @lang('Oui')</option>
                                    <option value="non" @if ($application->etatEpi == 'non') selected @endif>
                                        @lang('Non')</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <hr class="panel-wide">
                    <div class="form-group row">
                        <div class="col-xs-12 col-sm-12">
                            <table class="table table-striped table-bordered">
                                <tbody id="pesticide_area">
                                    @if ($applicationPesticides)
                                        @foreach ($applicationPesticides as $index => $applicationPesticide)
                                            <tr>
                                                <td class="row">
                                                    <div class="col-xs-12 col-sm-12 bg-success">
                                                        <badge class="btn  btn-outline--warning h-45 btn-sm text-white">
                                                            @lang('Pesticide')
                                                            {{ $index + 1 }}
                                                        </badge>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-xs-12 col-sm-4">
                                                            <div class="form-group row">
                                                                <label class="control-label">Pesticides</label>
                                                                <select name="pesticides[{{ $index }}][nom]"
                                                                    id="pesticides-{{ $index + 1 }}"
                                                                    class="form-control">
                                                                    <option value="">Selectionner une option</option>
                                                                    <option value="Herbicides"
                                                                        @if ($applicationPesticide->nom == 'Herbicides') selected @endif>
                                                                        Herbicides</option>
                                                                    <option value="Fongicides"
                                                                        @if ($applicationPesticide->nom == 'Fongicides') selected @endif>
                                                                        Fongicides</option>
                                                                    <option value="Nematicide"
                                                                        @if ($applicationPesticide->nom == 'Nematicides') selected @endif>
                                                                        Nematicides</option>
                                                                    <option value="Insecticides"
                                                                        @if ($applicationPesticide->nom == 'Insecticides') selected @endif>
                                                                        Insecticides</option>
                                                                    <option value="Acaricides"
                                                                        @if ($applicationPesticide->nom == 'Acaricides') selected @endif>
                                                                        Acaricides</option>
                                                                    <option value="Pesticides"
                                                                        @if ($applicationPesticide->nom == 'Pesticides') selected @endif>
                                                                        Pesticides</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-xs-12 col-sm-4">
                                                            <div class="form-group row">
                                                                {{ Form::label(__('Nom commercial'), null, ['class' => 'control-label']) }}
                                                                <input
                                                                    name="pesticides[{{ $index }}][nomCommercial]"
                                                                    id="nomCommercial-{{ $index + 1 }}"
                                                                    class="form-control" placeholder="Nom commercial"
                                                                    value="{{ $applicationPesticide->nomCommercial }}">
                                                            </div>
                                                        </div>
                                                        <div class="col-xs-12 col-sm-4">
                                                            <div class="form-group row">
                                                                <label>Matières actives</label>
                                                                <input type="text"
                                                                    name="pesticides[{{ $index }}][matiereActive]"
                                                                    id="matiereActive-{{ $index + 1 }}"
                                                                    class="form-control"
                                                                    placeholder="matière active 1, matière active 2 ...."
                                                                    value="{{  implode(",", $applicationPesticide->matieresActives->toArray()) }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row mt-3">
                                                        <div class="col-xs-12 col-sm-4">
                                                            <div class="form-group row">
                                                                <label class="control-label">Toxicicologie</label>
                                                                <select
                                                                    name="pesticides[{{ $index }}][toxicicologie]"
                                                                    id="toxicicologie-{{ $index + 1 }}"
                                                                    class="form-control">
                                                                    <option value="">Selectionner une option</option>
                                                                    <option value="I"
                                                                        @if ($applicationPesticide->toxicicologie == 'I') selected @endif>
                                                                        I</option>
                                                                    <option value="IA"
                                                                        @if ($applicationPesticide->toxicicologie == 'IA') selected @endif>
                                                                        IA</option>
                                                                    <option value="IB"
                                                                        @if ($applicationPesticide->toxicicologie == 'IB') selected @endif>
                                                                        IB</option>
                                                                    <option value="II"
                                                                        @if ($applicationPesticide->toxicicologie == 'II') selected @endif>
                                                                        II</option>
                                                                    <option value="III"
                                                                        @if ($applicationPesticide->toxicicologie == 'III') selected @endif>
                                                                        III</option>
                                                                    <option value="IV"
                                                                        @if ($applicationPesticide->toxicicologie == 'IV') selected @endif>
                                                                        IV</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-xs-12 col-sm-4">
                                                            <div class="form-group row">
                                                                <label>Dose</label>
                                                                <input name="pesticides[{{ $index }}][dose]"
                                                                    id="dose-{{ $index + 1 }}" class="form-control"
                                                                    placeholder="L/Ha"
                                                                    value="{{ $applicationPesticide->dose }}">
                                                            </div>
                                                        </div>
                                                        <div class="col-xs-12 col-sm-4">
                                                            <div class="form-group row">
                                                                <label>Fréquence</label>
                                                                <input name="pesticides[{{ $index }}][frequence]"
                                                                    id="frequence-{{ $index + 1 }}"
                                                                    class="form-control" placeholder="Fréquence"
                                                                    value="{{ $applicationPesticide->frequence }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                        @if (($index +1) > 1)
                                                            <div class="col-xs-12 col-sm-8">
                                                                <button type="button" id="{{ $index + 1 }}"
                                                                    class="removeRowPesticide btn btn-danger btn-sm"><i
                                                                        class="fa fa-minus"></i></button>
                                                            </div>  
                                                        @endif       
                                                </td>
                                            </tr>
                                        @endforeach
                                  
                                    @endif
                                </tbody>
                                <tfoot style="background: #e3e3e3;">
                                    <tr>

                                        <td colspan="3">
                                            
                                        </td>
                                    <tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <div class="form-group row mt-3">
                        <label class="col-sm-4 control-label">@lang('Maladies observées dans la parcelle')</label>
                        <div class="col-xs-12 col-sm-8">
                            <select disabled class="form-control select2-multi-select protections" name="maladies[]" multiple
                                required>
                                <option value="">@lang('Selectionner les protections')</option>
                                <option value="Mirides" {{ in_array('Mirides',$applicationMaladies) ? 'selected' : ''}}>Mirides</option>
                                <option value="Punaises" {{ in_array('Punaises',$applicationMaladies)? 'selected' : ''}}>Punaises</option>
                                <option value="Foreurs" {{ in_array('Foreurs',$applicationMaladies)? 'selected' : ''}} >Foreurs</option>
                                <option value="Chenilles" {{ in_array('Chenilles',$applicationMaladies)? 'selected' : ''}}>Chenilles</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <?php echo Form::label(__('Superficie Pulvérisée'), null, ['class' => 'col-sm-4 control-label required']); ?>
                        <div class="col-xs-12 col-sm-8">
                            <?php echo Form::text('superficiePulverisee', null, ['placeholder' => __('Superficie Pulvérisée'), 'class' => 'form-control superficiePulverisee', 'required', 'min' => '0.1','disabled']); ?>
                        </div>
                    </div>


                    <div class="form-group row">
                        <?php echo Form::label(__('Délais de Réentrée du produit en jours'), null, ['class' => 'col-sm-4 control-label required']); ?>
                        <div class="col-xs-12 col-sm-8">
                            <?php echo Form::number('delaisReentree', null, ['id' => 'delaisReentree', 'class' => 'form-control', 'required', 'min' => '1','disabled']); ?>
                        </div>
                    </div>

                    <hr class="panel-wide">

                    <div class="form-group row">
                        <?php echo Form::label(__('Heure d\'application'), null, ['class' => 'col-sm-4 control-label required']); ?>
                        <div class="col-xs-12 col-sm-8 bootstrap-timepicker timepicker">
                            <?php echo Form::text('heure_application', null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => 'Ex : 04:10','disabled']); ?>
                        </div>
                    </div>

                    <hr class="panel-wide">

                    <div class="form-group row">
                        {{ Form::label(__("Date d'application"), null, ['class' => 'col-sm-4 control-label required']) }}
                        <div class="col-xs-12 col-sm-8">
                            <?php echo Form::date('date_application', null, ['class' => 'form-control dateactivite required', 'required' => 'required','disabled']); ?>
                        </div>
                    </div>

                    <hr class="panel-wide">

                   
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <x-back route="{{ route('manager.suivi.application.index') }}" />
@endpush

@push('script')
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/daterangepicker.css') }}">
    <script src="{{ asset('assets/vendor/jquery/daterangepicker.min.js') }}"></script>
    <script type="text/javascript">
        $('#heure_application').timepicker({
            showMeridian: (false)
        });
        $(document).ready(function() {
            $('.personneApplication').change(function() {
                var personneApplication = $('.personneApplication').val();
                if (personneApplication == 'Independant') {
                    $('#infosIndependant').show('slow');
                    $('.suiviFormation').attr('required', true);
                    $('.attestion').attr('required', true);
                    $('.bilanSante').attr('required', true);

                } else {
                    $('#infosIndependant').hide('slow');
                    $('.suiviFormation').attr('required', false);
                    $('.attestion').attr('required', false);
                    $('.bilanSante').attr('required', false);
                }

                if (personneApplication == 'Applicateur coop') {
                    $('#applicateurs').show('slow');
                } else {
                    $('#applicateurs').hide('slow');
                }
            });
            if ($('.personneApplication').val() == 'Independant') {
                $('#infosIndependant').show('slow');
                $('.suiviFormation').attr('required', true);
                $('.attestion').attr('required', true);
                $('.bilanSante').attr('required', true);

            } else {
                $('#infosIndependant').hide('slow');
                $('.suiviFormation').attr('required', false);
                $('.attestion').attr('required', false);
                $('.bilanSante').attr('required', false);
            }
            $('.independantEpi').change(function() {
                var independantEpi = $('.independantEpi').val();
                if (independantEpi == 'oui') {
                    $('#etatEpis').show('slow');
                    $('.etatEpi').attr('required', true);
                } else {
                    $('#etatEpis').hide('slow');
                    $('.etatEpi').attr('required', false);
                }
            });
            if ($('.independantEpi').val() == 'oui') {
                $('#etatEpis').show('slow');
                $('.etatEpi').attr('required', true);
            } else {
                $('#etatEpis').hide('slow');
                $('.etatEpi').attr('required', false);
            }
        });
       
    </script>
@endpush
