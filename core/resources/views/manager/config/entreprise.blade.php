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
                                    <th>@lang('Adresse mail')</th>
                                    <th>@lang('Telephone')</th>
                                    <th>@lang('Adresse')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Last Update')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($entreprises as $entreprise)
                                    <tr>
                                        <td>
                                            <span>{{ $entreprise->nom_entreprise }}</span>
                                        </td>

                                        <td>
                                            <span>{{ __($entreprise->telephone_entreprise) }}</span>
                                        </td>
                                        <td>
                                            <span>{{ __($entreprise->mail_entreprise) }}</span>
                                        </td>
                                        <td>
                                            <span>{{ __($entreprise->adresse_entreprise) }}</span>
                                        </td>
                                        <td>
                                            @php
                                                echo $entreprise->statusBadge;
                                            @endphp
                                        </td>

                                        <td>
                                            <span class="d-block">{{ showDateTime($entreprise->updated_at) }}</span>
                                            <span>{{ diffForHumans($entreprise->updated_at) }}</span>
                                        </td>

                                        <td>
                                            <button type="button" class="btn btn-sm btn-outline--primary  updateType"
                                                data-id="{{ $entreprise->id }}" data-nom="{{ $entreprise->nom_entreprise }}"
                                                data-telephone = "{{ $entreprise->telephone_entreprise }}"
                                                data-mail = "{{ $entreprise->mail_entreprise }}"
                                                data-adresse = "{{ $entreprise->adresse_entreprise }}"><i
                                                    class="las la-pen"></i>@lang('Edit')</button>

                                            @if ($entreprise->status == Status::DISABLE)
                                                <button type="button"
                                                    class="btn btn-sm btn-outline--success confirmationBtn"
                                                    data-action="{{ route('settings.entreprise.status', $entreprise->id) }}"
                                                    data-question="@lang('Etes-vous sûr de vouloir activer ce entreprise?')">
                                                    <i class="la la-eye"></i> @lang('Activé')
                                                </button>
                                            @else
                                                <button type="button"
                                                    class="btn btn-sm btn-outline--danger confirmationBtn"
                                                    data-action="{{ route('settings.entreprise.status', $entreprise->id) }}"
                                                    data-question="@lang('Etes-vous sûr de vouloir désactiver ce entreprise?')">
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
                @if ($entreprises->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($entreprises) }}
                    </div>
                @endif
            </div>
        </div>
    </x-setting-card>
    <div id="typeModel" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Ajouter une entreprise')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i> </button>
                </div>
                <form action="{{ route('manager.settings.entreprise.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name='id'>
                        <div class="row">
                            <input type="hidden" value="true" name="page_reload" id="page_reload">
                            <div class="col-lg-12">
                                <x-forms.text :fieldLabel="__('Nom de l\'entreprise')" :fieldPlaceholder="__('Nom de l\'entreprise')" fieldName="nom_entreprise"
                                    fieldId="nom_entreprise" fieldValue="" :fieldRequired="true" />
                            </div>
                        </div>

                        <div class="row">
                            <input type="hidden" value="true" name="page_reload" id="page_reload">
                            <div class="col-lg-12">
                                <x-forms.text :fieldLabel="__('Adresse mail de entreprise')" :fieldPlaceholder="__('Adresse mail de l\'entreprise')" fieldName="mail_entreprise"
                                    fieldId="mail_entreprise" fieldValue="" :fieldRequired="true" />
                            </div>
                        </div>

                        <div class="row">
                            <input type="hidden" value="true" name="page_reload" id="page_reload">
                            <div class="col-lg-12">
                                <x-forms.number :fieldLabel="__('Téléphone de entreprise')" :fieldPlaceholder="__('Téléphone de l\'entreprise')" fieldName="telephone_entreprise"
                                    fieldId="telephone_entreprise" fieldValue="" :fieldRequired="true" />
                            </div>
                        </div>
                        <div class="row">
                            <input type="hidden" value="true" name="page_reload" id="page_reload">
                            <div class="col-lg-12">
                                <x-forms.text :fieldLabel="__('Adresse de l\'entreprise')" :fieldPlaceholder="__('Adresse de l\'entreprise')" fieldName="adresse_entreprise"
                                    fieldId="adresse_entreprise" fieldValue="" :fieldRequired="true" />
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
                modal.find('input[name=nom_entreprise]').val($(this).data('nom'));
                modal.find('input[name=telephone_entreprise]').val($(this).data('telephone'));
                modal.find('input[name=mail_entreprise]').val($(this).data('mail'));
                modal.find('input[name=adresse_entreprise]').val($(this).data('adresse'));
                modal.modal('show');
            });
        })(jQuery);

        $("#section").chained("#user");
    </script>
@endpush
