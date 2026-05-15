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
                                    <th>@lang('Programme')</th> 
                                    <th>@lang('Status')</th>
                                    <th>@lang('Last Update')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($programmes as $programme)
                                    <tr> 

                                        <td>
                                            <span>{{ __($programme->libelle) }}</span>
                                        </td> 
                                        <td>
                                            @php
                                                echo $programme->statusBadge;
                                            @endphp
                                        </td>

                                        <td>
                                            <span class="d-block">{{ showDateTime($programme->updated_at) }}</span>
                                            <span>{{ diffForHumans($programme->updated_at) }}</span>
                                        </td>

                                        <td> 
                                        <a href="{{ route('admin.config.programme.primeIndex', ['idcamp'=>$programme->id]) }}"
                                                class="btn btn-sm btn-outline--warning"><i
                                                    class="las la-money"></i>@lang('Primes du programme')</a>
                                            <button type="button" class="btn btn-sm btn-outline--primary  updateProgramme"
                                                data-id="{{ $programme->id }}" 
                                                data-nom="{{ $programme->libelle }}"><i
                                                 class="las la-pen"></i>@lang('Edit')</button>

                                            @if ($programme->status == Status::DISABLE)
                                                <button type="button"
                                                    class="btn btn-sm btn-outline--success confirmationBtn"
                                                    data-action="{{ route('admin.config.programme.status', $programme->id) }}"
                                                    data-question="@lang('Etes-vous sûr de vouloir activer cette programme?')">
                                                    <i class="la la-eye"></i> @lang('Activé')
                                                </button>
                                            @else
                                                <button type="button"
                                                    class="btn btn-sm btn-outline--danger confirmationBtn"
                                                    data-action="{{ route('admin.config.programme.status', $programme->id) }}"
                                                    data-question="@lang('Etes-vous sûr de vouloir désactiver cette programme?')">
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
                @if ($programmes->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($programmes) }}
                    </div>
                @endif
            </div>
        </div>
    </div>
    <div id="unitModel" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Ajouter une programme')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i> </button>
                </div>
                <form action="{{ route('admin.config.programme.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                     
                        <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group">
            {{ Form::label(__('Nom du programme de durabilité'), null, ['class' => 'control-label required']) }}
            {!! Form::text('nom', null, array('class' => 'form-control','required')) !!}
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


    <div id="updateProgrammeModel" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Mise à jour de la programme')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form action="{{ route('admin.config.programme.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="id">
                    <div class="modal-body"> 

                        <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group">
            {{ Form::label(__('Nom du programme de Dureabilité'), null, ['class' => 'control-label required']) }}
            {!! Form::text('nom', null, array('class' => 'form-control','required')) !!}
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
    <button class="btn btn-sm btn-outline--primary addProgramme"><i class="las la-plus"></i>@lang("Ajouter nouveau")</button>
@endpush


@push('script')
    <script>
        (function($) {
            "use strict";
            $('.addProgramme').on('click', function() {
                $('#unitModel').modal('show');
            });

            $('.updateProgramme').on('click', function() {
                var modal = $('#updateProgrammeModel');
                 
                modal.find('input[name=id]').val($(this).data('id'));
                modal.find('input[name=nom]').val($(this).data('nom')); 
                modal.modal('show');
            });
        })(jQuery);
    </script>
@endpush
