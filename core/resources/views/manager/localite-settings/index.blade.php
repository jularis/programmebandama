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
                <div class="card-body  p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Section')</th>
                                    <th>@lang('Localite')</th>
                                    <th>@lang('Code Localite')</th>
                                    <th>@lang('Type de localites')</th>
                                    <th>@lang('Ajoutée le')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($cooperativeLocalites as $localite)
                                    <tr>
                                        
                                        <td>
                                            <span class="fw-bold">{{ __($localite->section->libelle) }}</span>
                                        </td>
                                        <td>
                                            <span class="small">
                                                <a href="">
                                                    <span>@</span>{{ $localite->nom }}
                                                </a>
                                            </span>
                                        </td>
                                        <td>
                                            <span>{{ $localite->codeLocal }}</span>
                                        </td>
                                        <td>
                                            <span>{{ $localite->type_localites }}<br>{{ $localite->sousprefecture }}</span>
                                        </td>
                                        <td>
                                            <span class="d-block">{{ showDateTime($localite->created_at) }}</span>
                                            <span>{{ diffForHumans($localite->created_at) }}</span>
                                        </td>
                                        <td> @php echo $localite->statusBadge; @endphp </td>
                                        <td>
                                            
                                            <div class="task_view">
                            <a href="{{ route('manager.settings.localite-settings.edit', $localite->id) }}"
                                class="task_view_more d-flex align-items-center justify-content-center">
                                <i class="fa fa-edit icons mr-2"></i> @lang('app.edit')
                            </a>
                        </div>
                        <div class="task_view mt-1 mt-lg-0 mt-md-0">
                        @if ($localite->status == Status::DISABLE)
                                                    <button type="button" class="confirmationBtn  dropdown-item"
                                                        data-action="{{ route('manager.settings.localite-settings.status', $localite->id) }}"
                                                        data-question="@lang('Are you sure to enable this localite?')">
                                                        <i class="la la-eye"></i> @lang('Active')
                                                    </button>
                                                @else
                                                    <button type="button" class=" confirmationBtn   dropdown-item"
                                                        data-action="{{ route('manager.settings.localite-settings.status', $localite->id) }}"
                                                        data-question="@lang('Are you sure to disable this localite?')">
                                                        <i class="la la-eye-slash"></i> @lang('Désactive')
                                                    </button>
                                                @endif
                        </div>
                        <div class="task_view mt-1 mt-lg-0 mt-md-0">
                            <a href="javascript:;" data-durab-id="{{ $localite->id }}"
                                class="delete-category task_view_more d-flex align-items-center justify-content-center">
                                <i class="fa fa-trash icons mr-2"></i> @lang('app.delete')
                            </a>
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
                @if ($cooperativeLocalites->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($cooperativeLocalites) }}
                    </div>
                @endif
            </div>
        </div>
        </x-setting-card>

    <div id="typeModel" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Importer des localites')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i> </button>
                </div>
                <form action="{{ route('manager.settings.localite-settings.uploadcontent') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <p>Fichier d'exemple à utiliser :
                            <a href="{{ asset('assets/localite-import-exemple.xlsx') }}" target="_blank">@lang('localite-import-exemple.xlsx')</a>
                        </p>

                        <div class="form-group row">
                            <label class="col-sm-4 control-label">@lang('Section')</label>
                            <div class="col-xs-12 col-sm-8">
                                <select class="form-control" name="section">
                                    <option value="">@lang('Selectionner une option')</option>
                                    @foreach ($sections as $coop)
                                        <option value="{{ $coop->id }}" @selected(old('section'))>
                                            {{ __($coop->libelle) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            {{ Form::label(__('Fichier(.xls, .xlsx)'), null, ['class' => 'control-label col-sm-4']) }}
                            <div class="col-xs-12 col-sm-8 col-md-8">
                                <input type="file" name="uploaded_file" accept=".xls, .xlsx"
                                    class="form-control dropify-fr" placeholder="Choisir une image" id="image" required>
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
    <x-search-form placeholder="Search here..." />
    <a href="{{ route('manager.settings.localite-settings.create') }}" class="btn  btn-outline--primary h-45 addNewCooperative">
        <i class="las la-plus"></i>@lang('Ajouter nouveau')
    </a>
    <a class="btn  btn-outline--info h-45 addType"><i class="las la-cloud-upload-alt"></i> Importer des Localites</a>
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
        (function($) {
            "use strict";

            $('.addType').on('click', function() {
                $('#typeModel').modal('show');
            });


        })(jQuery)

        $('body').on('click', '.delete-category', function() {

var id = $(this).data('durab-id');

Swal.fire({
    title: "@lang('messages.sweetAlertTitle')",
    text: "@lang('messages.delete')",
    icon: 'warning',
    showCancelButton: true,
    focusConfirm: false,
    confirmButtonText: "@lang('messages.confirmDelete')",
    cancelButtonText: "@lang('app.cancel')",
    customClass: {
        confirmButton: 'btn btn-primary mr-3',
        cancelButton: 'btn btn-secondary'
    },
    showClass: {
        popup: 'swal2-noanimation',
        backdrop: 'swal2-noanimation'
    },
    buttonsStyling: false
}).then((result) => {
    if (result.isConfirmed) {

        var url = "{{ route('manager.settings.localite-settings.destroy', ':id') }}";
        url = url.replace(':id', id);

        var token = "{{ csrf_token() }}";

        $.easyAjax({
            type: 'POST',
            url: url,
            blockUI: true,
            data: {
                '_token': token,
                '_method': 'DELETE'
            },
            success: function(response) {
                if (response.status == "success") {
                    $('#type-' + id).fadeOut();
                    window.location.reload();
                }
            }
        });
    }
});
});
    </script>
@endpush
