@extends('manager.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
        <div class="card b-radius--10 mb-3">
                <div class="card-body">
                    <form action="">
                        <div class="d-flex flex-wrap gap-4"> 
                            <div class="flex-grow-1">
                                <label>@lang('Recherche par Mot(s) cle(s)')</label>
                                <input type="text" name="search"  value="{{ request()->search }}" class="form-control">
                            </div>
                            <div class="flex-grow-1">
                                <label>@lang('Magasin de Section')</label>
                                <select name="magasin" class="form-control">
                                    <option value="">@lang('Tous')</option>
                                    @foreach ($magasins as $local)
                                        <option value="{{ $local->id }}" {{ request()->magasin == $local->id ? 'selected' : '' }}>{{ $local->nom }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="flex-grow-1">
                                <label>@lang('Date')</label>
                                <input name="date" type="text" class="form-control dates"
                                    placeholder="@lang('Date de debut - Date de fin')" autocomplete="off" value="{{ request()->date }}">
                            </div>
                            <div class="flex-grow-1 align-self-end">
                                <button class="btn btn--primary w-100 h-45"><i class="fas fa-filter"></i>
                                    @lang('Filter')</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card b-radius--10 ">
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                
                                <th>@lang("Campagne")</th> 
                                <th>@lang("Periode")</th> 
                                    <th>@lang('Magasin Section')</th> 
                                    <th>@lang('Stock entrant')</th> 
                                    <th>@lang('Stock sortant')</th> 
                                    <th>@lang('Date de livraison')</th>  
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($stocks as $produit)
                                    <tr>
                                     
                                    <td>
                                            {{ $produit->campagne->nom }} 
                                        </td> 
                                        <td>
                                            {{ $produit->campagnePeriode->nom }} 
                                        </td>
                                        <td> 
                                            @if(@$produit->magasin_section_id)
                                                <span class="text--primary">{{ __($produit->magasinSection->nom) }}</span>
                                            @else
                                                <span>@lang('N/A')</span>
                                            @endif
                                        </td>
                                         
                                        <td>
                                            {{ showAmount($produit->stocks_entrant) }} 
                                        </td>
                                        <td>
                                            {{ showAmount($produit->stocks_sortant) }} 
                                        </td> 
                                        <td>
                                            {{ showDateTime($produit->livraisonInfo->estimate_date, 'd/m/Y') }}<br>
                                            {{ diffForHumans($produit->livraisonInfo->estimate_date) }}
                                        </td>
                                        <td>
                                            
                                            <a href="{{ route('manager.livraison.invoice', encrypt($produit->livraison_info_id)) }}"
                                                title="" class="btn btn-sm btn-outline--info">
                                                <i class="las la-file-invoice"></i> @lang("Détails livraisons")
                                            </a>
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
                @if ($stocks->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($stocks) }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins') 
<a href="{{ route('manager.livraison.create') }}" class="btn  btn-outline--primary h-45 addNewCooperative">
        <i class="las la-plus"></i>@lang("Enregistrement Achat Cacao brousse")
    </a>
<a href="{{ route('manager.livraison.stock.section.create') }}" class="btn  btn-outline--primary h-45 addNewCooperative">
        <i class="las la-plus"></i>@lang("Enregistrer Connaissement brousse vers Magasin Central")
    </a>
<a href="{{ route('manager.livraison.exportExcel.livraisonAll') }}" class="btn  btn-outline--warning h-45"><i class="las la-cloud-download-alt"></i> Exporter en Excel</a>
@endpush

@push('style-lib')
    <link rel="stylesheet" href="{{ asset('assets/fcadmin/css/vendor/datepicker.min.css') }}">
@endpush 
@push('script')
<script src="{{ asset('assets/fcadmin/js/vendor/datepicker.min.js') }}"></script>
    <script src="{{ asset('assets/fcadmin/js/vendor/datepicker.fr.js') }}"></script>
<script src="{{ asset('assets/fcadmin/js/vendor/datepicker.en.js') }}"></script>
    <script>
        (function($) {
            "use strict";

            $('.addType').on('click', function() {
                $('#typeModel').modal('show');
            });

            $('.dates').datepicker({
                maxDate: new Date(),
                range: true,
                multipleDatesSeparator: "-",
                language: 'fr'
            });

            let url = new URL(window.location).searchParams;
            if (url.get('localite') != undefined && url.get('localite') != '') {
                $('select[name=localite]').find(`option[value=${url.get('localite')}]`).attr('selected', true);
            }
            if (url.get('status') != undefined && url.get('status') != '') {
                $('select[name=status]').find(`option[value=${url.get('status')}]`).attr('selected', true);
            }

        })(jQuery)

        $('form select').on('change', function(){
    $(this).closest('form').submit();
});
    </script>
@endpush