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
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Cooperative')</th> 
                                    <th>@lang('Staff')</th> 
                                    <th>@lang('Nom magasin')</th>
                                    <th>@lang('Code')</th>
                                    <th>@lang('Coordonnees GPS')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Last Update')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($magasinCentraux as $magasin)
                                    <tr>
                                    <td>
                                            <span>{{ __($magasin->cooperative->name) }}</span>
                                        </td> 
                                    <td>
                                            <span>{{  $magasin->user->lastname }} {{  $magasin->user->firstname }}</span>
                                        </td> 
                                        
                                        <td>
                                            <span>{{ __($magasin->nom) }}</span>
                                        </td> 
                                        <td>
                                            <span>{{ __($magasin->code) }}</span>
                                        </td> 
                                        <td>
                                        @if($magasin->latitude)
                                            <span><a href="https://www.openstreetmap.org/directions?from={{$magasin->latitude}}%2C{{$magasin->longitude}}&to=" target="_blank">@lang('Voir la position')</a></span>
                                            @endif
                                        </td> 
                                        <td>
                                            @php
                                                echo $magasin->statusBadge;
                                            @endphp
                                        </td>

                                        <td>
                                            <span class="d-block">{{ showDateTime($magasin->updated_at) }}</span>
                                            <span>{{ diffForHumans($magasin->updated_at) }}</span>
                                        </td>

                                        <td>
                                            <button type="button" class="btn btn-sm btn-outline--primary  updateType"
                                                data-id="{{ $magasin->id }}" 
                                                data-nom="{{ $magasin->nom }}"
                                                data-code="{{ $magasin->code }}"
                                                data-longitude="{{ $magasin->longitude }}"
                                                data-latitude="{{ $magasin->latitude }}"
                                                data-user="{{ $magasin->staff_id }}"><i
                                                 class="las la-pen"></i>@lang('Edit')</button>

                                            @if ($magasin->status == Status::DISABLE)
                                                <button type="button"
                                                    class="btn btn-sm btn-outline--success confirmationBtn"
                                                    data-action="{{ route('manager.settings.magasinCentral.status', $magasin->id) }}"
                                                    data-question="@lang('Etes-vous sûr de vouloir activer ce magasin?')">
                                                    <i class="la la-eye"></i> @lang('Active')
                                                </button>
                                            @else
                                                <button type="button"
                                                    class="btn btn-sm btn-outline--danger confirmationBtn"
                                                    data-action="{{ route('manager.settings.magasinCentral.status', $magasin->id) }}"
                                                    data-question="@lang('Etes-vous sûr de vouloir désactiver ce magasin?')">
                                                    <i class="la la-eye-slash"></i>@lang('Désactive')
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
                @if ($magasinCentraux->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($magasinCentraux) }}
                    </div>
                @endif
            </div>
        </div>
        </x-setting-card>
    <div id="typeModel" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Ajouter un Magasin Central')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i> </button>
                </div>
                <form action="{{ route('manager.settings.magasinCentral.store') }}" method="POST">
                    @csrf
                    <div class="modal-body"> 
                    <input type="hidden" name='id'>
                    <div class="form-group row">
                                <label class="col-sm-4 control-label">@lang('Nom Staff')</label>
                                <div class="col-xs-12 col-sm-8">
                                <select class="form-control" name="user" id="user" required>
                                    <option value="">@lang('Selectionner une option')</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" @selected(old('user'))>
                                            {{ __($user->nom) }}</option>
                                    @endforeach
                                </select>
                                </div>
                            </div> 
                             

        <div class="form-group row">
            {{ Form::label(__('Nom du magasin'), null, ['class' => 'control-label col-sm-4']) }}
            <div class="col-xs-12 col-sm-8 col-md-8">
            {!! Form::text('nom', null, array('placeholder' => __('Nom du magasin'),'class' => 'form-control','required')) !!}
        </div>
    </div>
    <div class="form-group row">
            {{ Form::label(__('Code du magasin'), null, ['class' => 'control-label col-sm-4']) }}
            <div class="col-xs-12 col-sm-8 col-md-8">
            {!! Form::text('code', null, array('placeholder' => __('Code du magasin'),'class' => 'form-control')) !!}
        </div>
    </div>
    <div class="form-group row">
            {{ Form::label(__('Longitude'), null, ['class' => 'control-label col-sm-4']) }}
            <div class="col-xs-12 col-sm-8 col-md-8">
            {!! Form::text('longitude', null, array('placeholder' => __('Longitude'),'class' => 'form-control','id'=>'longitude')) !!}
        </div>
    </div>
    <div class="form-group row">
            {{ Form::label(__('Latitude'), null, ['class' => 'control-label col-sm-4']) }}
            <div class="col-xs-12 col-sm-8 col-md-8">
            {!! Form::text('latitude', null, array('placeholder' => __('Latitude'),'class' => 'form-control','id'=>'latitude')) !!}
        </div>
    </div>
    <div class="form-group row">
            {{ Form::label(__(''), null, ['class' => 'control-label col-sm-4']) }}
            <div class="col-xs-12 col-sm-8 col-md-8">
            <p id="status"></p>
            <a href="javascript:void(0)" id="find-me" class="btn btn--info">@lang('Obtenir les coordonnees GPS')</a>
        </div>
    </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn--primary w-100 h-45 ">@lang('Enregistrer')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')
    <button class="btn btn-sm btn-outline--primary addType"><i class="las la-plus"></i>@lang("Ajouter nouveau")</button>
@endpush


@push('script')
<script async src="https://maps.googleapis.com/maps/api/js?key={{env('GOOGLE_MAPS_KEY')}}" ></script>  
    <script>
        (function($) {
            "use strict";
            $('.addType').on('click', function() {
                $('#typeModel').modal('show');
            });

            $('.updateType').on('click', function() {
                var modal = $('#typeModel'); 
                modal.find('input[name=id]').val($(this).data('id')); 
                modal.find('input[name=nom]').val($(this).data('nom'));  
                modal.find('input[name=code]').val($(this).data('code'));  
                modal.find('input[name=longitude]').val($(this).data('longitude')); 
                modal.find('input[name=latitude]').val($(this).data('latitude')); 
                modal.find('select[name=user]').val($(this).data('user'));  
                modal.modal('show');
            });
        })(jQuery);

        $("#section").chained("#user");

        function geoFindMe() {
  const status = document.querySelector("#status");
  function success(position) {
    const latitude = position.coords.latitude;
    const longitude = position.coords.longitude;

$('input[name=longitude]').val(longitude);
$('input[name=latitude]').val(latitude);
$("input[name=longitude], input[name=latitude]").attr({"readonly": 'readonly'})
  }
  function error() {
    status.textContent = "Unable to retrieve your location";
  }
  if (!navigator.geolocation) {
    status.textContent = "Geolocation is not supported by your browser";
  } else {
    // status.textContent = "Locating…";
    navigator.geolocation.getCurrentPosition(success, error);
  }
}
 
document.querySelector("#find-me").addEventListener("click", geoFindMe); 
    </script>
@endpush
