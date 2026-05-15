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
                                    <th>@lang('Status')</th>
                                    <th>@lang('Last Update')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($typeArchive as $archive)
                                    <tr>

                                        <td>
                                            <span>{{ __($archive->nom) }}</span>
                                        </td>  
                                        <td>
                                            @php
                                                echo $archive->statusBadge;
                                            @endphp
                                        </td>

                                        <td>
                                            <span class="d-block">{{ showDateTime($archive->updated_at) }}</span>
                                            <span>{{ diffForHumans($archive->updated_at) }}</span>
                                        </td>

                                        <td>
                                            <button type="button" class="btn btn-sm btn-outline--primary  updateTravaux"
                                                data-id="{{ $archive->id }}" 
                                                data-nom="{{ $archive->nom }}"><i
                                                 class="las la-pen"></i>@lang('Edit')</button>

                                            @if ($archive->status == Status::DISABLE)
                                                <button type="button"
                                                    class="btn btn-sm btn-outline--success confirmationBtn"
                                                    data-action="{{ route('manager.settings.typeArchive.status', $archive->id) }}"
                                                    data-question="@lang('Etes-vous sûr de vouloir activer cet élément?')">
                                                    <i class="la la-eye"></i> @lang('Active')
                                                </button>
                                            @else
                                                <button type="button"
                                                    class="btn btn-sm btn-outline--danger confirmationBtn"
                                                    data-action="{{ route('manager.settings.typeArchive.status', $archive->id) }}"
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
                @if ($typeArchive->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($typeArchive) }}
                    </div>
                @endif
            </div>
        </div>
        </x-setting-card>
    <div id="archiveModel" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Ajouter un type archive')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i> </button>
                </div>
                <form action="{{ route('manager.settings.typeArchive.store') }}" method="POST">
                    @csrf
                    <div class="modal-body"> 

                    <input type="hidden" name='id'>     
        <div class="form-group row">
            {{ Form::label(__('Nom du Espèce Arbre'), null, ['class' => 'control-label col-sm-4']) }}
            <div class="col-xs-12 col-sm-8 col-md-8">
            {!! Form::text('nom', null, array('placeholder' => __('Type Archive'),'class' => 'form-control','required')) !!}
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
                $('#archiveModel').modal('show');
            });

            $('.updateTravaux').on('click', function() {
                var modal = $('#archiveModel'); 
                modal.find('input[name=id]').val($(this).data('id'));
                modal.find('input[name=nom]').val($(this).data('nom'));  
                modal.modal('show');
            });
        })(jQuery);
    </script>
@endpush
