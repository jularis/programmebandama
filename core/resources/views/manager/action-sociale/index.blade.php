@extends('manager.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card b-radius--10 mb-3">
                <div class="card-body">
                    <form action="">
                        <div class="d-flex flex-wrap gap-4">
                            <input type="hidden" name="table" value="applications" />
                            <div class="flex-grow-1">
                                <label>@lang('Recherche par Mot(s) clé(s)')</label>
                                <input type="text" name="search" value="{{ request()->search }}" class="form-control">
                            </div>
                            <div class="flex-grow-1">
                                <label>@lang('Localité')</label>
                                <select name="localite" class="form-control">
                                    <option value="">@lang('Toutes')</option>

                                </select>
                            </div>
                            <div class="flex-grow-1">
                                <label>@lang('Date')</label>
                                <input name="date" type="text" class="dates form-control"
                                    placeholder="@lang('Date de début - Date de fin')" autocomplete="off" value="{{ request()->date }}">
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
                                    <th>@lang('Cooperative')</th>
                                    <th>@lang('Code')</th>
                                    <th>@lang('Type projet')</th>
                                    <th>@lang('Titre')</th>
                                    <th>@lang('Date demarrage')</th>
                                    <th>@lang('Date fin projet')</th>
                                    <th>@lang('Coût projet')</th>
                                    <th>@lang('Niveau realisation')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($actions as $action)
                                    <tr>
                                        <td>
                                            <span class="fw-bold">{{ $action->cooperative->name }}</span>
                                        </td>
                                        <td>
                                            <span class="fw-bold">{{ $action->code }}</span>
                                        </td>
                                        <td>
                                            <span class="small">
                                                {{ $action->type_projet }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="small">
                                                {{ $action->titre_projet }}
                                            </span>
                                        </td>
                                        <td>
                                            <span>{{ $action->date_demarrage }}</span>
                                        </td>
                                        <td>
                                            <span>{{ diffForHumans($action->date_fin_projet) }}</span>
                                        </td>
                                        <td>
                                            <span class="small">
                                                {{ number_format($action->cout_projet, 0, ',', ' ') }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="small">
                                                {{ $action->niveau_realisation }}
                                            </span>
                                        </td>


                                        <td> @php echo $action->statusBadge; @endphp </td>
                                        <td>
                                            <a href="{{ route('manager.communaute.action.sociale.index', ['download' => encrypt($action->id)]) }}"
                                                class="btn btn-sm btn--danger"><i
                                                    class="la la-file-pdf-o"></i>@lang('PDF')</a>
                                            <a href="{{ route('manager.communaute.action.sociale.show', $action->id) }}"
                                                class="btn btn-sm btn-outline--info"><i
                                                    class="las la-file-invoice"></i>@lang('Détail')</a>

                                            <button type="button" class="btn btn-sm btn-outline--primary"
                                                data-bs-toggle="dropdown" aria-expanded="false"><i
                                                    class="las la-ellipsis-v"></i>@lang('Action')
                                            </button>
                                            <div class="dropdown-menu p-0">
                                                <a href="{{ route('manager.communaute.action.sociale.edit', $action->id) }}"
                                                    class="dropdown-item"><i class="la la-pen"></i>@lang('Editer')</a>

                                                @if ($action->status == Status::DISABLE)
                                                    <button type="button" class="confirmationBtn  dropdown-item"
                                                        data-action="{{ route('manager.communaute.action.sociale.status', $action->id) }}"
                                                        data-question="@lang('Are you sure to enable this application?')">
                                                        <i class="la la-eye"></i> @lang('Activé')
                                                    </button>
                                                @else
                                                    <button type="button" class="confirmationBtn dropdown-item"
                                                        data-action="{{ route('manager.communaute.action.sociale.status', $action->id) }}"
                                                        data-question="@lang('Are you sure to disable this application?')">
                                                        <i class="la la-eye-slash"></i> @lang('Désactivé')
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
                @if ($actions->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($actions) }}
                    </div>
                @endif
            </div>
        </div>
    </div>
    <x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')
    <a href="{{ route('manager.communaute.action.sociale.create') }}"
        class="btn  btn-outline--primary h-45 addNewCooperative">
        <i class="las la-plus"></i>@lang('Ajouter nouveau')
    </a>
    <a href="{{ route('manager.communaute.action.sociale.exportExcel.actionSocialeAll') }}"
        class="btn  btn-outline--warning h-45"><i class="las la-cloud-download-alt"></i> Exporter en Excel</a>
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
                language: 'en'
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
    </script>
@endpush
