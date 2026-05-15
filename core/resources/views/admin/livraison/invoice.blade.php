@extends('admin.layouts.app')
@section('panel')
    <div class="card">
        <div class="card-body">
            <div id="printFacture">
                <div class="content-header">
                    <div class="d-flex justify-content-between">
                        <div class="fw-bold">
                            @lang('Numero Facture'):
                            <small>#{{ $livraisonInfo->invoice_id }}</small>
                            <br>
                            @lang('Date'):
                            {{ showDateTime($livraisonInfo->created_at, 'd M Y') }}
                            <br>
                            @lang('Date estimative de livraison'):
                            {{ showDateTime($livraisonInfo->estimate_date, 'd M Y') }}
                        </div>
                        <div>
                        </div>
                    </div>
                </div>

                <div class="invoice">
                <?php $numeroScelle=''; ?>
                @foreach ($livraisonInfo->scelles as $scelle)
                            <?php $numeroScelle .= $scelle->numero_scelle.' | '; ?>
                            @endforeach
                    <div class="d-flex justify-content-between mt-3">
                        <div class="text-center">
                            <?php
                            $textQR = 'NUMEROS SCELLES :'."\n".$numeroScelle."\n"."\n".'N° COMMANDE: '.$livraisonInfo->code."\n".'N° FACTURE: '.$livraisonInfo->invoice_id;
                            ?>
                        {!! QrCode::size(150)->generate($textQR) !!}
                             
                        </div>
                        <div>
                            <b>@lang('N° Commande'):</b> {{ $livraisonInfo->code }}<br>
                            <b>@lang("Paiement Status"):</b>
                            @if ($livraisonInfo->payment->status == Status::PAYE)
                                <span class="badge badge--success">@lang('Paye')</span>
                            @else
                                <span class="badge badge--danger">@lang('Impaye')</span>
                            @endif
                            <br>
                            <b>@lang('Cooperative Expéditeur'):</b> {{ __($livraisonInfo->senderCooperative->name) }}<br>
                            <b>@lang('Cooperative Destinataire'):</b> {{ __($livraisonInfo->receiverCooperative->name) }}
                        </div>
                    </div>
                    <hr>
                    <div class="invoice-info d-flex justify-content-between">
                        <div style="width:30%;">
                            @lang('DE')
                            <address>
                                <strong>{{ __($livraisonInfo->sender_name) }}</strong><br>
                                {{ __($livraisonInfo->sender_address) }}<br>
                                @lang('Contact'): {{ $livraisonInfo->sender_phone }}<br>
                                @lang('Email'): {{ $livraisonInfo->sender_email }}
                            </address>
                        </div>
                        <div style="width:40%;">
                        <strong> @lang('NUMEROS DE SCELLES') </strong>
                            <address>
                            {{$numeroScelle}}
                            </address>
                        </div>
                        <div style="width:30%;">
                            @lang('A')
                            <address>
                                <strong>{{ __($livraisonInfo->magasinSection->nom) }}</strong><br>
                                {{ __($livraisonInfo->magasinSection->adresse) }}<br>
                                @lang('Contact'): {{ $livraisonInfo->magasinSection->phone }}<br>
                                @lang('Email'): {{ $livraisonInfo->magasinSection->email }}
                            </address>
                        </div>

                    </div>

                    <div class="row">
                        <div class="col-12">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>@lang('Parcelle')</th>
                                        <th>@lang('Type produit')</th>
                                        <th>@lang('Prix')</th>
                                        <th>@lang('Qte')</th>
                                        <th>@lang('Sous-total')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($livraisonInfo->products as $livraisonProductInfo)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $livraisonProductInfo->parcelle->codeParc }}</td>
                                            <td>{{ __(@$livraisonProductInfo->type_produit) }}</td>
                                            <td>{{ showAmount($livraisonProductInfo->fee) }} {{ $general->cur_sym }}</td>
                                            <td>{{ $livraisonProductInfo->qty }}</td>
                                            <td>{{ showAmount($livraisonProductInfo->fee) }} {{ $general->cur_sym }}</td>
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
                                            <th>@lang('Sous-total'):</th>
                                            <td>{{ showAmount($livraisonInfo->payment->amount) }} {{ $general->cur_sym }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>@lang('Reduction'):</th>
                                            <td>{{ showAmount($livraisonInfo->payment->discount) }} {{ $general->cur_sym }}
                                                <small class="text--danger">
                                                    ({{ getAmount($livraisonInfo->payment->percentage) }}%)
                                                </small>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>@lang('Total'):</th>
                                            <td>{{ showAmount($livraisonInfo->payment->final_amount) }} {{ $general->cur_sym }}
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
                        <button class="btn btn-outline--primary m-1 printFacture">
                            <i class="las la-download"></i>@lang('Imprimer')
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
 
@endsection

@push('breadcrumb-plugins')
    <x-back route="{{ route('admin.livraison.info.index') }}" />
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";
            $('.printFacture').click(function() {
                $("#printFacture").printThis();
            });
        })(jQuery)
    </script>
@endpush