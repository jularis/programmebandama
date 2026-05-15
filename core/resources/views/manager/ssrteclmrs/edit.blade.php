@extends('manager.layouts.app')
@section('panel')
    <div class="row mb-none-30">
        <div class="col-lg-12 mb-30">
            <div class="card">
                <div class="card-body">
                    {!! Form::model($ssrteclmrs, [
                        'method' => 'POST',
                        'route' => ['manager.suivi.ssrteclmrs.store', $ssrteclmrs->id],
                        'class' => 'form-horizontal',
                        'id' => 'flocal',
                        'enctype' => 'multipart/form-data',
                    ]) !!}
                    <input type="hidden" name="id" value="{{ $ssrteclmrs->id }}">

                    <div class="form-group row">
                        <label class="col-sm-4 control-label">@lang('Selectionner une section')</label>
                        <div class="col-xs-12 col-sm-8">
                            <select class="form-control" name="section" id="section" required>
                                <option value="">@lang('Selectionner une option')</option>
                                @foreach ($sections as $section)
                                    <option value="{{ $section->id }}" @selected($section->id == $ssrteclmrs->producteur->localite->section->id)>
                                        {{ $section->libelle }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-4 control-label">@lang('Selectionner une localite')</label>
                        <div class="col-xs-12 col-sm-8">
                            <select class="form-control" name="localite" id="localite" required>
                                <option value="">@lang('Selectionner une option')</option>
                                @foreach ($localites as $localite)
                                    <option value="{{ $localite->id }}" data-chained="{{ $localite->section->id }}"
                                        @selected($localite->id == $ssrteclmrs->producteur->localite->id)>
                                        {{ $localite->nom }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 control-label">@lang('Selectionner un producteur')</label>
                        <div class="col-xs-12 col-sm-8">
                            <select class="form-control" name="producteur" id="producteur_id" required>
                                <option value="">@lang('Selectionner une option')</option>
                                @foreach ($producteurs as $producteur)
                                    <option value="{{ $producteur->id }}" data-chained="{{ $producteur->localite->id }}"
                                        @selected($producteur->id == $ssrteclmrs->producteur->id)>
                                        {{ stripslashes($producteur->nom) }} {{ stripslashes($producteur->prenoms) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <hr class="panel-wide">
                    <div class="form-group row">
                        <?php
                        
                        use Illuminate\Support\Arr;
                        
                        echo Form::label(__('Nom du membre'), null, ['class' => 'col-sm-4 control-label']); ?>
                        <div class="col-xs-12 col-sm-8">
                            <?php echo Form::text('nomMembre', null, ['placeholder' => __('Nom du membre'), 'class' => 'form-control nomMembre', 'id' => 'nomMembre', 'required']); ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <?php echo Form::label(__('Prenom(s) du membre'), null, ['class' => 'col-sm-4 control-label']); ?>
                        <div class="col-xs-12 col-sm-8">
                            <?php echo Form::text('prenomMembre', null, ['placeholder' => __('Prenom(s) du membre'), 'class' => 'form-control prenomMembre', 'id' => 'prenomMembre', 'required']); ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <?php echo Form::label(__('Genre du membre'), null, ['class' => 'col-sm-4 control-label']); ?>
                        <div class="col-xs-12 col-sm-8">
                            <?php echo Form::select('sexeMembre', ['Homme' => __('Homme'), 'Femme' => __('Femme')], null, ['class' => 'form-control', 'required']); ?>

                        </div>
                    </div>

                    <div class="form-group row">
                        <?php echo Form::label(__('Date de naissance'), null, ['class' => 'col-sm-4 control-label']); ?>
                        <div class="col-xs-12 col-sm-8">
                            <?php echo Form::date('datenaissMembre', null, ['class' => 'form-control datenaissMembre naiss', 'id' => 'datenais', 'required']); ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <?php echo Form::label(__('Lien de parenté avec le producteur'), null, ['class' => 'col-sm-4 control-label']); ?>
                        <div class="col-xs-12 col-sm-8">
                            <?php echo Form::select('lienParente', $lienParente, null, ['placeholder' => __('Selectionner une option'), 'class' => 'form-control lienParente', 'id' => 'lienParente', 'required']); ?>
                        </div>
                    </div>
                    <div class="form-group row" id="autreLienParente">
                        <?php echo Form::label(__('Autre lien de parenté'), null, ['class' => 'col-sm-4 control-label']); ?>
                        <div class="col-xs-12 col-sm-8">
                            <?php echo Form::text('autreLienParente', null, ['placeholder' => '...', 'class' => 'form-control autreLienParente']); ?>
                        </div>
                    </div>
                    <hr class="panel-wide">

                    <div class="form-group row">
                        <?php echo Form::label(__('Va-t-il à l’école ?'), null, ['class' => 'col-sm-4 control-label']); ?>
                        <div class="col-xs-12 col-sm-8">
                            <?php echo Form::select('frequente', ['non' => __('non'), 'oui' => __('oui')], null, ['class' => 'form-control frequente', 'required']); ?>
                        </div>
                    </div>
                    <div id="frequentation">
                        <div class="form-group row">
                            <?php echo Form::label(__('Quel Est Ton Niveau D\'étude ?'), null, ['class' => 'col-sm-4 control-label']); ?>
                            <div class="col-xs-12 col-sm-8">
                                <select class="form-control" name="niveauEtude" id="niveauEtude">
                                    <option value="">@lang('Selectionner une option')</option>
                                    @foreach ($niveauEtude as $niveau)
                                        <option value="{{ $niveau->nom }}" @selected($niveau->nom == $ssrteclmrs->niveauEtude)>
                                            {{ $niveau->nom }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group row" id="classe">
                            <label class="col-sm-4 control-label">@lang('Classe')</label>
                            <div class="col-xs-12 col-sm-8">

                                <select class="form-control" name="classe" id="classeEtude">
                                    <option value="">@lang('Selectionner une option')</option>
                                    @foreach ($classes as $classe)
                                        <option value="{{ $classe->nom }}" data-chained="{{ $classe->niveau->nom }}"
                                            @selected($classe->nom == $ssrteclmrs->classe)>
                                            {{ $classe->nom }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>


                        <div class="form-group row">
                            <?php echo Form::label(__('Ton école est- elle située dans le village ?'), null, ['class' => 'col-sm-4 control-label']); ?>
                            <div class="col-xs-12 col-sm-8">
                                <?php echo Form::select('ecoleVillage', ['non' => __('non'), 'oui' => __('oui')], null, ['class' => 'form-control ecoleVillage']); ?>
                            </div>
                        </div>

                        <div class="form-group row" id="distanceEcole">
                            <?php echo Form::label(__('A Combien de Km du village est-elle située ?'), null, ['class' => 'col-sm-4 control-label']); ?>
                            <div class="col-xs-12 col-sm-8">
                                <?php echo Form::number('distanceEcole', null, ['class' => 'form-control distanceEcole']); ?>
                            </div>
                        </div>

                        <div class="form-group row">
                            <?php echo Form::label(__('Donne le nom de cette école'), null, ['class' => 'col-sm-4 control-label']); ?>
                            <div class="col-xs-12 col-sm-8">
                                <?php echo Form::text('nomEcole', null, ['class' => 'form-control nomEcole']); ?>
                            </div>
                        </div>

                        <div class="form-group row">
                            <?php echo Form::label(__('Comment te rends-tu dans cette école ?'), null, ['class' => 'col-sm-4 control-label']); ?>
                            <div class="col-xs-12 col-sm-8">
                                <?php echo Form::select('moyenTransport', $moyenTransport, null, ['placeholder' => __('Selectionner une option'), 'class' => 'form-control moyenTransport', 'id' => 'moyenTransport']); ?>
                            </div>
                        </div>

                    </div>

                    <div id="nonFrequentation">
                        <div class="form-group row">
                            <?php echo Form::label(__('As-tu été à l’école par le passé ?'), null, ['class' => 'col-sm-4 control-label']); ?>
                            <div class="col-xs-12 col-sm-8">
                                <?php echo Form::select('avoirFrequente', ['non' => __('non'), 'oui' => __('oui')], null, ['class' => 'form-control avoirFrequente']); ?>
                            </div>
                        </div>
                        <div id="ecoleAvant">

                            <div class="form-group row" id="niveauEtudeAtteint">
                                <?php echo Form::label(__('Quel niveau d’étude as-tu atteins ?'), null, ['class' => 'col-sm-4 control-label']); ?>
                                <div class="col-xs-12 col-sm-8">
                                    <select class="form-control" name="niveauEtudeAtteint" id="niveauEtudeAtteint">
                                        <option value="">@lang('Selectionner une option')</option>
                                        @foreach ($niveauEtudeAvant as $id => $nom)
                                            <option value="{{ $id }}"
                                                {{ array_search($ssrteclmrs->niveauEtudeAtteint, $niveauEtudeAvant) === $id ? 'selected' : '' }}>
                                                {{ $nom }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <?php echo Form::label(__('Quel est le motif d\'arrêt de la scolarisation ?'), null, ['class' => 'col-sm-4 control-label']); ?>
                                <div class="col-xs-12 col-sm-8">
                                    <?php $filtered5 = Arr::pluck($ssrteclmrs->raisonarretecoles, 'raisonarretecole');
                                    ?>
                                    <select class="form-control select2-multi-select" name="raisonArretEcole[]"
                                        id="raisonArretEcole" multiple>
                                        @foreach ($raisonArretEcole as $raison)
                                            <option value="{{ $raison->nom }}" @selected(in_array($raison->nom, $filtered5))>
                                                {{ $raison->nom }} </option>
                                        @endforeach
                                    </select>

                                </div>
                            </div>
                        </div>
                        <div class="form-group row" id='autreRaisonArretEcole'>
                            <?php echo Form::label(__('Autre raison'), null, ['class' => 'col-sm-4 control-label']); ?>
                            <div class="col-xs-12 col-sm-8">
                                <?php echo Form::text('autreRaisonArretEcole', null, ['placeholder' => __('Autre raison'), 'class' => 'form-control autreRaisonArretEcole']); ?>
                            </div>
                        </div>
                    </div>
                    <hr class="panel-wide">
                    <div class="fieldset-like">
                        <legend class="legend-center">
                            <h5 class="font-weight-bold text-decoration-underline">Traveaux dangereux</h5>
                        </legend>
                        <div class="form-group row">
                            <?php echo Form::label(__('Au cours de ces 2 dernières années lequel de ces travaux as-tu effectués ?'), null, ['class' => 'col-sm-4 control-label']); ?>
                            <div class="col-xs-12 col-sm-8">
                                <?php $filtered = Arr::pluck($ssrteclmrs->travauxdangereux, 'travauxdangereux'); ?>
                                <select class="form-control select2-multi-select" name="travauxDangereux[]"
                                    id="travauxDangereux" multiple required>
                                    @foreach ($travauxDangereux as $danger)
                                        <option value="{{ $danger->nom }}" @selected(in_array($danger->nom, $filtered))>
                                            {{ $danger->nom }} </option>
                                    @endforeach
                                </select>

                            </div>
                        </div>
                        <div class="form-group row">
                            <?php echo Form::label(__('Où as-tu effectué ces travaux ?'), null, ['class' => 'col-sm-4 control-label']); ?>
                            <div class="col-xs-12 col-sm-8">
                                <?php $filtered2 = Arr::pluck($ssrteclmrs->lieutravauxdangereux, 'lieutravauxdangereux'); ?>
                                <select class="form-control select2-multi-select" name="lieuTravauxDangereux[]"
                                    id="lieuTravauxDangereux" multiple required>
                                    @foreach ($lieuTravaux as $lieu)
                                        <option value="{{ $lieu->nom }}" @selected(in_array($lieu->nom, $filtered2))>
                                            {{ $lieu->nom }} </option>
                                    @endforeach
                                </select>

                            </div>
                        </div>
                    </div>

                    <div class="fieldset-like">
                        <legend class="legend-center">
                            <h5 class="font-weight-bold text-decoration-underline">Traveaux legers</h5>
                        </legend>

                        <div class="form-group row">
                            <?php echo Form::label(__('Au cours de ces 2 dernières années lequel de ces travaux a tu effectués ?'), null, ['class' => 'col-sm-4 control-label']); ?>
                            <div class="col-xs-12 col-sm-8">
                                <?php $filtered3 = Arr::pluck($ssrteclmrs->travauxlegers, 'travauxlegers'); ?>
                                <select class="form-control select2-multi-select" name="travauxLegers[]"
                                    id="travauxLegers" multiple required>
                                    @foreach ($travauxLegers as $leger)
                                        <option value="{{ $leger->nom }}" @selected(in_array($leger->nom, $filtered3))>
                                            {{ $leger->nom }} </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <?php echo Form::label(__('Où as-tu effectué ces travaux ?'), [], ['class' => 'col-sm-4 control-label']); ?>
                            <div class="col-xs-12 col-sm-8">
                                <?php $filtered4 = Arr::pluck($ssrteclmrs->lieutravauxlegers, 'lieutravauxlegers'); ?>
                                <select class="form-control select2-multi-select" name="lieuTravauxLegers[]"
                                    id="lieuTravauxLegers" multiple required>
                                    @foreach ($lieuTravaux as $lieu)
                                        <option value="{{ $lieu->nom }}" @selected(in_array($lieu->nom, $filtered4))>
                                            {{ $lieu->nom }} </option>
                                    @endforeach
                                </select>

                            </div>
                        </div>
                    </div>

                    <div class="fieldset-like">
                        <legend class="legend-center">
                            <h5 class="font-weight-bold text-decoration-underline">Informations sur l'enquêteur</h5>
                        </legend>

                        <div class="form-group row">
                            <?php echo Form::label(__('Nom de l\'enquêteur'), null, ['class' => 'col-sm-4 control-label']); ?>
                            <div class="col-xs-12 col-sm-8">
                                <?php echo Form::text('nomEnqueteur', null, ['placeholder' => __('Nom de l\'enquêteur'), 'class' => 'form-control nomEnqueteur', 'id' => 'nomEnqueteur', 'required']); ?>
                            </div>
                        </div>

                        <div class="form-group row">
                            <?php echo Form::label(__('Prenom(s) de l\'enquêteur'), null, ['class' => 'col-sm-4 control-label']); ?>
                            <div class="col-xs-12 col-sm-8">
                                <?php echo Form::text('prenomEnqueteur', null, ['placeholder' => __('Prenom(s) de l\'enquêteur'), 'class' => 'form-control prenomEnqueteur', 'id' => 'prenomEnqueteur', 'required']); ?>
                            </div>
                        </div>

                        <div class="form-group row">
                            <?php echo Form::label(__('Numéro de téléphone de l\'enquêteur'), null, ['class' => 'col-sm-4 control-label']); ?>
                            <div class="col-xs-12 col-sm-8">
                                <?php echo Form::text('telephoneEnqueteur', null, ['placeholder' => __('Numéro de téléphone de l\'enquêteur'), 'class' => 'form-control telephoneEnqueteur', 'id' => 'telephoneEnqueteur', 'required']); ?>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <?php echo Form::label(__("Date d'Enquête"), null, ['class' => 'col-sm-4 control-label']); ?>
                        <div class="col-xs-12 col-sm-8">
                            <?php echo Form::date('date_enquete', null, ['class' => 'form-control dateactivite dateEnquete', 'required']); ?>
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
    <x-back route="{{ route('manager.suivi.ssrteclmrs.index') }}" />
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
        $(document).ready(function() {

            $('#autreLienParente,#frequentation,#ecoleAvant,#autreRaisonArretEcole').hide();

            $('.lienParente').change(function() {
                var lienParente = $('.lienParente').val();
                if (lienParente == 'Autre') {
                    $('#autreLienParente').show('slow');
                    $('.autreLienParente').attr('required', true);
                    $('.autreLienParente').css('display', 'block');
                } else {
                    $('#autreLienParente').hide('slow');
                    $('.autreLienParente').val('');
                    $('.autreLienParente').attr('required', false);
                }
            });
            if ($('.lienParente').val() == 'Autre') {
                $('#autreLienParente').show('slow');
                $('.autreLienParente').attr('required', true);
                $('.autreLienParente').css('display', 'block');
            } else {
                $('#autreLienParente').hide('slow');
                $('.autreLienParente').val('');
                $('.autreLienParente').attr('required', false);
            }

            $('.ecoleVillage').change(function() {
                var ecoleVillage = $('.ecoleVillage').val();
                if (ecoleVillage == 'oui') {
                    $('#distanceEcole').hide('slow');
                    $('.distanceEcole').attr('required', false);
                } else {
                    $('#distanceEcole').show('slow');
                    $('.distanceEcole').attr('required', true);
                }
            });
            if ($('.ecoleVillage').val() == 'oui') {
                $('#distanceEcole').hide('slow');
                $('.distanceEcole').attr('required', false);
            } else {
                $('#distanceEcole').show('slow');
                $('.distanceEcole').attr('required', true);
            }


            $('.frequente').change(function() {
                var frequente = $('.frequente').val();
                if (frequente == 'oui') {
                    $('#frequentation').show('slow');
                    $('#nonFrequentation').hide('slow');
                    $('.niveauEtude').attr('required', true);
                    $('.classe').attr('required', true);
                    $('.ecoleVillage').attr('required', true);
                    $('.nomEcole').attr('required', true);
                    $('.moyenTransport').attr('required', true);

                    $('.ecoleVillage').change(function() {
                        var ecoleVillage = $('.ecoleVillage').val();
                        if (ecoleVillage == 'oui') {
                            $('#distanceEcole').hide('slow');
                            $('.distanceEcole').attr('required', false);
                        } else {
                            $('#distanceEcole').show('slow');
                            $('.distanceEcole').attr('required', true);
                        }
                    });
                } else {
                    $('#frequentation').hide('slow');
                    $('#nonFrequentation').show('slow');
                    $('.niveauEtude').attr('required', false);
                    $('.classe').attr('required', false);
                    $('.ecoleVillage').attr('required', false);
                    $('.nomEcole').attr('required', false);
                    $('.moyenTransport').attr('required', false);

                    $('.niveauEtude').val('');
                    $('.classe').val('');
                    $('.ecoleVillage').val('');
                    $('.nomEcole').val('');
                    $('.moyenTransport').val('');
                }
            });
            if ($('.frequente').val() == 'oui') {
                $('#frequentation').show('slow');
                $('#nonFrequentation').hide('slow');
                $('.niveauEtude').attr('required', true);
                $('.classe').attr('required', true);
                $('.ecoleVillage').attr('required', true);
                $('.nomEcole').attr('required', true);
                $('.moyenTransport').attr('required', true);

                $('.ecoleVillage').change(function() {
                    var ecoleVillage = $('.ecoleVillage').val();
                    if (ecoleVillage == 'oui') {
                        $('#distanceEcole').hide('slow');
                        $('.distanceEcole').attr('required', false);
                    } else {
                        $('#distanceEcole').show('slow');
                        $('.distanceEcole').attr('required', true);
                    }
                });
            } else {
                $('#frequentation').hide('slow');
                $('#nonFrequentation').show('slow');
                $('.niveauEtude').attr('required', false);
                $('.classe').attr('required', false);
                $('.ecoleVillage').attr('required', false);
                $('.nomEcole').attr('required', false);
                $('.moyenTransport').attr('required', false);

                $('.niveauEtude').val('');
                $('.classe').val('');
                $('.ecoleVillage').val('');
                $('.nomEcole').val('');
                $('.moyenTransport').val('');
            }

            function afficherAutreRaisonArretEcole() {
                var protections = $('#raisonArretEcole').find(":selected").map((key, item) => {
                    return item.textContent.trim();
                }).get();
                console.log(protections)
                if (protections.includes("Autre")) {

                    $('#autreRaisonArretEcole').show('slow');
                    $('.autreRaisonArretEcole').attr('required', true);
                    $('.autreRaisonArretEcole').show('slow');
                } else {

                    $('#autreRaisonArretEcole').hide('slow');
                    $('.autreRaisonArretEcole').attr('required', false);
                    $('.autreRaisonArretEcole').hide('slow');
                }
            };

            $('.avoirFrequente').change(function() {

                var avoirFrequente = $('.avoirFrequente').val();
                if (avoirFrequente == 'oui') {
                    $('#ecoleAvant').show('slow');
                    $('.niveauEtudeAtteint').attr('required', true);
                    $('.raisonArretEcole').attr('required', true);

                    $('#raisonArretEcole').change(function() {
                        afficherAutreRaisonArretEcole()
                    });

                } else {
                    $('#ecoleAvant').hide('slow');
                    $('.niveauEtudeAtteint').attr('required', false);
                    $('.raisonArretEcole').attr('required', false);
                    $('.niveauEtudeAtteint').val('');
                    $('.raisonArretEcole').val('');
                }
            });
            if ($('.avoirFrequente').val() == 'oui') {
                $('#ecoleAvant').show('slow');
                $('.niveauEtudeAtteint').attr('required', true);
                $('.raisonArretEcole').attr('required', true);

                afficherAutreRaisonArretEcole();

            } else {
                $('#ecoleAvant').hide('slow');
                $('.niveauEtudeAtteint').attr('required', false);
                $('.raisonArretEcole').attr('required', false);
                $('.niveauEtudeAtteint').val('');
                $('.raisonArretEcole').val('');
            }

        });
        $('#localite').chained("#section")
        $("#producteur").chained("#localite");
        $("#classeEtude").chained("#niveauEtude");
    </script>
@endpush
