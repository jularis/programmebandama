@extends('manager.layouts.app')
@section('panel')
    <div class="row mb-none-30">
        <div class="col-lg-12 mb-30">
            <div class="card">
                <div class="card-body">
                    <div class="alert alert-success" role="alert">
                        <h4>Important !</h4>
                        <p class="mb-0">Visite de plantation : dépistage des Pires Formes de Travail des Enfants (PFTE). Renseignez d'abord le filtre/consentement, puis les caractéristiques de la plantation et, le cas échéant, chaque enfant trouvé sur place.</p>
                    </div>

                    {!! Form::open([
                        'route' => ['manager.suivi.visiteplantation.store'],
                        'method' => 'POST',
                        'class' => 'form-horizontal',
                        'id' => 'formVisitePlantation',
                        'enctype' => 'multipart/form-data',
                    ]) !!}

                    <input type="hidden" name="etatSoumission" id="etatSoumission" value="Soumis">

                    <legend class="legend-center"><h5 class="font-weight-bold text-decoration-underline">Identification</h5></legend>

                    <div class="form-group row">
                        <label class="col-sm-4 control-label">@lang('Pourquoi faites-vous cet interview?')</label>
                        <div class="col-xs-12 col-sm-8">
                            {!! Form::text('raisonInterview', 'Enquête initiale', ['class' => 'form-control', 'readonly']) !!}
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 control-label">@lang("Type d'enquête")</label>
                        <div class="col-xs-12 col-sm-8">
                            {!! Form::text('typeEnquete', 'visite plantation', ['class' => 'form-control', 'readonly']) !!}
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-4 control-label">@lang('Section')</label>
                        <div class="col-xs-12 col-sm-8">
                            <select class="form-control" name="section" id="section" required>
                                <option value="">@lang('Selectionner une option')</option>
                                @foreach ($sections as $section)
                                    <option value="{{ $section->id }}">{{ $section->libelle }} ({{ $section->region }} - {{ $section->departement }})</option>
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
                                    <option value="{{ $localite->id }}" data-chained="{{ $localite->section_id }}">{{ $localite->nom }} @if($localite->sousprefecture) ({{ $localite->sousprefecture }}) @endif</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-4 control-label">@lang('Producteur/rice')</label>
                        <div class="col-xs-12 col-sm-8">
                            <select class="form-control" name="producteur" id="producteur" required>
                                <option value="">@lang('Selectionner une option')</option>
                                @foreach ($producteurs as $producteur)
                                    <option value="{{ $producteur->id }}" data-chained="{{ $producteur->localite_id }}" data-sexe="{{ $producteur->sexe }}" data-code="{{ $producteur->codeProdapp }}">
                                        {{ stripslashes($producteur->nom) }} {{ stripslashes($producteur->prenoms) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-4 control-label">@lang('Sexe du/de la producteur/rice')</label>
                        <div class="col-xs-12 col-sm-8">
                            <input type="text" class="form-control" id="sexeProducteurDisplay" disabled>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 control-label">@lang('Code du/de la producteur/rice')</label>
                        <div class="col-xs-12 col-sm-8">
                            <input type="text" class="form-control" id="codeProducteurDisplay" disabled>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-4 control-label">@lang("Date de réalisation de l'enquête")</label>
                        <div class="col-xs-12 col-sm-8">
                            {!! Form::date('dateEnquete', now()->toDateString(), ['class' => 'form-control', 'readonly']) !!}
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 control-label">@lang('Jour de cette visite plantation')</label>
                        <div class="col-xs-12 col-sm-8">
                            {!! Form::select('jourVisite', ['Lundi' => 'Lundi', 'Mardi' => 'Mardi', 'Mercredi' => 'Mercredi', 'Jeudi' => 'Jeudi', 'Vendredi' => 'Vendredi', 'Samedi' => 'Samedi', 'Dimanche' => 'Dimanche'], null, ['class' => 'form-control', 'placeholder' => 'Selectionner une option', 'required']) !!}
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 control-label">@lang("Nom et Prénom(s) de l'enquêteur/trice")</label>
                        <div class="col-xs-12 col-sm-8">
                            {!! Form::text('nomEnqueteur', $nomEnqueteurConnecte ?? null, ['class' => 'form-control', 'readonly']) !!}
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-4 control-label">@lang('Localisation GPS de la plantation')</label>
                        <div class="col-xs-12 col-sm-8">
                            {!! Form::text('latitude', null, ['class' => 'form-control', 'placeholder' => 'Latitude', 'required']) !!}
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 control-label"></label>
                        <div class="col-xs-12 col-sm-8">
                            {!! Form::text('longitude', null, ['class' => 'form-control', 'placeholder' => 'Longitude', 'required']) !!}
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="control-label col-sm-4"></label>
                        <div class="col-xs-12 col-sm-8">
                            <p id="status"></p>
                            <a href="javascript:void(0)" id="find-me" class="btn btn--info">Obtenir les coordonnées GPS</a>
                        </div>
                    </div>
                    <input type="hidden" name="altitude">
                    <input type="hidden" name="precisionGps">

                    <hr class="panel-wide">
                    <legend class="legend-center"><h5 class="font-weight-bold text-decoration-underline">Filtre / Consentement</h5></legend>

                    <div class="form-group row">
                        <label class="col-sm-4 control-label">@lang('Le répondant est-il le/la producteur/rice lui/elle-même?')</label>
                        <div class="col-xs-12 col-sm-8">
                            {!! Form::select('estProducteurRepondant', ['Oui' => 'Oui', 'Non' => 'Non'], null, ['class' => 'form-control', 'id' => 'estProducteurRepondant', 'required']) !!}
                        </div>
                    </div>
                    <div id="blocRepondant">
                        <div class="form-group row">
                            <label class="col-sm-4 control-label">@lang('Quel est le nom et prénoms du répondant?')</label>
                            <div class="col-xs-12 col-sm-8">
                                {!! Form::text('nomRepondant', null, ['class' => 'form-control js-required-visible', 'id' => 'nomRepondant']) !!}
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-4 control-label">@lang('Quelle est le titre du répondant?')</label>
                            <div class="col-xs-12 col-sm-8">
                                {!! Form::select('titreRepondant', array_combine($titresRepondant, $titresRepondant), null, ['placeholder' => 'Selectionner une option', 'class' => 'form-control js-required-visible']) !!}
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-4 control-label">@lang('Le/la producteur/rice est-il/elle disponible ?')</label>
                        <div class="col-xs-12 col-sm-8">
                            {!! Form::select('producteurDisponible', ['Oui' => 'Oui', 'Non' => 'Non'], null, ['class' => 'form-control', 'id' => 'producteurDisponible', 'required']) !!}
                        </div>
                    </div>

                    <div id="blocIndisponible">
                        <div class="form-group row">
                            <label class="col-sm-4 control-label">@lang('Si non, pour quelle raison?')</label>
                            <div class="col-xs-12 col-sm-8">
                                {!! Form::select('raisonIndisponibilite', array_combine($raisonsIndisponibilite, $raisonsIndisponibilite), null, ['placeholder' => 'Selectionner une option', 'class' => 'form-control js-required-visible', 'id' => 'raisonIndisponibilite']) !!}
                            </div>
                        </div>
                        <div id="blocReplanification" class="form-group row">
                            <label class="col-sm-4 control-label">@lang('Merci de re-planifier la visite chez ce producteur')</label>
                            <div class="col-xs-12 col-sm-8">
                                {!! Form::date('datePlanification', null, ['class' => 'form-control js-required-visible']) !!}
                            </div>
                        </div>
                        <div id="blocRefus">
                            <div class="form-group row">
                                <label class="col-sm-4 control-label">@lang("Pourquoi refusez-vous l'entretien?")</label>
                                <div class="col-xs-12 col-sm-8">
                                    <select class="form-control select2-multi-select js-required-visible" name="raisonRefus[]" id="raisonRefus" multiple="multiple">
                                        @foreach ($raisonsRefus as $raison)
                                            <option value="{{ $raison }}">{{ $raison }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row" id="blocAutreRaisonRefus">
                                <label class="col-sm-4 control-label">@lang("Veuillez préciser l'autre raison")</label>
                                <div class="col-xs-12 col-sm-8">
                                    {!! Form::text('autreRaisonRefus', null, ['class' => 'form-control js-required-visible']) !!}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="blocConsentement" class="form-group row">
                        <label class="col-sm-4 control-label">@lang("Le/la producteur/rice consent-il / t-elle à faire l'entretien?")</label>
                        <div class="col-xs-12 col-sm-8">
                            {!! Form::select('consentement', ['Oui' => 'Oui', 'Non' => 'Non'], null, ['class' => 'form-control js-required-visible', 'id' => 'consentement']) !!}
                        </div>
                    </div>

                    <div id="alertFinEnquete" class="alert alert-warning" style="display:none;">
                        Fin de l'enquête : l'entretien ne peut pas se poursuivre (producteur indisponible, refus ou non-consentement). Vous pouvez enregistrer ce constat.
                    </div>

                    <div id="suiteFormulaire">
                        <hr class="panel-wide">
                        <legend class="legend-center"><h5 class="font-weight-bold text-decoration-underline">Caractéristiques de la plantation</h5></legend>

                        <div class="form-group row">
                            <label class="col-sm-4 control-label">@lang('Quelle est la superficie de la plantation?')</label>
                            <div class="col-xs-12 col-sm-8">
                                {!! Form::number('superficiePlantation', null, ['class' => 'form-control js-required-visible', 'step' => '0.01', 'required']) !!}
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-4 control-label">@lang('Combien de manœuvres permanents travaillent habituellement dans la plantation')</label>
                            <div class="col-xs-12 col-sm-8">
                                {!! Form::number('nombreManoeuvresPermanents', 0, ['class' => 'form-control js-required-visible', 'required']) !!}
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-4 control-label">@lang('Parmi ces manœuvres permanents, y a-t-il des personnes de moins de 18 ans?')</label>
                            <div class="col-xs-12 col-sm-8">
                                {!! Form::select('manoeuvresPermanentsMoins18', ['Oui' => 'Oui', 'Non' => 'Non'], null, ['placeholder' => 'Selectionner une option', 'class' => 'form-control js-required-visible', 'required']) !!}
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-4 control-label">@lang('Combien de manœuvres journaliers travaillent souvent dans la plantation?')</label>
                            <div class="col-xs-12 col-sm-8">
                                {!! Form::number('nombreManoeuvresJournaliers', 0, ['class' => 'form-control js-required-visible', 'required']) !!}
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-4 control-label">@lang('Parmi ces manœuvres journaliers, y a-t-il souvent des personnes de moins de 18 ans?')</label>
                            <div class="col-xs-12 col-sm-8">
                                {!! Form::select('manoeuvresJournaliersMoins18', ['Oui' => 'Oui', 'Non' => 'Non'], null, ['placeholder' => 'Selectionner une option', 'class' => 'form-control js-required-visible', 'required']) !!}
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-4 control-label">@lang("Nombre d'enfant(s) de 0 à 4 ans dans le ménage/la plantation")</label>
                            <div class="col-xs-12 col-sm-8">
                                {!! Form::number('nombreEnfants0a4', 0, ['class' => 'form-control js-required-visible', 'required']) !!}
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-4 control-label">@lang("Nombre d'enfant(s) 5 à 17 ans dans le ménage/la plantation")</label>
                            <div class="col-xs-12 col-sm-8">
                                {!! Form::number('nombreEnfants5a17', 0, ['class' => 'form-control js-required-visible', 'required']) !!}
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-4 control-label">@lang('Nombre de personnes trouvées dans la plantation lors de la visite (manœuvres, le/la producteur/rice y compris membre du ménage du planteur qu\'il soit adulte ou enfant)')</label>
                            <div class="col-xs-12 col-sm-8">
                                {!! Form::number('nombrePersonnesTrouvees', 0, ['class' => 'form-control js-required-visible', 'required']) !!}
                            </div>
                        </div>

                        <hr class="panel-wide">
                        <legend class="legend-center"><h5 class="font-weight-bold text-decoration-underline">Enfant(s) trouvé(s) dans la plantation</h5></legend>

                        <div id="enfants-container"></div>

                        <div class="form-group row" id="blocAjouterEnfant" style="display:none;">
                            <div class="col-xs-12 col-sm-8 offset-sm-4">
                                <button type="button" id="btnAjouterEnfant" class="btn btn-outline--primary">
                                    <i class="las la-plus"></i> @lang('Ajouter un enfant')
                                </button>
                            </div>
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

    <script type="text/template" id="enfant-template">
        @include('manager.visiteplantation._enfant_fields', ['index' => '__INDEX__', 'num' => '__NUM__', 'enfant' => null])
    </script>
@endsection

@push('breadcrumb-plugins')
    <x-back route="{{ route('manager.suivi.visiteplantation.index') }}" />
@endpush

@push('style')
    <style type="text/css">
        .fieldset-like {
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 10px;
            margin-bottom: 20px;
            text-align: left;
        }

        .legend-center {
            text-align: center;
            margin-bottom: 10px;
        }
    </style>
@endpush

@push('script')
    <script type="text/javascript">
        $(document).ready(function() {
            var enfantIndex = 0;

            if ($.fn.select2) {
                $('.select2-multi-select').each(function() {
                    if (!$(this).hasClass('select2-hidden-accessible')) {
                        $(this).select2({ width: '100%' });
                    }
                });
            }

            function refreshRequired() {
                $('.js-required-visible').each(function() {
                    var $field = $(this);
                    if ($field.hasClass('select2-multi-select')) {
                        $field.prop('required', false);
                        return;
                    }
                    var $group = $field.closest('.form-group');
                    var visible = $group.length ? $group.is(':visible') : $field.is(':visible');
                    $field.prop('required', visible && !$field.prop('disabled'));
                });
            }

            function toggle($el, show) {
                if (show) {
                    $el.show('slow', refreshRequired);
                } else {
                    $el.hide();
                    $el.find('input, select, textarea').val('').trigger('change');
                    refreshRequired();
                }
            }

            function selectedTextes($select) {
                return $select.find(':selected').map(function() { return $(this).text().trim(); }).get();
            }

            function initSelect2($scope) {
                if ($.fn.select2) {
                    $scope.find('.select2-multi-select').select2({ width: '100%' });
                }
            }

            function renumeroterEnfants() {
                $('#enfants-container .enfant-block').each(function(i) {
                    $(this).find('.enfant-numero').first().text(i + 1);
                });
            }

            function ajouterEnfant() {
                var html = $('#enfant-template').html();
                html = html.split('__INDEX__').join(enfantIndex).split('__NUM__').join(enfantIndex + 1);
                var $block = $(html);
                $('#enfants-container').append($block);
                initSelect2($block);
                refreshRequired();
                enfantIndex++;
                renumeroterEnfants();
            }

            function synchroniserEnfantsDepuisNombre() {
                var cible = Math.max(0, parseInt($('#nombreEnfants5a17').val()) || 0);
                var actuel = $('#enfants-container .enfant-block').length;

                if (cible > actuel) {
                    while ($('#enfants-container .enfant-block').length < cible) {
                        ajouterEnfant();
                    }
                } else if (cible < actuel) {
                    while ($('#enfants-container .enfant-block').length > cible) {
                        $('#enfants-container .enfant-block').last().remove();
                    }
                }

                var aDesEnfants = cible > 0;
                $('#enfants-container .enfant-block').find('input, select, textarea').each(function() {
                    $(this).prop('required', aDesEnfants);
                });

                renumeroterEnfants();
                refreshRequired();
            }

            $('#btnAjouterEnfant').on('click', function(e) {
                e.preventDefault();
                ajouterEnfant();
            });

            $('#enfants-container').on('click', '.btnRetirerEnfant', function() {
                $(this).closest('.enfant-block').remove();
                renumeroterEnfants();
            });

            // Identification producteur -> affichage sexe/code
            $('#producteur').on('change', function() {
                var opt = $(this).find(':selected');
                $('#sexeProducteurDisplay').val(opt.data('sexe') || '');
                $('#codeProducteurDisplay').val(opt.data('code') || '');
            });

            $('#nombreEnfants5a17').on('input change', synchroniserEnfantsDepuisNombre);
            synchroniserEnfantsDepuisNombre();

            // Répondant
            function majNomRepondant() {
                var estProducteur = $('#estProducteurRepondant').val() == 'Oui';
                var nomProducteur = $('#producteur').find(':selected').text().trim();

                $('#nomRepondant').prop('readonly', estProducteur);
                toggle($('#blocRepondant'), !estProducteur);

                if (estProducteur) {
                    $('#nomRepondant').val(nomProducteur);
                }
            }

            $('#estProducteurRepondant').on('change', majNomRepondant);
            $('#producteur').on('change', majNomRepondant);
            majNomRepondant();

            // Disponibilité / consentement / fin d'enquête
            function majSuiteFormulaire() {
                var disponible = $('#producteurDisponible').val();
                var consentement = $('#consentement').val();

                toggle($('#blocIndisponible'), disponible == 'Non');
                $('#blocConsentement').toggle(disponible == 'Oui');

                if (disponible == 'Oui') {
                    $('#raisonIndisponibilite').val('N/A').trigger('change');
                } else if (disponible == 'Non') {
                    $('#raisonIndisponibilite').val('').trigger('change');
                }

                var poursuivre = (disponible == 'Oui' && consentement == 'Oui');
                $('#suiteFormulaire').toggle(poursuivre);
                $('#alertFinEnquete').toggle(disponible == 'Non' || (disponible == 'Oui' && consentement == 'Non'));
                refreshRequired();
            }

            $('#producteurDisponible').on('change', majSuiteFormulaire);
            $('#consentement').on('change', majSuiteFormulaire);
            majSuiteFormulaire();

            $('#raisonIndisponibilite').on('change', function() {
                var raison = $(this).val();
                toggle($('#blocReplanification'), raison == 'Temporairement absent');
                toggle($('#blocRefus'), raison == 'Refus');
            });
            $('#raisonRefus').on('change', function() {
                toggle($('#blocAutreRaisonRefus'), selectedTextes($(this)).includes('Autre raison'));
            });

            // Délégation des conditions internes à chaque bloc enfant
            $('#enfants-container').on('change', '.lienParente', function() {
                toggle($(this).closest('.enfant-block').find('.autreLienParenteWrap'), $(this).val() == 'Autre');
            });
            $('#enfants-container').on('change', '.raisonNeVitPasParents', function() {
                toggle($(this).closest('.enfant-block').find('.autreRaisonNeVitPasParentsWrap'), $(this).val() == 'Autres');
            });
            $('#enfants-container').on('change', '.situationScolaire', function() {
                var val = $(this).val();
                var $block = $(this).closest('.enfant-block');
                toggle($block.find('.niveauScolaireWrap'), val == 'Scolarisé' || val == 'Déscolarisé');
                toggle($block.find('.raisonNonScolarisationWrap'), val == 'Déscolarisé' || val == 'Jamais scolarisé');
                if (!(val == 'Déscolarisé' || val == 'Jamais scolarisé')) {
                    toggle($block.find('.autreRaisonNonScolarisationWrap'), false);
                }
            });
            $('#enfants-container').on('change', '.raisonNonScolarisation', function() {
                toggle($(this).closest('.enfant-block').find('.autreRaisonNonScolarisationWrap'), selectedTextes($(this)).includes('Autre'));
            });
            $('#enfants-container').on('change', '.extraitNaissance', function() {
                toggle($(this).closest('.enfant-block').find('.raisonPasExtraitWrap'), $(this).val() == 'Non');
            });
            $('#enfants-container').on('change', '.situationsPfte', function() {
                var textes = selectedTextes($(this));
                var actif = textes.length > 0 && !(textes.length == 1 && textes[0] == 'Aucune');
                toggle($(this).closest('.enfant-block').find('.raisonTravailAbusWrap'), actif);
            });
            $('#enfants-container').on('change', '.raisonTravailAbus', function() {
                toggle($(this).closest('.enfant-block').find('.autreRaisonTravailAbusWrap'), selectedTextes($(this)).includes('Autre'));
            });
            $('#enfants-container').on('change', '.mesures', function() {
                var $block = $(this).closest('.enfant-block');
                var toutesLesTextes = [];
                $block.find('.mesures').each(function() { toutesLesTextes = toutesLesTextes.concat(selectedTextes($(this))); });
                var contientAutre = toutesLesTextes.some(function(t) { return t.indexOf('Autre') !== -1 || t.indexOf('préciser') !== -1; });
                toggle($block.find('.autreMesureWrap'), contientAutre);
            });

            // Masquer initialement les blocs conditionnels (au cas où le navigateur pré-remplit)
            toggle($('#blocReplanification'), false);
            toggle($('#blocRefus'), false);
            refreshRequired();

            // Soumission : brouillon vs soumis
            $('#btnBrouillon').on('click', function() { $('#etatSoumission').val('Brouillon'); });
            $('#btnSoumettre').on('click', function() { $('#etatSoumission').val('Soumis'); });
        });

        $('#localite').chained('#section');
        $('#producteur').chained('#localite');

        function geoFindMe() {
            const status = document.querySelector('#status');
            function success(position) {
                $('input[name=longitude]').val(position.coords.longitude);
                $('input[name=latitude]').val(position.coords.latitude);
                $('input[name=altitude]').val(position.coords.altitude);
                $('input[name=precisionGps]').val(position.coords.accuracy);
                $('input[name=longitude], input[name=latitude]').attr('readonly', 'readonly');
            }
            function error() { status.textContent = 'Unable to retrieve your location'; }
            if (!navigator.geolocation) {
                status.textContent = 'Geolocation is not supported by your browser';
            } else {
                navigator.geolocation.getCurrentPosition(success, error);
            }
        }
        document.querySelector('#find-me').addEventListener('click', geoFindMe);
    </script>
@endpush
