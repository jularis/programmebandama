@extends('manager.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card b-radius--10">
                <div class="card-body  p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Code Enfant')</th>
                                    <th>@lang('Nom Enfant')</th>
                                    <th>@lang('Date Enquete')</th>
                                    <th>@lang('Enqueteur')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($suivis as $data)
                                    <tr>
                                        <td>
                                            <span class="fw-bold">{{ @$data->enfant->codeEnfant }}</span>
                                        </td>
                                        <td>
                                            <span class="small">{{ $data->nom }}</span>
                                        </td>
                                        <td>
                                            <span class="d-block">{{ showDateTime($data->dateEnquete) }}</span>
                                        </td>
                                        <td>
                                            <span>{{ $data->nomEnqueteur }}</span>
                                        </td>
                                        <td> @php echo $data->statusBadge; @endphp </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-outline--primary" data-bs-toggle="dropdown" aria-expanded="false"><i
                                                    class="las la-ellipsis-v"></i>@lang('Action')
                                            </button>
                                            <div class="dropdown-menu p-0">
                                                <a href="{{ route('manager.suivi.enfanttravailleur.edit', $data->id) }}"
                                                    class="dropdown-item"><i class="la la-pen"></i>@lang('Editer')</a>
                                                <a href="{{ route('manager.suivi.enfanttravailleur.show', $data->id) }}"
                                                    class="dropdown-item"><i class="las la-file-invoice"></i>@lang('Détail')</a>
                                                @if ($data->status == Status::DISABLE)
                                                    <button type="button" class="confirmationBtn  dropdown-item"
                                                        data-action="{{ route('manager.suivi.enfanttravailleur.status', $data->id) }}"
                                                        data-question="@lang('Are you sure to enable this suivi?')">
                                                        <i class="la la-eye"></i> @lang('Active')
                                                    </button>
                                                @else
                                                    <button type="button" class="confirmationBtn dropdown-item"
                                                        data-action="{{ route('manager.suivi.enfanttravailleur.status', $data->id) }}"
                                                        data-question="@lang('Are you sure to disable this suivi?')">
                                                        <i class="la la-eye-slash"></i> @lang('Désactive')
                                                    </button>
                                                @endif
                                                <button type="button" class="confirmationBtn dropdown-item"
                                                    data-action="{{ route('manager.suivi.enfanttravailleur.delete', encrypt($data->id)) }}"
                                                    data-question="@lang('Are you sure to delete this suivi?')">
                                                    <i class="la la-trash"></i> @lang('Supprimer')
                                                </button>
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
                @if($suivis->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($suivis) }}
                    </div>
                @endif
            </div>
        </div>
    </div>
    <x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')
    <a href="{{ route('manager.suivi.enfanttravailleur.create') }}" class="btn  btn-outline--primary h-45 addNewCooperative">
        <i class="las la-plus"></i>@lang("Ajouter nouveau")
    </a>
    <a href="{{ route('manager.suivi.enfanttravailleur.exportExcel.suiviAll') }}" class="btn  btn-outline--warning h-45"><i class="las la-cloud-download-alt"></i> @lang('Exporter en Excel')</a>
@endpush

@push('style')
    <style>
        .table-responsive {
            overflow-x: auto;
        }
    </style>
@endpush
