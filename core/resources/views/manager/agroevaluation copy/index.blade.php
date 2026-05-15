@extends('manager.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
        <div class="card b-radius--10 mb-3">
                <div class="card-body">
                    <form action="">
                        <div class="d-flex flex-wrap gap-4">
                            <input type="hidden" name="table" value="agroevaluations"/>
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
                                <label>@lang('Statut')</label>
                                <select name="status" class="form-control">
                                    <option value="">@lang('Tous')</option>
                                    <option value="0">@lang('Non atteint')</option>
                                    <option value="1">@lang('Atteint')</option>
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
                                    <th>@lang('Producteur')</th>
                                    <th>@lang('Parcelle')</th>
                                    <th>@lang('Superficie')</th>  
                                    <th>@lang('Quantite')</th>  
                                    <th>@lang("Date")</th> 
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($agroevaluations as $agroevaluation)
                                    <tr>
                                        <td>
                                            <span class="fw-bold">{{ $agroevaluation->parcelle->producteur->localite->nom }}</span>
                                        </td>
                                        <td> 
                                            <span class="small">
                                            {{ $agroevaluation->parcelle->producteur->nom }} {{ $agroevaluation->parcelle->producteur->prenoms }}
                                            </span>
                                        </td>
                                        <td>
                                            <span> <a href="{{ route('manager.agro.evaluation.edit', $agroevaluation->id) }}">
                                                    <span>@</span>{{ $agroevaluation->parcelle->codeParc }}
                                                </a></span>
                                        </td>
                                        
                                        <td>
                                            <span>{{ $agroevaluation->parcelle->superficie }}</span>
                                        </td>
                                        
                                        <td>
                                            <span>{{ $agroevaluation->quantite }}</span>
                                        </td>
                                        <td>
                                            <span class="d-block">{{ showDateTime($agroevaluation->created_at) }}</span>
                                            <span>{{ diffForHumans($agroevaluation->created_at) }}</span>
                                        </td> 
                                        <td>
                                        <a href="{{ route('manager.agro.evaluation.destroy', encrypt($agroevaluation->id)) }}"
                                                class="btn btn-sm btn-outline--danger"><i
                                                    class="las la-trash"></i>@lang('Delete')</a>
                                            
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
                @if ($agroevaluations->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($agroevaluations) }}
                    </div>
                @endif
            </div>
        </div>
    </div>
     
    <x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')
    <x-search-form placeholder="Search here..." />
    <a href="{{ route('manager.agro.evaluation.create') }}" class="btn  btn-outline--primary h-45 addNewCooperative">
        <i class="las la-plus"></i>@lang("Ajouter nouveau")
    </a> 
    <a href="{{ route('manager.agro.evaluation.exportExcel.evaluationsAll') }}" class="btn  btn-outline--warning h-45"><i class="las la-cloud-download-alt"></i> Exporter en Excel</a>
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
            if(url.get('status') != undefined && url.get('status') != ''){
                $('select[name=status]').find(`option[value=${url.get('status')}]`).attr('selected',true);
            }

        })(jQuery)
    </script>
@endpush

