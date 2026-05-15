@extends('manager.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
        <div class="card b-radius--10 mb-3">
                <div class="card-body">
                    <form action="">
                        <div class="d-flex flex-wrap gap-4"> 
                            <div class="flex-grow-1">
                                <label>@lang('Recherche par Mot(s) clé(s)')</label>
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
                            <label>@lang('Type de produit')</label>
                            <select class="form-control" name="produit">
                                                    <option  value="">@lang('Tous')</option> 
                                                        <option value="{{ __('Certifie') }}"
                                                        @selected(request()->produit=='Certifie')>{{ __('Certifie') }}</option>
                                                        <option value="{{ __('Ordinaire') }}"
                                                        @selected(request()->produit=='Ordinaire')>{{ __('Ordinaire') }}</option>
                                                </select> 
                            </div>
                            <div class="flex-grow-1">
                                <label>@lang('Date')</label>
                                <input name="date" type="text" class="form-control dates"
                                    placeholder="@lang('Date de début - Date de fin')" autocomplete="off" value="{{ request()->date }}">
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
                                    <th>@lang("Staff Expéditeur")</th>
                                    <th>@lang('Magasin Section')</th>
                                    <th>@lang('Producteur - Parcelle')</th>
                                    <th>@lang('Type Produit')</th>
                                    <th>@lang("Montant - Numéro Livraison")</th>
                                    <th>@lang('Quantite(KG)')</th>
                                    <th>@lang('Date de livraison')</th>  
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($livraisonProd as $produit)
                                    <tr>
                                    <td>
                                            {{ $produit->campagne->nom }} 
                                        </td>
                                        <td>
                                            {{ $produit->campagnePeriode->nom }} 
                                        </td>
                                        <td>
                                             
                                            <a class="text--primary" href="{{ route('manager.staff.edit', encrypt($produit->livraisonInfo->senderStaff->id)) }}">
                                                <span class="text--primary">@</span>{{ __($produit->livraisonInfo->senderStaff->lastname) }} {{ __($produit->livraisonInfo->senderStaff->firstname) }}
                                            </a>
                                        </td>
                                        <td>
                                             
                                            @if(@$produit->livraisonInfo->receiver_magasin_section_id)
                                            <span class="fw-bold">{{ __($produit->livraisonInfo->magasinSection->nom) }}</span>
                                            @else
                                                <span>@lang('N/A')</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="fw-bold">{{ $produit->parcelle->producteur->nom }} {{ $produit->parcelle->producteur->prenoms }}</span><br>
                                            <span>{{ $produit->parcelle->codeParc }}</span>
                                        </td>
                                        <td>
                                        <span class="fw-bold">{{ @$produit->type_produit }}</span><br>
                                            {{ $produit->certificat }}  
                                        </td>
                                        <td>
                                            <span class="fw-bold">{{ showAmount(@$produit->fee) }}
                                                {{ __($general->cur_text) }}</span><br>
                                            <span>{{ $produit->livraisonInfo->code }}</span>
                                        </td>
                                        <td>
                                            {{ $produit->qty }} 
                                        </td>
                                        <td>
                                            {{ showDateTime($produit->livraisonInfo->estimate_date, 'd M Y') }}<br>
                                            {{ diffForHumans($produit->livraisonInfo->estimate_date) }}
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
                @if ($livraisonProd->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($livraisonProd) }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins') 

<a href="{{ route('manager.livraison.create') }}" class="btn  btn-outline--primary h-45 addNewCooperative">
        <i class="las la-plus"></i>@lang("Enregistrer une livraison")
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