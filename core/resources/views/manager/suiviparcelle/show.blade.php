@extends('manager.layouts.app')
@section('panel')
    <div class="row mb-none-30">
        <div class="col-lg-12 mb-30">
            <div class="card">
                <div class="card-body">
                    {!! Form::model($suiviparcelle, [
                        'method' => 'POST',
                        'route' => ['manager.suivi.parcelles.store', $suiviparcelle->id],
                        'class' => 'form-horizontal',
                        'id' => 'flocal',
                        'enctype' => 'multipart/form-data',
                    ]) !!}
                    <input type="hidden" name="id" value="{{ $suiviparcelle->id }}">

                    <div class="fieldset-like">
                        <legend class="legend-center">
                            <h5 class="font-weight-bold text-decoration-underline">Informations sur la parcelle</h5>
                        </legend>

                        <div class="form-group row">
                            <?php echo Form::label(__('Campagne'), null, ['class' => 'col-sm-4 control-label required']); ?>
                            <div class="col-xs-12 col-sm-8">
                                <?php echo Form::select('campagne_id', $campagnes, null, ['class' => 'form-control campagnes', 'id' => 'campagnes', 'required' => 'required','disabled']); ?>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-4 control-label">@lang('Section')</label>
                            <div class="col-xs-12 col-sm-8">
                                <select class="form-control" name="section" id="section" required disabled>
                                    <option value="">@lang('Selectionner une option')</option>
                                    @foreach ($sections as $section)
                                        <option value="{{ $section->id }}" @selected($section->id == $suiviparcelle->parcelle->producteur->localite->section->id)>
                                            {{ $section->libelle }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-4 control-label">@lang('Localite')</label>
                            <div class="col-xs-12 col-sm-8">
                                <select class="form-control" name="localite" id="localite" required disabled>
                                    <option value="">@lang('Selectionner une option')</option>
                                    @foreach ($localites as $localite)
                                        <option value="{{ $localite->id }}" data-chained="{{ $localite->section->id }}"
                                            @selected($localite->id == $suiviparcelle->parcelle->producteur->localite->id)>
                                            {{ $localite->nom }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-4 control-label">@lang('Producteur')</label>
                            <div class="col-xs-12 col-sm-8">
                                <select class="form-control" name="producteur" id="producteur" required disabled>
                                    <option value="">@lang('Selectionner une option')</option>
                                    @foreach ($producteurs as $producteur)
                                        <option value="{{ $producteur->id }}" data-chained="{{ $producteur->localite->id }}"
                                            @selected($producteur->id == $suiviparcelle->parcelle->producteur->id)>
                                            {{ stripslashes($producteur->nom) }} {{ stripslashes($producteur->prenoms) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-4 control-label">@lang('Parcelle')</label>
                            <div class="col-xs-12 col-sm-8">
                                <select class="form-control" name="parcelle_id" id="parcelle" onchange="getSuperficie()"
                                    required disabled>
                                    <option value="">@lang('Selectionner une option')</option>
                                    @foreach ($parcelles as $parcelle)
                                        <option value="{{ $parcelle->id }}" data-chained="{{ $parcelle->producteur->id }}"
                                            @selected($parcelle->id == $suiviparcelle->parcelle->id)>
                                            {{ __('Parcelle') }} {{ $parcelle->codeParc }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-4 control-label">@lang('Quelle variété d’arbre ombrage souhaiterais-tu avoir
                                                                                             ?')</label>
                            <div class="col-xs-12 col-sm-8">
                                <select class="form-control select2-multi-select" name="arbre[]" id="arbre" multiple
                                    required disabled>
                                    <option value="">@lang('Selectionner une option')</option>
                                    @foreach ($arbres as $arbre)
                                        <option value="{{ $arbre->id }}" @selected(in_array($arbre->id, $arbreOmbrages))>
                                            {{ $arbre->nom }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <?php echo Form::label(__('Nombre de sauvageons observé dans la parcelle'), null, ['class' => 'col-sm-4 control-label']); ?>
                            <div class="col-xs-12 col-sm-8">
                                <?php echo Form::number('nombreSauvageons', null, ['placeholder' => __('Nombre'), 'class' => 'form-control nombreSauvageons', 'min' => '0','disabled']); ?>
                            </div>
                        </div>

                        <div class="form-group row">
                            <?php echo Form::label(__('As-tu bénéficié d’arbres agro-forestiers ?'), null, ['class' => 'col-sm-4 control-label']); ?>
                            <div class="col-xs-12 col-sm-8">
                                <?php echo Form::select('arbresagroforestiers', ['' => 'Selectionner une option', 'non' => __('non'), 'oui' => __('oui')], null, ['class' => 'form-control arbresagroforestiers', 'required','disabled']); ?>
                            </div>
                        </div>

                        <div class="form-group row" id="recu">
                            <?php echo Form::label(__('Quand avez-vous recu ?'), null, ['class' => 'col-sm-4 control-label']); ?>
                            <div class="col-xs-12 col-sm-8">
                                <?php echo Form::select('recuArbreAgroForestier', ['12 dernier mois' => __('12 dernier mois'), 'Il ya 2ans' => __(' Il ya 2ans'), 'Au dela de 02 ans' => 'Au dela de 02 ans'], null, ['class' => 'form-control','disabled']); ?>
                            </div>
                        </div>
                        <div class="row mb-30" id="agroforestiersobtenus">
                            <div class="col-lg-12">
                                <div class="card border--primary mt-3">
                                    <h5 class="card-header bg--primary text-white">@lang('Quels sont les arbres agro-forestiers obtenus ?')
                                        <button disabled type="button" class="btn btn-sm btn-outline-light float-end addUserData"><i
                                                class="la la-fw la-plus"></i>@lang('Ajouter un arbre agro-forestier')
                                        </button>
                                    </h5>
                                    <div class="card-body">
                                        <div class="row" id="addedField">
                                            <?php $i = 0; ?>
                                            @foreach ($arbreAgroForestiers as $item)
                                                <div class="row single-item gy-2">
                                                    <div class="col-md-3">
                                                        <select disabled class="form-control selected_type"
                                                            name="items[{{ $loop->index }}][arbre]"
                                                            id='producteur-<?php echo $i; ?>'
                                                            onchange=getParcelle(<?php echo $i;
                                                            ?>) required>
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
                                                            <input disabled type="number" class="form-control nombre"
                                                                value="{{ $item['nombre'] }}"
                                                                name="items[{{ $loop->index }}][nombre]" required>
                                                            <span class="input-group-text unit"><i
                                                                    class="las la-balance-scale"></i></span>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-1">
                                                        <button disabled class="btn btn--danger w-100 removeBtn w-100 h-45"
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

                    </div>
                    <div class="fieldset-like">
                        <legend class="legend-center">
                            <h5 class="font-weight-bold text-decoration-underline">Informations sur la campagne précédente
                            </h5>
                        </legend>
                        <div class="form-group row">
                            <?php echo Form::label(__('Quels sont les Pesticides utilisés l’année dernière'), null, ['class' => 'col-sm-12 control-label pt-3']); ?>
                            <div class="col-xs-12 col-sm-12">
                                <table class="table table-striped table-bordered">
                                    <tbody id="pesticidesAnneDerniere_area">
                                        @if ($pesticidesAnneDerniere)
                                            @foreach ($pesticidesAnneDerniere as $index => $pesticide)
                                                <tr>
                                                    <td class="row">
                                                        <div class="col-xs-12 col-sm-12 bg-success">
                                                            <badge class="btn  btn-outline--warning h-45 btn-sm text-white">
                                                                @lang('Pesticide') {{ $index + 1 }}
                                                            </badge>
                                                        </div>
                                                        <div class="col-xs-12 col-sm-4">
                                                            <div class="form-group row">
                                                                <label class="control-label">Nom</label>
                                                                <select disabled
                                                                    name="pesticidesAnneDerniere[{{ $index }}][nom]"
                                                                    id="pesticidesAnneDerniere-{{ $index }}"
                                                                    class="form-control">
                                                                    <option value="">Selectionner une option</option>
                                                                    <option value="Herbicides"
                                                                        {{ $pesticide['nom'] == 'Herbicides' ? 'selected' : '' }}>
                                                                        Herbicides</option>
                                                                    <option value="Fongicides"
                                                                        {{ $pesticide['nom'] == 'Fongicides' ? 'selected' : '' }}>
                                                                        Fongicides</option>
                                                                    <option value="Nematicides"
                                                                        {{ $pesticide['nom'] == 'Nematicides' ? 'selected' : '' }}>
                                                                        Nematicides</option>
                                                                    <option value="Insecticides"
                                                                        {{ $pesticide['nom'] == 'Insecticides' ? 'selected' : '' }}>
                                                                        Insecticides</option>
                                                                    <option value="Acaricides"
                                                                        {{ $pesticide['nom'] == 'Acaricides' ? 'selected' : '' }}>
                                                                        Acaricides</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-xs-12 col-sm-2">
                                                            <div class="form-group row">
                                                                <label class="control-label">Unité</label>
                                                                <select disabled class="form-control unite"
                                                                    name="pesticidesAnneDerniere[{{ $index }}][unite]"
                                                                    id="unite-{{ $index }}">
                                                                    <option value="">Selectionner une option</option>
                                                                    <option value="Kg"
                                                                        {{ $pesticide['unite'] == 'Kg' ? 'selected' : '' }}>
                                                                        Kg</option>
                                                                    <option value="L"
                                                                        {{ $pesticide['unite'] == 'L' ? 'selected' : '' }}>
                                                                        L</option>
                                                                    <option value="g"
                                                                        {{ $pesticide['unite'] == 'g' ? 'selected' : '' }}>
                                                                        g</option>
                                                                    <option value="ml"
                                                                        {{ $pesticide['unite'] == 'ml' ? 'selected' : '' }}>
                                                                            ml
                                                                        </option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-xs-12 col-sm-2">
                                                            <div class="form-group row">
                                                                <label class="control-label">Quantité</label>
                                                                <input type="number" disabled
                                                                    name="pesticidesAnneDerniere[{{ $index }}][quantite]"
                                                                    id="quantite-{{ $index }}"
                                                                    class="form-control quantite" placeholder="Quantité"
                                                                    value="{{ $pesticide['quantite'] }}">
                                                            </div>
                                                        </div>
                                                        <div class="col-xs-12 col-sm-2">
                                                            <div class="form-group row">
                                                                {{ Form::label(__('Type de contenant'), null, ['class' => '']) }}
                                                                <select disabled class="form-control contenant"
                                                                    name="pesticidesAnneDerniere[{{ $index }}][contenant]"
                                                                    id="contenant-{{ $index }}">
                                                                    <option value="">Selectionner une option</option>
                                                                    <option value="Sac"
                                                                        {{ $pesticide['contenant'] == 'Sac' ? 'selected' : '' }}>
                                                                        Sac</option>
                                                                    <option value="Sachet"
                                                                        {{ $pesticide['contenant'] == 'Sachet' ? 'selected' : '' }}>
                                                                        Sachet</option>
                                                                    <option value="Boîte"
                                                                        {{ $pesticide['contenant'] == 'Boîte' ? 'selected' : '' }}>
                                                                        Boîte</option>
                                                                    <option value="Pot"
                                                                        {{ $pesticide['contenant'] == 'Pot' ? 'selected' : '' }}>
                                                                        Pot</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-xs-12 col-sm-2">
                                                            <div class="form-group row">
                                                                {{ Form::label(__('Fréquence'), null, ['class' => '']) }}
                                                                <input disabled type="number"
                                                                    name="pesticidesAnneDerniere[{{ $index }}][frequence]"
                                                                    id="frequence-{{ $index }}"
                                                                    class="form-control frequence" placeholder="Fréquence"
                                                                    value="{{ $pesticide['frequence'] }}">
                                                            </div>
                                                        </div>
                                                        <div class="col-xs-12 col-sm-8">
                                                           
                                                        </div>
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
                                                    <div class="col-xs-12 col-sm-4">
                                                        <div class="form-group row">
                                                            <label class="control-label">Nom</label>
                                                            <select disaled name="pesticidesAnneDerniere[0][nom]"
                                                                id="pesticidesAnneDerniere-1" class="form-control">
                                                                <option value="">Selectionner une option</option>
                                                                <option value="Herbicides">Herbicides</option>
                                                                <option value="Fongicides">Fongicides</option>
                                                                <option value="Nematicides">Nematicides</option>
                                                                <option value="Insecticides">Insecticides</option>
                                                                <option value="Acaricides">Acaricides</option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="col-xs-12 col-sm-2">
                                                        <div class="form-group row">
                                                            <label class="control-label">Unité</label>
                                                            <select disabled class="form-control unite"
                                                                name="pesticidesAnneDerniere[0][unite]" id="unite-1">
                                                                <option value="">Selectionner une option</option>
                                                                <option value="Kg">Kg</option>
                                                                <option value="L">L</option>
                                                                <option value="g">g</option>
                                                                <option value="ml">ml</option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="col-xs-12 col-sm-2">
                                                        <div class="form-group row">
                                                            <label class="control-label">Quantité</label>

                                                            <input type="number" disabled
                                                                name="pesticidesAnneDerniere[0][quantite]" id="quantite-1"
                                                                class="form-control quantite" placeholder="Quantité">
                                                        </div>
                                                    </div>
                                                    <div class="col-xs-12 col-sm-2">
                                                        <div class="form-group row">
                                                            {{ Form::label(__('Type de contenant'), null, ['class' => '']) }}
                                                            <select disabled class="form-control contenant"
                                                                name="pesticidesAnneDerniere[0][contenant]"
                                                                id="contenant-1">
                                                                <option value="">Selectionner une option</option>
                                                                <option value="Sac">Sac</option>
                                                                <option value="Sachet">Sachet</option>
                                                                <option value="Boîte">Boîte</option>
                                                                <option value="Pot">Pot</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-xs-12 col-sm-2">
                                                        <div class="form-group row">
                                                            {{ Form::label(__('Fréquence'), null, ['class' => '']) }}
                                                            <input type="number" disabled
                                                                name="pesticidesAnneDerniere[0][frequence]"
                                                                id="frequence-1" class="form-control frequence"
                                                                placeholder="Fréquence">
                                                        </div>
                                                    </div>

                                                </td>
                                            </tr>
                                        @endif
                                    </tbody>
                                    <tfoot style="background: #e3e3e3;">
                                        <tr>

                                           
                                        <tr>
                                    </tfoot>
                                </table>
                            </div>

                        </div>
                        <div class="form-group row">
                            <?php echo Form::label(__('Quels sont les Intrants (fertilisant, biofertilisant) utilisés l’année dernière'), null, ['class' => 'col-sm-12 control-label pt-3']); ?>

                            {{-- NPK   Compost   Biofertilisant/Bio stimulant Engrais organique préfabriqué --}}
                            <div class="col-xs-12 col-sm-12">
                                <table class="table table-striped table-bordered">
                                    <tbody id="intrantsAnneDerniere_area">
                                        @if ($intrantsAnneDerniere)
                                            @foreach ($intrantsAnneDerniere as $index => $intrant)
                                                <tr>
                                                    <td class="row">
                                                        <div class="col-xs-12 col-sm-12 bg-success">
                                                            <badge class="btn  btn-outline--warning h-45 btn-sm text-white">
                                                                @lang('Intrant') {{ $index + 1 }}
                                                            </badge>
                                                        </div>
                                                        <div class="col-xs-12 col-sm-4">
                                                            <div class="form-group row">
                                                                <label class="control-label">Nom</label>
                                                                <select disabled
                                                                    name="intrantsAnneDerniere[{{ $index }}][nom]"
                                                                    id="intrantsAnneDerniere-{{ $index }}"
                                                                    class="form-control">
                                                                    <option value="">Selectionner une option</option>
                                                                    <option value="Herbicides"
                                                                        {{ $intrant['nom'] == 'Dechets animaux' ? 'selected' : '' }}>
                                                                        Dechets animaux</option>
                                                                    <option value="NPK"
                                                                        {{ $intrant['nom'] == 'NPK' ? 'selected' : '' }}>
                                                                        NPK</option>
                                                                    <option value="Compost"
                                                                        {{ $intrant['nom'] == 'Compost' ? 'selected' : '' }}>
                                                                        Compost</option>
                                                                    <option value="Biofertilisant/Bio stimulant"
                                                                        {{ $intrant['nom'] == 'Biofertilisant/Bio stimulant' ? 'selected' : '' }}>
                                                                        Biofertilisant/Bio stimulant</option>
                                                                    <option value="Engrais organique préfabriqué"
                                                                        {{ $intrant['nom'] == 'Engrais organique préfabriqué' ? 'selected' : '' }}>
                                                                        Engrais organique préfabriqué</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-xs-12 col-sm-2">
                                                            <div class="form-group row">
                                                                <label class="control-label">Unité</label>
                                                                <select disabled class="form-control unite"
                                                                    name="intrantsAnneDerniere[{{ $index }}][unite]"
                                                                    id="unite-{{ $index }}">
                                                                    <option value="">Selectionner une option</option>
                                                                    <option value="Kg"
                                                                        {{ $intrant['unite'] == 'Kg' ? 'selected' : '' }}>
                                                                        Kg</option>
                                                                    <option value="L"
                                                                        {{ $intrant['unite'] == 'L' ? 'selected' : '' }}>
                                                                        L</option>
                                                                    <option value="g"
                                                                        {{ $intrant['unite'] == 'g' ? 'selected' : '' }}>
                                                                        g</option>
                                                                    <option value="ml"
                                                                        {{ $intrant['unite'] == 'ml' ? 'selected' : '' }}>
                                                                            ml
                                                                        </option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-xs-12 col-sm-2">
                                                            <div class="form-group row">
                                                                <label class="control-label">Quantité</label>
                                                                <input disabled type="number"
                                                                    name="intrantsAnneDerniere[{{ $index }}][quantite]"
                                                                    id="quantite-{{ $index }}"
                                                                    class="form-control quantite" placeholder="Quantité"
                                                                    value="{{ $intrant['quantite'] }}">
                                                            </div>
                                                        </div>
                                                        <div class="col-xs-12 col-sm-2">
                                                            <div class="form-group row">
                                                                {{ Form::label(__('Type de contenant'), null, ['class' => '']) }}
                                                                <select disabled class="form-control contenant"
                                                                    name="intrantsAnneDerniere[{{ $index }}][contenant]"
                                                                    id="contenant-{{ $index }}">
                                                                    <option value="">Selectionner une option</option>
                                                                    <option value="Sac"
                                                                        {{ $intrant['contenant'] == 'Sac' ? 'selected' : '' }}>
                                                                        Sac</option>
                                                                    <option value="Sachet"
                                                                        {{ $intrant['contenant'] == 'Sachet' ? 'selected' : '' }}>
                                                                        Sachet</option>
                                                                    <option value="Boîte"
                                                                        {{ $intrant['contenant'] == 'Boîte' ? 'selected' : '' }}>
                                                                        Boîte</option>
                                                                    <option value="Pot"
                                                                        {{ $intrant['contenant'] == 'Pot' ? 'selected' : '' }}>
                                                                        Pot</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-xs-12 col-sm-2">
                                                            <div class="form-group row">
                                                                {{ Form::label(__('Fréquence'), null, ['class' => '']) }}
                                                                <input disabled type="number"
                                                                    name="intrantsAnneDerniere[{{ $index }}][frequence]"
                                                                    id="frequence-{{ $index }}"
                                                                    class="form-control frequence" placeholder="Fréquence"
                                                                    value="{{ $intrant['frequence'] }}">
                                                            </div>
                                                        </div>
                                                        <div class="col-xs-12 col-sm-8">
                                                            
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                         
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
                    </div>
                    <div class="fieldset-like">
                        <legend class="legend-center">
                            <h5 class="font-weight-bold text-decoration-underline">Technique culturale</h5>
                        </legend>
                        <div class="form-group row">
                            <?php echo Form::label(__('Activité de Taille dans la Parcelle'), null, ['class' => 'col-sm-4 control-label']); ?>
                            <div class="col-xs-12 col-sm-8">
                                <?php echo Form::select('activiteTaille', ['Faible' => __('Faible'), 'Moyen' => __('Moyen'), 'Elevé' => __('Elevé')], null, ['class' => 'form-control activiteTaille','disabled']); ?>
                            </div>
                        </div>


                        <div class="form-group row">
                            <?php echo Form::label(__('Activité d’Egourmandage dans la Parcelle'), null, ['class' => 'col-sm-4 control-label']); ?>
                            <div class="col-xs-12 col-sm-8">
                                <?php echo Form::select('activiteEgourmandage', ['Faible' => __('Faible'), 'Moyen' => __('Moyen'), 'Elevé' => __('Elevé')], null, ['class' => 'form-control activiteEgourmandage','disabled']); ?>
                            </div>
                        </div>


                        <div class="form-group row">
                            <?php echo Form::label(__('Activité de désherbage Manuel dans la Parcelle'), null, ['class' => 'col-sm-4 control-label']); ?>
                            <div class="col-xs-12 col-sm-8">
                                <?php echo Form::select('activiteDesherbageManuel', ['Faible' => __('Faible'), 'Moyen' => __('Moyen'), 'Elevé' => __('Elevé')], null, ['class' => 'form-control activiteDesherbageManuel','disabled']); ?>
                            </div>
                        </div>


                        <div class="form-group row">
                            <?php echo Form::label(__('Activité de Récolte Sanitaire dans la Parcelle'), null, ['class' => 'col-sm-4 control-label']); ?>
                            <div class="col-xs-12 col-sm-8">
                                <?php echo Form::select('activiteRecolteSanitaire', ['Faible' => __('Faible'), 'Moyen' => __('Moyen'), 'Elevé' => __('Elevé')], null, ['class' => 'form-control activiteRecolteSanitaire','disabled']); ?>
                            </div>
                        </div>
                        <div class="form-group row">
                            {{ Form::label(__("Nombre de désherbage manuel dans l'année"), null, ['class' => 'col-sm-4 control-label']) }}
                            <div class="col-xs-12 col-sm-8">
                                <?php echo Form::number('nombreDesherbage', null, ['class' => 'form-control', 'min' => '1', 'required','disabled']); ?>
                            </div>
                        </div>
                    </div>

                    <div class="fieldset-like">
                        <legend class="legend-center">
                            <h5 class="font-weight-bold text-decoration-underline">Etat sanitaire de la parcelle</h5>
                        </legend>

                        <div class="form-group row">
                            <?php echo Form::label(__('Présence de Pourriture Brune'), null, ['class' => 'col-sm-4 control-label']); ?>
                            <div class="col-xs-12 col-sm-8">
                                <?php echo Form::select('presencePourritureBrune', ['Faible' => __('Faible'), 'Moyen' => __('Moyen'), 'Elevé ' => __('Elevé'), 'inexistant' => __('Inexistant')], null, ['class' => 'form-control presencePourritureBrune','disabled']); ?>
                            </div>
                        </div>

                        <div class="form-group row">
                            <?php echo Form::label(__('Présence de Swollen Shoot '), null, ['class' => 'col-sm-4 control-label']); ?>
                            <div class="col-xs-12 col-sm-8">
                                <?php echo Form::select('presenceSwollenShoot', ['Faible' => __('Faible'), 'Moyen' => __('Moyen'), 'Elevé ' => __('Elevé'), 'inexistant' => __('Inexistant')], null, ['class' => 'form-control presenceSwollenShoot','disabled']); ?>
                            </div>
                        </div>
                    </div>
                    <div class="fieldset-like">
                        <legend class="legend-center">
                            <h5 class="font-weight-bold text-decoration-underline">Evaluation des insectes ravageurs ou
                                parasites du cacaoyer dans la parcelle</h5>
                        </legend>
                        <div class="form-group row">
                            <?php echo Form::label(__('Présence d’insectes parasites ou ravageurs ?'), null, ['class' => 'col-sm-6 control-label']); ?>
                            <div class="col-xs-12 col-sm-6">
                                <?php echo Form::select('presenceInsectesParasites', ['' => 'Selectionner une option', 'non' => __('non'), 'oui' => __('oui')], null, ['class' => 'form-control presenceInsectesParasites','disabled']); ?>
                            </div>
                        </div>
                        {{-- presence d'insecte  --}}
                        <div class="form-group row" id="presenceInsectesParasitesRavageurs">

                            <div class="col-xs-12 col-sm-12">
                                <table class="table table-striped table-bordered">
                                    <tbody id="insectesParasites_area">
                                        @if ($parasites)
                                            @foreach ($parasites as $index => $parasite)
                                                <tr>
                                                    <td class="row">
                                                        <div class="col-xs-12 col-sm-12 bg-success">
                                                            <badge class="btn  btn-outline--warning h-45 btn-sm text-white">
                                                                @lang('Insectes parasites ou ravageurs') {{ $index + 1 }}
                                                            </badge>
                                                        </div>
                                                        <div class="col-xs-12 col-sm-6">
                                                            <div class="form-group row">
                                                                {{ Form::label(__('Nom'), null, ['class' => 'control-label']) }}
                                                                <select name="insectesParasites[{{ $index }}][nom]"
                                                                    id="insectesParasites-{{ $index }}"
                                                                    class="form-control">
                                                                    <option value="">Selectionner une option</option>
                                                                    <option value="Mirides"
                                                                        {{ $parasite['parasite'] == 'Mirides' ? 'selected' : '' }}>
                                                                        Mirides</option>
                                                                    <option value="Punaises"
                                                                        {{ $parasite['parasite'] == 'Punaises' ? 'selected' : '' }}>
                                                                        Punaises</option>
                                                                    <option value="Foreurs"
                                                                        {{ $parasite['parasite'] == 'Foreurs' ? 'selected' : '' }}>
                                                                        Foreurs</option>
                                                                    <option value="Chenilles"
                                                                        {{ $parasite['parasite'] == 'Chenilles' ? 'selected' : '' }}>
                                                                        Chenilles</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-xs-12 col-sm-6">
                                                            <div class="form-group row">
                                                                <label>Quantité</label>
                                                                <select disabled class="form-control nombreinsectesParasites"
                                                                    name="insectesParasites[{{ $index }}][nombreinsectesParasites]"
                                                                    id="nombreinsectesParasites-{{ $index }}">
                                                                    <option value="">Selectionne une option</option>
                                                                    <option value="Faible"
                                                                        {{ $parasite['nombre'] == 'Faible' ? 'selected' : '' }}>
                                                                        Faible</option>
                                                                    <option value="Moyen"
                                                                        {{ $parasite['nombre'] == 'Moyen' ? 'selected' : '' }}>
                                                                        Moyen</option>
                                                                    <option value="Elevé"
                                                                        {{ $parasite['nombre'] == 'Elevé' ? 'selected' : '' }}>
                                                                        Elevé</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-xs-12 col-sm-8">
                                                            
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        
                                            </tr>
                                        @endif

                                    </tbody>
                                    <tfoot style="background: #e3e3e3;">
                                        <tr>

                                           
                                        <tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        {{-- fin presence d'insecte --}}

                        <div class="form-group row">
                            <?php echo Form::label(__('Avez-vous observé d\'autres insectes ou ravageur qui n\'apparaissent pas dans la liste précédente ?'), null, ['class' => 'col-sm-6 control-label']); ?>
                            <div class="col-xs-12 col-sm-6">
                                <?php echo Form::select('autreInsecte', ['' => 'Selectionner une option', 'non' => __('non'), 'oui' => __('oui')], null, ['class' => 'form-control autreInsecte', 'id' => 'autreInsecte', 'required','disabled']); ?>
                            </div>
                        </div>

                        {{-- autre insecte --}}
                        <div class="form-group row" id="presenceAutreInsecte">
                            <div class="col-xs-12 col-sm-12">
                                <table class="table table-striped table-bordered">
                                    <tbody id="presenceAutreInsecte_area">
                                        @if ($autreParasites)
                                            @foreach ($autreParasites as $index => $autreParasite)
                                                <tr>
                                                    <td class="row">
                                                        <div class="col-xs-12 col-sm-12 bg-success">
                                                            <badge class="btn  btn-outline--warning h-45 btn-sm text-white">
                                                                @lang('Autres insectes parasites ou ravageurs') {{ $index + 1 }}
                                                            </badge>
                                                        </div>
                                                        <div class="col-xs-12 col-sm-6">
                                                            <div class="form-group row">
                                                                {{ Form::label(__('Nom'), null, ['class' => 'control-label']) }}
                                                                <input disabled type="text"
                                                                    name="presenceAutreInsecte[{{ $index }}][autreInsecteNom]"
                                                                    id="autreInsecteNom-{{ $index }}"
                                                                    class="form-control autreInsecteNom"
                                                                    placeholder="Nom de l'insecte ou ravageur"
                                                                    value="{{ $autreParasite['parasite'] }}">
                                                            </div>
                                                        </div>
                                                        <div class="col-xs-12 col-sm-6">
                                                            <div class="form-group row">
                                                                <label>Quantite</label>
                                                                <select disabled class="form-control nombreAutreInsectesParasites"
                                                                    name="presenceAutreInsecte[{{ $index }}][nombreAutreInsectesParasites]"
                                                                    id="nombreAutreInsectesParasites-{{ $index }}">
                                                                    <option value="">Selectionne une option</option>
                                                                    <option value="Faible"
                                                                        {{ $autreParasite['nombre'] == 'Faible' ? 'selected' : '' }}>
                                                                        Faible</option>
                                                                    <option value="Moyen"
                                                                        {{ $autreParasite['nombre'] == 'Moyen' ? 'selected' : '' }}>
                                                                        Moyen</option>
                                                                    <option value="Elevé"
                                                                        {{ $autreParasite['nombre'] == 'Elevé' ? 'selected' : '' }}>
                                                                        Elevé</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-xs-12 col-sm-8">
                                                            
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        
                                        @endif
                                    </tbody>
                                    <tfoot style="background: #e3e3e3;">
                                        <tr>
                                        <tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        {{-- fin autre insecte --}}
                        <div class="form-group row">
                            <?php echo Form::label(__('Avez-vous traitez votre parcelle ?'), null, ['class' => 'col-sm-6 control-label']); ?>
                            <div class="col-xs-12 col-sm-6">
                                <?php echo Form::select('traiterParcelle', ['' => 'Selctionner une option', 'non' => __('non'), 'oui' => __('oui')], null, ['class' => 'form-control traiterParcelle', 'required','disabled']); ?>
                            </div>
                        </div>

                        <div class="form-group row" id="traite">

                            <div class="col-xs-12 col-sm-12">
                                <table class="table table-striped table-bordered">
                                    <tbody id="traitement_area">
                                        @if ($traitements)
                                            @foreach ($traitements as $index => $traitement)
                                                <tr>
                                                    <td class="row">
                                                        <div class="col-xs-12 col-sm-12 bg-success">
                                                            <badge class="btn  btn-outline--warning h-45 btn-sm text-white">
                                                                @lang('Traitement') {{ $index + 1 }}
                                                            </badge>
                                                        </div>
                                                        <div class="col-xs-12 col-sm-4">
                                                            <div class="form-group row">
                                                                <label class="control-label">Nom</label>
                                                                <select disabled name="traitement[{{ $index }}][nom]"
                                                                    id="traitement-{{ $index }}"
                                                                    class="form-control">
                                                                    <option value="">Selectionner une option
                                                                    </option>
                                                                    <option value="Herbicides"
                                                                        {{ $traitement['nom'] == 'Herbicides' ? 'selected' : '' }}>
                                                                        Herbicides</option>
                                                                    <option value="Fongicides"
                                                                        {{ $traitement['nom'] == 'Fongicides' ? 'selected' : '' }}>
                                                                        Fongicides</option>
                                                                    <option value="Compost"
                                                                        {{ $traitement['nom'] == 'Compost' ? 'selected' : '' }}>
                                                                        Compost</option>
                                                                    <option value="Déchets animaux"
                                                                        {{ $traitement['nom'] == 'Déchets animaux' ? 'selected' : '' }}>
                                                                        Déchets animaux</option>
                                                                    <option value="Fiente"
                                                                        {{ $traitement['nom'] == 'Fiente' ? 'selected' : '' }}>
                                                                        Fiente</option>
                                                                    <option value="Nematicides"
                                                                        {{ $traitement['nom'] == 'Nematicides' ? 'selected' : '' }}>
                                                                        Nematicides</option>
                                                                    <option value="Insecticide"
                                                                        {{ $traitement['nom'] == 'Insecticide' ? 'selected' : '' }}>
                                                                        Insecticide</option>
                                                                    <option value="Biofertilisant"
                                                                        {{ $traitement['nom'] == 'Biofertilisant' ? 'selected' : '' }}>
                                                                        Biofertilisant</option>
                                                                    <option value="Engrais chimique"
                                                                        {{ $traitement['nom'] == 'Engrais chimique' ? 'selected' : '' }}>
                                                                        Engrais chimique</option>
                                                                    <option value="Engrais foliaire"
                                                                        {{ $traitement['nom'] == 'Engrais foliaire' ? 'selected' : '' }}>
                                                                        Engrais foliaire</option>
                                                                    <option value="Bouse de vache"
                                                                        {{ $traitement['nom'] == 'Bouse de vache' ? 'selected' : '' }}>
                                                                        Bouse de vache</option>
                                                                    <option value="NPK"
                                                                        {{ $traitement['nom'] == 'NPK' ? 'selected' : '' }}>
                                                                        NPK</option>
                                                                    <option value="Insecticide organique"
                                                                        {{ $traitement['nom'] == 'Insecticide organique' ? 'selected' : '' }}>
                                                                        Insecticide organique</option>
                                                                    <option value="Insecticide chimique"
                                                                        {{ $traitement['nom'] == 'Insecticide chimique' ? 'selected' : '' }}>
                                                                        Insecticide chimique</option>
                                                                    <option value="Pesticides"
                                                                        {{ $traitement['nom'] == 'Pesticides' ? 'selected' : '' }}>
                                                                        Pesticides</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-xs-12 col-sm-2">
                                                            <div class="form-group row">
                                                                <label class="control-label">Unité</label>
                                                                <select disabled class="form-control unite"
                                                                    name="traitement[{{ $index }}][unite]"
                                                                    id="unite-{{ $index }}">
                                                                    <option value="">Selectionner une option
                                                                    </option>
                                                                    <option value="Kg"
                                                                        {{ $traitement['unite'] == 'Kg' ? 'selected' : '' }}>
                                                                        Kg</option>
                                                                    <option value="L"
                                                                        {{ $traitement['unite'] == 'L' ? 'selected' : '' }}>
                                                                        L</option>
                                                                    <option value="g"
                                                                        {{ $traitement['unite'] == 'g' ? 'selected' : '' }}>
                                                                        g</option>
                                                                    <option value="ml"
                                                                        {{ $traitement['unite'] == 'ml' ? 'selected' : '' }}>
                                                                        ml</option>
                                                                    
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-xs-12 col-sm-2">
                                                            <div class="form-group row">
                                                                <label class="control-label">Quantité</label>
                                                                <input disabled type="number"
                                                                    name="traitement[{{ $index }}][quantite]"
                                                                    id="quantite-{{ $index }}"
                                                                    class="form-control quantite" placeholder="Quantité"
                                                                    value="{{ $traitement['quantite'] }}">
                                                            </div>
                                                        </div>
                                                        <div class="col-xs-12 col-sm-2">
                                                            <div class="form-group row">
                                                                {{ Form::label(__('Type de contenant'), null, ['class' => '']) }}
                                                                <select disabled class="form-control contenant"
                                                                    name="traitement[{{ $index }}][contenant]"
                                                                    id="contenant-{{ $index }}">
                                                                    <option value="">Selectionner une option
                                                                    </option>
                                                                    <option value="Sac"
                                                                        {{ $traitement['contenant'] == 'Sac' ? 'selected' : '' }}>
                                                                        Sac</option>
                                                                    <option value="Sachet"
                                                                        {{ $traitement['contenant'] == 'Sachet' ? 'selected' : '' }}>
                                                                        Sachet</option>
                                                                    <option value="Boîte"
                                                                        {{ $traitement['contenant'] == 'Boîte' ? 'selected' : '' }}>
                                                                        Boîte</option>
                                                                    <option value="Pot"
                                                                        {{ $traitement['contenant'] == 'Pot' ? 'selected' : '' }}>
                                                                        Pot</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-xs-12 col-sm-2">
                                                            <div class="form-group row">
                                                                {{ Form::label(__('Fréquence'), null, ['class' => '']) }}
                                                                <input disabled type="number"
                                                                    name="traitement[{{ $index }}][frequence]"
                                                                    id="frequence-{{ $index }}"
                                                                    class="form-control frequence" placeholder="Fréquence"
                                                                    value="{{ $traitement['frequence'] }}">
                                                            </div>
                                                        </div>
                                                        <div class="col-xs-12 col-sm-8">
                                                            
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                    <tfoot style="background: #e3e3e3;">
                                        <tr>
                                            
                                        <tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="fieldset-like">
                        <legend class="legend-center">
                            <h5 class="font-weight-bold text-decoration-underline"> Evaluation des Insectes amis du
                                Cacaoyer dans la parcelle</h5>
                        </legend>

                        <div class="form-group row">
                            <?php echo Form::label(__('Présence de Fourmis Rouge'), null, ['class' => 'col-sm-4 control-label']); ?>
                            <div class="col-xs-12 col-sm-8">
                                <?php echo Form::select('presenceFourmisRouge', ['Faible' => __('Faible'), 'Moyen' => __('Moyen'), 'Elevé' => __('Elevé')], null, ['class' => 'form-control presenceFourmisRouge','disabled']); ?>
                            </div>
                        </div>

                        <div class="form-group row">
                            <?php echo Form::label(__('Présence d’Araignée'), null, ['class' => 'col-sm-4 control-label']); ?>
                            <div class="col-xs-12 col-sm-8">
                                <?php echo Form::select('presenceAraignee', ['Faible' => __('Faible'), 'Moyen' => __('Moyen'), 'Elevé' => __('Elevé')], null, ['class' => 'form-control presenceAraignee','disabled']); ?>
                            </div>
                        </div>

                        <div class="form-group row">
                            <?php echo Form::label(__('Présence de Ver de Terre'), null, ['class' => 'col-sm-4 control-label']); ?>
                            <div class="col-xs-12 col-sm-8">
                                <?php echo Form::select('presenceVerTerre', ['Faible' => __('Faible'), 'Moyen' => __('Moyen'), 'Elevé' => __('Elevé')], null, ['class' => 'form-control presenceVerTerre','disabled']); ?>
                            </div>
                        </div>

                        <div class="form-group row">
                            <?php echo Form::label(__('Présence de Mente Religieuse'), null, ['class' => 'col-sm-4 control-label']); ?>
                            <div class="col-xs-12 col-sm-8">
                                <?php echo Form::select('presenceMenteReligieuse', ['Faible' => __('Faible'), 'Moyen' => __('Moyen'), 'Elevé' => __('Elevé')], null, ['class' => 'form-control presenceMenteReligieuse','disabled']); ?>
                            </div>
                        </div>
                        <div class="form-group row">
                            <?php echo Form::label(__('Présence d’autres types d’insecte amis ?'), null, ['class' => 'col-sm-4 control-label']); ?>
                            <div class="col-xs-12 col-sm-8">
                                <?php echo Form::select('presenceAutreTypeInsecteAmi', ['' => 'Selectionner une option', 'non' => __('non'), 'oui' => __('oui')], null, ['class' => 'form-control presenceAutreTypeInsecteAmi','disabled']); ?>
                            </div>
                        </div>
                        {{-- presenceAutreTypeInsecteAmi --}}
                        <div class="form-group row" id="autreInsectesAmis">

                            <div class="col-xs-12 col-sm-12">
                                <table class="table table-striped table-bordered">
                                    <tbody id="insectesAmis_area">
                                        <?php
        if($amis)
        { 
        $i=0;
        $a=1;
        foreach($amis as $data) {
           ?>
                                        <tr>
                                            <td class="row">
                                                <div class="col-xs-12 col-sm-12 bg-success">
                                                    <badge class="btn  btn-outline--warning h-45 btn-sm text-white">@lang('Insectes amis')
                                                        <?php echo $a; ?>
                                                    </badge>
                                                </div>
                                                <div class="col-xs-12 col-sm-6">
                                                    <div class="form-group row">
                                                        {{ Form::label(__('Nom'), null, ['class' => '']) }}
                                                        <input disabled type="text" name="insectesAmis[]" placeholder="..."
                                                            id="insectesAmis-<?php echo $a; ?>" class="form-control"
                                                            value="<?php echo $data->nom; ?>">
                                                    </div>
                                                </div>

                                                <div class="col-xs-12 col-sm-6">
                                                    <div class="form-group row">
                                                        {{ Form::label(__('Quantite'), null, ['class' => '']) }}
                                                        <select disabled name="nombreinsectesAmis[]"
                                                            id="nombreinsectesAmis-<?php echo $a; ?>"
                                                            class="form-control nombreinsectesAmis">
                                                            <option value="Faible" <?php if ('Faible' == $data->nombre) {
                                                                echo 'selected';
                                                            } ?>>@lang('Faible')
                                                            </option>
                                                            <option value="Moyen" <?php if ('Moyen' == $data->nombre) {
                                                                echo 'selected';
                                                            } ?>>@lang('Moyen')
                                                            </option>
                                                            <option value="Elevé" <?php if ('Elevé' == $data->nombre) {
                                                                echo 'selected';
                                                            } ?>>@lang('Elevé')
                                                            </option>

                                                        </select>
                                                    </div>
                                                </div>
                                              
                                            </td>
                                        </tr>
                                        <?php
           $a++;
            $i++;
        }
    }else{
        ?>
                                        <tr>
                                            <td class="row">
                                                <div class="col-xs-12 col-sm-12 bg-success">
                                                    <badge class="btn  btn-outline--warning h-45 btn-sm text-white">@lang('Insectes amis')
                                                    </badge>
                                                </div>
                                                <div class="col-xs-12 col-sm-6">
                                                    <div class="form-group row">
                                                        {{ Form::label(__('Nom'), null, ['class' => '']) }}
                                                        <input type="text" name="insectesAmis[]"
                                                            placeholder="Autre Insecte ami" id="insectesAmis-1"
                                                            class="form-control">
                                                    </div>
                                                </div>

                                                <div class="col-xs-12 col-sm-6">
                                                    <div class="form-group row">
                                                        {{ Form::label(__('Quantite'), null, ['class' => '']) }}
                                                        <?php echo Form::select('nombreinsectesAmis[]', ['Faible' => __('Faible'), 'Moyen' => __('Moyen'), 'Elevé' => __('Elevé')], null, ['class' => 'form-control nombreinsectesAmis', 'id' => 'nombreinsectesAmis-1']); ?>
                                                    </div>
                                                </div>

                                            </td>
                                        </tr>
                                        <?php
        }
        ?>


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
                    </div>
                    <hr class="panel-wide">



                    <div class="fieldset-like">
                        <legend class="legend-center">
                            <h5 class="font-weight-bold text-decoration-underline">Evaluation de la faune sauvage dans la
                                parcelle</h5>
                        </legend>
                        <div class="form-group row">
                            <div class="col-xs-12 col-sm-12">
                                <table class="table table-striped table-bordered">
                                    <tbody id="animauxRencontres_area">

                                      <?php
        if($suiviparcelle->animal)
        {
        $i=0;
        $a=1;
        foreach ($suiviparcelle->animal as $data) {
           ?>
                                    <tr>
                                        <td class="row">
                                            <div class="col-xs-12 col-sm-12 bg-success">
                                                <badge class="btn  btn-outline--warning h-45 btn-sm text-white">@lang('Animal')
                                                    <?php echo $a; ?>
                                                </badge>
                                            </div>
                                            <div class="col-xs-12 col-sm-12">
                                                <div class="form-group row">
                                                    {{ Form::label(__('Nom'), null, ['class' => '']) }}
                                                    <input disabled type="text" name="animauxRencontres[]"
                                                        placeholder="Nom animal"
                                                        id="animauxRencontres-<?php echo $a; ?>" class="form-control"
                                                        value="<?php echo $data->animal; ?>">
                                                </div>
                                            </div>
                                            
                                        </td>
                                    </tr>
                                    <?php
           $a++;
            $i++;
        }
    }?>

                                    </tbody>
                                    <tfoot style="background: #e3e3e3;">
                                        <tr>

                                           
                                        <tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>

                    <hr class="panel-wide">

                    <div class="form-group row">
                        {{ Form::label(__('Date de la visite'), null, ['class' => 'col-sm-4 control-label required']) }}
                        <div class="col-xs-12 col-sm-8">
                            <?php echo Form::date('dateVisite', null, ['class' => 'form-control dateactivite required', 'required' => 'required','disabled']); ?>
                        </div>
                    </div>

                    <hr class="panel-wide">

                    {{-- <div class="form-group row">
                        <button type="submit" class="btn btn--primary w-100 h-45"> @lang('Envoyer')</button>
                    </div> --}}
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <x-back route="{{ route('manager.suivi.parcelles.index') }}" />
@endpush
@push('style')
    <style type="text/css">
        /* Styles CSS personnalisés */
        .fieldset-like {
            border: 1px solid #ccc;
            /* Ajoutez une bordure */
            border-radius: 5px;
            /* Ajoutez des coins arrondis */
            padding: 10px;
            /* Ajoutez de l'espace intérieur */
            margin-bottom: 20px;
            /* Ajoutez une marge en bas */
            text-align: left;
            /* Alignez le contenu du div à gauche */
        }

        .legend-center {
            text-align: center;
            /* Centrez horizontalement la légende */
            margin-bottom: 10px;
            /* Ajoutez de l'espace sous la légende */
        }
    </style>
@endpush
@push('script')
    <script type="text/javascript">
       
        $('#courseaux,#agroforestiersobtenus,#presenceInsectesParasitesRavageurs,#recu,#autrePesticides,#autreInsectesAmis,#autrePresenceInsectesParasitesRavageurs,#traite,#presenceAutreInsecte')
            .hide();

       


        $('.arbresagroforestiers').change(function() {
            var arbresagroforestiers = $('.arbresagroforestiers').val();
            if (arbresagroforestiers == 'oui') {
                $('#agroforestiersobtenus').show('slow');
                $('#recu').show('slow');
                $('.recuArbreAgroForestier').show('slow');
                $('.recuArbreAgroForestier').attr('required', true);
            } else {
                $('#agroforestiersobtenus').hide('slow');
                $('#recu').hide('slow');
                $('.recuArbreAgroForestier').hide('slow');
                $('.recuArbreAgroForestier').attr('required', false);
                $('.recuArbreAgroForestier').val('');
            }
        });
        if ($('.arbresagroforestiers').val() == 'oui') {
            $('#agroforestiersobtenus').show('slow');
            $('#recu').show('slow');
            $('.recuArbreAgroForestier').show('slow');
            $('.recuArbreAgroForestier').attr('required', true);
        } else {
            $('#agroforestiersobtenus').hide('slow');
            $('#recu').hide('slow');
            $('.recuArbreAgroForestier').hide('slow');
            $('.recuArbreAgroForestier').attr('required', false);
            $('.recuArbreAgroForestier').val('');
        }

        $('#traiterParcelle').change(function() {
            var traiterParcelle = $('#traiterParcelle').val();
            if (traiterParcelle == 'oui') {
                $('#traite').show('slow');
            } else {
                $('#traite').hide('slow');
                $('#traite input').val('');
                var selectElements = $('select[name^="traitement["]');

                // Faire quelque chose avec chaque élément sélectionné
                selectElements.each(function() {
                    // Vous pouvez accéder à chaque élément individuellement avec $(this)
                    $(this).val($(this).find('option:eq(0)').val());
                    console.log($(this))
                });
                //traitement[0][nom]
            }
        });
        if ($('#traiterParcelle').val() == 'oui') {
            $('#traite').show('slow');
        } else {
            $('#traite').hide('slow');
            $('#traite input').val('');
            var selectElements = $('select[name^="traitement["]');

            // Faire quelque chose avec chaque élément sélectionné
            selectElements.each(function() {
                // Vous pouvez accéder à chaque élément individuellement avec $(this)
                $(this).val($(this).find('option:eq(0)').val());
                console.log($(this))
            });
            //traitement[0][nom]
        }

        $('.presenceInsectesParasites').change(function() {
            var presenceInsectesParasites = $('.presenceInsectesParasites').val();
            if (presenceInsectesParasites == 'oui') {
                $('#presenceInsectesParasitesRavageurs').show('slow');
                $('.presenceInsectesParasitesRavageur').show('slow');
            } else {
                $('#presenceInsectesParasitesRavageurs').hide('slow');
                $('.presenceInsectesParasitesRavageur').val('');
                $('#autrePresenceInsectesParasitesRavageurs').hide('slow');

                var selectElements = $('select[name^="insectesParasites["]');

                // Faire quelque chose avec chaque élément sélectionné
                selectElements.each(function() {
                    // Vous pouvez accéder à chaque élément individuellement avec $(this)
                    $(this).val($(this).find('option:eq(0)').val());
                    console.log($(this))
                });
            }
        });
        if ($('.presenceInsectesParasites').val() == 'oui') {
            $('#presenceInsectesParasitesRavageurs').show('slow');
            $('.presenceInsectesParasitesRavageur').show('slow');
        } else {
            $('#presenceInsectesParasitesRavageurs').hide('slow');
            $('.presenceInsectesParasitesRavageur').val('');
            $('#autrePresenceInsectesParasitesRavageurs').hide('slow');

            var selectElements = $('select[name^="insectesParasites["]');

            // Faire quelque chose avec chaque élément sélectionné
            selectElements.each(function() {
                // Vous pouvez accéder à chaque élément individuellement avec $(this)
                $(this).val($(this).find('option:eq(0)').val());
                console.log($(this))
            });
        }

        $('.autreInsecte').change(function() {
            var autreInsecte = $('.autreInsecte').val();
            if (autreInsecte == 'oui') {
                $('#presenceAutreInsecte').show('slow');
            } else {
                $('#presenceAutreInsecte').hide('slow');
                $('#presenceAutreInsecte input').val('');
                var selectElements = $('select[name^="presenceAutreInsecte["]');

                // Faire quelque chose avec chaque élément sélectionné
                selectElements.each(function() {
                    // Vous pouvez accéder à chaque élément individuellement avec $(this)
                    $(this).val($(this).find('option:eq(0)').val());
                    console.log($(this))
                });

            }
        });
        if ($('.autreInsecte').val() == 'oui') {
            $('#presenceAutreInsecte').show('slow');
        } else {
            $('#presenceAutreInsecte').hide('slow');
            $('#presenceAutreInsecte input').val('');
            var selectElements = $('select[name^="presenceAutreInsecte["]');

            // Faire quelque chose avec chaque élément sélectionné
            selectElements.each(function() {
                // Vous pouvez accéder à chaque élément individuellement avec $(this)
                $(this).val($(this).find('option:eq(0)').val());
                console.log($(this))
            });

        }

        $('.presenceAutreTypeInsecteAmi').change(function() {
            var presenceAutreTypeInsecteAmi = $('.presenceAutreTypeInsecteAmi').val();
            if (presenceAutreTypeInsecteAmi == 'oui') {

                $('#autreInsectesAmis').show('slow');

            } else {
                $('#autreInsectesAmis').hide('slow');
                $('#autreInsectesAmis input').val('');
            }
        });
        if ($('.presenceAutreTypeInsecteAmi').val() == 'oui') {

            $('#autreInsectesAmis').show('slow');

        } else {
            $('#autreInsectesAmis').hide('slow');
            $('#autreInsectesAmis input').val('');
        }
    </script>

    <script>
        
    </script>
@endpush
