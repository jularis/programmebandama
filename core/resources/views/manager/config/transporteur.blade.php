@extends('manager.layouts.app')
@section('panel')
    <x-setting-sidebar :activeMenu="$activeSettingMenu" />
    <x-setting-card>
        <x-slot name="header">
            <div class="s-b-n-header" id="tabs">
                <h2 class="mb-0 p-20 f-21 font-weight-normal text-capitalize border-bottom-grey">
                    @lang($pageTitle)</h2>
            </div>
        </x-slot>
        <div class="col-lg-12 col-md-12 ntfcn-tab-content-left w-100 p-4 ">
            <div class="card b-radius--10 ">
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                <th>@lang('Cooperative')</th> 
                                <th>@lang('Entreprise')</th>
                                <th>@lang('Photo')</th>
                                    <th>@lang('Nom')</th>
                                    <th>@lang('Prenom')</th>
                                    <th>@lang('Sexe')</th>
                                    <th>@lang('Contact 1')</th>
                                    <th>@lang('Contact 2')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Last Update')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transporteurs as $transporteur)
                                    <tr>
                                        <td>
                                            <span>{{ $transporteur->entreprise->nom_entreprise ?? "" }}</span>
                                        </td> 
                                        <td>
                                        <img class="" src="{{ asset('core/storage/app/transporteur/' .$transporteur->photo) }}" alt="" width="200px">
                                        </td> 
                                    <td>
                                            <span>{{  $transporteur->nom }}</span>
                                        </td> 
                                        
                                        <td>
                                            <span>{{ __($transporteur->prenoms) }}</span>
                                        </td>
                                        <td>
                                            <span>{{ __($transporteur->sexe) }}</span>
                                        </td>
                                        <td>
                                            <span>{{ __($transporteur->phone1) }}</span>
                                        </td>
                                        <td>
                                            <span>{{ __($transporteur->phone2) }}</span>
                                        </td>
                                        <td>
                                            @php
                                                echo $transporteur->statusBadge;
                                            @endphp
                                        </td>

                                        <td>
                                            <span class="d-block">{{ showDateTime($transporteur->updated_at) }}</span>
                                            <span>{{ diffForHumans($transporteur->updated_at) }}</span>
                                        </td>

                                        <td>
                                            <button type="button" class="btn btn-sm btn-outline--primary  updateType"
                                                data-id="{{ $transporteur->id }}" 
                                                data-entreprise="{{ $transporteur->entreprise_id }}"
                                                data-nom="{{ $transporteur->nom }}"
                                                data-prenoms="{{ $transporteur->prenoms }}"
                                                data-sexe = "{{ $transporteur->sexe }}"
                                                data-datenaiss = "{{ $transporteur->date_naiss }}"
                                                data-phone1 = "{{ $transporteur->phone1 }}"
                                                data-phone2 = "{{ $transporteur->phone2 }}"
                                                data-nationalite = "{{ $transporteur->nationalite }}"
                                                data-niveauetude = "{{ $transporteur->niveau_etude }}"
                                                data-typepiece = "{{ $transporteur->type_piece }}"
                                                data-numpiece = "{{ $transporteur->num_piece }}"
                                                data-numpermis = "{{ $transporteur->num_permis }}"><i
                                                    class="las la-pen"></i>@lang('Edit')</button>

                                            @if ($transporteur->status == Status::DISABLE)
                                                <button type="button"
                                                    class="btn btn-sm btn-outline--success confirmationBtn"
                                                    data-action="{{ route('manager.settings.transporteur.status', $transporteur->id) }}"
                                                    data-question="@lang('Etes-vous sûr de vouloir activer ce transporteur?')">
                                                    <i class="la la-eye"></i> @lang('Active')
                                                </button>
                                            @else
                                                <button type="button"
                                                    class="btn btn-sm btn-outline--danger confirmationBtn"
                                                    data-action="{{ route('manager.settings.transporteur.status', $transporteur->id) }}"
                                                    data-question="@lang('Etes-vous sûr de vouloir désactiver ce transporteur?')">
                                                    <i class="la la-eye-slash"></i>@lang('Désactive')
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                    </tr>
                                @endforelse

                            </tbody>
                        </table>
                    </div>
                </div>
                @if ($transporteurs->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($transporteurs) }}
                    </div>
                @endif
            </div>
        </div>
    </x-setting-card>
    <div id="typeModel" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Ajouter un Magasin de Section')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i> </button>
                </div>
                <form action="{{ route('manager.settings.transporteur.store') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name='id'>
                        <div class="row">
                            <div class="col-lg-12">
                                <label for="entreprise_id" class="control-label">@lang('Entreprise')</label>
                                <select class="form-control" name="entreprise_id" id="entreprise_id" required>
                                    <option value="">@lang('Choisir une entreprise')</option>
                                    @foreach ($entreprises as $entreprise)
                                        <option value="{{ $entreprise->id }}">{{ $entreprise->nom_entreprise }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <?php echo Form::label(__('Nom du transporteur'), null, ['class' => 'col-sm-4 control-label required']); ?>
                            <div class="col-xs-12 col-sm-8">
                                <?php echo Form::text('nom', null, ['placeholder' => __('Nom du transporteur'), 'class' => 'form-control', 'required']); ?>
                            </div>
                        </div>

                        <div class="form-group row">
                            <?php echo Form::label(__('Prenoms du transporteur'), null, ['class' => 'col-sm-4 control-label required']); ?>
                            <div class="col-xs-12 col-sm-8">
                                <?php echo Form::text('prenoms', null, ['placeholder' => __('Prenoms du transporteur'), 'class' => 'form-control', 'required']); ?>
                            </div>
                        </div>


                        <div class="form-group row">
                            <?php echo Form::label(__('Genre'), null, ['class' => 'col-sm-4 control-label']); ?>
                            <div class="col-xs-12 col-sm-8">
                                <?php echo Form::select('sexe', ['Homme' => __('Homme'), 'Femme' => __('Femme')], null, ['class' => 'form-control', 'required']); ?>

                            </div>
                        </div>

                        <div class="form-group row">
                            <?php echo Form::label(__('Date de naissance'), null, ['class' => 'col-sm-4 control-label required']); ?>
                            <div class="col-xs-12 col-sm-8">
                                <?php echo Form::date('date_naiss', null, ['class' => 'form-control', 'id' => 'datenais', 'required']); ?>
                            </div>
                        </div>

                        <div class="form-group row">
                            <?php echo Form::label(__('Numero de téléphone 1'), null, ['class' => 'col-sm-4 control-label required']); ?>
                            <div class="col-xs-12 col-sm-8">
                                <?php echo Form::text('phone1', null, ['class' => 'form-control phone', 'required']); ?>
                            </div>
                        </div>

                        <div class="form-group row">
                            <?php echo Form::label(__('Numero de téléphone 2'), null, ['class' => 'col-sm-4 control-label']); ?>
                            <div class="col-xs-12 col-sm-8">
                                <?php echo Form::text('phone2', null, ['class' => 'form-control phone']); ?>
                            </div>
                        </div>

                        <div class="form-group row">
                            <?php echo Form::label(__('Pays'), null, ['class' => 'col-sm-4 control-label required']); ?>
                            <div class="col-xs-12 col-sm-8">
                                <select name="nationalite" id="nationalite" class="form-control nationalite select-picker"
                                    data-live-search="true" required>
                                    <option value="">@lang('Selectionner une option')</option>
                                    @foreach ($countries as $item)
                                        <option data-tokens="{{ $item->iso3 }}"
                                            data-phonecode = "{{ $item->phonecode }}"
                                            data-content="<span class='flag-icon flag-icon-{{ strtolower($item->iso) }} flag-icon-squared'></span> {{ $item->nicename }}"
                                            value="{{ $item->nicename }}">{{ $item->nicename }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <?php echo Form::label(__('Niveau d\'étude'), null, ['class' => 'col-sm-4 control-label']); ?>
                            <div class="col-xs-12 col-sm-8">
                                <?php echo Form::select('niveau_etude', ['Primaire' => 'Primaire', 'Collège (6e à 3ème)' => 'Collège (6e à 3ème)', 'Lycée (2nde à Tle)' => 'Lycée (2nde à Tle)', 'Superieur (BAC et Plus)' => 'Superieur (BAC et Plus)', 'Aucun' => 'Aucun'], null, ['placeholder' => __('Selectionner une option'), 'class' => 'form-control', 'required']); ?>
                            </div>
                        </div>

                        <div class="form-group row">
                            <?php echo Form::label(__('Type de pièces'), null, ['class' => 'col-sm-4 control-label required']); ?>
                            <div class="col-xs-12 col-sm-8">
                                <?php echo Form::select('type_piece', ['CNI' => 'CNI', 'Carte Consulaire' => 'Carte Consulaire', 'Passeport' => 'Passeport', 'Attestation' => 'Attestation', 'Extrait de naissance' => 'Extrait de naissance', 'Permis de conduire' => 'Permis de conduire', 'CMU' => 'CMU', 'Pas Disponible' => 'Pas Disponible'], null, ['placeholder' => __('Selectionner une option'), 'class' => 'form-control', 'required']); ?>
                            </div>
                        </div>

                        <div class="form-group row">
                            <?php echo Form::label(__('N° de la pièce d\'identité'), null, ['class' => 'col-sm-4 control-label required']); ?>
                            <div class="col-xs-12 col-sm-8">
                                <?php echo Form::text('num_piece', null, ['placeholder' => '', 'class' => 'form-control', 'required']); ?>
                            </div>
                        </div>
                        <div class="form-group row">
                            <?php echo Form::label(__('N° du permis de conduire'), null, ['class' => 'col-sm-4 control-label required']); ?>
                            <div class="col-xs-12 col-sm-8">
                                <?php echo Form::text('num_permis', null, ['placeholder' => '', 'class' => 'form-control', 'required']); ?>
                            </div>
                        </div>

                        <hr class="panel-wide">

                        <div class="form-group row">

                            <?php echo Form::label(__('Photo du transporteur'), null, ['class' => 'col-sm-4 control-label']); ?>
                            <div class="col-xs-12 col-sm-8">
                                <input type="file" name="photo" accept="image/*" class="form-control dropify-fr"
                                    data-max-file-size="2M" data-msg-required="Choisissez une image sur votre disque"
                                    id="image">
                            </div>
                        </div>
                        <div class="form-group row">

                            <?php echo Form::label(__('Pièce d\'identité'), null, ['class' => 'col-sm-4 control-label']); ?>
                            <div class="col-xs-12 col-sm-8">
                                <input type="file" name="photo_piece_identite" accept="image/*"
                                    class="form-control dropify-fr" data-max-file-size="2M"
                                    data-msg-required="Choisissez une image sur votre disque" id="piece">
                            </div>
                        </div>
                        <div class="form-group row">

                            <?php echo Form::label(__('Permis de conduire'), null, ['class' => 'col-sm-4 control-label']); ?>
                            <div class="col-xs-12 col-sm-8">
                                <input type="file" name="photo_permis_conduire" accept="image/*"
                                    class="form-control dropify-fr" data-max-file-size="2M"
                                    data-msg-required="Choisissez une image sur votre disque" id="permis">
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn--primary w-100 h-45 ">@lang('Enregistrer')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')
    <button class="btn btn-sm btn-outline--primary addType"><i class="las la-plus"></i>@lang('Ajouter nouveau')</button>
@endpush


@push('script')
    <script>
        (function($) {
            "use strict";
            $('.addType').on('click', function() {
                $('#typeModel').modal('show');
            });

            $('.updateType').on('click', function() {
                var modal = $('#typeModel'); 
                modal.find('input[name=id]').val($(this).data('id')); 
                modal.find('select[name=entreprise_id]').val($(this).data('entreprise')); 
                modal.find('input[name=nom]').val($(this).data('nom'));  
                modal.find('input[name=prenoms]').val($(this).data('prenoms'));  
                modal.find('input[name=sexe]').val($(this).data('sexe')); 
                modal.find('input[name=date_naiss]').val($(this).data('datenaiss')); 
                modal.find('input[name=phone1]').val($(this).data('phone1')); 
                modal.find('input[name=phone2]').val($(this).data('phone2')); 
                modal.find('select[name=nationalite]').val($(this).data('nationalite')); 
                modal.find('select[name=niveau_etude]').val($(this).data('niveauetude')); 
                modal.find('select[name=type_piece]').val($(this).data('typepiece')); 
                modal.find('input[name=num_piece]').val($(this).data('numpiece')); 
                modal.find('input[name=num_permis]').val($(this).data('numpermis'));  
                modal.modal('show');
            });
        })(jQuery);

        $("#section").chained("#user");
    </script>
@endpush
