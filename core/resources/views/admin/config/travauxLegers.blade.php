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
                                    <th>@lang('Nom')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Last Update')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($travauxLegers as $travaux)
                                    <tr>

                                        <td>
                                            <span>{{ __($travaux->nom) }}</span>
                                        </td> 
                                        <td>
                                            @php
                                                echo $travaux->statusBadge;
                                            @endphp
                                        </td>

                                        <td>
                                            <span class="d-block">{{ showDateTime($travaux->updated_at) }}</span>
                                            <span>{{ diffForHumans($travaux->updated_at) }}</span>
                                        </td>

                                        <td>
                                            <button type="button" class="btn btn-sm btn-outline--primary  updateTravaux"
                                                data-id="{{ $travaux->id }}" 
                                                data-nom="{{ $travaux->nom }}"><i
                                                 class="las la-pen"></i>@lang('Edit')</button>

                                            @if ($travaux->status == Status::DISABLE)
                                                <button type="button"
                                                    class="btn btn-sm btn-outline--success confirmationBtn"
                                                    data-action="{{ route('admin.config.travauxLegers.status', $travaux->id) }}"
                                                    data-question="@lang('Etes-vous sûr de vouloir activer ce travail legers?')">
                                                    <i class="la la-eye"></i> @lang('Activé')
                                                </button>
                                            @else
                                                <button type="button"
                                                    class="btn btn-sm btn-outline--danger confirmationBtn"
                                                    data-action="{{ route('admin.config.travauxLegers.status', $travaux->id) }}"
                                                    data-question="@lang('Etes-vous sûr de vouloir désactiver ce travail legers?')">
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
                @if ($travauxLegers->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($travauxLegers) }}
                    </div>
                @endif
            </div>
        </div>
    </div>
    <div id="travauxModel" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Ajouter un travail Legers')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i> </button>
                </div>
                <form action="{{ route('admin.config.travauxLegers.store') }}" method="POST">
                    @csrf
                    <div class="modal-body"> 
                    <input type="hidden" name='id'>
                        
        <div class="form-group row">
            {{ Form::label(__('Nom du travail Legers'), null, ['class' => 'control-label col-sm-4']) }}
            <div class="col-xs-12 col-sm-8 col-md-8">
            {!! Form::text('nom', null, array('placeholder' => __('Nom du travail legers'),'class' => 'form-control','required')) !!}
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
    <button class="btn btn-sm btn-outline--primary addTravaux"><i class="las la-plus"></i>@lang("Ajouter nouveau")</button>
@endpush


@push('script')
    <script>
        (function($) {
            "use strict";
            $('.addTravaux').on('click', function() {
                $('#travauxModel').modal('show');
            });

            $('.updateTravaux').on('click', function() {
                var modal = $('#travauxModel'); 
                modal.find('input[name=id]').val($(this).data('id'));
                modal.find('input[name=nom]').val($(this).data('nom')); 
                modal.modal('show');
            });
        })(jQuery);
    </script>
@endpush
