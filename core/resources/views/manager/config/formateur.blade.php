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
                                    <th>@lang('Entreprise')</th>
                                    <th>@lang('Nom')</th>
                                    <th>@lang('Prenom')</th>
                                    <th>@lang('Telephone')</th>
                                    <th>@lang('Poste')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Last Update')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($formateurs as $formateur)
                                    <tr>
                                        <td>
                                            <span>{{$formateur->entreprise->nom_entreprise}}</span>
                                        </td>
                                        <td>
                                            <span>{{ $formateur->nom_formateur }}</span>
                                        </td>
                                        <td>
                                            <span>{{ $formateur->prenom_formateur }}</span>
                                        </td>

                                        <td>
                                            <span>{{ __($formateur->telephone_formateur) }}</span>
                                        </td>
                                        <td>
                                            <span>{{ __($formateur->poste_formateur) }}</span>
                                        </td>
                                        <td>
                                            @php
                                                echo $formateur->statusBadge;
                                            @endphp
                                        </td>

                                        <td>
                                            <span class="d-block">{{ showDateTime($formateur->updated_at) }}</span>
                                            <span>{{ diffForHumans($formateur->updated_at) }}</span>
                                        </td>

                                        <td>
                                            <button type="button" class="btn btn-sm btn-outline--primary  updateType"
                                                data-id="{{ $formateur->id }}" 
                                                data-entreprise="{{ $formateur->entreprise_id }}"
                                                data-nom="{{ $formateur->nom_formateur }}"
                                                data-prenom="{{ $formateur->prenom_formateur }}"
                                                data-poste="{{ $formateur->poste_formateur }}"
                                                data-telephone = "{{ $formateur->telephone_formateur }}"
                                                ><i
                                                    class="las la-pen"></i>@lang('Edit')</button>

                                            @if ($formateur->status == Status::DISABLE)
                                                <button type="button"
                                                    class="btn btn-sm btn-outline--success confirmationBtn"
                                                    data-action="{{ route('settings.formateurStaff.status', $formateur->id) }}"
                                                    data-question="@lang('Etes-vous sûr de vouloir activer ce formateur?')">
                                                    <i class="la la-eye"></i> @lang('Activé')
                                                </button>
                                            @else
                                                <button type="button"
                                                    class="btn btn-sm btn-outline--danger confirmationBtn"
                                                    data-action="{{ route('settings.formateurStaff.status', $formateur->id) }}"
                                                    data-question="@lang('Etes-vous sûr de vouloir désactiver ce formateur?')">
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
                @if ($formateurs->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($formateurs) }}
                    </div>
                @endif
            </div>
        </div>
    </x-setting-card>
    <div id="typeModel" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Ajouter un formateur')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i> </button>
                </div>
                <form action="{{ route('manager.settings.formateurStaff.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
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
                        <input type="hidden" name='id'>
                        <div class="row">
                            <input type="hidden" value="true" name="page_reload" id="page_reload">
                            <div class="col-lg-12">
                                <x-forms.text :fieldLabel="__('Nom')" :fieldPlaceholder="__('Nom ')" fieldName="nom_formateur"
                                    fieldId="nom_formateur" fieldValue="" :fieldRequired="true" />
                            </div>
                        </div>

                        <div class="row">
                            <input type="hidden" value="true" name="page_reload" id="page_reload">
                            <div class="col-lg-12">
                                <x-forms.text :fieldLabel="__('Prénom')" :fieldPlaceholder="__('Prenom')" fieldName="prenom_formateur"
                                    fieldId="prenom_formateur" fieldValue="" :fieldRequired="true" />
                            </div>
                        </div>

                        <div class="row">
                            <input type="hidden" value="true" name="page_reload" id="page_reload">
                            <div class="col-lg-12">
                                <x-forms.number :fieldLabel="__('Téléphone')" :fieldPlaceholder="__('Téléphone')" fieldName="telephone_formateur"
                                    fieldId="telephone_formateur" fieldValue="" :fieldRequired="true" />
                            </div>
                        </div>

                        <div class="row">
                            <input type="hidden" value="true" name="page_reload" id="page_reload">
                            <div class="col-lg-12">
                                <x-forms.text :fieldLabel="__('Poste')" :fieldPlaceholder="__('Poste')" fieldName="poste_formateur"
                                    fieldId="poste_formateur" fieldValue="" :fieldRequired="true" />
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
                modal.find('input[name=nom_formateur]').val($(this).data('nom'));
                modal.find('input[name=prenom_formateur]').val($(this).data('prenom'));
                modal.find('input[name=telephone_formateur]').val($(this).data('telephone'));
                modal.find('input[name=poste_formateur]').val($(this).data('poste'));
                modal.find('select[name=entreprise_id]').val($(this).data('entreprise'));
                modal.modal('show');
            });
        })(jQuery);

        $("#section").chained("#user");
    </script>
@endpush
