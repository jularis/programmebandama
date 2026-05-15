@extends('admin.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card b-radius--10 ">
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Nom-Adresse')</th>
                                    <th>@lang('Code Coop')</th>
                                    <th>@lang('Code Coop App')</th>
                                    <th>@lang('Email-Contact')</th>
                                    <th>@lang('Utilisateurs Mobile')</th>
                                    <th>@lang('Utilisateurs Web')</th>
                                    <th>@lang('Couleur')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Creations Date')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($cooperatives as $cooperative)
                                    <tr>
                                        <td>
                                            <span class="fw-bold d-block">{{ __($cooperative->name) }}</span>
                                            <small class="text-muted"> <i>{{ __($cooperative->address) }}</i></span>
                                        </td>
                                        <td>
                                            <span class="fw-bold d-block">{{ __($cooperative->codeCoop) }}</span> 
                                        </td>
                                        <td>
                                            <span class="fw-bold d-block">{{ __($cooperative->codeApp) }}</span> 
                                        </td>
                                        <td>
                                            <span class="d-block">{{ $cooperative->email }}</span>
                                            <span>{{ $cooperative->phone }}</span>
                                        </td>
                                        <td> 
                                            <span>{{ $cooperative->mobile }}</span>
                                        </td>
                                        <td>
                                            <span class="d-block">{{ $cooperative->web }}</span> 
                                        </td>
                                        <td>
                                            <span class="badge " style="background:{{ $cooperative->color }}">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> 
                                        </td>
                                        <td>  @php echo $cooperative->statusBadge; @endphp </td>
                                        <td>
                                            <span class="d-block">{{ showDateTime($cooperative->created_at) }}</span>
                                            <span>{{ diffForHumans($cooperative->created_at) }}</span>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-outline--primary editCooperative"
                                                data-id="{{ $cooperative->id }}"
                                                data-codeapp="{{ $cooperative->codeApp }}"
                                                data-codecoop="{{ $cooperative->codeCoop }}"
                                                data-name="{{ $cooperative->name }}"
                                                data-email="{{ $cooperative->email }}" 
                                                data-phone="{{ $cooperative->phone }}"
                                                data-address="{{ $cooperative->address }}"
                                                data-mobile="{{ $cooperative->mobile }}"
                                                data-web="{{ $cooperative->web }}"
                                                data-color="{{ Str::replace('#','',$cooperative->color) }}"
                                                 ><i
                                                    class="las la-pen"></i>@lang('Edit')</button>

                                            @if ($cooperative->status == Status::DISABLE)
                                                <button type="button"
                                                    class="btn btn-sm btn-outline--success  confirmationBtn"
                                                    data-action="{{ route('admin.cooperative.status', $cooperative->id) }}"
                                                    data-question="@lang('Are you sure to enable this cooperative?')">
                                                    <i class="la la-eye"></i>@lang('Activé')
                                                </button>
                                            @else
                                                <button type="button"
                                                    class="btn btn-sm btn-outline--danger confirmationBtn"
                                                    data-action="{{ route('admin.cooperative.status', $cooperative->id) }}"
                                                    data-question="@lang('Are you sure to disable this cooperative?')">
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
                        </table><!-- table end -->
                    </div>
                </div>
                @if ($cooperatives->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($cooperatives) }}
                    </div>
                @endif
            </div><!-- card end -->
        </div>
    </div>

    <div id="cooperativeModel" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Create New Cooperative')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i> </button>
                </div>
                <form action="{{ route('admin.cooperative.store') }}" class="resetForm" method="POST">
                    @csrf
                    <input type="hidden" name="id">
                    <input type="hidden" name="codeApp">

                    <div class="modal-body">
                        <div class="form-group">
                            <label>@lang('Nom de la cooperative')</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>

                        <div class="form-group">
                            <label>@lang('Code de la cooperative')</label>
                            <input type="text" class="form-control" name="codeCoop" required>
                        </div>

                        <div class="form-group">
                            <label>@lang('Adresse Email de la coopérative')</label>
                            <input type="email" class="form-control" name="email" required>
                        </div>

                        <div class="form-group">
                            <label>@lang('Contacts')</label>
                            <input type="text" class="form-control" name="phone" required>
                        </div>


                        <div class="form-group">
                            <label>@lang('Adresse de la coopérative')</label>
                            <input type="text" class="form-control" name="address" required>
                        </div>
                        <div class="form-group">
                            <label>@lang('Utilisateurs Mobile')</label>
                            <input type="number" class="form-control" name="mobile" required>
                        </div>
                        <div class="form-group">
                            <label>@lang('Utilisateurs Web')</label>
                            <input type="number" class="form-control" name="web" required>
                        </div>
                        <div class="form-group">
                                <label> @lang('Couleur')</label>
                                <div class="input-group">
                                    <span class="input-group-text p-0 border-0">
                                        <input type='text' class="form-control colorPicker"
                                            value="" />
                                    </span>
                                    <input type="text" class="form-control colorCode" name="color"
                                        value="" required/>
                                </div>
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
    <x-search-form placeholder="Search here..." />
    <button class="btn  btn-outline--primary h-45 addNewCooperative"><i class="las la-plus"></i>@lang("Ajouter nouveau")</button>
@endpush


@push('script')
    <script>
        (function($) {
            "use strict";
            $('.addNewCooperative').on('click', function() {
                $('.resetForm').trigger('reset');
                $('#cooperativeModel').modal('show');
            });
            $('.editCooperative').on('click', function() {
                let title = "@lang('Update Cooperative')";
                var modal = $('#cooperativeModel');
                let id = $(this).data('id');
                let name = $(this).data('name');
                let codeapp = $(this).data('codeapp');
                let codecoop = $(this).data('codecoop');
                let email = $(this).data('email');
                let phone = $(this).data('phone');
                let address = $(this).data('address');
                let web = $(this).data('web');
                let mobile = $(this).data('mobile');
                let color = $(this).data('color');
                modal.find('.modal-title').text(title)
                modal.find('input[name=codeCoop]').val(codecoop);
                modal.find('input[name=codeApp]').val(codeapp);
                modal.find('input[name=id]').val(id);
                modal.find('input[name=name]').val(name);
                modal.find('input[name=email]').val(email);
                modal.find('input[name=phone]').val(phone);
                modal.find('input[name=address]').val(address);
                modal.find('input[name=web]').val(web);
                modal.find('input[name=mobile]').val(mobile);
                modal.find('input[name=color]').val(color);
                modal.modal('show');
            });

        })(jQuery);
    </script>
@endpush

@push('script-lib')
    <script src="{{ asset('assets/fcadmin/js/spectrum.js') }}"></script>
@endpush

@push('style-lib')
    <link rel="stylesheet" href="{{ asset('assets/fcadmin/css/spectrum.css') }}">
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";
            $('.colorPicker').spectrum({
                color: $(this).data('color'),  
                showButtons: false,
                move: function(color) {
                    $(this).parent().siblings('.colorCode').val(color.toHexString().replace(/^#?/, ''));
                }
            });

            $('.colorCode').on('input', function() {
                var clr = $(this).val(); 
                $(this).parents('.input-group').find('.colorPicker').spectrum({
                    color: clr,
                });
            });
 
        })(jQuery);
    </script>
@endpush
