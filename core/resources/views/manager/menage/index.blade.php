@extends('manager.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card b-radius--10 mb-3">
                <div class="card-body">
                    <form action="">
                        <div class="d-flex flex-wrap gap-4">
                            <input type="hidden" name="table" value="menages" />
                            <div class="flex-grow-1">
                                <label>@lang('Recherche par Mot(s) cle(s)')</label>
                                <input type="text" name="search" value="{{ request()->search }}" class="form-control">
                            </div>
                            <div class="flex-grow-1">
                                <label>@lang('Localité')</label>
                                <select name="localite" class="form-control">
                                    <option value="">@lang('Toutes')</option>
                                    @foreach ($localites as $local)
                                        <option value="{{ $local->id }}">{{ $local->nom }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="flex-grow-1">
                                <label>@lang('Date')</label>
                                <input name="date" type="text" class="dates form-control"
                                    placeholder="@lang('Date de debut - Date de fin')" autocomplete="off" value="{{ request()->date }}">
                            </div>
                            <div class="flex-grow-1 align-self-end">
                                <button class="btn btn--primary w-100 h-45"><i class="fas fa-filter"></i>
                                    @lang('Filter')</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card b-radius--10 ">
                <div class="card-body  p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Section')</th>
                                    <th>@lang('Localite')</th>
                                    <th>@lang('Producteur')</th>
                                    <th>@lang('Quartier')</th>
                                    <th>@lang('Ajoutée le')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($menages as $menage)
                                    <tr>
                                        <td>
                                            <span class="fw-bold">{{ $menage->producteur->localite->section->libelle }}</span>
                                        </td>
                                        <td>
                                            <span class="fw-bold">{{ $menage->producteur->localite->nom }}</span>
                                        </td>
                                        <td>
                                            <span class="small">
                                                {{ $menage->producteur->nom }} {{ $menage->producteur->prenoms }}
                                            </span>
                                        </td>
                                        <td>
                                            <span>{{ $menage->quartier }}</span>
                                        </td>

                                        <td>
                                            <span class="d-block">{{ showDateTime($menage->created_at) }}</span>
                                            <span>{{ diffForHumans($menage->created_at) }}</span>
                                        </td>
                                        <td> @php echo $menage->statusBadge; @endphp </td>
                                        <td>

                                            <button type="button" class="btn btn-sm btn-outline--primary"
                                                data-bs-toggle="dropdown" aria-expanded="false"><i
                                                    class="las la-ellipsis-v"></i>@lang('Action')
                                            </button>
                                            <div class="dropdown-menu p-0">
                                                <a href="{{ route('manager.suivi.menage.edit', $menage->id) }}"
                                                    class="dropdown-item"><i class="la la-pen"></i>@lang('Editer')</a>
                                                <a href="{{ route('manager.suivi.menage.show', $menage->id) }}"
                                                    class="dropdown-item"><i class="las la-file-invoice"></i>@lang('Détail')</a>
                                                @if ($menage->status == Status::DISABLE)
                                                    <button type="button" class="confirmationBtn  dropdown-item"
                                                        data-action="{{ route('manager.suivi.menage.status', $menage->id) }}"
                                                        data-question="@lang('Are you sure to enable this menage?')">
                                                        <i class="la la-eye"></i> @lang('Active')
                                                    </button>
                                                @else
                                                    <button type="button" class="confirmationBtn dropdown-item"
                                                        data-action="{{ route('manager.suivi.menage.status', $menage->id) }}"
                                                        data-question="@lang('Are you sure to disable this menage?')">
                                                        <i class="la la-eye-slash"></i> @lang('Désactive')
                                                    </button>
                                                @endif

                                            </div>
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
                @if ($menages->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($menages) }}
                    </div>
                @endif
            </div>
        </div>
    </div>
    <x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')
    <a href="{{ route('manager.suivi.menage.create') }}" class="btn  btn-outline--primary h-45 addNewCooperative">
        <i class="las la-plus"></i>@lang('Ajouter nouveau')
    </a>
    <a href="{{ route('manager.suivi.menage.exportExcel.menageAll') }}" class="btn  btn-outline--warning h-45"><i
            class="las la-cloud-download-alt"></i> @lang('Exporter en Excel')</a>
@endpush
@push('style')
    <style>
        .table-responsive {
            overflow-x: auto;
        }
    </style>
@endpush
@push('style-lib')
    <link rel="stylesheet" href="{{ asset('assets/fcadmin/css/vendor/datepicker.min.css') }}">
@endpush
@push('script')
    <script src="{{ asset('assets/fcadmin/js/vendor/datepicker.min.js') }}"></script>
    <script src="{{ asset('assets/fcadmin/js/vendor/datepicker.fr.js') }}"></script>
<script src="{{ asset('assets/fcadmin/js/vendor/datepicker.en.js') }}"></script>
@endpush
@push('script')
    <script>
        (function($) {
            "use strict";

            $('.dates').datepicker({
                maxDate: new Date(),
                range: true,
                multipleDatesSeparator: "-",
                language: 'fr'
            });

            let url = new URL(window.location).searchParams;
            if (url.get('localite') != undefined && url.get('localite') != '') {
                $('select[name=localite]').find(`option[value=${url.get('localite')}]`).attr('selected', true);
            }
            if (url.get('payment_status') != undefined && url.get('payment_status') != '') {
                $('select[name=payment_status]').find(`option[value=${url.get('payment_status')}]`).attr('selected',
                    true);
            }

        })(jQuery)

        $('form select').on('change', function(){
    $(this).closest('form').submit();
});
    </script>
@endpush
