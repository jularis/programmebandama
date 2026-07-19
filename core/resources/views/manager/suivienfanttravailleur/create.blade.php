@extends('manager.layouts.app')
@section('panel')
    <div class="row mb-none-30">
        <div class="col-lg-12 mb-30">
            <div class="card">
                <div class="card-body">
                    <div class="alert alert-success" role="alert">
                        <h4>Important !</h4>
                        <p class="mb-0">Suivi enfant travailleur : réévaluation de la situation d'un enfant déjà identifié lors d'une enquête ménage PFTE.</p>
                    </div>

                    {!! Form::open([
                        'route' => ['manager.suivi.enfanttravailleur.store'],
                        'method' => 'POST',
                        'class' => 'form-horizontal',
                        'id' => 'formSuiviEnfant',
                        'enctype' => 'multipart/form-data',
                    ]) !!}

                    <input type="hidden" name="etatSoumission" id="etatSoumission" value="Soumis">

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
                            {!! Form::date('dateEnquete', null, ['class' => 'form-control', 'required']) !!}
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 control-label">@lang("Nom et Prénom(s) de l'enquêteur/trice")</label>
                        <div class="col-xs-12 col-sm-8">
                            {!! Form::text('nomEnqueteur', null, ['class' => 'form-control', 'required']) !!}
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
                                        data-localite="{{ @$e->menage->localite->nom }}">
                                        {{ $e->codeEnfant }} — {{ $e->nom }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 control-label">@lang('Producteur/rice')</label>
                        <div class="col-xs-12 col-sm-8">
                            <input type="text" class="form-control" id="producteurDisplay" disabled>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 control-label">@lang('Localite')</label>
                        <div class="col-xs-12 col-sm-8">
                            <input type="text" class="form-control" id="localiteDisplay" disabled>
                        </div>
                    </div>

                    <hr class="panel-wide">
                    <legend class="legend-center"><h5 class="font-weight-bold text-decoration-underline">Suivi depuis la dernière visite</h5></legend>

                    <div class="form-group row">
                        <label class="col-sm-4 control-label">@lang("Quelle action de remédiation a été menée pour l'enfant, pour sa famille ou sa communauté?")</label>
                        <div class="col-xs-12 col-sm-8">
                            <select class="form-control select2-multi-select" name="actionsRemediation[]" id="actionsRemediation" multiple="multiple">
                                @foreach ($actionsRemediation as $action)
                                    <option value="{{ $action }}">{{ $action }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <hr class="panel-wide">
                    <legend class="legend-center"><h5 class="font-weight-bold text-decoration-underline">Réévaluation de la situation de l'enfant</h5></legend>

                    <div class="form-group row">
                        <label class="col-sm-4 control-label">@lang("Nom et Prénoms de l'Enfant")</label>
                        <div class="col-xs-12 col-sm-8">
                            {!! Form::text('nom', null, ['class' => 'form-control', 'required']) !!}
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 control-label">@lang("Date de naissance l'enfant (jj-mm-aa)")</label>
                        <div class="col-xs-12 col-sm-8">
                            {!! Form::date('dateNaissance', null, ['class' => 'form-control', 'required']) !!}
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 control-label">@lang("Sexe de l'enfant (M/F)")</label>
                        <div class="col-xs-12 col-sm-8">
                            <select name="sexe" id="sexe" class="form-control" required>
                                <option value="">@lang('Selectionner une option')</option>
                                <option value="M">M</option>
                                <option value="F">F</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 control-label">@lang('Lien de parenté avec le/la producteur/rice')</label>
                        <div class="col-xs-12 col-sm-8">
                            <select name="lienParente" id="lienParente" class="form-control">
                                <option value="">@lang('Selectionner une option')</option>
                                @foreach ($lienParenteEnfant as $lien)
                                    <option value="{{ $lien }}">{{ $lien }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group row" id="autreLienParenteWrap">
                        <label class="col-sm-4 control-label">@lang('Préciser cet autre lien de parenté avec le/la producteur/rice')</label>
                        <div class="col-xs-12 col-sm-8">
                            {!! Form::text('autreLienParente', null, ['class' => 'form-control']) !!}
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 control-label">@lang("Si l'enfant ne vit pas avec ses parents (père et/ou mère), quelles sont les raisons?")</label>
                        <div class="col-xs-12 col-sm-8">
                            <select name="raisonNeVitPasParents" id="raisonNeVitPasParents" class="form-control">
                                <option value="">@lang('Selectionner une option')</option>
                                @foreach ($raisonsNeVitPasParents as $raison)
                                    <option value="{{ $raison }}">{{ $raison }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group row" id="autreRaisonNeVitPasParentsWrap">
                        <label class="col-sm-4 control-label">@lang("Préciser cette autre raison pour laquelle l'enfant ne vit pas avec son père et/ou sa mère")</label>
                        <div class="col-xs-12 col-sm-8">
                            {!! Form::text('autreRaisonNeVitPasParents', null, ['class' => 'form-control']) !!}
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-4 control-label">@lang("Situation scolaire de l'enfant")</label>
                        <div class="col-xs-12 col-sm-8">
                            <select name="situationScolaire" id="situationScolaire" class="form-control" required>
                                <option value="">@lang('Selectionner une option')</option>
                                <option value="Scolarisé">Scolarisé</option>
                                <option value="Déscolarisé">Déscolarisé</option>
                                <option value="Jamais scolarisé">Jamais scolarisé</option>
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
                                            <option value="{{ $classe->nom }}">{{ $classe->nom }}</option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group row" id="raisonNonScolarisationWrap">
                        <label class="col-sm-4 control-label">@lang("Pourquoi l'enfant n'est il pas scolarisé/est il déscolarisé ?")</label>
                        <div class="col-xs-12 col-sm-8">
                            <select name="raisonNonScolarisation[]" id="raisonNonScolarisation" class="form-control select2-multi-select" multiple="multiple">
                                @foreach ($raisonsNonScolarisation as $raison)
                                    <option value="{{ $raison }}">{{ $raison }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group row" id="autreRaisonNonScolarisationWrap">
                        <label class="col-sm-4 control-label">@lang("Préciser cette raison pour laquelle l'enfant n'est pas scolairsé / est descolarisé")</label>
                        <div class="col-xs-12 col-sm-8">
                            {!! Form::text('autreRaisonNonScolarisation', null, ['class' => 'form-control']) !!}
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-4 control-label">@lang("L'enfent a-t-il un extrait de naissance?")</label>
                        <div class="col-xs-12 col-sm-8">
                            <select name="extraitNaissance" id="extraitNaissance" class="form-control">
                                <option value="">@lang('Selectionner une option')</option>
                                <option value="Oui">Oui</option>
                                <option value="Non">Non</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row" id="raisonPasExtraitWrap">
                        <label class="col-sm-4 control-label">@lang("Pourquoi l'enfant n'a-t'il pas d'extrait de naissance?")</label>
                        <div class="col-xs-12 col-sm-8">
                            <select name="raisonPasExtrait[]" class="form-control select2-multi-select" multiple="multiple">
                                @foreach ($raisonsPasExtrait as $raison)
                                    <option value="{{ $raison }}">{{ $raison }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-4 control-label">@lang("Quelle(s) est/sont la/les situation(s) pour laquelle/lesquelles vous estimez qu'il y a besoin d'apporter du soutien pour cet enfant, sa famille ou sa communauté?")</label>
                        <div class="col-xs-12 col-sm-8">
                            <select name="situationsPfte[]" id="situationsPfte" class="form-control select2-multi-select" multiple="multiple">
                                @foreach ($situationsPfte as $situation)
                                    <option value="{{ $situation }}">{{ $situation }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group row" id="raisonTravailAbusWrap">
                        <label class="col-sm-4 control-label">@lang("Pourquoi l'enfant exécute ces travaux ou est-il victime de cet abus ou de cette violation de ses droits ou besoins ?")</label>
                        <div class="col-xs-12 col-sm-8">
                            <select name="raisonTravailAbus[]" id="raisonTravailAbus" class="form-control select2-multi-select" multiple="multiple">
                                @foreach ($raisonsTravailAbus as $raison)
                                    <option value="{{ $raison }}">{{ $raison }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group row" id="autreRaisonTravailAbusWrap">
                        <label class="col-sm-4 control-label">@lang("Préciser cette autre raison pour laquelle il effectue un travail dangereux ou est victime d'abus?")</label>
                        <div class="col-xs-12 col-sm-8">
                            {!! Form::text('autreRaisonTravailAbus', null, ['class' => 'form-control']) !!}
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-4 control-label">@lang("AU NIVEAU DE L'ENFANT, quelles mesure(s) préconisez/riez-vous si l'enfant effectue un ou des travail/aux dangereux, ou est victime de PFTE ou si l'un de ses droits ou besoins n'est pas respecté, ou s'il subit une situation qu'il pourrait entraver sa scolarité?")</label>
                        <div class="col-xs-12 col-sm-8">
                            <select name="mesuresEnfant[]" class="form-control select2-multi-select mesures" multiple="multiple">
                                @foreach ($mesuresEnfant as $mesure)
                                    <option value="{{ $mesure }}">{{ $mesure }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 control-label">@lang("AU NIVEAU DU MENAGE DE L'ENFANT, quelles mesure(s) préconisez/riez-vous si l'enfant effectue un ou des travail/aux dangereux, ou est victime de PFTE ou si l'un de ses droits ou besoins n'est pas respecté")</label>
                        <div class="col-xs-12 col-sm-8">
                            <select name="mesuresMenage[]" class="form-control select2-multi-select mesures" multiple="multiple">
                                @foreach ($mesuresMenage as $mesure)
                                    <option value="{{ $mesure }}">{{ $mesure }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 control-label">@lang("AU NIVEAU DE LA COMMUNAUTE Où vit l'enfant, quelles mesure(s) préconisez/riez-vous si l'enfant effectue un ou des travail/aux dangereux, ou est victime de PFTE ou si l'un de ses droits ou besoins n'est pas respecté")</label>
                        <div class="col-xs-12 col-sm-8">
                            <select name="mesuresCommunaute[]" class="form-control select2-multi-select mesures" multiple="multiple">
                                @foreach ($mesuresCommunaute as $mesure)
                                    <option value="{{ $mesure }}">{{ $mesure }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group row" id="autreMesureWrap">
                        <label class="col-sm-4 control-label">@lang('Préciser cette autre mesure qui répondrait mieux au cas que vous avez identifié')</label>
                        <div class="col-xs-12 col-sm-8">
                            {!! Form::text('autreMesure', null, ['class' => 'form-control']) !!}
                        </div>
                    </div>

                    <hr class="panel-wide">
                    <legend class="legend-center"><h5 class="font-weight-bold text-decoration-underline">Sensibilisation de proximité</h5></legend>

                    <div class="form-group row">
                        <label class="col-sm-4 control-label">@lang('Quel(s) Thème(s) a/ont été abordé(s)?')</label>
                        <div class="col-xs-12 col-sm-8">
                            <select class="form-control select2-multi-select" name="themesSensibilisation[]" id="themesSensibilisation" multiple="multiple">
                                @foreach ($sensibilisationThemes as $theme)
                                    <option value="{{ $theme }}">{{ $theme }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group row" id="autreThemeSensibilisationWrap">
                        <label class="col-sm-4 control-label">@lang('Préciser cet/ces autre(s) thèmes abordés au cours de la sensibilisation')</label>
                        <div class="col-xs-12 col-sm-8">
                            {!! Form::text('autreThemeSensibilisation', null, ['class' => 'form-control']) !!}
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 control-label">@lang('Outils utilisés pour faire la sensibilisation')</label>
                        <div class="col-xs-12 col-sm-8">
                            <select class="form-control select2-multi-select" name="outilsSensibilisation[]" multiple="multiple">
                                @foreach ($sensibilisationOutils as $outil)
                                    <option value="{{ $outil }}">{{ $outil }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 control-label">@lang("Nombre d'adulte(s) hommes sensibilisé(s)")</label>
                        <div class="col-xs-12 col-sm-8">
                            {!! Form::number('nombreHommesSensibilises', 0, ['class' => 'form-control effectifSensibilisation']) !!}
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 control-label">@lang("Nombre d'adulte(s) femmes sensibilisée(s)")</label>
                        <div class="col-xs-12 col-sm-8">
                            {!! Form::number('nombreFemmesSensibilisees', 0, ['class' => 'form-control effectifSensibilisation']) !!}
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 control-label">@lang("Nombre d'enfant(s) garçons sensibilisé(s)")</label>
                        <div class="col-xs-12 col-sm-8">
                            {!! Form::number('nombreGarconsSensibilises', 0, ['class' => 'form-control effectifSensibilisation']) !!}
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 control-label">@lang("Nombre d'enfant(s) filles sensibilisée(s)")</label>
                        <div class="col-xs-12 col-sm-8">
                            {!! Form::number('nombreFillesSensibilisees', 0, ['class' => 'form-control effectifSensibilisation']) !!}
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 control-label">@lang('Total personnes sensibilisée(s)')</label>
                        <div class="col-xs-12 col-sm-8">
                            <input type="text" class="form-control" id="totalPersonnesSensibilisees" disabled value="0">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 control-label">@lang("Numéro de téléphone du/de la producteur/rice ou d'un proche")</label>
                        <div class="col-xs-12 col-sm-8">
                            {!! Form::text('telephoneProducteurSensibilisation', null, ['class' => 'form-control']) !!}
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 control-label">@lang('Veuillez prendre/télécharger photo de sensibilisation')</label>
                        <div class="col-xs-12 col-sm-8">
                            <input type="file" name="photoSensibilisation" accept="image/*" class="form-control dropify-fr">
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
            if ($.fn.select2) {
                $('.select2-multi-select, .select2-single-select').each(function() {
                    if (!$(this).hasClass('select2-hidden-accessible')) {
                        $(this).select2({ width: '100%' });
                    }
                });
            }

            function toggle($el, show) {
                if (show) { $el.show('slow'); } else { $el.hide(); $el.find('input, select, textarea').val(''); }
            }

            function selectedTextes($select) {
                return $select.find(':selected').map(function() { return $(this).text().trim(); }).get();
            }

            function recalculerTotalSensibilisation() {
                var total = 0;
                $('.effectifSensibilisation').each(function() { total += parseInt($(this).val()) || 0; });
                $('#totalPersonnesSensibilisees').val(total);
            }

            // Sélection de l'enfant -> pré-remplissage
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
            $('#raisonNeVitPasParents').on('change', function() {
                toggle($('#autreRaisonNeVitPasParentsWrap'), $(this).val() == 'Autres');
            });
            $('#situationScolaire').on('change', function() {
                var val = $(this).val();
                toggle($('#niveauScolaireWrap'), val == 'Scolarisé' || val == 'Déscolarisé');
                toggle($('#raisonNonScolarisationWrap'), val == 'Déscolarisé' || val == 'Jamais scolarisé');
                if (!(val == 'Déscolarisé' || val == 'Jamais scolarisé')) {
                    toggle($('#autreRaisonNonScolarisationWrap'), false);
                }
            });
            $('#raisonNonScolarisation').on('change', function() {
                toggle($('#autreRaisonNonScolarisationWrap'), selectedTextes($(this)).includes('Autre'));
            });
            $('#extraitNaissance').on('change', function() {
                toggle($('#raisonPasExtraitWrap'), $(this).val() == 'Non');
            });
            $('#situationsPfte').on('change', function() {
                var textes = selectedTextes($(this));
                var actif = textes.length > 0 && !(textes.length == 1 && textes[0] == 'Aucune');
                toggle($('#raisonTravailAbusWrap'), actif);
            });
            $('#raisonTravailAbus').on('change', function() {
                toggle($('#autreRaisonTravailAbusWrap'), selectedTextes($(this)).includes('Autre'));
            });
            $('.mesures').on('change', function() {
                var toutesLesTextes = [];
                $('.mesures').each(function() { toutesLesTextes = toutesLesTextes.concat(selectedTextes($(this))); });
                var contientAutre = toutesLesTextes.some(function(t) { return t.indexOf('Autre') !== -1 || t.indexOf('préciser') !== -1; });
                toggle($('#autreMesureWrap'), contientAutre);
            });

            toggle($('#autreThemeSensibilisationWrap'), false);
            $('#themesSensibilisation').on('change', function() {
                toggle($('#autreThemeSensibilisationWrap'), selectedTextes($(this)).includes('Autres thèmes'));
            });
            $('.effectifSensibilisation').on('input change', recalculerTotalSensibilisation);
            recalculerTotalSensibilisation();

            // Masquer initialement les blocs conditionnels
            toggle($('#autreLienParenteWrap'), false);
            toggle($('#autreRaisonNeVitPasParentsWrap'), false);
            toggle($('#niveauScolaireWrap'), false);
            toggle($('#raisonNonScolarisationWrap'), false);
            toggle($('#autreRaisonNonScolarisationWrap'), false);
            toggle($('#raisonPasExtraitWrap'), false);
            toggle($('#raisonTravailAbusWrap'), false);
            toggle($('#autreRaisonTravailAbusWrap'), false);
            toggle($('#autreMesureWrap'), false);

            $('#btnBrouillon').on('click', function() { $('#etatSoumission').val('Brouillon'); });
            $('#btnSoumettre').on('click', function() { $('#etatSoumission').val('Soumis'); });
        });
    </script>
@endpush
