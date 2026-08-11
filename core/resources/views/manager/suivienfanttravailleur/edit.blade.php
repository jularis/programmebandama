@extends('manager.layouts.app')
@section('panel')
    @php
        $selRaisonNonScolarisation = $suivi->raisonsNonScolarisation->pluck('valeur')->toArray();
        $selRaisonPasExtrait = $suivi->raisonsPasExtrait->pluck('valeur')->toArray();
        $selSituationsPfte = $suivi->situationsPfte->pluck('valeur')->toArray();
        $selRaisonTravailAbus = $suivi->raisonsTravailAbus->pluck('valeur')->toArray();
        $selMesuresEnfant = $suivi->mesuresEnfant->pluck('valeur')->toArray();
        $selMesuresMenage = $suivi->mesuresMenage->pluck('valeur')->toArray();
        $selMesuresCommunaute = $suivi->mesuresCommunaute->pluck('valeur')->toArray();
        $selActionsRemediation = $suivi->actionsRemediation->pluck('valeur')->toArray();
        $selThemes = $suivi->themes->pluck('valeur')->toArray();
        $selOutils = $suivi->outils->pluck('valeur')->toArray();
    @endphp
    <div class="row mb-none-30">
        <div class="col-lg-12 mb-30">
            <div class="card">
                <div class="card-body">
                    {!! Form::open([
                        'route' => ['manager.suivi.enfanttravailleur.store'],
                        'method' => 'POST',
                        'class' => 'form-horizontal',
                        'id' => 'formSuiviEnfant',
                        'enctype' => 'multipart/form-data',
                    ]) !!}

                    <input type="hidden" name="id" value="{{ $suivi->id }}">
                    <input type="hidden" name="etatSoumission" id="etatSoumission" value="{{ $suivi->etatSoumission }}">

                    <legend class="legend-center"><h5 class="font-weight-bold text-decoration-underline">Identification</h5></legend>

                    <div class="form-group row">
                        <label class="col-sm-4 control-label">@lang('Pourquoi faites-vous cet interview?')</label>
                        <div class="col-xs-12 col-sm-8">
                            {!! Form::text('raisonInterview', 'Visite de suivi', ['class' => 'form-control', 'readonly']) !!}
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 control-label">@lang("Date de réalisation de l'enquête")</label>
                        <div class="col-xs-12 col-sm-8">
                            {!! Form::date('dateEnquete', $suivi->dateEnquete, ['class' => 'form-control', 'required', 'readonly']) !!}
                            <span class="form-text text-muted">@lang("Date enregistrée automatiquement lors de la création. Le champ est verrouillé.")</span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 control-label">@lang("Nom et Prénom(s) de l'enquêteur/trice")</label>
                        <div class="col-xs-12 col-sm-8">
                            {!! Form::text('nomEnqueteur', trim((auth()->user()->lastname ?? '') . ' ' . (auth()->user()->firstname ?? '')), ['class' => 'form-control', 'required', 'readonly']) !!}
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 control-label">@lang("Entrer le code de l'enfant (ID Child)")</label>
                        <div class="col-xs-12 col-sm-8">
                            <select class="form-control select2-single-select" name="enfant" id="enfant" required>
                                <option value="">@lang('Selectionner une option')</option>
                                @foreach ($enfants as $e)
                                    <option value="{{ $e->id }}"
                                        data-nom="{{ $e->nom }}"
                                        data-datenaissance="{{ $e->dateNaissance }}"
                                        data-sexe="{{ $e->sexe }}"
                                        data-lienparente="{{ $e->lienParente }}"
                                        data-autrelienparente="{{ $e->autreLienParente }}"
                                        data-raisonnevitpasparents="{{ $e->raisonNeVitPasParents }}"
                                        data-autreraisonnevitpasparents="{{ $e->autreRaisonNeVitPasParents }}"
                                        data-situationscolaire="{{ $e->situationScolaire }}"
                                        data-niveauscolaire="{{ $e->niveauScolaire }}"
                                        data-autreraisonnonscolarisation="{{ $e->autreRaisonNonScolarisation }}"
                                        data-extraitnaissance="{{ $e->extraitNaissance }}"
                                        data-autreraisontravailabus="{{ $e->autreRaisonTravailAbus }}"
                                        data-autremesure="{{ $e->autreMesure }}"
                                        data-producteur="{{ stripslashes(@$e->menage->producteur->nom) }} {{ stripslashes(@$e->menage->producteur->prenoms) }}"
                                        data-localite="{{ @$e->menage->localite->nom }}"
                                        @selected($suivi->enfant_id == $e->id)>
                                        {{ $e->codeEnfant }} — {{ $e->nom }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 control-label">@lang('Producteur/rice')</label>
                        <div class="col-xs-12 col-sm-8">
                            <input type="text" class="form-control" id="producteurDisplay" value="{{ stripslashes(@$suivi->enfant->menage->producteur->nom) }} {{ stripslashes(@$suivi->enfant->menage->producteur->prenoms) }}" disabled>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 control-label">@lang('Localite')</label>
                        <div class="col-xs-12 col-sm-8">
                            <input type="text" class="form-control" id="localiteDisplay" value="{{ @$suivi->enfant->menage->localite->nom }}" disabled>
                        </div>
                    </div>

                    <hr class="panel-wide">
                    <legend class="legend-center"><h5 class="font-weight-bold text-decoration-underline">Suivi depuis la dernière visite</h5></legend>

                    <div class="form-group row">
                        <label class="col-sm-4 control-label">@lang("Quelle action de remédiation a été menée pour l'enfant, pour sa famille ou sa communauté?")</label>
                        <div class="col-xs-12 col-sm-8">
                            <select class="form-control select2-multi-select" name="actionsRemediation[]" id="actionsRemediation" multiple="multiple" required>
                                @foreach ($actionsRemediation as $action)
                                    <option value="{{ $action }}" @selected(in_array($action, $selActionsRemediation))>{{ $action }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <hr class="panel-wide">
                    <legend class="legend-center"><h5 class="font-weight-bold text-decoration-underline">Réévaluation de la situation de l'enfant</h5></legend>

                    <div class="form-group row">
                        <label class="col-sm-4 control-label">@lang("Nom et Prénoms de l'Enfant")</label>
                        <div class="col-xs-12 col-sm-8">
                            {!! Form::text('nom', $suivi->nom, ['class' => 'form-control', 'required']) !!}
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 control-label">@lang("Date de naissance l'enfant (jj-mm-aa)")</label>
                        <div class="col-xs-12 col-sm-8">
                            {!! Form::date('dateNaissance', $suivi->dateNaissance, ['class' => 'form-control', 'required']) !!}
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 control-label">@lang("Sexe de l'enfant (M/F)")</label>
                        <div class="col-xs-12 col-sm-8">
                            <select name="sexe" id="sexe" class="form-control" required>
                                <option value="">@lang('Selectionner une option')</option>
                                <option value="M" @selected($suivi->sexe == 'M')>M</option>
                                <option value="F" @selected($suivi->sexe == 'F')>F</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 control-label">@lang('Lien de parenté avec le/la producteur/rice')</label>
                        <div class="col-xs-12 col-sm-8">
                            <select name="lienParente" id="lienParente" class="form-control" required>
                                <option value="">@lang('Selectionner une option')</option>
                                @foreach ($lienParenteEnfant as $lien)
                                    <option value="{{ $lien }}" @selected($suivi->lienParente == $lien)>{{ $lien }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group row" id="autreLienParenteWrap">
                        <label class="col-sm-4 control-label">@lang('Préciser cet autre lien de parenté avec le/la producteur/rice')</label>
                        <div class="col-xs-12 col-sm-8">
                            {!! Form::text('autreLienParente', $suivi->autreLienParente, ['class' => 'form-control']) !!}
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 control-label">@lang("Si l'enfant ne vit pas avec ses parents (père et/ou mère), quelles sont les raisons?")</label>
                        <div class="col-xs-12 col-sm-8">
                            <select name="raisonNeVitPasParents" id="raisonNeVitPasParents" class="form-control" required>
                                <option value="">@lang('Selectionner une option')</option>
                                @foreach ($raisonsNeVitPasParents as $raison)
                                    <option value="{{ $raison }}" @selected($suivi->raisonNeVitPasParents == $raison)>{{ $raison }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group row" id="autreRaisonNeVitPasParentsWrap">
                        <label class="col-sm-4 control-label">@lang("Préciser cette autre raison pour laquelle l'enfant ne vit pas avec son père et/ou sa mère")</label>
                        <div class="col-xs-12 col-sm-8">
                            {!! Form::text('autreRaisonNeVitPasParents', $suivi->autreRaisonNeVitPasParents, ['class' => 'form-control', 'required']) !!}
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-4 control-label">@lang("Situation scolaire de l'enfant")</label>
                        <div class="col-xs-12 col-sm-8">
                            <select name="situationScolaire" id="situationScolaire" class="form-control" required>
                                <option value="">@lang('Selectionner une option')</option>
                                <option value="Scolarisé" @selected($suivi->situationScolaire == 'Scolarisé')>Scolarisé</option>
                                <option value="Déscolarisé" @selected($suivi->situationScolaire == 'Déscolarisé')>Déscolarisé</option>
                                <option value="Jamais scolarisé" @selected($suivi->situationScolaire == 'Jamais scolarisé')>Jamais scolarisé</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row" id="niveauScolaireWrap">
                        <label class="col-sm-4 control-label">@lang("Si scolarisé ou descolarisé, quel est le niveau scolaire ou dernier niveau ateint?")</label>
                        <div class="col-xs-12 col-sm-8">
                            <select name="niveauScolaire" id="niveauScolaire" class="form-control">
                                <option value="">@lang('Selectionner une option')</option>
                                @foreach ($niveauEtude as $niveau)
                                    <optgroup label="{{ $niveau->nom }}">
                                        @foreach ($classes->where('niveaux_etude_id', $niveau->id) as $classe)
                                            <option value="{{ $classe->nom }}" @selected($suivi->niveauScolaire == $classe->nom)>{{ $classe->nom }}</option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group row" id="raisonNonScolarisationWrap">
                        <label class="col-sm-4 control-label">@lang("Pourquoi l'enfant n'est il pas scolarisé/est il déscolarisé ?")</label>
                        <div class="col-xs-12 col-sm-8">
                            <select name="raisonNonScolarisation[]" id="raisonNonScolarisation" class="form-control select2-multi-select" multiple="multiple" required>
                                @foreach ($raisonsNonScolarisation as $raison)
                                    <option value="{{ $raison }}" @selected(in_array($raison, $selRaisonNonScolarisation))>{{ $raison }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group row" id="autreRaisonNonScolarisationWrap">
                        <label class="col-sm-4 control-label">@lang("Préciser cette raison pour laquelle l'enfant n'est pas scolairsé / est descolarisé")</label>
                        <div class="col-xs-12 col-sm-8">
                            {!! Form::text('autreRaisonNonScolarisation', $suivi->autreRaisonNonScolarisation, ['class' => 'form-control', 'required']) !!}
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-4 control-label">@lang("L'enfent a-t-il un extrait de naissance?")</label>
                        <div class="col-xs-12 col-sm-8">
                            <select name="extraitNaissance" id="extraitNaissance" class="form-control" required>
                                <option value="">@lang('Selectionner une option')</option>
                                <option value="Oui" @selected($suivi->extraitNaissance == 'Oui')>Oui</option>
                                <option value="Non" @selected($suivi->extraitNaissance == 'Non')>Non</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row" id="raisonPasExtraitWrap">
                        <label class="col-sm-4 control-label">@lang("Pourquoi l'enfant n'a-t'il pas d'extrait de naissance?")</label>
                        <div class="col-xs-12 col-sm-8">
                            <select name="raisonPasExtrait[]" class="form-control select2-multi-select" multiple="multiple" required>
                                @foreach ($raisonsPasExtrait as $raison)
                                    <option value="{{ $raison }}" @selected(in_array($raison, $selRaisonPasExtrait))>{{ $raison }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-4 control-label">@lang("L'enfant est-il présent et disponible lors de cette visite?")</label>
                        <div class="col-xs-12 col-sm-8">
                            <select name="presentDisponible" id="presentDisponible" class="form-control" required>
                                <option value="">@lang('Selectionner une option')</option>
                                <option value="0" @selected($suivi->presentDisponible === '0')>@lang('Non')</option>
                                <option value="1" @selected($suivi->presentDisponible === '1')>@lang('Oui')</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row" id="raisonAbsentWrap">
                        <label class="col-sm-4 control-label">@lang("Si non, pourquoi l'enfant est-il/elle absent(e) ?")</label>
                        <div class="col-xs-12 col-sm-8">
                            <select name="raisonAbsent" id="raisonAbsent" class="form-control" required>
                                <option value="">@lang('Selectionner une option')</option>
                                <option value="0" @selected($suivi->raisonAbsent === '0')>@lang('Départ des parents')</option>
                                <option value="1" @selected($suivi->raisonAbsent === '1')>@lang("L'enfant ne vit pas dans la localité")</option>
                                <option value="2" @selected($suivi->raisonAbsent === '2')>@lang("L'enfant est décédé")</option>
                                <option value="3" @selected($suivi->raisonAbsent === '3')>@lang("L'enfant est scolarisé loin de chez lui (temporaire)")</option>
                                <option value="4" @selected($suivi->raisonAbsent === '4')>@lang('Vacances scolaires')</option>
                                <option value="5" @selected($suivi->raisonAbsent === '5')>@lang('Sorties scolaires')</option>
                                <option value="6" @selected($suivi->raisonAbsent === '6')>@lang("L'enfant est à la ferme")</option>
                                <option value="7" @selected($suivi->raisonAbsent === '7')>@lang('Autre (temporaire)')</option>
                                <option value="8" @selected($suivi->raisonAbsent === '8')>@lang('Autre (permanent)')</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-4 control-label">@lang("Combien d'heures l'enfant a-t-il travaillé, toutes tâches confondues, au cours des 7 derniers jours ? (heures par semaine)")</label>
                        <div class="col-xs-12 col-sm-8">
                            {!! Form::number('heuresTravailSemaine', $suivi->heuresTravailSemaine, ['class' => 'form-control', 'min' => 0, 'max' => 168, 'required']) !!}
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 control-label">@lang("Sur combien de jours l'enfant a-t-il fait ces heures de travail, au cours des 7 derniers jours ? (jours par semaine)")</label>
                        <div class="col-xs-12 col-sm-8">
                            <select name="joursTravail" id="joursTravail" class="form-control" required>
                                <option value="">@lang('Selectionner une option')</option>
                                <option value="0" @selected($suivi->joursTravail == '0')>@lang("L'enfant n'a fait aucune tâche")</option>
                                <option value="1" @selected($suivi->joursTravail == '1')>@lang('Sur 1 jour')</option>
                                <option value="2" @selected($suivi->joursTravail == '2')>@lang('Sur 2 jours')</option>
                                <option value="3" @selected($suivi->joursTravail == '3')>@lang('Sur 3 jours')</option>
                                <option value="4" @selected($suivi->joursTravail == '4')>@lang('Sur 4 jours')</option>
                                <option value="5" @selected($suivi->joursTravail == '5')>@lang('Sur 5 jours')</option>
                                <option value="6" @selected($suivi->joursTravail == '6')>@lang('Sur 6 jours')</option>
                                <option value="7" @selected($suivi->joursTravail == '7')>@lang('Sur 7 jours')</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 control-label">@lang("Combien d'heures l'enfant a-t-il travaillé, toutes tâches confondues, au cours de la journée la plus chargée de la semaine écoulée ?")</label>
                        <div class="col-xs-12 col-sm-8">
                            <select name="heuresTravailJournee" id="heuresTravailJournee" class="form-control" required>
                                <option value="">@lang('Selectionner une option')</option>
                                <option value="0" @selected($suivi->heuresTravailJournee == 0)>@lang("L'enfant n'a fait aucune tâche")</option>
                                @for ($h = 1; $h <= 24; $h++)
                                    <option value="{{ $h }}" @selected($suivi->heuresTravailJournee == $h)>{{ $h }}h</option>
                                @endfor
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-4 control-label">@lang("Quelle(s) est/sont la/les situation(s) pour laquelle/lesquelles vous estimez qu'il y a besoin d'apporter du soutien pour cet enfant, sa famille ou sa communauté?")</label>
                        <div class="col-xs-12 col-sm-8">
                            <select name="situationsPfte[]" id="situationsPfte" class="form-control select2-multi-select" multiple="multiple" required>
                                @foreach ($situationsPfte as $situation)
                                    <option value="{{ $situation }}" @selected(in_array($situation, $selSituationsPfte))>{{ $situation }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group row" id="raisonTravailAbusWrap">
                        <label class="col-sm-4 control-label">@lang("Pourquoi l'enfant exécute ces travaux ou est-il victime de cet abus ou de cette violation de ses droits ou besoins ?")</label>
                        <div class="col-xs-12 col-sm-8">
                            <select name="raisonTravailAbus[]" id="raisonTravailAbus" class="form-control select2-multi-select" multiple="multiple" required>
                                @foreach ($raisonsTravailAbus as $raison)
                                    <option value="{{ $raison }}" @selected(in_array($raison, $selRaisonTravailAbus))>{{ $raison }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group row" id="autreRaisonTravailAbusWrap">
                        <label class="col-sm-4 control-label">@lang("Préciser cette autre raison pour laquelle il effectue un travail dangereux ou est victime d'abus?")</label>
                        <div class="col-xs-12 col-sm-8">
                            {!! Form::text('autreRaisonTravailAbus', $suivi->autreRaisonTravailAbus, ['class' => 'form-control']) !!}
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-4 control-label">@lang("AU NIVEAU DE L'ENFANT, quelles mesure(s) préconisez/riez-vous si l'enfant effectue un ou des travail/aux dangereux, ou est victime de PFTE ou si l'un de ses droits ou besoins n'est pas respecté, ou s'il subit une situation qu'il pourrait entraver sa scolarité?")</label>
                        <div class="col-xs-12 col-sm-8">
                            <select name="mesuresEnfant[]" class="form-control select2-multi-select mesures" multiple="multiple" required>
                                @foreach ($mesuresEnfant as $mesure)
                                    <option value="{{ $mesure }}" @selected(in_array($mesure, $selMesuresEnfant))>{{ $mesure }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 control-label">@lang("AU NIVEAU DU MENAGE DE L'ENFANT, quelles mesure(s) préconisez/riez-vous si l'enfant effectue un ou des travail/aux dangereux, ou est victime de PFTE ou si l'un de ses droits ou besoins n'est pas respecté")</label>
                        <div class="col-xs-12 col-sm-8">
                            <select name="mesuresMenage[]" class="form-control select2-multi-select mesures" multiple="multiple" required>
                                @foreach ($mesuresMenage as $mesure)
                                    <option value="{{ $mesure }}" @selected(in_array($mesure, $selMesuresMenage))>{{ $mesure }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 control-label">@lang("AU NIVEAU DE LA COMMUNAUTE Où vit l'enfant, quelles mesure(s) préconisez/riez-vous si l'enfant effectue un ou des travail/aux dangereux, ou est victime de PFTE ou si l'un de ses droits ou besoins n'est pas respecté")</label>
                        <div class="col-xs-12 col-sm-8">
                            <select name="mesuresCommunaute[]" class="form-control select2-multi-select mesures" multiple="multiple" required>
                                @foreach ($mesuresCommunaute as $mesure)
                                    <option value="{{ $mesure }}" @selected(in_array($mesure, $selMesuresCommunaute))>{{ $mesure }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group row" id="autreMesureWrap">
                        <label class="col-sm-4 control-label">@lang('Préciser cette autre mesure qui répondrait mieux au cas que vous avez identifié')</label>
                        <div class="col-xs-12 col-sm-8">
                            {!! Form::text('autreMesure', $suivi->autreMesure, ['class' => 'form-control', 'required']) !!}
                        </div>
                    </div>

                    <hr class="panel-wide">
                    <legend class="legend-center"><h5 class="font-weight-bold text-decoration-underline">Sensibilisation de proximité</h5></legend>

                    <div class="form-group row">
                        <label class="col-sm-4 control-label">@lang('Quel(s) Thème(s) a/ont été abordé(s)?')</label>
                        <div class="col-xs-12 col-sm-8">
                            <select class="form-control select2-multi-select" name="themesSensibilisation[]" id="themesSensibilisation" multiple="multiple" required>
                                <option value="Port de charges lourdes" @selected(in_array('Port de charges lourdes', $selThemes))>Port de charges lourdes</option>
                                <option value="Défrichage" @selected(in_array('Défrichage', $selThemes))>Défrichage</option>
                                <option value="Taille, Récolte ou Cabossage avec objet tranchant" @selected(in_array('Taille, Récolte ou Cabossage avec objet tranchant', $selThemes))>Taille, Récolte ou Cabossage avec objet tranchant</option>
                                <option value="Dessouchage" @selected(in_array('Dessouchage', $selThemes))>Dessouchage</option>
                                <option value="Abattage des arbres" @selected(in_array('Abattage des arbres', $selThemes))>Abattage des arbres</option>
                                <option value="Brulages des parcelles" @selected(in_array('Brulages des parcelles', $selThemes))>Brulages des parcelles</option>
                                <option value="Production de bois de chauffe" @selected(in_array('Production de bois de chauffe', $selThemes))>Production de bois de chauffe</option>
                                <option value="Chasse de gibier avec une arme" @selected(in_array('Chasse de gibier avec une arme', $selThemes))>Chasse de gibier avec une arme</option>
                                <option value="Manipulation de produits agro-chimiques" @selected(in_array('Manipulation de produits agro-chimiques', $selThemes))>Manipulation de produits agro-chimiques</option>
                                <option value="Trouaison" @selected(in_array('Trouaison', $selThemes))>Trouaison</option>
                                <option value="Conduite d'engins motorisés" @selected(in_array('Conduite d'engins motorisés', $selThemes))>Conduite d'engins motorisés</option>
                                <option value="Droits des enfants" @selected(in_array('Droits des enfants', $selThemes))>Droits des enfants</option>
                                <option value="Longues heures sur les tâches non-dangereuses" @selected(in_array('Longues heures sur les tâches non-dangereuses', $selThemes))>Longues heures sur les tâches non-dangereuses</option>
                                <option value="Travail de nuit" @selected(in_array('Travail de nuit', $selThemes))>Travail de nuit</option>
                                <option value="Maltraitance physique ou morale" @selected(in_array('Maltraitance physique ou morale', $selThemes))>Maltraitance physique ou morale</option>
                                <option value="Autres thèmes" @selected(in_array('Autres thèmes', $selThemes))>Autres thèmes</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row" id="autreThemeSensibilisationWrap">
                        <label class="col-sm-4 control-label">@lang('Préciser cet/ces autre(s) thèmes abordés au cours de la sensibilisation')</label>
                        <div class="col-xs-12 col-sm-8">
                            {!! Form::text('autreThemeSensibilisation', $suivi->autreThemeSensibilisation, ['class' => 'form-control', 'required']) !!}
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 control-label">@lang('Outils utilisés pour faire la sensibilisation')</label>
                        <div class="col-xs-12 col-sm-8">
                            @php $outils = isset($sensibilisationOutils) ? $sensibilisationOutils : []; $existingOutils = $selOutils ?? []; @endphp
                            <select class="form-control select2-multi-select" name="outilsSensibilisation[]" multiple="multiple" required>
                                <?php foreach ($outils as $outil): ?>
                                    <option value="<?php echo e($outil); ?>" <?php echo in_array($outil, $existingOutils) ? 'selected' : ''; ?>><?php echo e($outil); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 control-label">@lang("Nombre d'adulte(s) hommes sensibilisé(s)")</label>
                        <div class="col-xs-12 col-sm-8">
                            {!! Form::number('nombreHommesSensibilises', $suivi->nombreHommesSensibilises, ['class' => 'form-control effectifSensibilisation', 'required']) !!}
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 control-label">@lang("Nombre d'adulte(s) femmes sensibilisée(s)")</label>
                        <div class="col-xs-12 col-sm-8">
                            {!! Form::number('nombreFemmesSensibilisees', $suivi->nombreFemmesSensibilisees, ['class' => 'form-control effectifSensibilisation', 'required']) !!}
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 control-label">@lang("Nombre d'enfant(s) garçons sensibilisé(s)")</label>
                        <div class="col-xs-12 col-sm-8">
                            {!! Form::number('nombreGarconsSensibilises', $suivi->nombreGarconsSensibilises, ['class' => 'form-control effectifSensibilisation', 'required']) !!}
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 control-label">@lang("Nombre d'enfant(s) filles sensibilisée(s)")</label>
                        <div class="col-xs-12 col-sm-8">
                            {!! Form::number('nombreFillesSensibilisees', $suivi->nombreFillesSensibilisees, ['class' => 'form-control effectifSensibilisation', 'required']) !!}
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 control-label">@lang('Total personnes sensibilisée(s)')</label>
                        <div class="col-xs-12 col-sm-8">
                            <input type="text" class="form-control" id="totalPersonnesSensibilisees" disabled value="{{ $suivi->totalPersonnesSensibilisees }}">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 control-label">@lang("Numéro de téléphone du/de la producteur/rice ou d'un proche")</label>
                        <div class="col-xs-12 col-sm-8">
                            {!! Form::text('telephoneProducteurSensibilisation', $suivi->telephoneProducteurSensibilisation, ['class' => 'form-control', 'required', 'inputmode' => 'numeric', 'pattern' => '[0-9]*']) !!}
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 control-label">@lang('Veuillez prendre/télécharger photo de sensibilisation')</label>
                        <div class="col-xs-12 col-sm-8">
                            <input type="file" name="photoSensibilisation" accept="image/*" class="form-control dropify-fr" data-default-file="{{ $suivi->photoSensibilisation ? asset(str_replace('public/', 'storage/', $suivi->photoSensibilisation)) : '' }}" required>
                        </div>
                    </div>

                    <hr class="panel-wide">
                    <div class="form-group row">
                        <div class="col-xs-12 col-sm-6">
                            <button type="submit" id="btnBrouillon" class="btn btn-outline--warning w-100 h-45">@lang('Enregistrer brouillon')</button>
                        </div>
                        <div class="col-xs-12 col-sm-6">
                            <button type="submit" id="btnSoumettre" class="btn btn--primary w-100 h-45">@lang('Soumettre')</button>
                        </div>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <x-back route="{{ route('manager.suivi.enfanttravailleur.index') }}" />
@endpush

@push('style')
    <style type="text/css">
        .legend-center {
            text-align: center;
            margin-bottom: 10px;
        }
    </style>
@endpush

@push('script')
    <script type="text/javascript">
        $(document).ready(function() {
            $('input[name="telephoneProducteurSensibilisation"]').on('input', function() {
                this.value = this.value.replace(/[^0-9]/g, '');
            });

            if ($.fn.select2) {
                $('.select2-multi-select, .select2-single-select').each(function() {
                    if (!$(this).hasClass('select2-hidden-accessible')) {
                        $(this).select2({ width: '100%' });
                    }
                });
            }

            function toggle($el, show) {
                if (show) {
                    $el.show();
                    $el.find('input, select, textarea').prop('disabled', false);
                } else {
                    $el.hide();
                    $el.find('input, select, textarea').prop('disabled', true).val('');
                }
            }

            function selectedTextes($select) {
                return $select.find(':selected').map(function() { return $(this).text().trim(); }).get();
            }

            function recalculerTotalSensibilisation() {
                var total = 0;
                $('.effectifSensibilisation').each(function() { total += parseInt($(this).val()) || 0; });
                $('#totalPersonnesSensibilisees').val(total);
            }

            // Sélection d'un autre enfant -> pré-remplissage à partir de sa fiche d'origine
            $('#enfant').on('change', function() {
                var opt = $(this).find(':selected');
                $('#producteurDisplay').val(opt.data('producteur') || '');
                $('#localiteDisplay').val(opt.data('localite') || '');

                $('input[name=nom]').val(opt.data('nom') || '');
                $('input[name=dateNaissance]').val(opt.data('datenaissance') || '');
                $('#sexe').val(opt.data('sexe') || '').trigger('change');
                $('#lienParente').val(opt.data('lienparente') || '').trigger('change');
                $('input[name=autreLienParente]').val(opt.data('autrelienparente') || '');
                $('#raisonNeVitPasParents').val(opt.data('raisonnevitpasparents') || '').trigger('change');
                $('input[name=autreRaisonNeVitPasParents]').val(opt.data('autreraisonnevitpasparents') || '');
                $('#situationScolaire').val(opt.data('situationscolaire') || '').trigger('change');
                $('#niveauScolaire').val(opt.data('niveauscolaire') || '');
                $('input[name=autreRaisonNonScolarisation]').val(opt.data('autreraisonnonscolarisation') || '');
                $('#extraitNaissance').val(opt.data('extraitnaissance') || '').trigger('change');
                $('input[name=autreRaisonTravailAbus]').val(opt.data('autreraisontravailabus') || '');
                $('input[name=autreMesure]').val(opt.data('autremesure') || '');
            });

            $('#lienParente').on('change', function() {
                toggle($('#autreLienParenteWrap'), $(this).val() == 'Autre');
            });
            toggle($('#autreLienParenteWrap'), $('#lienParente').val() == 'Autre');

            $('#raisonNeVitPasParents').on('change', function() {
                toggle($('#autreRaisonNeVitPasParentsWrap'), $(this).val() == 'Autres');
            });
            toggle($('#autreRaisonNeVitPasParentsWrap'), $('#raisonNeVitPasParents').val() == 'Autres');

            function majBlocScolaire() {
                var val = $('#situationScolaire').val();
                toggle($('#niveauScolaireWrap'), val == 'Scolarisé' || val == 'Déscolarisé');
                toggle($('#raisonNonScolarisationWrap'), val == 'Déscolarisé' || val == 'Jamais scolarisé');
            }
            $('#situationScolaire').on('change', majBlocScolaire);
            majBlocScolaire();

            $('#raisonNonScolarisation').on('change', function() {
                toggle($('#autreRaisonNonScolarisationWrap'), selectedTextes($(this)).includes('Autre'));
            });
            toggle($('#autreRaisonNonScolarisationWrap'), selectedTextes($('#raisonNonScolarisation')).includes('Autre'));

            $('#extraitNaissance').on('change', function() {
                toggle($('#raisonPasExtraitWrap'), $(this).val() == 'Non');
            });
            $('#presentDisponible').on('change', function() {
                toggle($('#raisonAbsentWrap'), $(this).val() == '0');
            });
            toggle($('#raisonPasExtraitWrap'), $('#extraitNaissance').val() == 'Non');
            toggle($('#raisonAbsentWrap'), $('#presentDisponible').val() == '0');

            function majBlocTravailAbus() {
                var textes = selectedTextes($('#situationsPfte'));
                var actif = textes.length > 0 && !(textes.length == 1 && textes[0] == 'Aucune');
                toggle($('#raisonTravailAbusWrap'), actif);
            }
            $('#situationsPfte').on('change', majBlocTravailAbus);
            majBlocTravailAbus();

            $('#raisonTravailAbus').on('change', function() {
                toggle($('#autreRaisonTravailAbusWrap'), selectedTextes($(this)).includes('Autre'));
            });
            toggle($('#autreRaisonTravailAbusWrap'), selectedTextes($('#raisonTravailAbus')).includes('Autre'));

            function majAutreMesure() {
                var toutesLesTextes = [];
                $('.mesures').each(function() { toutesLesTextes = toutesLesTextes.concat(selectedTextes($(this))); });
                var contientAutre = toutesLesTextes.some(function(t) { return t.indexOf('Autre') !== -1 || t.indexOf('préciser') !== -1; });
                toggle($('#autreMesureWrap'), contientAutre);
            }
            $('.mesures').on('change', majAutreMesure);
            majAutreMesure();

            $('#themesSensibilisation').on('change', function() {
                toggle($('#autreThemeSensibilisationWrap'), selectedTextes($(this)).includes('Autres thèmes'));
            });
            toggle($('#autreThemeSensibilisationWrap'), selectedTextes($('#themesSensibilisation')).includes('Autres thèmes'));

            $('.effectifSensibilisation').on('input change', recalculerTotalSensibilisation);

            $('#btnBrouillon').on('click', function() { $('#etatSoumission').val('Brouillon'); });
            $('#btnSoumettre').on('click', function() { $('#etatSoumission').val('Soumis'); });
        });
    </script>
@endpush
