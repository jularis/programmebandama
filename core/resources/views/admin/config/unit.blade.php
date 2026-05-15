@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card b-radius--10">
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Nom')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($units as $unit)
                                    <tr>
                                        <td>{{ __($unit->name) }}</td>
                                        <td> @php  echo $unit->statusBadge; @endphp </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-outline--primary updateUnit"
                                                data-id="{{ $unit->id }}" data-name="{{ $unit->name }}"><i
                                                    class="las la-pen"></i>@lang('Edit')</button>

                                            @if ($unit->status == Status::DISABLE)
                                                <button type="button"
                                                    class="btn btn-sm btn-outline--success confirmationBtn"
                                                    data-action="{{ route('admin.courier.unit.status', $unit->id) }}"
                                                    data-question="@lang('Are you sure to enable this unit?')">
                                                    <i class="la la-eye"></i> @lang('Activé')
                                                </button>
                                            @else
                                                <button type="button"
                                                    class="btn btn-sm btn-outline--danger confirmationBtn"
                                                    data-action="{{ route('admin.courier.unit.status', $unit->id) }}"
                                                    data-question="@lang('Are you sure to disable this unit?')">
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
                @if ($units->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($units) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div id="unitModel" class="modal fade">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Add Unit')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal">
                        <i class="las la-times"></i></button>
                </div>
                <form action="{{ route('admin.courier.unit.store') }}" class="resetForm" method="POST">
                    @csrf
                    <input type="hidden" name='id'>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>@lang('Nom')</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn--primary h-45 w-100">@lang('Envoyer')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')
    <button class="btn btn-sm btn-outline--primary addUnit"><i class="las la-plus"></i>@lang("Ajouter nouveau")</button>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";
            $('.addUnit').on('click', function() {
                $('#unitModel').modal('show');
                $('.resetForm').trigger('reset');
            });

            $('.updateUnit').on('click', function() {
                let title = "Update Unit"
                let id = $(this).data('id');
                let name = $(this).data('name');
                var modal = $('#unitModel');
                modal.find('.modal-title').text(title);
                modal.find('input[name=id]').val(id);
                modal.find('input[name=name]').val(name);
                modal.modal('show');
            });
        })(jQuery);
    </script>
@endpush
