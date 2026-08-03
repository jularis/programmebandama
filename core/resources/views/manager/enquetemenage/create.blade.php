@extends('manager.layouts.app')
@section('panel')
    <div class="row mb-none-30">
        <div class="col-lg-12 mb-30">
            <div class="card">
                <div class="card-body">
                    <div class="alert alert-success" role="alert">
                        <h4>Important !</h4>
                        <p class="mb-0">Enquête ménage : dépistage des Pires Formes de Travail des Enfants (PFTE). Renseignez d'abord le filtre/consentement, puis les caractéristiques du ménage et, le cas échéant, chaque enfant à charge.</p>
                    </div>

                    {!! Form::open([
                        'route' => ['manager.suivi.menage.store'],
                        'method' => 'POST',
                        'class' => 'form-horizontal',
                        'id' => 'formEnqueteMenage',
                        'enctype' => 'multipart/form-data',
                        'novalidate' => true,
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
                            {!! Form::text('typeEnquete', 'Menage', ['class' => 'form-control', 'readonly']) !!}
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
                            {!! Form::date('dateEnquete', date('Y-m-d'), ['class' => 'form-control', 'readonly']) !!}
                            <small class="form-text text-muted">La date est affectée automatiquement par le serveur.</small>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 control-label">@lang("Nom et Prénom(s) de l'enquêteur/trice")</label>
                        <div class="col-xs-12 col-sm-8">
                            {!! Form::text('nomEnqueteur', $nomEnqueteurConnecte, ['class' => 'form-control', 'readonly']) !!}
                        </div>
                    </div>
                    {!! Form::hidden('nombreEnfantsEnquetes', 0, ['id' => 'nombreEnfantsEnquetes']) !!}

                    <div class="form-group row">
                        <label class="col-sm-4 control-label">@lang('Latitude')</label>
                        <div class="col-xs-12 col-sm-8">
                            {!! Form::text('latitude', null, ['class' => 'form-control', 'required']) !!}
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 control-label">@lang('Longitude')</label>
                        <div class="col-xs-12 col-sm-8">
                            {!! Form::text('longitude', null, ['class' => 'form-control', 'required']) !!}
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
                                {!! Form::text('nomRepondant', null, ['class' => 'form-control', 'id' => 'nomRepondant']) !!}
                            </div>
                        </div>
                        <div class="form-group row" id="titreRepondantGroup">
                            <label class="col-sm-4 control-label">@lang('Quelle est le titre du répondant?')</label>
                            <div class="col-xs-12 col-sm-8">
                                {!! Form::select('titreRepondant', array_combine($titresRepondant, $titresRepondant), null, ['placeholder' => 'Selectionner une option', 'class' => 'form-control', 'id' => 'titreRepondant']) !!}
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
                                {!! Form::select('raisonIndisponibilite', array_combine($raisonsIndisponibilite, $raisonsIndisponibilite), null, ['placeholder' => 'Selectionner une option', 'class' => 'form-control', 'id' => 'raisonIndisponibilite', 'required']) !!}
                            </div>
                        </div>
                        <div id="blocReplanification" class="form-group row">
                            <label class="col-sm-4 control-label">@lang('Merci de re-planifier la visite chez ce producteur')</label>
                            <div class="col-xs-12 col-sm-8">
                                {!! Form::date('datePlanification', null, ['class' => 'form-control', 'required']) !!}
                            </div>
                        </div>
                        <div id="blocRefus">
                            <div class="form-group row">
                                <label class="col-sm-4 control-label">@lang("Pourquoi refusez-vous l'entretien?")</label>
                                <div class="col-xs-12 col-sm-8">
                                    <select class="form-control select2-multi-select" name="raisonRefus[]" id="raisonRefus" multiple="multiple" required>
                                        @foreach ($raisonsRefus as $raison)
                                            <option value="{{ $raison }}">{{ $raison }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row" id="blocAutreRaisonRefus">
                                <label class="col-sm-4 control-label">@lang("Veuillez préciser l'autre raison")</label>
                                <div class="col-xs-12 col-sm-8">
                                    {!! Form::text('autreRaisonRefus', null, ['class' => 'form-control', 'required']) !!}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="blocConsentement" class="form-group row">
                        <label class="col-sm-4 control-label">@lang("Le/la producteur/rice consent-il / t-elle à faire l'entretien?")</label>
                        <div class="col-xs-12 col-sm-8">
                            {!! Form::select('consentement', ['Oui' => 'Oui', 'Non' => 'Non'], null, ['class' => 'form-control', 'id' => 'consentement', 'required']) !!}
                        </div>
                    </div>

                    <div id="alertFinEnquete" class="alert alert-warning" style="display:none;">
                        Fin de l'enquête : l'entretien ne peut pas se poursuivre (producteur indisponible, refus ou non-consentement). Vous pouvez enregistrer ce constat.
                    </div>

                    <div id="suiteFormulaire">
                        <hr class="panel-wide">
                        <legend class="legend-center"><h5 class="font-weight-bold text-decoration-underline">Caractéristiques du ménage</h5></legend>

                        <div class="form-group row">
                            <label class="col-sm-4 control-label">@lang('Situation matrimoniale du/de la producteur/rice')</label>
                            <div class="col-xs-12 col-sm-8">
                                {!! Form::select('situationMatrimoniale', array_combine($situationsMatrimoniales, $situationsMatrimoniales), null, ['placeholder' => 'Selectionner une option', 'class' => 'form-control', 'required']) !!}
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-4 control-label">@lang("Nombre d'adulte(s) dans le ménage (y compris le/la producteur/rice) [18 ans et plus]")</label>
                            <div class="col-xs-12 col-sm-8">
                                {!! Form::number('nombreAdultes', 0, ['class' => 'form-control effectifMenage', 'required']) !!}
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-4 control-label">@lang("Nombre d'enfant(s) de 0 à 4 ans dans le ménage/la plantation")</label>
                            <div class="col-xs-12 col-sm-8">
                                {!! Form::number('nombreEnfants0a4', 0, ['class' => 'form-control effectifMenage', 'required']) !!}
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-4 control-label">@lang("Nombre d'enfant(s) 5 à 17 ans dans le ménage/la plantation")</label>
                            <div class="col-xs-12 col-sm-8">
                                {!! Form::number('nombreEnfants5a17', 0, ['class' => 'form-control effectifMenage', 'required']) !!}
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-4 control-label">@lang('Nombre total de personnes dans le ménage/la plantation (y compris le/la producteur/rice)')</label>
                            <div class="col-xs-12 col-sm-8">
                                <input type="text" class="form-control" id="totalPersonnesMenage" disabled value="0">
                            </div>
                        </div>

                        <hr class="panel-wide">
                        <legend class="legend-center"><h5 class="font-weight-bold text-decoration-underline">Enfant(s) du ménage</h5></legend>

                        <div class="form-group row">
                            <label class="col-sm-4 control-label">@lang('Le producteur a-t-il des enfants à charge ?')</label>
                            <div class="col-xs-12 col-sm-8">
                                {!! Form::select('aEnfantACharge', ['Oui' => 'Oui', 'Non' => 'Non'], null, ['class' => 'form-control', 'id' => 'aEnfantACharge', 'required']) !!}
                            </div>
                        </div>

                        <div id="blocEnfants">
                            <div class="form-group row">
                                <label class="col-sm-4 control-label">@lang("Combien d'enfants âgés de 5 à 17 ans a-t-il à sa charge?")</label>
                                <div class="col-xs-12 col-sm-8">
                                    {!! Form::number('nombreEnfantsACharge', null, ['class' => 'form-control', 'required']) !!}
                                </div>
                            </div>

                            <div id="enfants-container"></div>

                            <div class="form-group row">
                                <div class="col-xs-12 col-sm-8 offset-sm-4">
                                    <button type="button" id="btnAjouterEnfant" class="btn btn-outline--primary">
                                        <i class="las la-plus"></i> @lang('Ajouter un enfant')
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div id="blocSensibilisation">
                            <hr class="panel-wide">
                            <legend class="legend-center"><h5 class="font-weight-bold text-decoration-underline">Sensibilisation de proximité du ménage</h5></legend>

                            <div class="form-group row">
                                <label class="col-sm-4 control-label">@lang('Quel(s) Thème(s) a/ont été abordé(s)?')</label>
                                <div class="col-xs-12 col-sm-8">
                                    <select class="form-control select2-multi-select" name="themesSensibilisation[]" id="themesSensibilisation" multiple="multiple" required>
                                        @foreach ($sensibilisationThemes as $theme)
                                            <option value="{{ $theme }}">{{ $theme }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row" id="blocAutreThemeSensibilisation">
                                <label class="col-sm-4 control-label">@lang('Préciser cet/ces autre(s) thèmes abordés au cours de la sensibilisation')</label>
                                <div class="col-xs-12 col-sm-8">
                                    {!! Form::text('autreThemeSensibilisation', null, ['class' => 'form-control', 'required']) !!}
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-4 control-label">@lang('Outils utilisés pour faire la sensibilisation')</label>
                                <div class="col-xs-12 col-sm-8">
                                    <select class="form-control select2-multi-select" name="outilsSensibilisation[]" multiple="multiple" required>
                                        @foreach ($sensibilisationOutils as $outil)
                                            <option value="{{ $outil }}">{{ $outil }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-4 control-label">@lang("Nombre d'adulte(s) hommes sensibilisé(s)")</label>
                                <div class="col-xs-12 col-sm-8">
                                    {!! Form::number('nombreHommesSensibilises', 0, ['class' => 'form-control effectifSensibilisation', 'required']) !!}
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-4 control-label">@lang("Nombre d'adulte(s) femmes sensibilisée(s)")</label>
                                <div class="col-xs-12 col-sm-8">
                                    {!! Form::number('nombreFemmesSensibilisees', 0, ['class' => 'form-control effectifSensibilisation', 'required']) !!}
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-4 control-label">@lang("Nombre d'enfant(s) garçons sensibilisé(s)")</label>
                                <div class="col-xs-12 col-sm-8">
                                    {!! Form::number('nombreGarconsSensibilises', 0, ['class' => 'form-control effectifSensibilisation', 'required']) !!}
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-4 control-label">@lang("Nombre d'enfant(s) filles sensibilisée(s)")</label>
                                <div class="col-xs-12 col-sm-8">
                                    {!! Form::number('nombreFillesSensibilisees', 0, ['class' => 'form-control effectifSensibilisation', 'required']) !!}
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
                                    {!! Form::text('telephoneProducteurSensibilisation', null, ['class' => 'form-control', 'required']) !!}
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-4 control-label">@lang('Veuillez prendre/télécharger photo de sensibilisation')</label>
                                <div class="col-xs-12 col-sm-8">
                                    <input type="file" name="photoSensibilisation" accept="image/*" class="form-control dropify-fr" required>
                                </div>
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
        @include('manager.enquetemenage._enfant_fields', ['index' => '__INDEX__', 'num' => '__NUM__', 'enfant' => null])
    </script>
@endsection

@push('breadcrumb-plugins')
    <x-back route="{{ route('manager.suivi.menage.index') }}" />
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

            function toggle($el, show) {
                if (show) {
                    $el.show('slow');
                    $el.find('input, select, textarea').prop('disabled', false);
                } else {
                    $el.hide();
                    $el.find('input, select, textarea').prop('disabled', true).val('');
                    $el.find('select').trigger('change');
                }
            }

            function recalculerTotalMenage() {
                var total = 0;
                $('.effectifMenage').each(function() { total += parseInt($(this).val()) || 0; });
                $('#totalPersonnesMenage').val(total);
            }

            function recalculerTotalSensibilisation() {
                var total = 0;
                $('.effectifSensibilisation').each(function() { total += parseInt($(this).val()) || 0; });
                $('#totalPersonnesSensibilisees').val(total);
            }

            function selectedTextes($select) {
                return $select.find(':selected').map(function() { return $(this).text().trim(); }).get();
            }

            function getProducteurNom() {
                return $('#producteur option:selected').text().trim();
            }

            function majRepondant() {
                var est = $('#estProducteurRepondant').val();
                var nomProducteur = getProducteurNom();
                if (est === 'Oui') {
                    $('#nomRepondant').val(nomProducteur).prop('readonly', true).removeAttr('required');
                    $('#titreRepondantGroup').hide();
                    $('#titreRepondant').prop('disabled', true).val('').removeAttr('required');
                } else {
                    $('#nomRepondant').prop('readonly', false).attr('required', 'required');
                    $('#titreRepondantGroup').show();
                    $('#titreRepondant').prop('disabled', false).attr('required', 'required');
                }
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
                updateEnfantControls();
            }

            function updateEnfantControls() {
                var max = parseInt($('#nombreEnfantsEnquetes').val()) || 0;
                var current = $('#enfants-container .enfant-block').length;
                var disabled = max <= 0 || current >= max;
                $('#btnAjouterEnfant').prop('disabled', disabled).toggle(!disabled);
                if (disabled) {
                    $('#btnAjouterEnfant').attr('title', 'Nombre maximal d\'enfants atteint ou nombre invalide');
                } else {
                    $('#btnAjouterEnfant').removeAttr('title');
                }
            }

            function ajouterEnfant() {
                var html = $('#enfant-template').html();
                html = html.split('__INDEX__').join(enfantIndex).split('__NUM__').join(enfantIndex + 1);
                var $block = $(html);
                $('#enfants-container').append($block);
                initSelect2($block);
                enfantIndex++;
                renumeroterEnfants();
            }

            function ensureSubmissionButtonsEnabled() {
                $('#btnBrouillon, #btnSoumettre').prop('disabled', false).removeAttr('disabled');
            }

            function submitFormWithState(state) {
                $('#etatSoumission').val(state);
                ensureSubmissionButtonsEnabled();
                document.getElementById('formEnqueteMenage').submit();
            }

            $('#btnAjouterEnfant').on('click', function(e) {
                e.preventDefault();
                ajouterEnfant();
            });

            $('#enfants-container').on('click', '.btnRetirerEnfant', function() {
                $(this).closest('.enfant-block').remove();
                renumeroterEnfants();
            });

            $('#nombreEnfantsEnquetes').on('input change', updateEnfantControls);
            $('#nombreEnfants5a17').on('input change', function() {
                $('#nombreEnfantsEnquetes').val($(this).val() || 0).trigger('change');
                majBlocEnfants();
            }).trigger('change');

            // Identification producteur -> affichage sexe/code
            $('#producteur').on('change', function() {
                var opt = $(this).find(':selected');
                $('#sexeProducteurDisplay').val(opt.data('sexe') || '');
                $('#codeProducteurDisplay').val(opt.data('code') || '');
                if ($('#estProducteurRepondant').val() === 'Oui') {
                    majRepondant();
                }
            });

            // Répondant
            $('#estProducteurRepondant').on('change', majRepondant);
            majRepondant();

            // Disponibilité / consentement / fin d'enquête
            function majSuiteFormulaire() {
                var disponible = $('#producteurDisponible').val();
                var consentement = $('#consentement').val();

                toggle($('#blocIndisponible'), disponible == 'Non');
                $('#blocConsentement').toggle(disponible == 'Oui');

                var poursuivre = (disponible == 'Oui' && consentement == 'Oui');
                $('#suiteFormulaire').toggle(poursuivre);
                $('#alertFinEnquete').toggle(disponible == 'Non' || (disponible == 'Oui' && consentement == 'Non'));
            }

            $('#producteurDisponible').on('change', function() {
                if ($(this).val() != 'Non') {
                    $('#raisonIndisponibilite').val('').trigger('change').prop('disabled', true).removeAttr('required');
                } else {
                    $('#raisonIndisponibilite').prop('disabled', false).attr('required', 'required');
                }
                majSuiteFormulaire();
            });
            $('#consentement').on('change', majSuiteFormulaire);
            $('#producteurDisponible').trigger('change');
            majSuiteFormulaire();

            $('#raisonIndisponibilite').on('change', function() {
                var raison = $(this).val();
                toggle($('#blocReplanification'), raison == 'Temporairement absent');
                toggle($('#blocRefus'), raison == 'Refus');
            });
            $('#raisonRefus').on('change', function() {
                toggle($('#blocAutreRaisonRefus'), selectedTextes($(this)).includes('Autre raison'));
            });

            // Effectifs ménage
            $('.effectifMenage').on('input change', recalculerTotalMenage);
            recalculerTotalMenage();

            // Enfant(s) à charge
            function majBlocEnfants() {
                var count517 = parseInt($('#nombreEnfants5a17').val()) || 0;
                if (count517 === 0) {
                    $('#aEnfantACharge').val('Non');
                    $('#nombreEnfantsACharge').val(0);
                    $('#enfants-container').empty();
                }

                var aCharge = $('#aEnfantACharge').val() === 'Oui' && count517 > 0;
                if (!aCharge) {
                    $('#enfants-container').empty();
                    $('#nombreEnfantsACharge').val(0);
                }

                toggle($('#blocEnfants'), aCharge);
                $('#blocSensibilisation').toggle(aCharge);
                updateEnfantControls();
            }
            $('#aEnfantACharge').on('change', majBlocEnfants);
            majBlocEnfants();

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
            $('#enfants-container').on('change', '.presentDisponible', function() {
                var $block = $(this).closest('.enfant-block');
                toggle($block.find('.raisonAbsentWrap'), $(this).val() !== '1');
            });
            $('#enfants-container .presentDisponible').trigger('change');
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

            // Sensibilisation
            toggle($('#blocAutreThemeSensibilisation'), false);
            $('#themesSensibilisation').on('change', function() {
                toggle($('#blocAutreThemeSensibilisation'), selectedTextes($(this)).includes('Autres thèmes'));
            });
            $('.effectifSensibilisation').on('input change', recalculerTotalSensibilisation);
            recalculerTotalSensibilisation();

            // Masquer initialement les blocs conditionnels enfant (au cas où le navigateur pré-remplit)
            toggle($('#blocReplanification'), false);
            toggle($('#blocRefus'), false);

            // Soumission : brouillon vs soumis
            ensureSubmissionButtonsEnabled();
            $('#btnBrouillon').on('click', function(e) {
                e.preventDefault();
                submitFormWithState('Brouillon');
            });
            $('#btnSoumettre').on('click', function(e) {
                e.preventDefault();
                submitFormWithState('Soumis');
            });
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
