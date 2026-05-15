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
                                    <th>@lang('Programme de durabilité')</th>
                                    <th>@lang('Ajoutée le')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($programmeDurabilites as $programmeDurabilite)
                                    <tr>
                                        <td> 
                                            <span class="small">
                                                <a href="{{ route('manager.settings.durabilite-settings.edit', $programmeDurabilite->id) }}">
                                                    <span>@</span>{{$programmeDurabilite->libelle }}
                                                </a>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="d-block">{{ showDateTime($programmeDurabilite->created_at) }}</span>
                                            <span>{{ diffForHumans($programmeDurabilite->created_at) }}</span>
                                        </td>
                                        <td>
                                        <div class="task_view">
                            <a href="{{ route('manager.settings.durabilite-settings.edit', $programmeDurabilite->id) }}"
                                class="task_view_more d-flex align-items-center justify-content-center">
                                <i class="fa fa-edit icons mr-2"></i> @lang('app.edit')
                            </a>
                        </div>
                        <div class="task_view mt-1 mt-lg-0 mt-md-0">
                            <a href="javascript:;" data-durab-id="{{ $programmeDurabilite->id }}"
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
                @if ($programmeDurabilites->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($programmeDurabilites) }}
                    </div>
                @endif
            </div>
            </div>
            </x-setting-card>



    {{-- modal qui permet d'importer des progragrammes de durabilités (besoins d'explication)--}}
    <div id="typeModel" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Importer des sections')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i> </button>
                </div>
                <form action="" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">   
                        <p>Fichier d'exemple à utiliser :<a href="" target="_blank">@lang('section-import-exemple.xlsx')</a></p>
                    </div>    
                    <div class="form-group row">
                        <label class="col-sm-4 control-label">@lang('Type de Formation')</label>
                        <div class="col-xs-12 col-sm-8">
                            <select class="form-control" name="localite_id" required>
                                <option value="">@lang('Selectionner une option')</option>
                                {{-- @foreach($localites as $localite)
                                    <option value="{{ $localite->id }}" @selected(old('cooperative'))> {{ __($localite->libelle) }}</option>
                                @endforeach --}}
                            </select>
                        </div> 
                    </div>
                    <div class="form-group row">
                        
    
                        {{ Form::label(__('Fichier(.xls, .xlsx)'), null, ['class' => 'control-label col-sm-4']) }}
                        <div class="col-xs-12 col-sm-8 col-md-8">
                            <input type="file" name="uploaded_file" accept=".xls, .xlsx" class="form-control dropify-fr" placeholder="Choisir une image" id="image" required> 
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
    <a href="{{ route('manager.settings.durabilite-settings.create') }}" class="btn  btn-primary h-45 addNewCooperative">
        <i class="las la-plus"></i>@lang("Ajouter nouveau")
    </a>
    <a class="btn  btn-outline--info h-45 addType"><i class="las la-cloud-upload-alt"></i> Importer des programmes de durabilité</a>
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

        var url = "{{ route('manager.settings.durabilite-settings.destroy', ':id') }}";
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

