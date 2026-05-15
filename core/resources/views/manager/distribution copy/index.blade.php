@extends('manager.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
        <div class="card b-radius--10 mb-3">
                <div class="card-body">
                    <form action="">
                        <div class="d-flex flex-wrap gap-4">
                            <input type="hidden" name="table" value="distributions"/>
                            <div class="flex-grow-1">
                                <label>@lang('Recherche par Mot(s) clé(s)')</label>
                                <input type="text" name="search" value="{{ request()->search }}" class="form-control">
                            </div>
                            <div class="flex-grow-1">
                                <label>@lang('Localité')</label>
                                <select name="localite" class="form-control">
                                    <option value="">@lang('Toutes')</option>
                                    @foreach($localites as $local)
                                    <option value="{{ $local->id }}">{{ $local->nom }}</option>
                                    @endforeach 
                                </select>
                            </div> 
                            <div class="flex-grow-1">
                                <label>@lang('Date')</label>
                                <input name="date" type="text" class="dates form-control" placeholder="@lang('Date de début - Date de fin')" autocomplete="off" value="{{ request()->date }}">
                            </div>
                            <div class="flex-grow-1 align-self-end">
                                <button class="btn btn--primary w-100 h-45"><i class="fas fa-filter"></i> @lang('Filter')</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card b-radius--10 ">
                <div class="card-body  p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr> 
                                    <th>@lang('Localite')</th>
                                    <th>@lang('Parcelle')</th>
                                    <th>@lang('Producteur')</th>
                                    <th>@lang('Quantite')</th> 
                                    <th>@lang('Ajoutée le')</th> 
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($distributions as $distribution)
                                    <tr>
                                        <td>
                                            <span class="fw-bold">{{ $distribution->parcelle->producteur->localite->nom }}</span>
                                        </td>
                                        <td>
                                            <span> <a href="{{ route('manager.agro.distribution.edit', $distribution->id) }}">
                                                    <span>@</span>{{ $distribution->parcelle->codeParc }}
                                                </a></span>
                                        </td>
                                        <td> 
                                            <span class="small">
                                            {{ $distribution->parcelle->producteur->nom }} {{ $distribution->parcelle->producteur->prenoms }}
                                            </span>
                                        </td>
                                        <td>
                                            <span>{{ array_sum(Arr::pluck(json_decode($distribution->especes),'total')) }}</span>
                                        </td> 
                                        <td>
                                            <span class="d-block">{{ showDateTime($distribution->created_at) }}</span>
                                            <span>{{ diffForHumans($distribution->created_at) }}</span>
                                        </td> 
                                        <td>
                                        <a href="{{ route('manager.agro.distribution.edit', $distribution->id) }}"
                                                class="btn btn-sm btn-outline--primary"><i
                                                    class="las la-pen"></i>@lang('Edit')</a>
                                             
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
                @if ($distributions->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($distributions) }}
                    </div>
                @endif
            </div>
        </div>
    </div>
     
    <x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')
    
    <a href="{{ route('manager.agro.distribution.create') }}" class="btn  btn-outline--primary h-45 addNewCooperative">
        <i class="las la-plus"></i>@lang("Ajouter nouveau")
    </a>
    <a href="{{ route('manager.agro.distribution.exportExcel.distributionAll') }}" class="btn  btn-outline--warning h-45"><i class="las la-cloud-download-alt"></i> Exporter en Excel</a>
@endpush
@push('style')
    <style>
        .table-responsive {
            overflow-x: auto;
        }
    </style>
@endpush
@push('style-lib')
    <link rel="stylesheet" href="{{ asset('assets/fcadmin/css/vendor/datepicker.min.css') }}">
@endpush
@push('script')
    <script src="{{ asset('assets/fcadmin/js/vendor/datepicker.min.js') }}"></script>
    <script src="{{ asset('assets/fcadmin/js/vendor/datepicker.fr.js') }}"></script>
<script src="{{ asset('assets/fcadmin/js/vendor/datepicker.en.js') }}"></script>
@endpush
@push('script')
    <script>
        (function($) {
            "use strict";

            $('.addType').on('click', function() {
                $('#typeModel').modal('show');
            });
            
            $('.dates').datepicker({
                maxDate:new Date(),
                range:true,
                multipleDatesSeparator:"-",
                language:'en'
            });

            let url=new URL(window.location).searchParams;
            if(url.get('localite') != undefined && url.get('localite') != ''){
                $('select[name=localite]').find(`option[value=${url.get('localite')}]`).attr('selected',true);
            }
            if(url.get('payment_status') != undefined && url.get('payment_status') != ''){
                $('select[name=payment_status]').find(`option[value=${url.get('payment_status')}]`).attr('selected',true);
            }

        })(jQuery)
    </script>
@endpush

