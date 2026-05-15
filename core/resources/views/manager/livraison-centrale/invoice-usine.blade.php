@extends('manager.layouts.app')
@section('panel')
    <div class="card">
        <div class="card-body">
            <div id="printFacture">
                <div class="content-header d-flex justify-content-between">
                <div style="width:60%;">
                    <h3>
                        @lang('N° Connaissement Usine'):
                        <small>{{ $livraisonInfo->numeroCU }}</small>
                        <br> 
                        @lang('Date de livraison: ') {{ showDateTime($livraisonInfo->date_livraison, 'd M Y') }}
                        <br>
                            <b>@lang('Cooperative'):</b> {{ __($livraisonInfo->cooperative->name) }}
                            <br>
                            <b>@lang('Magasin Central'):</b> {{ __($livraisonInfo->magasinCentral->nom) }}
                    </h3>
                </div>
                <div style="width:30%;">
                <div class="text-center">
                <?php $numeroProducteurs=''; ?>
                @foreach($livraisonInfo->products as $prodc)
                            <?php $numeroProducteurs .= $prodc->producteur->nom.' '.$prodc->producteur->prenoms.'('.$prodc->producteur->codeProdapp.')'."\n"; ?>
                            @endforeach
                            <?php 
                            $textQR = 'N° CONNAISSEMENT USINE: '.$livraisonInfo->numeroCU."\n".'Date de livraison:'.showDateTime($livraisonInfo->date_livraison, 'd/m/Y')."\n".'COOPERATIVE:'.$livraisonInfo->cooperative->name."\n".'MAGASIN CENTRAL:'.$livraisonInfo->magasinCentral->nom."\n".'PRODUCTEURS :'."\n".$numeroProducteurs;
                            ?>
                        {!! QrCode::size(150)->generate($textQR) !!}
                             
                        </div>
                </div>
                </div>

                <div class="invoice">
                 
                    <div class=" invoice-info d-flex justify-content-between">
                        <div style="width:30%;">
                            @lang('DE')
                            <address>
                                <strong>{{ __($livraisonInfo->magasinCentral->nom) }}</strong><br>
                                {{ __($livraisonInfo->magasinCentral->user->adresse) }}<br>
                                @lang('Contact'): {{ $livraisonInfo->magasinCentral->user->mobile }}<br>
                                @lang('Email'): {{ $livraisonInfo->magasinCentral->user->email }}
                            </address>
                        </div> 
                        <div style="width:30%;">
                        @lang('A')
                            <address>
                                <strong>COMPAGNIE CACAOYERE DU BANDAMA (CCB)</strong><br>
                                {{ __($livraisonInfo->receiver_address) }}<br>
                                @lang('Contact'): {{ $livraisonInfo->receiver_phone }}<br>
                                @lang('Email'): {{ $livraisonInfo->receiver_email }}
                            </address>
                        </div>

                    </div>

                    <div class="row">
                        <div class="col-12">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>@lang('Programme')</th>
                                        <th>@lang('Producteur')</th> 
                                        <th>@lang('Parcelle')</th>
                                        <th>@lang('Certificat')</th> 
                                        <th>@lang('Type produit')</th> 
                                        <th>@lang('Sous-total')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                     
                                    @foreach ($livraisonInfo->products as $livraisonProductInfo)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $livraisonProductInfo->producteur->programme->libelle }}</td>
                                            <td>{{ $livraisonProductInfo->producteur->nom }} {{ $livraisonProductInfo->producteur->prenoms }}</td> 
                                            <td>{{ $livraisonProductInfo->parcelle->codeParc }}</td>
                                            <td>{{ __(@$livraisonProductInfo->certificat) }}</td>
                                            <td>{{ __(@$livraisonProductInfo->type_produit) }}</td> 
                                            <td>{{ $livraisonProductInfo->quantite }} </td> 
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
                                            <th>@lang('Total'):</th>
                                            <td>{{ showAmount(@$livraisonInfo->products->sum('quantite')) }} Kg
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
    <x-back route="{{ route('manager.livraison.usine.connaissement') }}" />
@endpush
