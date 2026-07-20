@extends('manager.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card b-radius--10 mb-3">
                <div class="card-body">
                    <form action="">
                        <div class="d-flex flex-wrap gap-4">
                            <input type="hidden" name="table" value="enquetemenage"/>
                            <div class="flex-grow-1">
                                <label>@lang('Recherche par Mot(s) cle(s)')</label>
                                <input type="text" name="search" value="{{ request()->search }}" class="form-control">
                            </div>
                            <div class="flex-grow-1">
                                <label>@lang('Localite')</label>
                                <select name="localite" class="form-control">
                                    <option value="">@lang('Toutes')</option>
                                    @foreach($localites as $local)
                                        <option value="{{ $local->id }}" @selected(request()->localite == $local->id)>{{ $local->nom }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="flex-grow-1 align-self-end">
                                <button class="btn btn--primary w-100 h-45"><i class="fas fa-filter"></i> @lang('Filter')</button>
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
                                    <th>@lang('Localite')</th>
                                    <th>@lang('Producteur')</th>
                                    <th>@lang('Date Enquete')</th>
                                    <th>@lang("Nb. d'enfants")</th>
                                    <th>@lang('Statut fin')</th>
                                    <th>@lang('Contenu')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($enqueteMenages as $data)
                                    <tr>
                                        <td>
                                            <span class="fw-bold">{{ @$data->localite->nom }}</span>
                                        </td>
                                        <td>
                                            <span class="small">
                                                {{ stripslashes(@$data->producteur->nom) }} {{ stripslashes(@$data->producteur->prenoms) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="d-block">{{ showDateTime($data->dateEnquete) }}</span>
                                        </td>
                                        <td>
                                            <span>{{ $data->enfants->count() }}</span>
                                        </td>
                                        <td>
                                            <span>{{ $data->statutFin }}</span>
                                        </td>
                                        <td> @php echo $data->etatSoumissionBadge; @endphp </td>
                                        <td> @php echo $data->statusBadge; @endphp </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-outline--primary" data-bs-toggle="dropdown" aria-expanded="false"><i
                                                    class="las la-ellipsis-v"></i>@lang('Action')
                                            </button>
                                            <div class="dropdown-menu p-0">
                                                <a href="{{ route('manager.suivi.menage.edit', $data->id) }}"
                                                    class="dropdown-item"><i class="la la-pen"></i>@lang('Editer')</a>
                                                <a href="{{ route('manager.suivi.menage.show', $data->id) }}"
                                                    class="dropdown-item"><i class="las la-file-invoice"></i>@lang('Détail')</a>
                                                @if ($data->status == Status::DISABLE)
                                                    <button type="button" class="confirmationBtn  dropdown-item"
                                                        data-action="{{ route('manager.suivi.menage.status', $data->id) }}"
                                                        data-question="@lang('Are you sure to enable this enquête ménage?')">
                                                        <i class="la la-eye"></i> @lang('Active')
                                                    </button>
                                                @else
                                                    <button type="button" class="confirmationBtn dropdown-item"
                                                        data-action="{{ route('manager.suivi.menage.status', $data->id) }}"
                                                        data-question="@lang('Are you sure to disable this enquête ménage?')">
                                                        <i class="la la-eye-slash"></i> @lang('Désactive')
                                                    </button>
                                                @endif
                                                <button type="button" class="confirmationBtn dropdown-item"
                                                    data-action="{{ route('manager.suivi.menage.delete', encrypt($data->id)) }}"
                                                    data-question="@lang('Are you sure to delete this enquête ménage?')">
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
                @if($enqueteMenages->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($enqueteMenages) }}
                    </div>
                @endif
            </div>
        </div>
    </div>
    <x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')
    <a href="{{ route('manager.suivi.menage.create') }}" class="btn  btn-outline--primary h-45 addNewCooperative">
        <i class="las la-plus"></i>@lang("Ajouter nouveau")
    </a>
    <a href="{{ route('manager.suivi.menage.exportExcel.menageAll') }}" class="btn  btn-outline--warning h-45"><i class="las la-cloud-download-alt"></i> @lang('Exporter en Excel')</a>
@endpush

@push('style')
    <style>
        .table-responsive {
            overflow-x: auto;
        }
    </style>
@endpush

@push('script')
    <script>
        $('form select').on('change', function(){
            $(this).closest('form').submit();
        });
    </script>
@endpush
