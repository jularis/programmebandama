@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card b-radius--10 ">
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Campagne')</th>
                                    <th>@lang('Période')</th>
                                    <th>@lang('Prix Bord Champ(FCFA/Kg)')</th>
                                    <th>@lang('Date début')</th>
                                    <th>@lang('Date fin')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Last Update')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($periodes as $periode)
                                    <tr>
                                    <td>
                                            <span class="fw-bold">{{ __($periode->campagne->nom) }}</span>
                                        </td>

                                        <td>
                                            <span class="fw-bold">{{ __($periode->nom) }}</span>
                                        </td>
 

                                        <td>
                                            <span>{{ $periode->prix_champ }} {{ __($general->cur_text) }}</span>
                                        </td>
                                        <td>
                                            <span>{{ __($periode->periode_debut) }}</span>
                                        </td>
                                        <td>
                                            <span>{{ __($periode->periode_fin) }}</span>
                                        </td>
                                        <td>
                                            @php
                                                echo $periode->statusBadge;
                                            @endphp
                                        </td>

                                        <td>
                                            <span class="d-block">{{ showDateTime($periode->updated_at) }}</span>
                                            <span>{{ diffForHumans($periode->updated_at) }}</span>
                                        </td>

                                        <td>
                                     
                                            <button type="button" class="btn btn-sm btn-outline--primary  updateCampagne"
                                                data-id="{{ $periode->id }}"  
                                                data-nom="{{ $periode->nom }}" 
                                                data-debut="{{ $periode->periode_debut }}"
                                                data-fin="{{ $periode->periode_fin }}"
                                                data-prixchamp="{{ $periode->prix_champ }}"><i
                                                 class="las la-pen"></i>@lang('Edit')</button>

                                            @if ($periode->status == Status::DISABLE)
                                                <button type="button"
                                                    class="btn btn-sm btn-outline--success confirmationBtn"
                                                    data-action="{{ route('admin.config.campagne.periodeStatus', $periode->id) }}"
                                                    data-question="@lang('Etes-vous sûr de vouloir activer cette periode?')">
                                                    <i class="la la-eye"></i> @lang('Activé')
                                                </button>
                                            @else
                                                <button type="button"
                                                    class="btn btn-sm btn-outline--danger confirmationBtn"
                                                    data-action="{{ route('admin.config.campagne.periodeStatus', $periode->id) }}"
                                                    data-question="@lang('Etes-vous sûr de vouloir désactiver cette periode?')">
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
                @if ($periodes->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($periodes) }}
                    </div>
                @endif
            </div>
        </div>
    </div>
    <div id="unitModel" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Ajouter une Période de Campagne')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i> </button>
                </div>
                <form action="{{ route('admin.config.campagne.periodeStore') }}" method="POST">
                    @csrf
                    <div class="modal-body"> 
        <input type="hidden" name="campagne" value="{{ request()->idcamp}}">
                        <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group">
            {{ Form::label(__('Nom de la période'), null, ['class' => 'control-label required']) }}
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
            {{ Form::label(__("Prix Bord Champ"), null, ['class' => 'control-label']) }}
            {!! Form::number('prix_champ', null, array('class' => 'form-control','required')) !!}
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
                    <h5 class="modal-title">@lang('Mise à jour de la Période de Campagne')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form action="{{ route('admin.config.campagne.periodeStore') }}" method="POST">
                    @csrf
                    <input type="hidden" name="id">
                    <div class="modal-body">
                    <input type="hidden" name="campagne" value="{{ request()->idcamp}}">

                        <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group">
            {{ Form::label(__('Nom de la période'), null, ['class' => 'control-label required']) }}
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
            {{ Form::label(__("Prix Bord Champ"), null, ['class' => 'control-label']) }}
            {!! Form::number('prix_champ', null, array('class' => 'form-control','required')) !!}
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
                modal.find('input[name=nom]').val($(this).data('nom'));
                modal.find('input[name=periode_debut]').val($(this).data('debut'));
                modal.find('input[name=periode_fin]').val($(this).data('fin'));
                modal.find('input[name=prix_champ]').val($(this).data('prixchamp')); 
                modal.modal('show');
            });
        })(jQuery);
    </script>
@endpush
