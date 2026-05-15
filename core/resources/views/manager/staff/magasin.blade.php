@extends('manager.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card b-radius--10 ">
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Staff')</th>
                                    <th>@lang('Code')</th>
                                    <th>@lang('Nom magasin')</th>
                                    <th>@lang('Email')</th>
                                    <th>@lang('Adresse')</th>
                                    <th>@lang('Ajouté le')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                 
                                @forelse($magasins as $magasin)
                                    <tr>
                                        <td>
                                            <span>{{ __($magasin->user->lastname) }} {{ __($magasin->user->firstname) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span>{{ $magasin->code }}</span>
                                        </td>
                                        <td> 
                                        <span>{{ $magasin->nom }}</span> 
                                        </td>

                                        <td>
                                            <span>{{ $magasin->email }}<br>{{$magasin->phone }}</span>
                                        </td>
                                        <td>
                                            <span>{{ $magasin->adresse }}</span>
                                        </td>

                                        <td>
                                            {{ showDateTime($magasin->created_at) }}
                                        </td>

                                        <td>
                                            @php
                                                echo $magasin->statusBadge;
                                            @endphp
                                        </td>

                                        <td>  
                                                <button type="button" class="btn btn-sm btn-outline--primary  updateMagasin"
                                                data-id="{{ $magasin->id }}" 
                                                data-nom="{{ $magasin->nom }}"
                                                data-email="{{ $magasin->email }}"
                                                data-phone="{{ $magasin->phone }}"
                                                data-adresse="{{ $magasin->adresse }}"><i
                                                 class="las la-pen"></i>@lang('Edit')</button>
                                            @if($magasin->status == Status::BAN_USER)
                                                <button type="button"
                                                    class="btn btn-sm btn-outline--success confirmationBtn"
                                                    data-action="{{ route('manager.staff.magasin.status', $magasin->id) }}"
                                                    data-question="@lang('Êtes-vous sûr d\'activer ce magasin?')">
                                                    <i class="la la-eye"></i> @lang('Activer')
                                                </button>
                                            @else
                                                <button type="button"
                                                    class="btn btn-sm btn-outline--danger  confirmationBtn"
                                                    data-action="{{ route('manager.staff.magasin.status', $magasin->id) }}"
                                                    data-question="@lang('Êtes-vous sûr de désactiver ce magasin?')">
                                                    <i class="la la-eye-slash"></i> @lang('Désactiver')
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
                @if ($magasins->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($magasins) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div id="magasinModel" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Ajouter un magasin de section')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i> </button>
                </div>
                <form action="{{ route('manager.staff.magasin.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                    <input type="hidden" name="id">
                    <input type="hidden" name="staff" value="{{ $staffId }}"> 
                        <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group">
            {{ Form::label(__('Nom du magasin'), null, ['class' => 'control-label']) }}
            {!! Form::text('nom', null, array('placeholder' => __('Nom du magasin'),'class' => 'form-control','required')) !!}
        </div>
    </div>


    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group">
            {{ Form::label(__('Contacts'), null, ['class' => 'control-label required']) }}
            {!! Form::text('phone', null, array('class' => 'form-control','required')) !!}
        </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group">
            {{ Form::label(__('Email'), null, ['class' => 'control-label ']) }}
            {!! Form::email('email', null, array('class' => 'form-control', )) !!}
        </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group">
            {{ Form::label(__('Adresse'), null, ['class' => 'control-label']) }}
            {!! Form::text('adresse', null, array('class' => 'form-control')) !!}
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
    <x-search-form placeholder="Search here" />
    <button class="btn  btn-outline--primary h-45 addMagasin"><i
            class="las la-plus"></i>@lang("Ajouter nouveau")</button>
    <x-back route="{{ route('manager.staff.index') }}"/>
@endpush
@push('script')
    <script>
        (function($) {
            "use strict";
            $('.addMagasin').on('click', function() {
                $('#magasinModel').modal('show');
            });

            $('.updateMagasin').on('click', function() {
                var modal = $('#magasinModel');
                 
                modal.find('input[name=id]').val($(this).data('id'));
                modal.find('input[name=nom]').val($(this).data('nom'));
                modal.find('input[name=phone]').val($(this).data('phone'));
                modal.find('input[name=email]').val($(this).data('email'));
                modal.find('input[name=adresse]').val($(this).data('adresse')); 
                modal.modal('show');
            });
        })(jQuery);
    </script>
@endpush