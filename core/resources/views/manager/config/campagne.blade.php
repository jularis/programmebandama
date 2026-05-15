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
                                    <th>@lang('Produit')</th>
                                    <th>@lang('Campagne')</th>
                                    <th>@lang('Prix d\'achat')</th>
                                    <th>@lang('Prix Bord Champ')</th>
                                    <th>@lang('Prime')</th>
                                    <th>@lang('Date début')</th>
                                    <th>@lang('Date fin')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Last Update')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($campagnes as $campagne)
                                    <tr>
                                        <td>
                                            <span class="fw-bold">{{ __($campagne->produit) }}</span>
                                        </td>

                                        <td>
                                            <span>{{ __($campagne->nom) }}</span>
                                        </td>
                                        <td>
                                            <span>{{ $campagne->prix_achat }} {{ __($general->cur_text) }}</span>
                                        </td>
                                        <td>
                                            <span>{{ $campagne->prix_champ }} {{ __($general->cur_text) }}</span>
                                        </td>
                                        <td>
                                            <span>{{ $campagne->prime }} {{ __($general->cur_text) }}</span>
                                        </td>
                                        <td>
                                            <span>{{ __($campagne->periode_debut) }}</span>
                                        </td>
                                        <td>
                                            <span>{{ __($campagne->periode_fin) }}</span>
                                        </td>
                                        <td>
                                            @php
                                                echo $campagne->statusBadge;
                                            @endphp
                                        </td>

                                        <td>
                                            <span class="d-block">{{ showDateTime($campagne->updated_at) }}</span>
                                            <span>{{ diffForHumans($campagne->updated_at) }}</span>
                                        </td>

                                        <td>
                                            <button type="button" class="btn btn-sm btn-outline--primary  updateCampagne"
                                                data-id="{{ $campagne->id }}" 
                                                data-produit="{{ $campagne->produit }}"
                                                data-nom="{{ $campagne->nom }}"
                                                data-debut="{{ $campagne->periode_debut }}"
                                                data-fin="{{ $campagne->periode_fin }}"
                                                data-prix="{{ $campagne->prix_achat }}"
                                                data-champ="{{ $campagne->prix_champ }}"
                                                data-prime="{{ $campagne->prime }}"><i
                                                 class="las la-pen"></i>@lang('Edit')</button>

                                            @if ($campagne->status == Status::DISABLE)
                                                <button type="button"
                                                    class="btn btn-sm btn-outline--success confirmationBtn"
                                                    data-action="{{ route('manager.settings.campagne.status', $campagne->id) }}"
                                                    data-question="@lang('Etes-vous sûr de vouloir activer cette campagne?')">
                                                    <i class="la la-eye"></i> @lang('Activé')
                                                </button>
                                            @else
                                                <button type="button"
                                                    class="btn btn-sm btn-outline--danger confirmationBtn"
                                                    data-action="{{ route('manager.settings.campagne.status', $campagne->id) }}"
                                                    data-question="@lang('Etes-vous sûr de vouloir désactiver cette campagne?')">
                                                    <i class="la la-eye-slash"></i>@lang('Désactivé')
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
                @if ($campagnes->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($campagnes) }}
                    </div>
                @endif
            </div>
        </div>
        </x-setting-card>
    <div id="unitModel" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Ajouter une campagne')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i> </button>
                </div>
                <form action="{{ route('manager.settings.campagne.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                    <div class="form-group">
                            <label>@lang('Produit')</label>
                            <select class="form-control" name="produit" required>
                                <option value="">@lang('Selectionner une option')</option> 
                                    <option value="{{ __('Cacao') }}">{{ __('Cacao') }}</option>
                                    <option value="{{ __('Cafe') }}">{{ __('Cafe') }}</option>
                                    <option value="{{ __('Anacarde') }}">{{ __('Anacarde') }}</option>
                                 
                            </select>
                        </div>

                        <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group">
            {{ Form::label(__('Nom de la campagne'), null, ['class' => 'control-label required']) }}
            {!! Form::text('nom', null, array('placeholder' => __('Campagne 2021-2022'),'class' => 'form-control','required')) !!}
        </div>
    </div>


    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group">
            {{ Form::label(__('Date de début'), null, ['class' => 'control-label required']) }}
            {!! Form::date('periode_debut', null, array('class' => 'form-control','required')) !!}
        </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group">
            {{ Form::label(__('Date de fin'), null, ['class' => 'control-label required']) }}
            {!! Form::date('periode_fin', null, array('class' => 'form-control','required')) !!}
        </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group">
            {{ Form::label(__("Prix d'achat"), null, ['class' => 'control-label']) }}
            {!! Form::number('prix_achat', null, array('class' => 'form-control','required')) !!}
        </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group">
            {{ Form::label(__("Prix bord champ"), null, ['class' => 'control-label']) }}
            {!! Form::number('prix_champ', null, array('class' => 'form-control','required')) !!}
        </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group">
            {{ Form::label(__('Prime du producteur'), null, ['class' => 'control-label required']) }}
            {!! Form::number('prime', null, array('class' => 'form-control','min'=>'50', 'max'=>'5000')) !!}
        </div>
    </div> 

                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn--primary w-100 h-45 ">@lang('Envoyer')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <div id="updateCampagneModel" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Mise à jour de la campagne')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form action="{{ route('manager.settings.campagne.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="id">
                    <div class="modal-body">
                    <div class="form-group">
                            <label>@lang('Produit')</label>
                            <select class="form-control" name="produit" required>
                                <option value="">@lang('Selectionner une option')</option> 
                                    <option value="{{ __('Cacao') }}">{{ __('Cacao') }}</option>
                                    <option value="{{ __('Cafe') }}">{{ __('Cafe') }}</option>
                                    <option value="{{ __('Anacarde') }}">{{ __('Anacarde') }}</option>
                                 
                            </select>
                        </div>

                        <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group">
            {{ Form::label(__('Nom de la campagne'), null, ['class' => 'control-label required']) }}
            {!! Form::text('nom', null, array('placeholder' => __('Campagne 2021-2022'),'class' => 'form-control','required')) !!}
        </div>
    </div>


    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group">
            {{ Form::label(__('Date de début'), null, ['class' => 'control-label required']) }}
            {!! Form::date('periode_debut', null, array('class' => 'form-control','required')) !!}
        </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group">
            {{ Form::label(__('Date de fin'), null, ['class' => 'control-label required']) }}
            {!! Form::date('periode_fin', null, array('class' => 'form-control','required')) !!}
        </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group">
            {{ Form::label(__("Prix d'achat"), null, ['class' => 'control-label']) }}
            {!! Form::number('prix_achat', null, array('class' => 'form-control','required')) !!}
        </div>
    </div> 
    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group">
            {{ Form::label(__("Prix Bord Champ"), null, ['class' => 'control-label']) }}
            {!! Form::number('prix_champ', null, array('class' => 'form-control','required')) !!}
        </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group">
            {{ Form::label(__('Prime du producteur'), null, ['class' => 'control-label required']) }}
            {!! Form::number('prime', null, array('class' => 'form-control')) !!}
        </div>
    </div> 

                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn--primary w-100 h-45">@lang('Envoyer')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')
    <button class="btn btn-sm btn-outline--primary addCampagne"><i class="las la-plus"></i>@lang("Ajouter nouveau")</button>
@endpush


@push('script')
    <script>
        (function($) {
            "use strict";
            $('.addCampagne').on('click', function() {
                $('#unitModel').modal('show');
            });

            $('.updateCampagne').on('click', function() {
                var modal = $('#updateCampagneModel');
                 
                modal.find('input[name=id]').val($(this).data('id'));
                modal.find('select[name=produit]').val($(this).data('produit'));
                modal.find('input[name=nom]').val($(this).data('nom'));
                modal.find('input[name=periode_debut]').val($(this).data('debut'));
                modal.find('input[name=periode_fin]').val($(this).data('fin'));
                modal.find('input[name=prix_achat]').val($(this).data('prix'));
                modal.find('input[name=prix_champ]').val($(this).data('champ'));
                modal.find('input[name=prime]').val($(this).data('prime'));
                modal.modal('show');
            });
        })(jQuery);
    </script>
@endpush
