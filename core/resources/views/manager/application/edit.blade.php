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
                            <?php echo Form::select('campagne_id', $campagnes, null, ['class' => 'form-control campagnes', 'id' => 'campagnes', 'required' => 'required']); ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-4 control-label">@lang('Section')</label>
                        <div class="col-xs-12 col-sm-8">
                            <select class="form-control" name="section" id="section" required>
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
                            <select class="form-control" name="localite" id="localite" required>
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
                            <select class="form-control" name="producteur" id="producteur" required>
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
                            <select class="form-control" name="parcelle_id" id="parcelle" onchange="getSuperficie()"
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
                            <select class="form-control personneApplication" name="personneApplication" id="application"
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
                                                                    value="{{ implode(',', $applicationPesticide->matieresActives->toArray()) }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row mt-3">
                                                        <div class="col-xs-12 col-sm-6">
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
                                                        <div class="col-xs-12 col-sm-6">
                                                            <div class="form-group row">
                                                                <label>Dose</label>
                                                                <div class="input-group mb-3">
                                                                    <input type="number"
                                                                        name="pesticides[{{ $index }}][dosage]"
                                                                        id="dosage-{{ $index + 1 }}"
                                                                        class="form-control"
                                                                        value="{{ $applicationPesticide->dosage }}"
                                                                        placeholder="Dose" aria-label="Texte">
                                                                    <div class="input-group-append">
                                                                        <select
                                                                            name="pesticides[{{ $index }}][doseUnite]"
                                                                            id="doseUnite-{{ $index + 1 }}"
                                                                            class="form-control">
                                                                            <option value="L/HA"
                                                                                @if ($applicationPesticide->doseUnite == 'L/HA') selected @endif>
                                                                                L/HA</option>
                                                                            <option value="mL/HA"
                                                                                @if ($applicationPesticide->doseUnite == 'mL/HA') selected @endif>
                                                                                mL/HA</option>
                                                                            <option value="Kg/HA"
                                                                                @if ($applicationPesticide->doseUnite == 'Kg/HA') selected @endif>
                                                                                Kg/HA</option>
                                                                            <option value="g/HA"
                                                                                @if ($applicationPesticide->doseUnite == 'g/HA') selected @endif>
                                                                                g/HA</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row mt-3">
                                                        <div class="col-xs-12 col-sm-6">
                                                            <div class="form-group row">
                                                                <label>Quantité</label>
                                                                <div class="input-group mb-3">
                                                                    <input type="number"
                                                                        name="pesticides[{{ $index }}][quantite]"
                                                                        id="quantite-{{ $index + 1 }}"
                                                                        value="{{ $applicationPesticide->quantite }}"
                                                                        class="form-control" placeholder="Quantité"
                                                                        aria-label="Texte">
                                                                    <div class="input-group-append">
                                                                        <select
                                                                            name="pesticides[{{ $index }}][quantiteUnite]"
                                                                            id="quantiteUnite-{{ $index + 1 }}"
                                                                            class="form-control">
                                                                            <option value="Kg"
                                                                                @if ($applicationPesticide->quantiteUnite == 'Kg') selected @endif>
                                                                                Kg</option>
                                                                            <option value="g"
                                                                                @if ($applicationPesticide->quantiteUnite == 'g') selected @endif>
                                                                                g</option>
                                                                            <option value="L"
                                                                                @if ($applicationPesticide->quantiteUnite == 'L') selected @endif>
                                                                                L</option>
                                                                            <option value="mL"
                                                                                @if ($applicationPesticide->quantiteUnite == 'mL') selected @endif>
                                                                                mL</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-xs-12 col-sm-6">
                                                            <div class="form-group row">
                                                                <label>Fréquence</label>
                                                                <input name="pesticides[{{ $index }}][frequence]"
                                                                    id="frequence-{{ $index + 1 }}"
                                                                    class="form-control" placeholder="Fréquence"
                                                                    value="{{ $applicationPesticide->frequence }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @if ($index + 1 > 1)
                                                        <div class="col-xs-12 col-sm-8">
                                                            <button type="button" id="{{ $index + 1 }}"
                                                                class="removeRowPesticide btn btn-danger btn-sm"><i
                                                                    class="fa fa-minus"></i></button>
                                                        </div>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td class="row">
                                                <div class="col-xs-12 col-sm-12 bg-success">
                                                    <badge class="btn  btn-outline--warning h-45 btn-sm text-white">
                                                        @lang('Pesticide')
                                                    </badge>
                                                </div>
                                                <div class="row">
                                                    <div class="col-xs-12 col-sm-4">
                                                        <div class="form-group row">
                                                            <label class="control-label">Pesticides</label>
                                                            <select name="pesticides[0][nom]" id="pesticides-1"
                                                                class="form-control">
                                                                <option value="">Selectionner une option</option>
                                                                <option value="Herbicides">Herbicides</option>
                                                                <option value="Fongicides">Fongicides</option>
                                                                <option value="Nematicide">Nematicide</option>
                                                                <option value="Insecticides">Insecticides</option>
                                                                <option value="Acaricides">Acaricides</option>
                                                                <option value="Pesticides">Pesticides</option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="col-xs-12 col-sm-4">
                                                        <div class="form-group row">
                                                            {{ Form::label(__('Nom commercial'), null, ['class' => 'control-label']) }}
                                                            <input name="pesticides[0][nomCommercial]"
                                                                id="nomCommercial-1" class="form-control"
                                                                placeholder="Nom commercial">
                                                        </div>
                                                    </div>

                                                    <div class="col-xs-12 col-sm-4">
                                                        <div class="form-group row">
                                                            <label>Matières actives</label>
                                                            <input type="text" name="pesticides[0][matiereActive]"
                                                                id="matiereActive-1" class="form-control"
                                                                placeholder="matière active 1, matière active 2 ....">
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row mt-3">
                                                    <div class="col-xs-12 col-sm-6">
                                                        <div class="form-group row">
                                                            <label class="control-label">Toxicicologie</label>
                                                            <select name="pesticides[0][toxicicologie]"
                                                                id="toxicicologie-1" class="form-control">
                                                                <option value="">Selectionner une option</option>
                                                                <option value="I">I</option>
                                                                <option value="IA">IA</option>
                                                                <option value="IB">IB</option>
                                                                <option value="II">II</option>
                                                                <option value="III">III</option>
                                                                <option value="IV">IV</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-xs-12 col-sm-6">
                                                        <div class="form-group row">
                                                            <label>Dose</label>
                                                            <div class="input-group mb-3">
                                                                <input type="number" id="dosage-1"
                                                                    name="pesticides[0][dosage]" class="form-control"
                                                                    placeholder="Dose" aria-label="Texte">
                                                                <div class="input-group-append">
                                                                    <select name="pesticides[0][doseUnite]"
                                                                        id="doseUnite-1" class="form-control">
                                                                        <option value="L/HA">L/HA</option>
                                                                        <option value="mL/HA">mL/HA</option>
                                                                        <option value="Kg/HA">Kg/HA</option>
                                                                        <option value="g/HA">g/HA</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row mt-3">
                                                    <div class="col-xs-12 col-sm-6">

                                                        <div class="form-group row">
                                                            <label>Quantité</label>
                                                            <div class="input-group mb-3">
                                                                <input type="number" name="pesticides[0][quantite]"
                                                                    id="quantite-1" class="form-control"
                                                                    placeholder="Quantité" aria-label="Texte">
                                                                <div class="input-group-append">
                                                                    <select name="pesticides[0][quantiteUnite]"
                                                                        id="quantiteUnite-1" class="form-control">
                                                                        <option value="Kg">Kg</option>
                                                                        <option value="g">g</option>
                                                                        <option value="L">L</option>
                                                                        <option value="mL">mL</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-xs-12 col-sm-6">
                                                        <div class="form-group row">
                                                            <label>Fréquence</label>
                                                            <input type="number" name="pesticides[0][frequence]"
                                                                id="frequence-1" class="form-control"
                                                                placeholder="Fréquence">
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                                <tfoot style="background: #e3e3e3;">
                                    <tr>

                                        <td colspan="3">
                                            <button id="addRowPesticide" type="button" class="btn btn-success btn-sm"><i
                                                    class="fa fa-plus"></i></button>
                                        </td>
                                    <tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <div class="form-group row mt-3">
                        <label class="col-sm-4 control-label">@lang('Quelles sont les maladies/Ravageurs observés dans la parcelle ?')</label>
                        <div class="col-xs-12 col-sm-8">
                            <select class="form-control select2-multi-select protections" name="maladies[]" multiple
                                required>
                                <option value="">@lang('Selectionner les protections')</option>
                                <option value="Mirides" {{ in_array('Mirides', $applicationMaladies) ? 'selected' : '' }}>
                                    Mirides</option>
                                <option value="Punaises"
                                    {{ in_array('Punaises', $applicationMaladies) ? 'selected' : '' }}>
                                    Punaises</option>
                                <option value="Foreurs" {{ in_array('Foreurs', $applicationMaladies) ? 'selected' : '' }}>
                                    Foreurs</option>
                                <option value="Chenilles"
                                    {{ in_array('Chenilles', $applicationMaladies) ? 'selected' : '' }}>Chenilles</option>
                                <option value="Pourriture brune"
                                    {{ in_array('Pourriture brune', $applicationMaladies) ? 'selected' : '' }}>Pourriture
                                    brune</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <?php echo Form::label(__('Avez-vous d\' autres maladies/Ravageur ?'), null, ['class' => 'col-sm-4 control-label']); ?>
                        <div class="col-xs-12 col-sm-8">
                            <?php echo Form::select('reponse', ['' => 'Selectionner une option', 'non' => __('non'), 'oui' => __('oui')], null, ['class' => 'form-control reponse']); ?>
                        </div>
                    </div>
                    <div id="autreMaladie">
                        <div class="form-group row">
                            <?php echo Form::label(__('Autre Maladie/Ravageur'), null, ['class' => 'col-sm-4 control-label']); ?>
                            <div class="col-xs-12 col-sm-8">
                                <select name="autreMaladie[]" id="autreMaladies"
                                    class="form-control select2-auto-tokenize autreMaladies" multiple>
                                     @if (@$autreMaladies->count())
                                    @foreach ($autreMaladies as $autreMaladie)
                                        <option value="{{$autreMaladie->libelle }}" selected>{{ __($autreMaladie->libelle) }}
                                        </option>
                                    @endforeach
                                @endif
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <?php echo Form::label(__('Superficie Pulvérisée'), null, ['class' => 'col-sm-4 control-label required']); ?>
                        <div class="col-xs-12 col-sm-8">
                            <?php echo Form::text('superficiePulverisee', null, ['placeholder' => __('Superficie Pulvérisée'), 'class' => 'form-control superficiePulverisee', 'required', 'min' => '0.1']); ?>
                        </div>
                    </div>


                    <div class="form-group row">
                        <?php echo Form::label(__('Délais de Réentrée du produit en jours'), null, ['class' => 'col-sm-4 control-label required']); ?>
                        <div class="col-xs-12 col-sm-8">
                            <?php echo Form::number('delaisReentree', null, ['id' => 'delaisReentree', 'class' => 'form-control', 'required', 'min' => '1']); ?>
                        </div>
                    </div>

                    <hr class="panel-wide">

                    <div class="form-group row">
                        <?php echo Form::label(__('Durée d\'application'), null, ['class' => 'col-sm-4 control-label required']); ?>
                        <div class="col-xs-12 col-sm-8 bootstrap-timepicker timepicker">
                            <?php echo Form::text('heure_application', null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => 'Ex : 04:10']); ?>
                        </div>
                    </div>

                    <hr class="panel-wide">

                    <div class="form-group row">
                        {{ Form::label(__("Date d'application"), null, ['class' => 'col-sm-4 control-label required']) }}
                        <div class="col-xs-12 col-sm-8">
                            <?php echo Form::date('date_application', null, ['class' => 'form-control dateactivite required', 'required' => 'required']); ?>
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

            var pesticideCount = $("#pesticide_area tr").length + 1;
            $(document).on('click', '#addRowPesticide', function() {

                //---> Start create table tr
                var html_table = '<tr>';
                html_table +=
                    '<td class="row"><div class="col-xs-12 col-sm-12 bg-success"><badge class="btn  btn-outline--warning h-45 btn-sm text-white">Pesticide ' +
                    pesticideCount +
                    '</badge></div><div class="col-xs-12 col-sm-4"><div class="form-group row"><label for="" class="">Pesticides</label><select class="form-control" id="pesticides-' +
                    pesticideCount +
                    '" name="pesticides[' + pesticideCount +
                    '][nom]"><option value="">Selectionner une option</option><option value="Herbicides">Herbicides</option><option value="Fongicides">Fongicides</option><option value="Nematicide">Nematicide</option><option value="Insecticide">Insecticide</option><option value="Acaricides">Acaricides</option><option value="Pesticides">Pesticides</option></select></div></div><div class="col-xs-12 col-sm-4"><div class="form-group row"><label> Nom commercial</label><input type="text" name="pesticides[' +
                    pesticideCount +
                    '][nomCommercial]" id="nomCommercial' +
                    pesticideCount +
                    '" class="form-control" placeholder="Nom commercial"></div></div><div class="col-xs-12 col-sm-4"><div class="form-group"><label for="" class="">Matières actives</label><input type="text" name="pesticides[' +
                    pesticideCount +
                    '][matiereActive]" id="matiereActive' +
                    pesticideCount +
                    '" class="form-control" placeholder="matière active 1, matière active 2 ...."></div></div><di class="row mt-3"><div class="col-xs-12 col-sm-4"><div class="form-group row"><label class="control-label">Toxicicologie</label><select class="form-control" id="toxicicologie-' +
                    pesticideCount +
                    '" name="pesticides[' + pesticideCount +
                    '][toxicicologie]"> <option value="">Selectionner une option</option><option value="I">I</option><option value="IA">IA</option><option value="IB">IB</option><option value="II">II</option><option value="III">III</option><option value="IV">IV</option></select></div></div><div class="col-xs-12 col-sm-4"><div class="form-group row"><label>Dose</label><input name="pesticides[' +
                    pesticideCount +
                    '][dosage]" id="dosage-' +
                    pesticideCount +
                    '"class="form-control" placeholder="Dose"></div></div><div class="col-xs-12 col-sm-4"><div class="form-group row"><label>Unité de Dose</label><select class="form-control" id="doseUnite-' +
                    pesticideCount +
                    '" name="pesticides[' + pesticideCount +
                    '][doseUnite]"><option value="L/HA">L/HA</option><option value="mL/HA">mL/HA</option><option value="Kg/HA">Kg/HA</option><option value="g/HA">g/HA</option></select></div></div></div><div class="row mt-3"><div class="col-xs-12 col-sm-4"><div class="form-group row"><label>Quantité</label><input name="pesticides[' +
                    pesticideCount +
                    '][quantite]" id="quantite-' +
                    pesticideCount +
                    '"class="form-control" placeholder="Quantité"></div></div><div class="col-xs-12 col-sm-4"><div class="form-group row"><label>Unité de quantité</label><select  name="pesticides[' +
                    pesticideCount +
                    '][quantiteUnite]" id="quantiteUnite-' +
                    pesticideCount +
                    '" class="form-control"><option value="Kg">Kg</option><option value="g">g</option><option value="L">L</option><option value="mL">mL</option></select></div></div><div class="col-xs-12 col-sm-4"><div class="form-group row"><label>Fréquence</label><input type="text" name="pesticides[' +
                    pesticideCount +
                    '][frequence]" id="frequence' +
                    pesticideCount +
                    '" class="form-control" placeholder="Fréquence"></div></div></div></div><div class="col-xs-12 col-sm-4"><button type="button" id="' +
                    pesticideCount +
                    '" class="removeRowPesticide btn btn-danger btn-sm"><i class="fa fa-minus"></i></button></div></td>';

                html_table += '</tr>';
                //---> End create table tr
                //---> End create table tr

                pesticideCount = parseInt(pesticideCount) + 1;
                $('#pesticide_area').append(html_table);

            });
            $(document).on('click', '.removeRowPesticide', function() {

                var row_id = $(this).attr('id');

                // delete only last row id
                if (row_id == $("#pesticide_area tr").length) {

                    $(this).parents('tr').remove();

                    pesticideCount = parseInt(pesticideCount) - 1;
                }
            });
            $('#applicateurs,#infosIndependant,#etatEpis,#autreMaladie').hide();

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

            $('.reponse').change(function() {
                var reponse = $('.reponse').val();
                if (reponse == 'oui') {
                    $('#autreMaladie').show('slow');
                } else {
                    $('#autreMaladie').hide('slow');
                    $('#autreMaladies').val(null).trigger('change'); // Ajouté cette ligne
                }
            });
            if ($('.reponse').val() == 'oui') {
                $('#autreMaladie').show('slow');
            } else {
                $('#autreMaladie').hide('slow');
                $('#autreMaladies').val(null).trigger('change'); // Ajouté cette ligne
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
        $('#localite').chained("#section")
        $("#producteur").chained("#localite");
        $("#parcelle").chained("#producteur");
    </script>
@endpush
