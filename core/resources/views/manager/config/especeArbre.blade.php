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
                                    <th>@lang('Nom')</th>
                                    <th>@lang('Nom Scientifique')</th>
                                    <th>@lang('Strate')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Last Update')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($especeArbre as $espece)
                                    <tr>

                                        <td>
                                            <span>{{ __($espece->nom) }}</span>
                                        </td> 
                                        <td>
                                            <span>{{ __($espece->nom_scientifique) }}</span>
                                        </td> 
                                        <td>
                                            <span>{{ __($espece->strate) }}</span>
                                        </td>
                                        <td>
                                            @php
                                                echo $espece->statusBadge;
                                            @endphp
                                        </td>

                                        <td>
                                            <span class="d-block">{{ showDateTime($espece->updated_at) }}</span>
                                            <span>{{ diffForHumans($espece->updated_at) }}</span>
                                        </td>

                                        <td>
                                            <button type="button" class="btn btn-sm btn-outline--primary  updateTravaux"
                                                data-id="{{ $espece->id }}" 
                                                data-nom="{{ $espece->nom }}"
                                                data-strate="{{ $espece->strate }}"
                                                data-scientifique="{{ $espece->nom_scientifique }}"><i
                                                 class="las la-pen"></i>@lang('Edit')</button>

                                            @if ($espece->status == Status::DISABLE)
                                                <button type="button"
                                                    class="btn btn-sm btn-outline--success confirmationBtn"
                                                    data-action="{{ route('manager.settings.especeArbre.status', $espece->id) }}"
                                                    data-question="@lang('Etes-vous sûr de vouloir activer cet élément?')">
                                                    <i class="la la-eye"></i> @lang('Active')
                                                </button>
                                            @else
                                                <button type="button"
                                                    class="btn btn-sm btn-outline--danger confirmationBtn"
                                                    data-action="{{ route('manager.settings.especeArbre.status', $espece->id) }}"
                                                    data-question="@lang('Etes-vous sûr de vouloir désactiver cet élément?')">
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
                @if ($especeArbre->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($especeArbre) }}
                    </div>
                @endif
            </div>
        </div>
        </x-setting-card>
    <div id="especeModel" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Ajouter un Espèce Arbre')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i> </button>
                </div>
                <form action="{{ route('manager.settings.especeArbre.store') }}" method="POST">
                    @csrf
                    <div class="modal-body"> 

                    <input type="hidden" name='id'>     
        <div class="form-group row">
            {{ Form::label(__('Nom du Espèce Arbre'), null, ['class' => 'control-label col-sm-4']) }}
            <div class="col-xs-12 col-sm-8 col-md-8">
            {!! Form::text('nom', null, array('placeholder' => __('Nom du Espèce Arbre'),'class' => 'form-control','required')) !!}
        </div>
    </div>
    <div class="form-group row">
            {{ Form::label(__('Nom Scientifique'), null, ['class' => 'control-label col-sm-4']) }}
            <div class="col-xs-12 col-sm-8 col-md-8">
            {!! Form::text('nom_scientifique', null, array('placeholder' => __('Nom Scientifique'),'class' => 'form-control')) !!}
        </div>
    </div>
    <div class="form-group row">
            {{ Form::label(__('Strate'), null, ['class' => 'control-label col-sm-4']) }}
            <div class="col-xs-12 col-sm-8 col-md-8">
            {!! Form::number('strate', null, array('placeholder' => __('Strate'),'class' => 'form-control','required')) !!}
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
    <x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')
    <button class="btn btn-sm btn-outline--primary addTravaux"><i class="las la-plus"></i>@lang("Ajouter nouveau")</button>
@endpush


@push('script')
    <script>
        (function($) {
            "use strict";
            $('.addTravaux').on('click', function() {
                $('#especeModel').modal('show');
            });

            $('.updateTravaux').on('click', function() {
                var modal = $('#especeModel'); 
                modal.find('input[name=id]').val($(this).data('id'));
                modal.find('input[name=nom]').val($(this).data('nom')); 
                modal.find('input[name=nom_scientifique]').val($(this).data('scientifique'));
                modal.find('input[name=strate]').val($(this).data('strate')); 
                modal.modal('show');
            });
        })(jQuery);
    </script>
@endpush
