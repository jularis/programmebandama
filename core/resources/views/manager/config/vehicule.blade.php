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
                                    <th>@lang('Marque')</th>
                                    <th>@lang('Matricule véhicule')</th>
                                    <th>@lang('Matricule remorque')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Last Update')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($vehicules as $vehicule)
                                    <tr>
                                        <td>
                                            <span>{{ $vehicule->cooperative->name }}</span>
                                        </td>
                                        <td>
                                            <span>{{ $vehicule->marque->nom }}</span>
                                        </td>

                                        <td>
                                            <span>{{ __($vehicule->vehicule_immat) }}</span>
                                        </td>

                                        <td>
                                            <span>{{ __($vehicule->remorque_immat) }}</span>
                                        </td>

                                        <td>
                                            @php
                                                echo $vehicule->statusBadge;
                                            @endphp
                                        </td>

                                        <td>
                                            <span class="d-block">{{ showDateTime($vehicule->updated_at) }}</span>
                                            <span>{{ diffForHumans($vehicule->updated_at) }}</span>
                                        </td>

                                        <td>
                                            <button type="button" class="btn btn-sm btn-outline--primary  updateType"
                                                data-id="{{ $vehicule->id }}" data-marque="{{ $vehicule->marque_id }}"
                                                data-matricule="{{ $vehicule->vehicule_immat }}"><i
                                                    class="las la-pen"></i>@lang('Edit')</button>

                                            @if ($vehicule->status == Status::DISABLE)
                                                <button type="button"
                                                    class="btn btn-sm btn-outline--success confirmationBtn"
                                                    data-action="{{ route('manager.settings.vehicule.status', $vehicule->id) }}"
                                                    data-question="@lang('Etes-vous sûr de vouloir activer ce vehicule?')">
                                                    <i class="la la-eye"></i> @lang('Active')
                                                </button>
                                            @else
                                                <button type="button"
                                                    class="btn btn-sm btn-outline--danger confirmationBtn"
                                                    data-action="{{ route('manager.settings.vehicule.status', $vehicule->id) }}"
                                                    data-question="@lang('Etes-vous sûr de vouloir désactiver ce vehicule?')">
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
                @if ($vehicules->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($vehicules) }}
                    </div>
                @endif
            </div>
        </div>
    </x-setting-card>
    <div id="typeModel" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Ajouter un Vehicule')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i> </button>
                </div>
                <form action="{{ route('manager.settings.vehicule.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name='id'>

                        <div class="form-group row">
                            <label class="col-sm-4 control-label">@lang('Marque de Véhicule')</label>
                            <div class="col-xs-12 col-sm-8">
                                <select class="form-control" data-live-search="true" name="marque"
                                    id="marque" required>
                                    <option value="">@lang('Selectionner une option')</option>
                                    @foreach ($marques as $marque)
                                        <option value="{{ $marque->id }}" @selected(old('marque'))>
                                            {{ __($marque->nom) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            {{ Form::label(__('Matricule du vehicule'), null, ['class' => 'control-label col-sm-4']) }}
                            <div class="col-xs-12 col-sm-8 col-md-8">
                                {!! Form::text('matricule', null, [
                                    'placeholder' => __('Matricule du vehicule'),
                                    'class' => 'form-control',
                                    'required',
                                ]) !!}
                            </div>
                        </div>

                        {{-- <div class="form-group row">
                            {{ Form::label(__('Matricule de la remorque'), null, ['class' => 'control-label col-sm-4']) }}
                            <div class="col-xs-12 col-sm-8 col-md-8">
                                {!! Form::text('remorque', null, [
                                    'placeholder' => __('Matricule de la remorque'),
                                    'class' => 'form-control',
                                    'required',
                                ]) !!}
                            </div>
                        </div> --}}
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
                modal.find('select[name=marque]').val($(this).data('marque'));
                modal.find('input[name=matricule]').val($(this).data('matricule'));
                //modal.find('input[name=remorque]').val($(this).data('remorque'));
                modal.modal('show');
            });
        })(jQuery);

        $("#section").chained("#user");
    </script>
@endpush
