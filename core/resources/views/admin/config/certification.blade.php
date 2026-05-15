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
                                    <th>@lang('Titre')</th>  
                                    <th>@lang('Status')</th>
                                    <th>@lang('Last Update')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($certifications as $certification)
                                    <tr> 
                                        <td>
                                            <span>{{ __($certification->nom) }}</span>
                                        </td>
 
                                        <td>
                                            @php
                                                echo $certification->statusBadge;
                                            @endphp
                                        </td>

                                        <td>
                                            <span class="d-block">{{ showDateTime($certification->updated_at) }}</span>
                                            <span>{{ diffForHumans($certification->updated_at) }}</span>
                                        </td>

                                        <td> 
                                            <button type="button" class="btn btn-sm btn-outline--primary  updateCampagne"
                                                data-id="{{ $certification->id }}" 
                                                data-nom="{{ $certification->nom }}"><i
                                                 class="las la-pen"></i>@lang('Edit')</button>

                                            @if ($certification->status == Status::DISABLE)
                                                <button type="button"
                                                    class="btn btn-sm btn-outline--success confirmationBtn"
                                                    data-action="{{ route('admin.config.certification.status', $certification->id) }}"
                                                    data-question="@lang('Etes-vous sûr de vouloir activer cette certification?')">
                                                    <i class="la la-eye"></i> @lang('Activé')
                                                </button>
                                            @else
                                                <button type="button"
                                                    class="btn btn-sm btn-outline--danger confirmationBtn"
                                                    data-action="{{ route('admin.config.certification.status', $certification->id) }}"
                                                    data-question="@lang('Etes-vous sûr de vouloir désactiver cette certification?')">
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
                @if ($certifications->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($certifications) }}
                    </div>
                @endif
            </div>
        </div>
    </div>
    <div id="unitModel" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Ajouter une certification')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i> </button>
                </div>
                <form action="{{ route('admin.config.certification.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                   
                        <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group">
            {{ Form::label(__('Nom de la certification'), null, ['class' => 'control-label required']) }}
            {!! Form::text('nom', null, array('placeholder' => __(''),'class' => 'form-control','required')) !!}
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


    <div id="updateCampagneModel" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Mise à jour de la certification')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form action="{{ route('admin.config.certification.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="id">
                    <div class="modal-body">
                     
                        <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group">
            {{ Form::label(__('Nom de la certification'), null, ['class' => 'control-label required']) }}
            {!! Form::text('nom', null, array('placeholder' => __(''),'class' => 'form-control','required')) !!}
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
    <button class="btn btn-sm btn-outline--primary addCampagne"><i class="las la-plus"></i>@lang("Ajouter nouveau")</button>
@endpush


@push('script')
    <script>
        (function($) {
            "use strict";
            $('.addCampagne').on('click', function() {
                $('#unitModel').modal('show');
            });

            $('.updateCampagne').on('click', function() {
                var modal = $('#updateCampagneModel');
                 
                modal.find('input[name=id]').val($(this).data('id'));
                modal.find('input[name=nom]').val($(this).data('nom'));
                modal.modal('show');
            });
        })(jQuery);
    </script>
@endpush
