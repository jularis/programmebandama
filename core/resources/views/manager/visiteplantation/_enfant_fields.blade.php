@php
    $selRaisonsNonScolarisation = $enfant ? $enfant->raisonsNonScolarisation->pluck('valeur')->toArray() : [];
    $selRaisonsPasExtrait = $enfant ? $enfant->raisonsPasExtrait->pluck('valeur')->toArray() : [];
    $selSituationsPfte = $enfant ? $enfant->situationsPfte->pluck('valeur')->toArray() : [];
    $selRaisonsTravailAbus = $enfant ? $enfant->raisonsTravailAbus->pluck('valeur')->toArray() : [];
    $selMesuresEnfant = $enfant ? $enfant->mesuresEnfant->pluck('valeur')->toArray() : [];
    $selMesuresMenage = $enfant ? $enfant->mesuresMenage->pluck('valeur')->toArray() : [];
    $selMesuresCommunaute = $enfant ? $enfant->mesuresCommunaute->pluck('valeur')->toArray() : [];
    $readonly = $readonly ?? false;
@endphp
<div class="fieldset-like enfant-block" data-enfant-index="{{ $index }}">
    <legend class="legend-center">
        <h5 class="font-weight-bold">Enfant n°<span class="enfant-numero">{{ $num }}</span>
            @unless($readonly)
                <button type="button" class="btn btn-sm btn-outline--danger btnRetirerEnfant float-end"><i class="la la-trash"></i> @lang('Retirer')</button>
            @endunless
        </h5>
    </legend>

    <div class="form-group row">
        <label class="col-sm-4 control-label">@lang("Nom et Prénoms de l'Enfant")</label>
        <div class="col-xs-12 col-sm-8">
            <input type="text" name="enfants[{{ $index }}][nom]" class="form-control js-required-visible" value="{{ $enfant->nom ?? '' }}" @if($readonly) disabled @endif>
        </div>
    </div>
    <div class="form-group row">
        <label class="col-sm-4 control-label">@lang("Date de naissance l'enfant (jj-mm-aa)")</label>
        <div class="col-xs-12 col-sm-8">
            <input type="date" name="enfants[{{ $index }}][dateNaissance]" class="form-control js-required-visible" value="{{ $enfant->dateNaissance ?? '' }}" @if($readonly) disabled @endif>
        </div>
    </div>
    <div class="form-group row">
        <label class="col-sm-4 control-label">@lang("Sexe de l'enfant (M/F)")</label>
        <div class="col-xs-12 col-sm-8">
            <select name="enfants[{{ $index }}][sexe]" class="form-control js-required-visible" @if($readonly) disabled @endif>
                <option value="">@lang('Selectionner une option')</option>
                <option value="M" @selected(($enfant->sexe ?? '') == 'M')>M</option>
                <option value="F" @selected(($enfant->sexe ?? '') == 'F')>F</option>
            </select>
        </div>
    </div>
    <div class="form-group row">
        <label class="col-sm-4 control-label">@lang('Lien de parenté avec le/la producteur/rice')</label>
        <div class="col-xs-12 col-sm-8">
            <select name="enfants[{{ $index }}][lienParente]" class="form-control lienParente js-required-visible" @if($readonly) disabled @endif>
                <option value="">@lang('Selectionner une option')</option>
                @foreach ($lienParenteEnfant as $lien)
                    <option value="{{ $lien }}" @selected(($enfant->lienParente ?? '') == $lien)>{{ $lien }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="form-group row autreLienParenteWrap">
        <label class="col-sm-4 control-label">@lang('Préciser cet autre lien de parenté avec le/la producteur/rice')</label>
        <div class="col-xs-12 col-sm-8">
            <input type="text" name="enfants[{{ $index }}][autreLienParente]" class="form-control js-required-visible" value="{{ $enfant->autreLienParente ?? '' }}" @if($readonly) disabled @endif>
        </div>
    </div>
    <div class="form-group row">
        <label class="col-sm-4 control-label">@lang("Si l'enfant ne vit pas avec ses parents (père et/ou mère), quelles sont les raisons?")</label>
        <div class="col-xs-12 col-sm-8">
            <select name="enfants[{{ $index }}][raisonNeVitPasParents]" class="form-control raisonNeVitPasParents js-required-visible" @if($readonly) disabled @endif>
                <option value="">@lang('Selectionner une option')</option>
                @foreach ($raisonsNeVitPasParents as $raison)
                    <option value="{{ $raison }}" @selected(($enfant->raisonNeVitPasParents ?? '') == $raison)>{{ $raison }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="form-group row autreRaisonNeVitPasParentsWrap">
        <label class="col-sm-4 control-label">@lang('Préciser cette autre raison pour laquelle l\'enfant ne vit pas avec son père et/ou sa mère')</label>
        <div class="col-xs-12 col-sm-8">
            <input type="text" name="enfants[{{ $index }}][autreRaisonNeVitPasParents]" class="form-control js-required-visible" value="{{ $enfant->autreRaisonNeVitPasParents ?? '' }}" @if($readonly) disabled @endif>
        </div>
    </div>

    <div class="form-group row">
        <label class="col-sm-4 control-label">@lang("Situation scolaire de l'enfant")</label>
        <div class="col-xs-12 col-sm-8">
            <select name="enfants[{{ $index }}][situationScolaire]" class="form-control situationScolaire js-required-visible" @if($readonly) disabled @endif>
                <option value="">@lang('Selectionner une option')</option>
                <option value="Scolarisé" @selected(($enfant->situationScolaire ?? '') == 'Scolarisé')>Scolarisé</option>
                <option value="Déscolarisé" @selected(($enfant->situationScolaire ?? '') == 'Déscolarisé')>Déscolarisé</option>
                <option value="Jamais scolarisé" @selected(($enfant->situationScolaire ?? '') == 'Jamais scolarisé')>Jamais scolarisé</option>
            </select>
        </div>
    </div>
    <div class="form-group row niveauScolaireWrap">
        <label class="col-sm-4 control-label">@lang("Si scolarisé ou descolarisé, quel est le niveau scolaire ou dernier niveau ateint?")</label>
        <div class="col-xs-12 col-sm-8">
            <select name="enfants[{{ $index }}][niveauScolaire]" class="form-control js-required-visible" @if($readonly) disabled @endif>
                <option value="">@lang('Selectionner une option')</option>
                @foreach ($niveauEtude as $niveau)
                    <optgroup label="{{ $niveau->nom }}">
                        @foreach ($classes->where('niveaux_etude_id', $niveau->id) as $classe)
                            <option value="{{ $classe->nom }}" @selected(($enfant->niveauScolaire ?? '') == $classe->nom)>{{ $classe->nom }}</option>
                        @endforeach
                    </optgroup>
                @endforeach
            </select>
        </div>
    </div>
    <div class="form-group row raisonNonScolarisationWrap">
        <label class="col-sm-4 control-label">@lang("Pourquoi l'enfant n'est il pas scolarisé/est il déscolarisé ?")</label>
        <div class="col-xs-12 col-sm-8">
            <select name="enfants[{{ $index }}][raisonNonScolarisation][]" class="form-control select2-multi-select raisonNonScolarisation js-required-visible" multiple="multiple" @if($readonly) disabled @endif>
                @foreach ($raisonsNonScolarisation as $raison)
                    <option value="{{ $raison }}" @selected(in_array($raison, $selRaisonsNonScolarisation))>{{ $raison }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="form-group row autreRaisonNonScolarisationWrap">
        <label class="col-sm-4 control-label">@lang('Préciser cette raison pour laquelle l\'enfant n\'est pas scolairsé / est descolarisé')</label>
        <div class="col-xs-12 col-sm-8">
            <input type="text" name="enfants[{{ $index }}][autreRaisonNonScolarisation]" class="form-control js-required-visible" value="{{ $enfant->autreRaisonNonScolarisation ?? '' }}" @if($readonly) disabled @endif>
        </div>
    </div>

    <div class="form-group row">
        <label class="col-sm-4 control-label">@lang("L'enfent a-t-il un extrait de naissance?")</label>
        <div class="col-xs-12 col-sm-8">
            <select name="enfants[{{ $index }}][extraitNaissance]" class="form-control extraitNaissance js-required-visible" @if($readonly) disabled @endif>
                <option value="">@lang('Selectionner une option')</option>
                <option value="Oui" @selected(($enfant->extraitNaissance ?? '') == 'Oui')>Oui</option>
                <option value="Non" @selected(($enfant->extraitNaissance ?? '') == 'Non')>Non</option>
            </select>
        </div>
    </div>
    <div class="form-group row raisonPasExtraitWrap">
        <label class="col-sm-4 control-label">@lang("Pourquoi l'enfant n'a-t'il pas d'extrait de naissance?")</label>
        <div class="col-xs-12 col-sm-8">
            <select name="enfants[{{ $index }}][raisonPasExtrait][]" class="form-control select2-multi-select js-required-visible" multiple="multiple" @if($readonly) disabled @endif>
                @foreach ($raisonsPasExtrait as $raison)
                    <option value="{{ $raison }}" @selected(in_array($raison, $selRaisonsPasExtrait))>{{ $raison }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="form-group row">
        <label class="col-sm-4 control-label">@lang('Quelle(s) est/sont la/les situation(s) pour laquelle/lesquelles vous estimez qu\'il y a besoin d\'apporter du soutien pour cet enfant, sa famille ou sa communauté?')</label>
        <div class="col-xs-12 col-sm-8">
            <select name="enfants[{{ $index }}][situationsPfte][]" class="form-control select2-multi-select situationsPfte js-required-visible" multiple="multiple" @if($readonly) disabled @endif>
                @foreach ($situationsPfte as $situation)
                    <option value="{{ $situation }}" @selected(in_array($situation, $selSituationsPfte))>{{ $situation }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="form-group row raisonTravailAbusWrap">
        <label class="col-sm-4 control-label">@lang("Pourquoi l'enfant exécute ces travaux ou est-il victime de cet abus ou de cette violation de ses droits ou besoins ?")</label>
        <div class="col-xs-12 col-sm-8">
            <select name="enfants[{{ $index }}][raisonTravailAbus][]" class="form-control select2-multi-select raisonTravailAbus js-required-visible" multiple="multiple" @if($readonly) disabled @endif>
                @foreach ($raisonsTravailAbus as $raison)
                    <option value="{{ $raison }}" @selected(in_array($raison, $selRaisonsTravailAbus))>{{ $raison }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="form-group row autreRaisonTravailAbusWrap">
        <label class="col-sm-4 control-label">@lang('Préciser cette autre raison pour laquelle il effectue un travail dangereux ou est victime d\'abus?')</label>
        <div class="col-xs-12 col-sm-8">
            <input type="text" name="enfants[{{ $index }}][autreRaisonTravailAbus]" class="form-control js-required-visible" value="{{ $enfant->autreRaisonTravailAbus ?? '' }}" @if($readonly) disabled @endif>
        </div>
    </div>

    <div class="form-group row">
        <label class="col-sm-4 control-label">@lang('AU NIVEAU DE L\'ENFANT, quelles mesure(s) préconisez/riez-vous si l\'enfant effectue un ou des travail/aux  dangereux, ou est victime de PFTE ou si l\'un de ses droits ou besoins n\'est pas respecté, ou s\'il subit une situation qu\'il pourrait entraver sa scolarité?')</label>
        <div class="col-xs-12 col-sm-8">
            <select name="enfants[{{ $index }}][mesuresEnfant][]" class="form-control select2-multi-select mesures js-required-visible" multiple="multiple" @if($readonly) disabled @endif>
                @foreach ($mesuresEnfant as $mesure)
                    <option value="{{ $mesure }}" @selected(in_array($mesure, $selMesuresEnfant))>{{ $mesure }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="form-group row">
        <label class="col-sm-4 control-label">@lang('AU NIVEAU DU MENAGE DE L\'ENFANT, quelles mesure(s) préconisez/riez-vous si l\'enfant effectue un ou des travail/aux  dangereux, ou est victime de PFTE ou si l\'un de ses droits ou ')</label>
        <div class="col-xs-12 col-sm-8">
            <select name="enfants[{{ $index }}][mesuresMenage][]" class="form-control select2-multi-select mesures js-required-visible" multiple="multiple" @if($readonly) disabled @endif>
                @foreach ($mesuresMenage as $mesure)
                    <option value="{{ $mesure }}" @selected(in_array($mesure, $selMesuresMenage))>{{ $mesure }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="form-group row">
        <label class="col-sm-4 control-label">@lang('AU NIVEAU DE LA COMMUNAUTE Où vit l\'enfant, quelles mesure(s) préconisez/riez-vous si l\'enfant effectue un ou des travail/aux  dangereux, ou est victime de PFTE ou si l\'un de ses droits ou ')</label>
        <div class="col-xs-12 col-sm-8">
            <select name="enfants[{{ $index }}][mesuresCommunaute][]" class="form-control select2-multi-select mesures js-required-visible" multiple="multiple" @if($readonly) disabled @endif>
                @foreach ($mesuresCommunaute as $mesure)
                    <option value="{{ $mesure }}" @selected(in_array($mesure, $selMesuresCommunaute))>{{ $mesure }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="form-group row autreMesureWrap">
        <label class="col-sm-4 control-label">@lang('Préciser cette autre mesure qui répondrait mieux au cas que vous avez identifié')</label>
        <div class="col-xs-12 col-sm-8">
            <input type="text" name="enfants[{{ $index }}][autreMesure]" class="form-control js-required-visible" value="{{ $enfant->autreMesure ?? '' }}" @if($readonly) disabled @endif>
        </div>
    </div>
</div>
