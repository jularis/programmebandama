@extends('manager.layouts.app')
@section('panel')
    <div class="row mb-none-30">
        <div class="col-lg-12 mb-30">
            <div class="card">
                <div class="card-body">
                    {!! Form::model($producteur, [
                        'method' => 'POST',
                        'route' => ['manager.traca.producteur.update', $producteur->id],
                        'class' => 'form-horizontal',
                        'id' => 'flocal',
                        'enctype' => 'multipart/form-data',
                    ]) !!}
                    <input type="hidden" name="id" value="{{ $producteur->id }}">

                    <div class="form-group row">
                        <?php echo Form::label(__('Accord de consentement du producteur'), null, ['class' => 'col-sm-4 control-label']); ?>
                        <div class="col-xs-12 col-sm-8">
                            <?php echo Form::select('consentement', ['non' => 'Non', 'oui' => 'Oui'], null, ['class' => 'form-control']); ?>
                        </div>
                    </div>
                    {{-- proprietaire --}}

                    <div class="form-group row">
                        <?php echo Form::label(__('Comment vous vous definissez ?'), null, ['class' => 'col-sm-4 control-label']); ?>
                        <div class="col-xs-12 col-sm-8">
                            <?php echo Form::select('proprietaires', ['Proprietaire' => 'Proprietaire', 'Exploitant' => 'Exploitant', 'Metayer(aboussan)' => 'Metayer(aboussan)', 'Planté-partager' => 'Planté-partager', 'Garantie' => 'Garantie', 'Non Occupant' => 'Non Occupant'], null, ['class' => 'form-control proprietaires', 'required']); ?>
                        </div>
                    </div>

                    <div class="form-group row" id="plantePartager">
                        <?php echo Form::label(__('Quel est votre associé'), null, ['class' => 'col-sm-4 control-label']); ?>
                        <div class="col-xs-12 col-sm-8">
                            <?php echo Form::text('plantePartage', null, ['id' => 'plantePartage', 'placeholder' => __('Saisissez le nom de votre associé'), 'class' => 'form-control mb-3 plantePartage']); ?>
                        </div>
                        <?php echo Form::label(__('Quel est le numero de votre associé'), null, ['class' => 'col-sm-4 control-label']); ?>
                        <div class="col-xs-12 col-sm-8">
                            <?php echo Form::text('numeroAssocie', null, ['id' => 'numeroAssocie', 'placeholder' => __('Saisissez le numero de téléphone votre associé'), 'class' => 'form-control numeroAssocie']); ?>
                        </div>
                    </div>

                    <div id="garantie">
                        <div class="form-group row">
                            <?php echo Form::label(__('Année de démarrage'), null, ['class' => 'col-sm-4 control-label']); ?>
                            <div class="col-xs-12 col-sm-8">
                                <?php echo Form::text('anneeDemarrage', null, ['placeholder' => 'Ex. 2024', 'pattern' => '[0-9]{4}', 'class' => 'form-control anneeDemarrage', 'id' => 'anneeDemarrage']); ?>
                            </div>
                        </div>
                        <div class="form-group row">
                            <?php echo Form::label(__('Année de fin'), null, ['class' => 'col-sm-4 control-label']); ?>
                            <div class="col-xs-12 col-sm-8">
                                <?php echo Form::text('anneeFin', null, ['placeholder' => 'Ex. 2024', 'pattern' => '[0-9]{4}', 'class' => 'form-control anneeFin', 'id' => 'anneeFin']); ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <?php echo Form::label(__('Statut'), null, ['class' => 'col-sm-4 control-label']); ?>
                        <div class="col-xs-12 col-sm-8">
                            <?php echo Form::select('statut', ['Candidat' => 'Candidat', 'Certifie' => 'Certifie'], null, ['class' => 'form-control statut', 'required']); ?>
                        </div>
                    </div>
                    <div id="statutCertifie">
                        <div class="form-group row">
                            <?php echo Form::label(__('Année de certification'), null, ['class' => 'col-sm-4 control-label']); ?>
                            <div class="col-xs-12 col-sm-8">
                                <?php echo Form::number('certificat', null, ['id' => 'certificat', 'class' => 'form-control certificat', 'min' => '1990']); ?>
                            </div>
                        </div>
                        <div class="form-group row">
                            <?php echo Form::label(__('Code producteur'), null, ['class' => 'col-sm-4 control-label']); ?>
                            <div class="col-xs-12 col-sm-8">
                                <?php echo Form::text('codeProd', null, ['id' => 'codeProd', 'placeholder' => __('Code producteur'), 'class' => 'form-control codeProd']); ?>
                            </div>
                        </div>
                        {{-- Selectionner le Certificat --}}
                        <div class="form-group row">
                            <?php echo Form::label(__('Certificat'), null, ['class' => 'col-sm-4 control-label']); ?>

                            <div class="col-xs-12 col-sm-8">
                                <select class="form-control select2-multi-select certificats" name="certificats[]" multiple
                                    required>
                                    <option value="">@lang('Selectionner les options')</option>
                                    @foreach ($certificationAll as $certification)
                                        <option value="{{ $certification->nom }}"
                                            {{ in_array($certification->nom, old('certificats', $certifications)) ? 'selected' : '' }}>
                                            {{ __($certification->nom) }}</option>
                                    @endforeach
                                    <option value="Autre"{{ in_array('Autre', $certifications) ? 'selected' : '' }}>Autre
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div id="autreCertificat">
                        <div class="form-group row">
                            <?php echo Form::label(__('Autre Certificat'), null, ['class' => 'col-sm-4 control-label']); ?>
                            <div class="col-xs-12 col-sm-8">
                                <?php echo Form::text('autreCertificats', null, ['id' => 'autreCertificats', 'placeholder' => __('Autre certificat'), 'class' => 'form-control autreCertificats']); ?>
                            </div>
                        </div>
                    </div>
                    {{-- selection sections --}}
                    <div class="form-group row">
                        <label class="col-sm-4 control-label">@lang('Section')</label>
                        <div class="col-xs-12 col-sm-8">
                            <select class="form-control" name="section" id="section" required disabled>
                                <option value="">@lang('Selectionner une option')</option>
                                @foreach ($sections as $section)
                                    <option value="{{ $section->id }}" @selected($section->id == $producteur->localite->section->id)>
                                        {{ __($section->libelle) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    {{-- selection localite --}}
                    <div class="form-group row">
                        <label class="col-sm-4 control-label">@lang('Localite')</label>
                        <div class="col-xs-12 col-sm-8">
                            <select class="form-control" name="localite_id" id="localite_id" required>
                                <option value="">@lang('Selectionner une option')</option>
                                @foreach ($localites as $localite)
                                    <option value="{{ $localite->id }}"
                                        data-chained="{{ optional($localite->section)->id }}" @selected($localite->id == $producteur->localite_id)>
                                        {{ __($localite->nom) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    {{-- programme de durabilité --}}
                    <div class="form-group row">
                        <label class="col-sm-4 control-label">@lang('Programme')</label>
                        <div class="col-xs-12 col-sm-8">
                            <select class="form-control programme_id" name="programme_id" id="programme_id" required>
                                <option value="">@lang('Selectionner une option')</option>
                                @foreach ($programmes as $programme)
                                    <option value="{{ $programme->id }}" @selected($producteur->programme_id == $programme->id)>
                                        {{ __($programme->libelle) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- saisie où le producteur habite --}}
                    <div class="form-group row">
                        <?php echo Form::label(__('Habitez-vous dans un campement ou village ?'), null, ['class' => 'col-sm-4 control-label']); ?>
                        <div class="col-xs-12 col-sm-8">
                            <?php echo Form::select('habitationProducteur', ['Village' => 'Village', 'Campement' => 'Campement'], null, ['class' => 'form-control habitationProducteur', 'id' => 'habitationProducteur', 'required']); ?>
                        </div>
                    </div>



                    <div class="form-group row">
                        <?php echo Form::label(__('Nom du producteur'), null, ['class' => 'col-sm-4 control-label']); ?>
                        <div class="col-xs-12 col-sm-8">
                            <?php echo Form::text('nom', null, ['placeholder' => __('Nom du producteur'), 'class' => 'form-control', 'required']); ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <?php echo Form::label(__('Prenoms du producteur'), null, ['class' => 'col-sm-4 control-label']); ?>
                        <div class="col-xs-12 col-sm-8">
                            <?php echo Form::text('prenoms', null, ['placeholder' => __('Prenoms du producteur'), 'class' => 'form-control', 'required']); ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <?php echo Form::label(__('Genre'), null, ['class' => 'col-sm-4 control-label']); ?>
                        <div class="col-xs-12 col-sm-8">
                            <?php echo Form::select('sexe', ['Homme' => 'Homme', 'Femme' => 'Femme'], null, ['class' => 'form-control', 'required']); ?>
                        </div>
                    </div>
                    {{-- situation matrimoniale  --}}
                    <div class="form-group row">
                        <?php echo Form::label(__('Statut matrimonial'), null, ['class' => 'col-sm-4 control-label']); ?>
                        <div class="col-xs-12 col-sm-8">
                            <?php echo Form::select('statutMatrimonial', ['Célibataire' => 'Célibataire', 'Concubinage' => 'Concubinage', 'Marié(mariage civil)' => 'Marié(mariage civil)', 'Mariage réligieux' => 'Mariage réligieux', 'Mariage réligieux' => 'Mariage réligieux','Mariage traditionnel' => 'Mariage traditionnel','Divorcé' => 'Divorcé', 'Veuf(ve)' => 'Veuf(ve)'], null, ['class' => 'form-control', 'required']); ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <?php echo Form::label(__('Nationalité'), null, ['class' => 'col-sm-4 control-label']); ?>
                        <div class="col-xs-12 col-sm-8">
                            <select class="form-control select2-basic" name="nationalite" id="nationalite" required>
                                <option value="">@lang('Selectionner une option')</option>
                                @foreach ($countries as $nationalite)
                                    <option value="{{ $nationalite->id }}"
                                        {{ $producteur->nationalite == $nationalite->id ? 'selected' : '' }}>
                                        {{ __($nationalite->nicename) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <?php echo Form::label(__('Date de naissance'), null, ['class' => 'col-sm-4 control-label']); ?>
                        <div class="col-xs-12 col-sm-8">
                            <?php echo Form::date('dateNaiss', null, ['class' => 'form-control naiss', 'id' => 'datenais', 'required']); ?>
                        </div>
                    </div>
                    <div class="form-group row">
                        <?php echo Form::label(__('Numero de téléphone'), null, ['class' => 'col-sm-4 control-label']); ?>
                        <div class="col-xs-12 col-sm-8">
                            <?php echo Form::text('phone1', null, ['class' => 'form-control phone', 'required']); ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <?php echo Form::label(__('Avez-vous un proche à contacter pour vous joindre'), null, ['class' => 'col-sm-4 control-label']); ?>
                        <div class="col-xs-12 col-sm-8">
                            <?php echo Form::select('autreMembre', ['non' => 'Non', 'oui' => 'Oui'], null, ['class' => 'form-control autreMembre']); ?>
                        </div>
                    </div>
                    <div id="autrePhones">
                        <div class="form-group row">
                            <?php echo Form::label(__(''), null, ['class' => 'col-sm-4 control-label']); ?>
                            <div class="col-xs-12 col-sm-8">
                                <?php echo Form::select('autrePhone', ['' => null, 'Membre de famille' => 'Membre de famille', 'Staff de la Coopérative' => 'Staff de la Coopérative', 'Autre' => 'Autre'], null, ['id' => 'autrePhone', 'class' => 'form-control autrePhone']); ?>
                            </div>
                        </div>
                        <div class="form-group row">
                            <?php echo Form::label(__('Numero de téléphone'), null, ['class' => 'col-sm-4 control-label']); ?>
                            <div class="col-xs-12 col-sm-8">
                                <?php echo Form::text('phone2', null, ['id' => 'phone2', 'placeholder' => __('Numéro de téléphone'), 'class' => 'form-control phone2']); ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <?php echo Form::label(__("Niveau d'étude"), null, ['class' => 'col-sm-4 control-label']); ?>
                        <div class="col-xs-12 col-sm-8">
                            <?php echo Form::select('niveau_etude', ['Primaire' => 'Primaire', 'Collège (6e à 3ème)' => 'Collège (6e à 3ème)', 'Lycée (2nde à Tle)' => 'Lycée (2nde à Tle)', 'Superieur (BAC et Plus)' => 'Superieur (BAC et Plus)', 'Aucun' => 'Aucun'], null, ['placeholder' => __('Selectionner une option'), 'class' => 'form-control', 'required']); ?>
                        </div>
                    </div>
                    <div class="form-group row">
                        <?php echo Form::label(__('Type de pièces'), null, ['class' => 'col-sm-4 control-label']); ?>
                        <div class="col-xs-12 col-sm-8">
                            <?php echo Form::select('type_piece', ['CNI' => 'CNI', 'Carte Consulaire' => 'Carte Consulaire', 'Passeport' => 'Passeport', 'Attestation' => 'Attestation', 'Extrait de naissance' => 'Extrait de naissance', 'Permis de conduire' => 'Permis de conduire', 'CMU' => 'CMU', 'Pas Disponible' => 'Pas Disponible'], null, ['placeholder' => __('Selectionner une option'), 'class' => 'form-control', 'required']); ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <?php echo Form::label(__('N° de la pièce'), null, ['class' => 'col-sm-4 control-label']); ?>
                        <div class="col-xs-12 col-sm-8">
                            <?php echo Form::text('numPiece', null, ['placeholder' => __('N° de la pièce'), 'class' => 'form-control', 'required']); ?>
                        </div>
                    </div>
                    {{-- Numero de carte ccc --}}

                    <div class="form-group row">
                        <?php echo Form::label(__('N° de carte CCC'), null, ['class' => 'col-sm-4 control-label']); ?>
                        <div class="col-xs-12 col-sm-8">
                            <?php echo Form::text('num_ccc', null, ['placeholder' => __('N° de carte CCC'), 'class' => 'form-control text11']); ?>
                        </div>
                    </div>
                    {{-- Avez-vous une carte CMU ? --}}
                    <div class="form-group row">
                        <?php echo Form::label(__('Avez-vous une carte CMU ?'), null, ['class' => 'col-sm-4 control-label']); ?>
                        <div class="col-xs-12 col-sm-8">
                            <?php echo Form::select('carteCMU', ['non' => 'Non', 'oui' => 'Oui'], null, ['class' => 'form-control carteCMU']); ?>
                        </div>
                    </div>
                    <div id="pieceCMU">
                        <div class="form-group row">
                            <?php echo Form::label(__('Est elle disponible ?'), null, ['class' => 'col-sm-4 control-label']); ?>
                            <div class="col-xs-12 col-sm-8">
                                <?php echo Form::select('carteCMUDispo', ['Null' => 'Selectionner un option', 'non' => 'Non', 'oui' => 'Oui'], null, ['class' => 'form-control carteCMUDispo']); ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row" id="carteCMUDispos">
                        <?php echo Form::label(__('N° de la pièce CMU'), null, ['class' => 'col-sm-4 control-label']); ?>
                        <div class="col-xs-12 col-sm-8">
                            <?php echo Form::text('numCMU', null, ['id' => 'numCMU', 'placeholder' => __('N° de la pièce CMU'), 'class' => 'form-control numCMU']); ?>
                        </div>
                    </div>
                    {{-- quel est votre carte d'assurance  --}}
                    <div class="form-group row">
                        <?php echo Form::label(__('Votre type de carte de sécurité social'), null, ['class' => 'col-sm-4 control-label']); ?>
                        <div class="col-xs-12 col-sm-8">
                            <?php echo Form::select('typeCarteSecuriteSociale', ['AUCUN' => 'AUCUN', 'CNPS' => 'CNPS', 'CMU' => 'CMU'], null, ['class' => 'form-control typeCarteSecuriteSociale', 'required']); ?>
                        </div>
                    </div>
                    <div id="typeCarteSecuriteSociales">
                        <div class="form-group row">
                            <?php echo Form::label(__('N° de carte de sécurité sociale'), null, ['class' => 'col-sm-4 control-label']); ?>
                            <div class="col-xs-12 col-sm-8">

                                <?php echo Form::text('numSecuriteSociale', null, ['id' => 'numSecuriteSociale', 'placeholder' => __('N° de carte de sécurité sociale'), 'class' => 'form-control numSecuriteSociale']); ?>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <?php echo Form::label(__('Photo du producteur'), null, ['class' => 'col-sm-4 control-label']); ?>
                        <div class="col-xs-12 col-sm-8">
                            <input type="file" name="picture" accept="image/*" class="form-control dropify-fr"
                                placeholder="Choisir une image" id="image">
                        </div>
                    </div>
                    <hr class="panel-wide">

                    <div class="form-group">
                        <button type="submit" class="btn btn--primary w-100 h-45"> @lang('Envoyer')</button>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <x-back route="{{ route('manager.traca.producteur.index') }}" />
@endpush

@push('script')
    <script type="text/javascript">
        $("#section_id").chained("#localite");
    </script>
@endpush

@push('script')
    <script type="text/javascript">
        $('#listecultures,#gardePapiersChamps,#numeroCompteMM,#typeCarteSecuriteSociales,#garantie,#autrePhones,#autreCertificat,#plantePartager,#statutCertifie')
            .hide();
        $(document).ready(function() {
            function handleStatutChange() {
                var statut = $('.statut').val();
                if (statut == 'Certifie') {
                    $('#statutCertifie').show('slow');
                    $('.certificat').show('slow');
                    $('#certificat').prop('required', true);
                    $('.certificats').show('slow');
                    $('.select2-multi-select.certificats').prop('required', true);
                } else {
                    $('#statutCertifie').hide('slow');
                    $('#certificat').val('');
                    $('#certificat').prop('required', false);
                    $('.certificats').hide('slow');
                    var select2Element = $('.select2-multi-select.certificats');
                    select2Element.val(null).trigger('change');
                    select2Element.prop('required', false);
                }
            }

            $('.statut').change(handleStatutChange);
            handleStatutChange();
        });
        //afficher le champ de saisie du numero de la piece de sécurité sociale
        $('.typeCarteSecuriteSociale').change(function() {
            var typeCarteSecuriteSociale = $('.typeCarteSecuriteSociale').val();
            if (typeCarteSecuriteSociale == 'AUCUN') {

                $('#typeCarteSecuriteSociales').hide('slow');
                $('.numSecuriteSociale').val('');
                $("#numSecuriteSociale").prop("required", false);
            } else {
                $('#typeCarteSecuriteSociales').show('slow');
                $('.numSecuriteSociale').show('slow');
                $("#numSecuriteSociale").prop("required", true);
            }
        });
        if ($('.typeCarteSecuriteSociale').val() == 'AUCUN') {
            $('#typeCarteSecuriteSociales').hide('slow');
            $('.numSecuriteSociale').val('');
            $("#numSecuriteSociale").prop("required", false);
        } else {
            $('#typeCarteSecuriteSociales').show('slow');
            $('.numSecuriteSociale').show('slow');
            $("#numSecuriteSociale").prop("required", true);
        }

        //afficher le champ autre certificat

        $('.certificats').change(function() {
            var certificats = $('.certificats').find(":selected").map((key, item) => {
                return item.textContent.trim();
            }).get();
            console.log(certificats);
            if (certificats.includes("Autre")) {
                $('#autreCertificat').show('slow');
                $("#autreCertificats").prop("required", true);
            } else {
                $('#autreCertificat').hide('slow');
                $('.autreCertificats').val('');
                $("#autreCertificats").prop("required", false);
            }
        });
        if ($('.certificats').find(":selected").map((key, item) => {
                return item.textContent.trim();
            }).get().includes("Autre")) {
            $('#autreCertificat').show('slow');
            $("#autreCertificats").prop("required", true);
        } else {
            $('#autreCertificat').hide('slow');
            $('.autreCertificats').val('');
            $("#autreCertificats").prop("required", false);
        }

        //afficher le champ de saisie du numéro de téléphone d'une autre personne

        $('.autreMembre').change(function() {
            var autreMembre = $('.autreMembre').val();
            if (autreMembre == 'oui') {
                $('#autrePhones').show('slow');
                $('.autrePhone').show('slow');
                $("#autrePhone").prop("required", true);
                $('.phone2').show('slow');
                $("#phone2").prop("required", true);
            } else {
                $('#autrePhones').hide('slow');
                $('.autrePhone').val('');
                $('.phone2').val('');
                $("#autrePhone").prop("required", false);
                $("#phone2").prop("required", false);
            }
        });
        if ($('.autreMembre').val() == 'oui') {
            $('#autrePhones').show('slow');
            $('.autrePhone').show('slow');
            $("#autrePhone").prop("required", true);
            $('.phone2').show('slow');
            $("#phone2").prop("required", true);
        } else {
            $('#autrePhones').hide('slow');
            $('.autrePhone').val('');
            $('.phone2').val('');
            $("#autrePhone").prop("required", false);
            $("#phone2").prop("required", false);
        }

        //afficher le champ de saisie année de garantie

        $('.proprietaires').change(function() {
            var proprietaires = $('.proprietaires').val();
            if (proprietaires == 'Garantie') {
                $('#garantie').show('slow');
                $("#anneeDemarrage").prop("required", true);
                $("#anneeFin").prop("required", true);

            } else {
                $('#garantie').hide('slow');
                $('anneeDemarrage').val('');
                $('anneeFin').val('');
                $("#anneeDemarrage").prop("required", false);
                $("#anneeFin").prop("required", false);
            }
        });
        if ($('.proprietaires').val() == 'Garantie') {
            $('#garantie').show('slow');
            $("#anneeDemarrage").prop("required", true);
            $("#anneeFin").prop("required", true);

        } else {
            $('#garantie').hide('slow');
            $('anneeDemarrage').val('');
            $('anneeFin').val('');
            $("#anneeDemarrage").prop("required", false);
            $("#anneeFin").prop("required", false);
        }

        $('.proprietaires').change(function() {
            var proprietaires = $('.proprietaires').val();
            if (proprietaires == 'Planté-partager') {
                $('#plantePartager').show('slow');
                $('.plantePartage').show('slow');
                $("#plantePartage").prop("required", true);
                $('.numeroAssocie').show('slow');
                $("#numeroAssocie").prop("required", true);
            } else {
                $('#plantePartager').hide('slow');
                $('.plantePartage').val('');
                $("#plantePartage").prop("required", false);
                $('#numeroAssocie').hide('slow');
                $('.numeroAssocie').val('');
            }
        });
        if ($('.proprietaires').val() == 'Planté-partager') {
            $('#plantePartager').show('slow');
            $('.plantePartage').show('slow');
            $("#plantePartage").prop("required", true);
            $('.numeroAssocie').show('slow');
            $("#numeroAssocie").prop("required", true);

        } else {
            $('#plantePartager').hide('slow');
            $('.plantePartage').val('');
            $("#plantePartage").prop("required", false);
            $('#numeroAssocie').hide('slow');
            $('.numeroAssocie').val('');
        }

        //afficher le champ de saisie du numero de la piece CMU
        $('.carteCMU').change(function() {
            var cmu = $('.carteCMU').val();
            if (cmu == 'oui') {
                $('#pieceCMU').show('slow');
                $('.numCMU').show('slow');
                $("#numCMU").prop("required", true);
                $('.carteCMUDispo').show('slow');
                $("#carteCMUDispo").prop("required", true);

            } else {
                $('#pieceCMU').hide('slow');
                $('.numCMU').val('');
                $("#numCMU").prop("required", false);
                $('.carteCMUDispo').hide('slow');
                $("#carteCMUDispo").prop("required", false);
                $('.carteCMUDispo').val('');
            }
        });
        $('.carteCMUDispo').change(function() {
            var cmuDispo = $('.carteCMUDispo').val();
            if (cmuDispo == 'oui') {
                $('#carteCMUDispos').show('slow');
                $('.numCMU').show('slow');
                $("#numCMU").prop("required", true);
            } else {
                $('#carteCMUDispos').hide('slow');
                $('.numCMU').val('');
                $("#numCMU").prop("required", false);

            }
        });
        if ($('.carteCMU').val() == 'oui') {
            $('#pieceCMU').show('slow');
            $('.numCMU').show('slow');
            $("#numCMU").prop("required", true);
            $('.carteCMUDispo').show('slow');
            $("#carteCMUDispo").prop("required", true);
        } else {
            $('#pieceCMU').hide('slow');
            $('.numCMU').val('');
            $("#numCMU").prop("required", false);
            $('.carteCMUDispo').hide('slow');
            $("#carteCMUDispo").prop("required", false);
            $('.carteCMUDispo').val('');
        }
        if ($('.carteCMUDispo').val() == 'oui') {
            $('#carteCMUDispos').show('slow');
            $('.numCMU').show('slow');
            $("#numCMU").prop("required", true);
        } else {
            $('#carteCMUDispos').hide('slow');
            $('.numCMU').val('');
            $("#numCMU").prop("required", false);
        }

        $('#superficie').hide();
        $('.foretsjachere').change(function() {
            var foretsjachere = $('.foretsjachere').val();
            if (foretsjachere == 'oui') {
                $('#superficie').show('slow');
            } else {
                $('#superficie').hide('slow');
                $('.superficie').val('');
            }
        });
        if ($('.foretsjachere').val() == 'oui') {
            $('#superficie').show('slow');
        } else {
            $('#superficie').hide('slow');
            $('.superficie').val('');
        }
    </script>
    <script type="text/javascript">
        $("#localite_id").chained("#section");

        $(document).ready(function() {
            $(".select2-basic").select2();
        });
    </script>
@endpush
