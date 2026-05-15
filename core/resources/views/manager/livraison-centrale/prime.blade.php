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
                                    <th>@lang('Producteur')</th>
                                    <th>@lang('Quantite')</th>
                                    <th>@lang('Montant')</th>  
                                    <th>@lang('Statut')</th>
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
                                        <span class="fw-bold">{{ $produit->parcelle->producteur->nom }} {{ $produit->parcelle->producteur->prenoms }}</span><br>
                                            <span>{{ $produit->parcelle->codeParc }}</span>
                                        </td>
                                         
                                        <td>
                                            {{ showAmount($produit->qty) }} 
                                        </td>
                                        <td>
                                            {{ showAmount($produit->somme) }} 
                                        </td> 
                                        <td> 
                                            @if($produit->status == Status::COURIER_DISPATCH)
                                                <span class="badge badge--dark">@lang('Impayé')</span>
                                            @else($produit->status == Status::COURIER_DELIVERYQUEUE)
                                                <span class="badge badge--success">@lang("Payé")</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('manager.livraison.prime.invoice',['campagne'=>$produit->campagne_id,'periode'=>$produit->campagne_periode_id,'producteur'=>$produit->producteur_id])}}"
                                                title="" class="btn btn-sm btn-outline--info">
                                                <i class="las la-file-invoice"></i> @lang("Détails livraisons")
                                            </a>
                                            @if ($produit->status == 1)
                                                <button class="btn btn-sm btn-outline--primary  delivery"
                                                    data-campagne="{{ $produit->campagne_id }}"
                                                    data-periode ="{{ $produit->campagne_periode_id }}"
                                                    data-producteur ="{{ $produit->producteur_id }}" 
                                                    ><i class="las la-truck"></i>
                                                    @lang('Confirmer le paiement')</button>
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
                @if ($stocks->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($stocks) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="modal fade" id="deliveryBy" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="" lass="modal-title" id="exampleModalLabel">@lang('Confirmation de reception')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Fermer">
                        <span class="fa fa-times"></span>
                    </button>
                </div>
                <form action="{{ route('manager.livraison.prime.delivery') }}" method="POST">
                    @csrf
                    @method('POST')
                    <input type="hidden" name="campagne">
                    <input type="hidden" name="periode">
                    <input type="hidden" name="producteur">
                    <div class="modal-body">
                        <p>@lang('Etre-vous sûr de vouloir confirmer le paiement de ce producteur?')</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn--dark" data-bs-dismiss="modal">@lang('Fermer')</button>
                        <button type="submit" class="btn btn--primary">@lang('Confirmer')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins') 
 
<a href="{{ route('manager.livraison.exportExcel.livraisonAll') }}" class="btn  btn-outline--warning h-45"><i class="las la-cloud-download-alt"></i> @lang('Exporter en Excel')</a>
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
            $('.delivery').on('click', function() {
                var modal = $('#deliveryBy');
                modal.find('input[name=campagne]').val($(this).data('campagne'))
                modal.find('input[name=periode]').val($(this).data('periode'))
                modal.find('input[name=producteur]').val($(this).data('producteur'))
                modal.modal('show');
            });
        })(jQuery)
        
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