@extends('manager.layouts.app')
@section('panel')
    <div class="row mb-none-30">
        <div class="col-lg-12 mb-30">
            <div class="card">
                <div class="card-body">
                    {!! Form::model($menage, [
                        'method' => 'POST',
                        'route' => ['manager.suivi.menage.store', $menage->id],
                        'class' => 'form-horizontal',
                        'id' => 'flocal',
                        'enctype' => 'multipart/form-data',
                    ]) !!}
                    <input type="hidden" name="id" value="{{ $menage->id }}">

                    <div class="form-group row">
                        <label class="col-sm-4 control-label">@lang('Selectionner une section')</label>
                        <div class="col-xs-12 col-sm-8">
                            <select disabled class="form-control" name="section" id="section" required>
                                <option value="">@lang('Selectionner une option')</option>
                                @foreach ($sections as $section)
                                    <option value="{{ $section->id }}" @selected($section->id == $menage->producteur->localite->section_id)>
                                        {{ $section->libelle }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-4 control-label">@lang('Selectionner une localite')</label>
                        <div class="col-xs-12 col-sm-8">
                            <select disabled class="form-control" name="localite" id="localite" required>
                                <option value="">@lang('Selectionner une option')</option>
                                @foreach ($localites as $localite)
                                    <option value="{{ $localite->id }}" data-chained="{{ $localite->section->id }}"
                                        @selected($localite->id == $menage->producteur->localite_id)>
                                        {{ $localite->nom }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-4 control-label">@lang('Selectionner un producteur')</label>
                        <div class="col-xs-12 col-sm-8">
                            <select disabled class="form-control" name="producteur_id" id="producteur" required>
                                <option value="">@lang('Selectionner une option')</option>
                                @foreach ($producteurs as $producteur)
                                    <option value="{{ $producteur->id }}" data-chained="{{ $producteur->localite->id }}"
                                        @selected($producteur->id == $menage->producteur_id)>
                                        {{ stripslashes($producteur->nom) }} {{ stripslashes($producteur->prenoms) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        {{ Form::label(__('Quartier'), null, ['class' => 'col-sm-4 control-label']) }}
                        <div class="col-xs-12 col-sm-8">
                            <?php echo Form::text('quartier', null, ['placeholder' => '...', 'class' => 'form-control quartier', 'id' => 'quartier', 'required','disabled']); ?>
                        </div>
                    </div>
                    <hr class="panel-wide">
                    <div class="form-group row">
                        <?php echo Form::label(__('Nombre d’enfants de 0 à 5ans  présents dans le ménage ?'), null, ['class' => 'col-sm-4 control-label']); ?>
                        <div class="col-xs-12 col-sm-8">
                            <?php echo Form::number('ageEnfant6A17', null, ['placeholder' => 'Nombre', 'class' => 'form-control', 'min' => '0', 'required','disabled']); ?>
                        </div>
                    </div>
                    <div class="form-group row">
                        <?php echo Form::label(__('Nombre d’enfants de 6 à 17ans  présents dans le ménage ?'), null, ['class' => 'col-sm-4 control-label']); ?>
                        <div class="col-xs-12 col-sm-8">
                            <?php echo Form::number('ageEnfant0A5', null, ['placeholder' => 'Nombre', 'class' => 'form-control', 'min' => '0', 'required','disabled']); ?>
                        </div>
                    </div>
                    <div class="form-group row">
                        <?php echo Form::label(__('Parmi les enfants de 6 à 17, combien sont scolarisés ? ?'), null, ['class' => 'col-sm-4 control-label']); ?>
                        <div class="col-xs-12 col-sm-8">
                            <?php echo Form::number('enfantscolarises', null, ['placeholder' => 'Nombre', 'class' => 'form-control', 'required', 'min' => '0','disabled']); ?>
                        </div>
                    </div>
                    <div class="form-group row">
                        <?php echo Form::label(__('Parmi les enfants de 0 à 5ans, combien n’ont pas d’extrait de naissance ?'), null, ['class' => 'col-sm-4 control-label']); ?>
                        <div class="col-xs-12 col-sm-8">
                            <?php echo Form::number('enfantsPasExtrait', null, ['placeholder' => 'Nombre', 'class' => 'form-control', 'min' => '0', 'required','disabled']); ?>
                        </div>
                    </div>
                    <div class="form-group row">
                        <?php echo Form::label(__('Parmi les enfants de 6 à 17ans, combien n’ont pas d’extrait de naissance ?'), null, ['class' => 'col-sm-4 control-label']); ?>
                        <div class="col-xs-12 col-sm-8">
                            <?php echo Form::number('enfantsPasExtrait6A17', null, ['placeholder' => 'Nombre', 'class' => 'form-control', 'min' => '0', 'required','disabled']); ?>
                        </div>
                    </div>
                    <hr class="panel-wide">

                    <div class="form-group row">
                        {{ Form::label(__('Source Energie du ménage'), null, ['class' => 'col-sm-4 control-label']) }}
                        <div class="col-xs-12 col-sm-8">
                            <select disabled class="form-control select2-multi-select sources_energies" name="sourcesEnergie[]"
                                multiple id="sources_energies" required>
                                <option value="">@lang('Selectionner les options')</option>
                                <option value="Bois de chauffe"
                                    {{ in_array('Bois de chauffe', $energies) ? 'selected' : '' }}>Bois de chauffe</option>
                                <option value="Charbon" {{ in_array('Charbon', $energies) ? 'selected' : '' }}>Charbon
                                </option>
                                <option value="Gaz" {{ in_array('Gaz', $energies) ? 'selected' : '' }}>Gaz</option>
                                <option value="Four à pétrole"
                                    {{ in_array('Four à pétrole', $energies) ? 'selected' : '' }}>Four à pétrole</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row" id="boisChauffes">
                        {{ Form::label(__('Combien de bois par semaine?'), null, ['class' => 'col-sm-4 control-label']) }}
                        <div class="col-xs-12 col-sm-8">
                            <?php echo Form::number('boisChauffe', null, ['id' => 'boisChauffe', 'placeholder' => __('Quantité'), 'class' => 'form-control boisChauffe', 'min' => '1','disabled']); ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        {{ Form::label(__('Comment gérez-vous les ordures ménagères ?'), null, ['class' => 'col-sm-4 control-label']) }}
                        <div class="col-xs-12 col-sm-8">
                            <select disabled class="form-control select2-multi-select ordureMenagere" name="ordureMenagere[]"
                                multiple required>
                                <option value="">@lang('Selectionner les options')</option>
                                <option value="Dépotoirs Publique"
                                    {{ in_array('Dépotoirs Publique', $ordures) ? 'selected' : '' }}>Dépotoirs Publique
                                </option>
                                <option value="Poubelle de Maison"
                                    {{ in_array('Poubelle de Maison', $ordures) ? 'selected' : '' }}>Poubelle de Maison
                                </option>
                                <option value="Ramassage ordures organisé"
                                    {{ in_array('Ramassage ordures organisé', $ordures) ? 'selected' : '' }}>Ramassage
                                    ordures organisé</option>
                                <option value="Aucun" {{ in_array('Aucun', $ordures) ? 'selected' : '' }}>Aucun</option>
                            </select>
                        </div>
                    </div>


                    <div class="form-group row">
                        <?php echo Form::label(__('Pratiquez-vous la séparation des déchets ?'), null, ['class' => 'col-sm-4 control-label']); ?>
                        <div class="col-xs-12 col-sm-8">
                            <?php echo Form::select('separationMenage', ['non' => __('non'), 'oui' => __('oui')], null, ['class' => 'form-control separationMenage', 'required','disabled']); ?>
                        </div>
                    </div>


                    <div class="form-group row">
                        {{ Form::label(__("Comment gérez-vous l'eau de toilette ?"), null, ['class' => 'col-sm-4 control-label']) }}
                        <div class="col-xs-12 col-sm-8">
                            <?php echo Form::select('eauxToillette', ['Air Libre' => 'Air Libre', 'Fosse Septique' => 'Fosse Septique'], null, ['placeholder' => __('Selectionner une reponse'), 'class' => 'form-control', 'id' => 'eauxToillette', 'required','disabled']); ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        {{ Form::label(__("Comment gérez-vous l'eau de Vaisselle ?"), null, ['class' => 'col-sm-4 control-label']) }}
                        <div class="col-xs-12 col-sm-8">
                            <?php echo Form::select('eauxVaisselle', ['Air Libre' => 'Air Libre', 'Fosse Septique' => 'Fosse Septique'], null, ['placeholder' => __('Selectionner une reponse'), 'class' => 'form-control', 'id' => 'eauxVaisselle', 'required','disabled']); ?>
                        </div>
                    </div>


                    <div class="form-group row">
                        <?php echo Form::label(__('Existe-t-il un WC pour le ménage ?'), null, ['class' => 'col-sm-4 control-label']); ?>
                        <div class="col-xs-12 col-sm-8">
                            <?php echo Form::select('wc', ['non' => __('non'), 'oui' => __('oui')], null, ['class' => 'form-control wc', 'required','disabled']); ?>
                        </div>
                    </div>


                    <div class="form-group row">
                        {{ Form::label(__("Où procurez-Vous l'eau potable ?"), null, ['class' => 'col-sm-4 control-label']) }}
                        <div class="col-xs-12 col-sm-8">
                            <?php echo Form::select('sources_eaux', ['Pompe Hydraulique' => 'Pompe Hydraulique', 'Marigot' => 'Marigot', 'Puits' => 'Puits', 'Eaux Courante nationale' => 'Eaux Courante nationale', 'Autre' => 'Autre'], null, ['placeholder' => __('Selectionner une reponse'), 'class' => 'form-control sources_eaux', 'id' => 'sources_eaux', 'required','disabled']); ?>
                        </div>
                    </div>
                    <div class="form-group row" id="autreSourceEaux">
                        {{ Form::label(__(''), null, ['class' => 'col-sm-4 control-label']) }}
                        <div class="col-xs-12 col-sm-8">
                            <?php echo Form::text('autreSourceEau', null, ['id' => 'autreSourceEau', 'placeholder' => __('Autre'), 'class' => 'form-control autreSourceEau','disabled']); ?>
                        </div>
                    </div>

                    <hr class="panel-wide">

                    <div class="form-group row">
                        <?php echo Form::label(__('Effectuez vous vous-même les traitement phyto sanitaire dans vos champs ?'), null, ['class' => 'col-sm-4 control-label', 'required']); ?>
                        <div class="col-xs-12 col-sm-8">
                            <?php echo Form::select('traitementChamps', ['non' => __('non'), 'oui' => __('oui')], null, ['class' => 'form-control traitementChamps', 'required','disabled']); ?>
                        </div>
                    </div>
                    <div class="form-group row" id="infosPersonneTraitant">
                        <div class="form-group row">
                            <?php echo Form::label(__('Donnez le nom de la personne qui éffectue les traitement phyto sanitaire dans vos champs'), null, ['class' => 'col-sm-4 control-label']); ?>
                            <div class="col-xs-12 col-sm-8">
                                <?php echo Form::text('nomApplicateur', null, ['id' => 'nomApplicateur', 'class' => 'form-control nomApplicateur','disabled']); ?>
                            </div>
                        </div>
                        <div class="form-group row">
                            <?php echo Form::label(__('Donnez son numéro de téléphone'), null, ['class' => 'col-sm-4 control-label']); ?>
                            <div class="col-xs-12 col-sm-8">
                                <?php echo Form::text('numeroApplicateur', null, ['id' => 'numeroApplicateur', 'class' => 'form-control numeroApplicateur','disabled']); ?>
                            </div>
                        </div>
                    </div>

                    <div id="avoirMachine">

                        <div class="form-group row">
                            {{ Form::label(__('Quel type de machine ?'), null, ['class' => 'col-sm-4 control-label']) }}
                            <div class="col-xs-12 col-sm-8">
                                <?php echo Form::select('type_machines', ['Pulverisateur' => 'Pulverisateur', 'Atomiseur' => 'Atomiseur', 'Autre' => 'Autre'], null, ['placeholder' => __('Selectionner une reponse'), 'class' => 'form-control type_machines', 'id' => 'type_machines','disabled']); ?>
                            </div>
                        </div>
                        <div class="form-group row" id="etatatomiseurs">
                            <?php echo Form::label(__('La machine est-elle en bon état?'), null, ['class' => 'col-sm-4 control-label']); ?>
                            <div class="col-xs-12 col-sm-8">
                                <?php echo Form::select('etatatomiseur', ['oui' => __('oui'), 'non' => __('non')], null, ['id' => 'etatatomiseur', 'class' => 'form-control etatatomiseur','disabled']); ?>
                            </div>
                        </div>
                        <div class="form-group row" id="autreMachines">

                            <div class="form-group row">
                                <?php echo Form::label(__('Quel est son nom ?'), null, ['class' => 'col-sm-4 control-label']); ?>
                                <div class="col-xs-12 col-sm-8">
                                    <?php echo Form::text('autreMachine', null, ['class' => 'form-control autreMachine', 'placeholder' => __('Autre machine'),'disabled']); ?>
                                </div>
                            </div>
                            <div class="form-group row">
                                <?php echo Form::label(__('La machine est-elle en bon état?'), null, ['class' => 'col-sm-4 control-label']); ?>
                                <div class="col-xs-12 col-sm-8">
                                    <?php echo Form::select('etatAutreMachine', ['oui' => __('oui'), 'non' => __('non')], null, ['id' => 'etatAutreMachine', 'class' => 'form-control etatAutreMachine','disabled']); ?>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            {{ Form::label(__('Où gardez-vous cette machine ?'), null, ['class' => 'col-sm-4 control-label']) }}
                            <div class="col-xs-12 col-sm-8">
                                <?php echo Form::select('garde_machines', ['Dans la maison' => 'Dans la maison', 'Dans un magasin à la maison' => 'Dans un magasin à la maison', 'Au Champs' => 'Au Champs', 'Autre' => 'Autre'], null, ['placeholder' => __('Selectionner une reponse'), 'class' => 'form-control garde_machines', 'id' => 'garde_machines','disabled']); ?>
                            </div>
                        </div>
                        <div class="form-group row" id="autreEndroits">
                            <?php echo Form::label(__('Quel est l\'endroit ?'), null, ['class' => 'col-sm-4 control-label']); ?>
                            <div class="col-xs-12 col-sm-8">
                                <?php echo Form::text('autreEndroit', null, ['id' => 'autreEndroit', 'class' => 'form-control autreEndroit', 'placeholder' => __('Autre Endroit où la machine est gardée'),'disabled']); ?>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <?php echo Form::label(__('Avez-vous des Equipements de Protection Individuel ?'), null, ['class' => 'col-sm-4 control-label']); ?>
                        <div class="col-xs-12 col-sm-8">
                            <?php echo Form::select('equipements', ['non' => __('non'), 'oui' => __('oui')], null, ['class' => 'form-control equipements', 'required','disabled']); ?>
                        </div>
                    </div>

                    <hr class="panel-wide">


                    <div class="form-group row">
                        <?php echo Form::label(__('Votre conjoint(e) exerce-t-il/elle une autre activité générant des revenus ?'), null, ['class' => 'col-sm-4 control-label']); ?>
                        <div class="col-xs-12 col-sm-8">
                            <?php echo Form::select('activiteFemme', ['' => 'Selectionner une option', 'non' => __('non'), 'oui' => __('oui')], null, ['class' => 'form-control activiteFemme', 'required','disabled']); ?>
                        </div>
                    </div>
                    <div class="form-group row" id="typeActivite">
                        <?php echo Form::label(__('Quel type d\'activité ?'), null, ['class' => 'col-sm-4 control-label']); ?>
                        <div class="col-xs-12 col-sm-8">
                            <?php echo Form::select('typeActivite', ['' => 'Selectionner une option', 'Activite agricole' => __('Activité agricole'), 'Activite non agricole' => __('Activité non agricole')], null, ['class' => 'form-control typeActivite','disabled']); ?>
                        </div>
                    </div>
                    <div class="form-group row" id="agricole">
                        <?php echo Form::label(__('Nom de l\'activité'), null, ['class' => 'col-sm-4 control-label']); ?>
                        <div class="col-xs-12 col-sm-8">
                            <?php echo Form::select('nomActiviteAgricole', ['' => 'Selectionner une option', 'Cacao' => __('Cacao'), 'Café' => __('Café'), 'Hévéa' => __('Hévéa'), 'Palmier' => __('Palmier'), 'Anacarde' => __('Anacarde'), 'Banane' => __('Banane'), 'Riz' => __('Riz'), 'Igname' => __('Igname'), 'Manioc' => __('Manioc'), 'Maïs' => __('Maïs'), 'Autre' => __('Autre')], null, ['class' => 'form-control nomActiviteAgricole','disabled']); ?>
                        </div>
                    </div>
                    <div class="form-group row" id="autreAgricole">
                        <?php echo Form::label(__('Autre activité'), null, ['class' => 'col-sm-4 control-label']); ?>
                        <div class="col-xs-12 col-sm-8">
                            <?php echo Form::text('autreActiviteAgricole', null, ['class' => 'form-control autreActiviteAgricole', 'placeholder' => 'Autre activité','disabled']); ?>
                        </div>
                    </div>

                    <div class="form-group row" id="nonAgricole">
                        <?php echo Form::label(__('Nom de l\'activité'), null, ['class' => 'col-sm-4 control-label']); ?>
                        <div class="col-xs-12 col-sm-8">
                            <?php echo Form::select('nomActiviteNonAgricole', ['' => 'Selectionner une option', 'Commerce' => __('Commerce'), 'Elevage' => __('Elevage'), 'Autre' => __('Autre')], null, ['class' => 'form-control nomActiviteNonAgricole','disabled']); ?>
                        </div>
                    </div>
                    <div class="form-group row" id="autreNonAgricole">
                        <?php echo Form::label(__('Autre activité'), null, ['class' => 'col-sm-4 control-label']); ?>
                        <div class="col-xs-12 col-sm-8">
                            <?php echo Form::text('autreActiviteNonAgricole', null, ['class' => 'form-control autreActiviteNonAgricole', 'placeholder' => 'Autre activité','disabled']); ?>
                        </div>
                    </div>
                    <div class="form-group row" id="nombreHectareConjoint">
                        <?php echo Form::label(__('Quelle est sa superficie ?'), null, ['class' => 'col-sm-4 control-label']); ?>
                        <div class="col-xs-12 col-sm-8">
                            <input disabled type="number" min="0.1" name="nombreHectareConjoint" placeholder="Ex: 2 ha"
                                class="form-control nombreHectareConjoint" value="{{ old('nombreHectareConjoint', $menage->nombreHectareConjoint) }}">
                        </div>
                    </div>

                    <div id="autreInfos">
                        <div class="form-group row">
                            <?php echo Form::label(__('Comment avez-vous obtenu le capital de démarrage ?'), null, ['class' => 'col-sm-4 control-label']); ?>
                            <div class="col-xs-12 col-sm-8">
                                <?php echo Form::select('capitalDemarrage', ['' => 'Selectionner une option', 'Vente de cacao' => __('Vente de cacao'), 'AVEC' => __('AVEC'), 'Association' => __('Association'), 'Subvention' => __('Subvention'), 'Micro crédit' => __('Micro crédit'), 'Autre' => __('Autre')], null, ['id' => 'capitalDemarrage', 'class' => 'form-control capitalDemarrage','disabled']); ?>
                            </div>
                        </div>
                        <div class="form-group row" id="autreCapital">
                            <?php echo Form::label(__('Autre capital'), null, ['class' => 'col-sm-4 control-label']); ?>
                            <div class="col-xs-12 col-sm-8">
                                <?php echo Form::text('autreCapital', null, ['class' => 'form-control autreCapital', 'placeholder' => 'Autre capital','disabled']); ?>
                            </div>
                        </div>
                        <div class="form-group row">
                            <?php echo Form::label(__('Dépuis combien de temps(mois) pratiquez vous cette activité ?'), null, ['class' => 'col-sm-4 control-label']); ?>
                            <div class="col-xs-12 col-sm-8">
                                <?php echo Form::number('dureeActivite', null, ['id' => 'dureeActivite', 'class' => 'form-control dureeActivite', 'placeholder' => 'Le mois en chiffre','disabled']); ?>
                            </div>
                        </div>
                        <div class="form-group row">
                            <?php echo Form::label(__('Avez-vous bénéficiez d\'une formation ?'), null, ['class' => 'col-sm-4 control-label']); ?>
                            <div class="col-xs-12 col-sm-8">
                                <?php echo Form::select('formation', ['' => 'Selectionner une option', 'non' => 'non', 'oui' => __('oui')], null, ['id' => 'formation', 'class' => 'form-control formation','disabled']); ?>
                            </div>
                        </div>
                        <div class="form-group row" id="entite">
                            <?php echo Form::label(__('Par qui ? '), null, ['class' => 'col-sm-4 control-label']); ?>
                            <div class="col-xs-12 col-sm-8">
                                <?php echo Form::text('entite', null, ['class' => 'form-control entite', 'placeholder' => 'Entreprise / particulier','disabled']); ?>
                            </div>
                        </div>
                    </div>
                    
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <x-back route="{{ route('manager.suivi.menage.index') }}" />
@endpush
@push('script')
    <script type="text/javascript">
       
        $(document).ready(function() {
            $('#avoirMachine,#boisChauffes,#etatatomiseurs,#autreMachine,#autreEndroits,#autreSourceEaux,#etatEpis,#typeActivite,#agricole,#nonAgricole,#nombreHectareConjoint,#autreInfos,#autreCapital,#entite,#autreAgricole,#autreNonAgricole')
                .hide();
            $('.traitementChamps').change(function() {
                var traitementChamps = $('.traitementChamps').val();
                if (traitementChamps == 'oui') {
                    $('#avoirMachine').show('slow');
                    $('#infosPersonneTraitant').hide('slow');
                    $('#autreMachines').hide('slow');
                    $('.nomApplicateur').hide('slow');
                    $('#nomApplicateur').prop('required', false);
                    $('.nomApplicateur').val('');
                    $('.numeroApplicateur').hide('slow');
                    $('#numeroApplicateur').prop('required', false);
                    $('.numeroApplicateur').val('');
                    $('#type_machines').prop('required', true);
                    $('#garde_machines').prop('required', true);
                } else {
                    $('#avoirMachine').hide('slow');
                    $('#type_machines').prop('required', false);
                    $('#garde_machines').prop('required', false);
                    $('#infosPersonneTraitant').show('slow');
                    $('.nomApplicateur').show('slow');
                    $('#nomApplicateur').prop('required', true);
                    $('.numeroApplicateur').show('slow');
                    $('#numeroApplicateur').prop('required', true);
                    $('#autreMachines').hide('slow');
                }
            });
            if ($('.traitementChamps').val() == 'oui') {
                $('#infosPersonneTraitant').hide('slow');
                $('#autreMachines').hide('slow');
                $('.nomApplicateur').hide('slow');
                $('#nomApplicateur').prop('required', false);
                $('.nomApplicateur').val('');
                $('.numeroApplicateur').hide('slow');
                $('#numeroApplicateur').prop('required', false);
                $('.numeroApplicateur').val('');
                $('#avoirMachine').show('slow');
                $('#etatatomiseurs').show('slow');

            } else {
                $('#infosPersonneTraitant').show('slow');
                $('.nomApplicateur').show('slow');
                $('#nomApplicateur').prop('required', true);
                $('.numeroApplicateur').show('slow');
                $('#numeroApplicateur').prop('required', true);
                $('#avoirMachine').hide('slow');
                $('#etatatomiseurs').hide('slow');

            }

            $('.garde_machines').change(function() {
                var garde_machines = $('.garde_machines').val();
                if (garde_machines == 'Autre') {
                    $('#autreEndroits').show('slow');
                    $('.autreEndroit').show('slow');
                    $('#autreEndroit').prop('required', true);
                } else {
                    $('#autreEndroits').hide('slow');
                    $('#autreEndroit').prop('required', false);
                    $('.autreEndroit').val('');
                }
            });
            if ($('.garde_machines').val() == 'Autre') {
                $('#autreEndroits').show('slow');
                $('.autreEndroit').show('slow');
                $('#autreEndroit').prop('required', true);
            } else {
                $('#autreEndroits').hide('slow');
                $('#autreEndroit').prop('required', false);
                $('.autreEndroit').val('');
            }

            $('.type_machines').change(function() {
                var type_machines = $('.type_machines').val();
                if (type_machines == 'Atomiseur' || type_machines == 'Pulverisateur') {
                    $('#etatatomiseurs').show('slow');
                    $('.etatatomiseur').show('slow');
                    $('#etatatomiseur').prop('required', true);
                } else {
                    $('#etatatomiseurs').hide('slow');
                    $('#etatatomiseur').hide('slow');
                    $('.etatatomiseur').val('');
                    $('#etatatomiseur').prop('required', false);
                }
                if (type_machines == 'Autre') {
                    $('#autreMachines').show('slow');
                    $('#autreMachine').show('slow');
                    $('.autreMachine').show('slow');
                } else {
                    $('#autreMachine').hide('slow');
                    $('.autreMachine').val('');
                    $('#autreMachines').hide('slow');
                }
            });
            if ($('.type_machines').val() == 'Atomiseur' || $('.type_machines').val() == 'Pulverisateur') {
                $('#etatatomiseurs').show('slow');
                $('.etatatomiseur').show('slow');
                $('#etatatomiseur').prop('required', true);
            } else {
                $('#etatatomiseur').hide('slow');
                $('.etatatomiseur').val('');
                $('#etatatomiseur').prop('required', false);
            }

            if ($('.type_machines').val() == 'Autre') {
                $('#autreMachine').show('slow');
                $('.autreMachine').show('slow');
                $('#autreMachines').show('slow');
            } else {
                $('#autreMachine').hide('slow');
                $('.autreMachine').val('');
                $('#autreMachines').hide('slow');
            }

            $('.sources_energies').change(function() {
                var sources_energies = $('.sources_energies').find(":selected").map((key, item) => {
                    return item.textContent.trim();
                }).get();
                if (sources_energies.includes("Bois de chauffe")) {
                    $('#boisChauffes').show('slow');
                    $('.boisChauffe').show('slow');
                    $('#boisChauffe').prop('required', true);
                    $('.boisChauffe').css('display', 'block');
                } else {
                    $('#boisChauffes').hide('slow');
                    $('.boisChauffe').val('');
                    $('#boisChauffe').prop('required', false);
                }
            });
            if ($('.sources_energies').find(":selected").map((key, item) => {
                    return item.textContent.trim();
                }).get().includes("Bois de chauffe")) {
                $('#boisChauffes').show('slow');
                $('.boisChauffe').show('slow');
                $('#boisChauffe').prop('required', true);
                $('.boisChauffe').css('display', 'block');
            } else {
                $('#boisChauffes').hide('slow');
                $('.boisChauffe').val('');
                $('#boisChauffe').prop('required', false);
            }

            $('.sources_eaux').change(function() {
                var sources_eaux = $('.sources_eaux').val();
                if (sources_eaux == 'Autre') {
                    $('#autreSourceEaux').show('slow');
                    $('.autreSourceEau').show('slow');
                    $('#autreSourceEau').prop('required', true);
                } else {
                    $('#autreSourceEaux').hide('slow');
                    $('.autreSourceEau').val('');
                    $('#autreSourceEau').prop('required', false);
                }
            });

            if ($('.sources_eaux').val() == 'Autre') {
                $('#autreSourceEaux').show('slow');
                $('.autreSourceEau').show('slow');
                $('#autreSourceEau').prop('required', true);
            } else {
                $('#autreSourceEaux').hide('slow');
                $('.autreSourceEau').val('');
                $('#autreSourceEau').prop('required', false);
            }
            $('.activiteFemme').change(function() {
                var activiteFemme = $('.activiteFemme').val();
                if (activiteFemme == 'oui') {
                    $('#typeActivite').show('slow');
                    $('.typeActivite').show('slow');
                    $('.typeActivite').prop('required', true);

                } else {
                    $('#typeActivite').hide('slow');
                    $('.typeActivite').hide('slow');
                    $('.typeActivite').prop('required', false);
                }
            });
            if ($('.activiteFemme').val() == 'oui') {
                $('#typeActivite').show('slow');
                $('.typeActivite').show('slow');
                $('.typeActivite').prop('required', true);
            } else {
                $('#typeActivite').hide('slow');
                $('.typeActivite').hide('slow');
                $('.typeActivite').prop('required', false);
            }

            $('.typeActivite').change(function() {
                var typeActivite = $('.typeActivite').val();
                if (typeActivite == 'Activite agricole') {
                    $('#agricole').show('slow');
                    $('#agricole select[name="nomActiviteAgricole"]').show('slow');
                    $('#agricole select[name="nomActiviteAgricole"]').prop('required', true);
                    $('#nombreHectareConjoint').show('slow');
                    $('.nombreHectareConjoint').show('slow');
                    $('.nombreHectareConjoint').prop('required', true);
                } else {
                    $('#agricole').hide('slow');
                    $('#agricole select[name="nomActiviteAgricole"]').hide('slow');
                    $('#agricole select[name="nomActiviteAgricole"]').prop('required', false);
                    $('#agricole select[name="nomActiviteAgricole"]').find('option[value=""]').prop(
                        'selected',
                        true);
                    $('#nombreHectareConjoint').hide('slow');
                    $('.nombreHectareConjoint').hide('slow');
                    $('.nombreHectareConjoint').prop('required', false);
                    $('.nombreHectareConjoint').val('');
                }
            });
            if ($('.typeActivite').val() == 'Activite agricole') {
                $('#agricole').show('slow');
                $('#agricole select[name="nomActiviteAgricole"]').show('slow');
                $('#agricole select[name="nomActiviteAgricole"]').prop('required', true);
                $('#nombreHectareConjoint').show('slow');
                $('.nombreHectareConjoint').show('slow');
                $('.nombreHectareConjoint').prop('required', true);
            } else {
                $('#agricole').hide('slow');
                $('#agricole select[name="nomActiviteAgricole"]').hide('slow');
                $('#agricole select[name="nomActiviteAgricole"]').prop('required', false);
                $('#agricole select[name="nomActiviteAgricole"]').find('option[value=""]').prop(
                    'selected',
                    true);
                $('#nombreHectareConjoint').hide('slow');
                $('.nombreHectareConjoint').hide('slow');
                $('.nombreHectareConjoint').prop('required', false);
                $('.nombreHectareConjoint').val('');
            }
            $('.nomActiviteAgricole').change(function() {
                var nomActiviteAgricole = $('.nomActiviteAgricole').val();
                if (nomActiviteAgricole == 'Autre') {
                    $('#autreAgricole').show('slow');
                    $('.autreActiviteAgricole').show('slow');
                    $('.autreActiviteAgricole').prop('required', true);
                } else {
                    $('#autreAgricole').hide('slow');
                    $('.autreActiviteAgricole').val('');
                    $('.autreActiviteAgricole').prop('required', false);
                }
            });

            if ($('.nomActiviteAgricole').val() == 'Autre') {
                $('#autreAgricole').show('slow');
                $('.autreActiviteAgricole').show('slow');
                $('.autreActiviteAgricole').prop('required', true);
            } else {
                $('#autreAgricole').hide('slow');
                $('.autreActiviteAgricole').val('');
                $('.autreActiviteAgricole').prop('required', false);
            }
            $('.nomActiviteNonAgricole').change(function() {
                var nomActiviteNonAgricole = $('.nomActiviteNonAgricole').val();
                if (nomActiviteNonAgricole == 'Autre') {
                    $('#autreNonAgricole').show('slow');
                    $('.autreActiviteNonAgricole').show('slow');
                    $('.autreActiviteNonAgricole').prop('required', true);
                } else {
                    $('#autreNonAgricole').hide('slow');
                    $('.autreActiviteNonAgricole').val('');
                    $('.autreActiviteNonAgricole').prop('required', false);
                }
            });

            if ($('.nomActiviteNonAgricole').val() == 'Autre') {
                $('#autreNonAgricole').show('slow');
                $('.autreActiviteNonAgricole').show('slow');
                $('.autreActiviteNonAgricole').prop('required', true);
            } else {
                $('#autreNonAgricole').hide('slow');
                $('.autreActiviteNonAgricole').val('');
                $('.autreActiviteNonAgricole').prop('required', false);
            }
            $('.typeActivite').change(function() {
                var typeActivite = $('.typeActivite').val();
                if (typeActivite == 'Activite agricole' || typeActivite == 'Activite non agricole') {
                    $('#autreInfos').show('slow');
                    $('#autreInfos select[name="capitalDemarrage"]').show('slow');
                    $('#autreInfos select[name="capitalDemarrage"]').prop('required', true);
                    $('#autreInfos select[name="formation"]').show('slow');
                    $('#autreInfos select[name="formation"]').prop('required', true);
                    $('#autreInfos input[name="dureeActivite"]').show('slow');
                    $('#autreInfos input[name="dureeActivite"]').prop('required', true);
                } else {
                    $('#autreInfos').hide('slow');
                    $('#autreInfos select[name="capitalDemarrage"]').hide('slow');
                    $('#autreInfos select[name="capitalDemarrage"]').prop('required', false);
                    $('#autreInfos select[name="capitalDemarrage"]').find('option[value=""]').prop(
                        'selected',
                        true);
                    $('#autreInfos select[name="formation"]').hide('slow');
                    $('#autreInfos select[name="formation"]').prop('required', false);
                    $('#autreInfos select[name="formation"]').find('option[value=""]').prop('selected',
                        true);
                    $('#autreInfos input[name="dureeActivite"]').hide('slow');
                    $('#autreInfos input[name="dureeActivite"]').prop('required', false);
                    $('#autreInfos input[name="dureeActivite"]').val('');
                }
            });
            
            if ($('.typeActivite').val() == 'Activite agricole' || $('.typeActivite').val() == 'Activite non agricole') {
                $('#autreInfos').show('slow');
                $('#autreInfos select[name="capitalDemarrage"]').show('slow');
                $('#autreInfos select[name="capitalDemarrage"]').prop('required', true);
                $('#autreInfos select[name="formation"]').show('slow');
                $('#autreInfos select[name="formation"]').prop('required', true);
                $('#autreInfos input[name="dureeActivite"]').show('slow');
                $('#autreInfos input[name="dureeActivite"]').prop('required', true);
            } else {
                $('#autreInfos').hide('slow');
                $('#autreInfos select[name="capitalDemarrage"]').hide('slow');
                $('#autreInfos select[name="capitalDemarrage"]').prop('required', false);
                $('#autreInfos select[name="capitalDemarrage"]').find('option[value=""]').prop(
                    'selected',
                    true);
                $('#autreInfos select[name="formation"]').hide('slow');
                $('#autreInfos select[name="formation"]').prop('required', false);
                $('#autreInfos select[name="formation"]').find('option[value=""]').prop('selected',
                    true);
                $('#autreInfos input[name="dureeActivite"]').hide('slow');
                $('#autreInfos input[name="dureeActivite"]').prop('required', false);
                $('#autreInfos input[name="dureeActivite"]').val('');
            }
            $('.capitalDemarrage').change(function() {
                var capitalDemarrage = $('.capitalDemarrage').val();
                if (capitalDemarrage == 'Autre') {
                    $('#autreCapital').show('slow');
                    $('.autreCapital').show('slow');
                    $('.autreCapital').prop('required', true);
                } else {
                    $('#autreCapital').hide('slow');
                    $('.autreCapital').val('');
                    $('.autreCapital').prop('required', false);
                }
            });
            if ($('.capitalDemarrage').val() == 'Autre') {
                $('#autreCapital').show('slow');
                $('.autreCapital').show('slow');
                $('.autreCapital').prop('required', true);
            } else {
                $('#autreCapital').hide('slow');
                $('.autreCapital').val('');
                $('.autreCapital').prop('required', false);
            }
            $('.formation').change(function() {
                var formation = $('.formation').val();
                if (formation == 'oui') {
                    $('#entite').show('slow');
                    $('.entite').show('slow');
                    $('.entite').prop('required', true);
                } else {
                    $('#entite').hide('slow');
                    $('.entite').val('');
                    $('.entite').prop('required', false);
                }
            });
            if ($('.formation').val() == 'oui') {
                $('#entite').show('slow');
                $('.entite').show('slow');
                $('.entite').prop('required', true);
            } else {
                $('#entite').hide('slow');
                $('.entite').val('');
                $('.entite').prop('required', false);
            }
        });
    </script>
@endpush
