@extends('manager.layouts.app')
@section('panel')
<?php use App\Models\LivraisonProduct; ?>
    <div class="card">
        <div class="card-body">
            <div id="printFacture">
                

                <div class="invoice"> 
                    <hr>
                    <div class=" invoice-info d-flex justify-content-between">
                         
                        <div style="width:30%;">
                        @lang('A')
                            <address>
                                <strong>COMPAGNIE CACAOYERE DU BANDAMA (CCB)</strong><br>
                                @lang('Contact'): <br>
                                @lang('Email'):
                            </address>
                        </div>

                    </div>

                    <div class="row">
                        <div class="col-12">
                         
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>@lang('Campagne')</th>
                                        <th>@lang('Periode')</th>
                                        <th>@lang('Producteur')</th>
                                        <th>@lang('Parcelle')</th>
                                        <th>@lang('Type produit')</th> 
                                        <th>@lang('Quantite')</th>
                                        <th>@lang('Montant')</th>
                                        <th>@lang("Date de livraison")</th>
                                        
                                    </tr>
                                </thead>
                                <tbody>
                                     
                                    @foreach ($livraisonInfo as $livraisonProductInfo)
                                        <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            {{ $livraisonProductInfo->campagne->nom }} 
                                        </td> 
                                        <td>
                                            {{ $livraisonProductInfo->campagnePeriode->nom }} 
                                        </td>  
                                            <td>{{ $livraisonProductInfo->parcelle->producteur->nom }} {{ $livraisonProductInfo->parcelle->producteur->prenoms }}</td>
                                            <td>{{ $livraisonProductInfo->parcelle->codeParc }}</td>
                                            <td><?php $produit = LivraisonProduct::where([['campagne_id', $livraisonProductInfo->campagne_id],['campagne_periode_id', $livraisonProductInfo->campagne_periode_id],['parcelle_id', $livraisonProductInfo->parcelle_id],['livraison_info_id',$livraisonProductInfo->livraison_info_id],['qty',$livraisonProductInfo->quantite]])->first();
                                            echo $produit->type_produit;
                                            ?></td> 
                                            <td>{{ getAmount($livraisonProductInfo->quantite) }} </td>
                                            <td>
                                                {{ getAmount($livraisonProductInfo->montant) }} {{ $general->cur_sym }}
                                            </td>
                                            <td>
                                                {{ showDateTime($livraisonProductInfo->created_at, 'd M Y') }}
                                            </td>

                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="row mt-30 mb-none-30">
                        <div class="col-lg-12 mb-30">
                            <div class="table-responsive">
                                <table class="table">
                                    <tbody>
                                        
                                        <tr>
                                            <td>@lang('Total'):</td>
                                            <td>{{ showAmount(@$livraisonInfo->sum('montant')) }} {{ $general->cur_sym }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <hr>
                </div>
            </div>
            <div class="row no-print">
                <div class="col-sm-12">
                    <div class="float-sm-end">
                        <button class="btn btn-outline--primary  printFacture"><i
                                class="las la-download"></i></i>@lang('Imprimer')</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('script')
    <script>
        "use strict";
        $('.printFacture').click(function() {
            $('#printFacture').printThis();
        });
    </script>
@endpush
@push('breadcrumb-plugins')
    <x-back route="{{ route('manager.livraison.prime.producteur') }}" />
@endpush
