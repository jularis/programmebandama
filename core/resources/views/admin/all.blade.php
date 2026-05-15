@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card b-radius--10 ">
                <div class="card-body p-0">
                    <div class="table-responsive--md  table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Nom')</th>
                                    <th>@lang("Nom d'utilisateur")</th>
                                    <th>@lang('Email')</th>
                                    @if ($adminId == Status::SUPER_ADMIN_ID)
                                    <th>@lang('Action')</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($admins as $admin)
                                    <tr>
                                        <td>
                                            <span>{{ __($admin->name) }}</span>
                                        </td>
                                        <td>
                                            <span>{{ $admin->username }}</span>
                                        </td>
                                        <td>
                                            {{ $admin->email }}
                                        </td>
                                        @if ($adminId == Status::SUPER_ADMIN_ID)
                                        <td>
                                            @if ($admin->id != Status::SUPER_ADMIN_ID)
                                            <button class="btn btn-sm btn-outline--primary editBtn"
                                                data-name="{{ $admin->name }}" data-username="{{ $admin->username }}"
                                                data-email="{{ $admin->email }}" data-id="{{ $admin->id }}">
                                                <i class="la la-pen"></i>@lang('Edit')
                                            </button>
                                            <button class="btn btn-sm btn-outline--danger confirmationBtn"
                                                data-action="{{ route('admin.remove', $admin->id) }}"
                                                data-question="@lang('Êtes-vous sûr de vouloir supprimer ce admin?')">
                                                <i class="las la-trash"></i> @lang('Delete')
                                            </button>
                                            @endif
                                        </td>
                                         @endif
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                    </tr>
                                @endforelse

                            </tbody>
                        </table><!-- table end -->
                    </div>
                </div>
                @if ($admins->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($admins) }}
                    </div>
                @endif
            </div>
        </div>
    </div>
    <!-- Create Modal -->
    <div class="modal fade" id="manageAdmin">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">@lang('Create Admin')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal">
                        <i class="las la-times"></i></button>
                </div>
                <form action="{{ route('admin.store') }}" method="post" class="resetForm">
                    @csrf
                    <input type="hidden" name="id">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>@lang('Nom')</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>@lang('Email')</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>@lang("Nom d'utilisateur")</label>
                            <input type="text" name="username" class="form-control" required>
                        </div>
                        <div class="form-group pass">
                            <label>@lang("Mot de passe")</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="form-group cpass">
                            <label>@lang('Confirm Password')</label>
                            <input type="password" name="password_confirmation" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn--primary w-100 h-45">@lang('Envoyer')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <x-confirmation-modal />
@endsection
@push('breadcrumb-plugins')
    <button type="button" class="btn btn-sm btn-outline--primary addAdmin">
        <i class="las la-plus"></i>@lang("Ajouter nouveau")
    </button>
@endpush


@push('script')
    <script>
        (function($) {
            "use strict";
            $('.editBtn').on('click', function() {
                let title = 'Update Admin'
                let name = $(this).data('name');
                let id = $(this).data('id');
                let username = $(this).data('username');
                let email = $(this).data('email');
                let modal = $('#manageAdmin');
                modal.find('.modal-title').text(title)
                modal.find('input[name=name]').val(name);
                modal.find('input[name=id]').val(id);
                modal.find('input[name=username]').val(username);
                modal.find('input[name=email]').val(email);
                modal.find('input[name=password_confirmation]').removeAttr('required','required');
                modal.find('input[name=password]').removeAttr('required','required');
                modal.find('label[for=password_confirmation]').removeClass('required');
                modal.find('label[for=password]').removeClass('required');
                modal.modal('show');
            });
            $('.addAdmin').on('click', function() {
                let modal = $('#manageAdmin');
                $('.resetForm').trigger('reset');
                $(`input[name=id]`).val(0);
                modal.find('.pass').removeClass('d-none');
                modal.find('.cpass').removeClass('d-none');
                modal.modal('show')
            });
        })(jQuery);
    </script>
@endpush
