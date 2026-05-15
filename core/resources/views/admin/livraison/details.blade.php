@extends('admin.layouts.app')
@section('panel')
    <div class="row mb-none-30">
        <div class="col-xl-3 col-lg-5 col-md-5 col-sm-12">
            <div class="card b-radius--10 overflow-hidden box--shadow1">
                <div class="card-body">
                    <h5 class="mb-20 text-muted">@lang('Sender Staff')</h5>
                    <ul class="list-group">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Fullname')
                            <span>{{ __($livraisonInfo->senderStaff->fullname) }}</span>
                        </li>

                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Email')
                            <span>{{ __($livraisonInfo->senderStaff->email) }}</span>
                        </li>

                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Cooperative')
                            <span>{{ __($livraisonInfo->senderCooperative->name) }}</span>
                        </li>

                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Status')
                            @if ($livraisonInfo->senderStaff->status == Status::ENABLE)
                                <span class="badge badge-pill badge--success">@lang('Active')</span>
                            @elseif($livraisonInfo->senderStaff->status == Status::DISABLE)
                                <span class="badge badge-pill badge--danger">@lang('Banned')</span>
                            @endif
                        </li>
                    </ul>
                </div>
            </div>

            @if ($livraisonInfo->receiver_staff_id)
                <div class="card b-radius--10 overflow-hidden mt-30 box--shadow1">
                    <div class="card-body">
                        <h5 class="mb-20 text-muted">@lang('Receiver Staff')</h5>
                        <ul class="list-group">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                @lang('Fullname')
                                <span>{{ __($livraisonInfo->receiverStaff->fullname) }}</span>
                            </li>

                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                @lang('Email')
                                <span>{{ __($livraisonInfo->receiverStaff->email) }}</span>
                            </li>

                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                @lang('Cooperative')
                                <span>{{ __($livraisonInfo->receiverCooperative->name) }}</span>
                            </li>

                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                @lang('Status')
                                @if ($livraisonInfo->receiverStaff->status == Status::ENABLE)
                                    <span class="badge badge-pill badge--success">@lang('Active')</span>
                                @elseif($livraisonInfo->receiverStaff->status == Status::DISABLE)
                                    <span class="badge badge-pill badge--danger">@lang('Banned')</span>
                                @endif
                            </li>
                        </ul>
                    </div>
                </div>
            @endif
        </div>

        <div class="col-xl-9 col-lg-7 col-md-7 col-sm-12 mt-10">
            <div class="row mb-30">
                <div class="col-lg-6 mt-2">
                    <div class="card border--dark">
                        <h5 class="card-header bg--dark">@lang('Information Expéditeur')</h5>
                        <div class="card-body">
                            <ul class="list-group">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    @lang('Nom')
                                    <span>{{ __($livraisonInfo->receiverCooperative->name) }}</span>
                                </li>

                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    @lang('Email')
                                    <span>{{ __($livraisonInfo->receiverCooperative->email) }}</span>
                                </li>

                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    @lang('Contact')
                                    <span>{{ __($livraisonInfo->receiverCooperative->phone) }}</span>
                                </li>

                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    @lang('Adresse')
                                    <span>{{ __($livraisonInfo->receiverCooperative->address) }}</span>
                                </li>

                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    @lang('Numero Commande')
                                    <span class="fw-bold">{{ $livraisonInfo->code }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 mt-2">
                    <div class="card border--dark">
                        <h5 class="card-header bg--dark">@lang('Information Destinataire')</h5>
                        <div class="card-body">
                            <ul class="list-group">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    @lang('Nom')
                                    <span>{{ __($livraisonInfo->magasinSection->nom) }}</span>
                                </li>

                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    @lang('Email')
                                    <span>{{ $livraisonInfo->magasinSection->email }}</span>
                                </li>

                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    @lang('Contact')
                                    <span>{{ $livraisonInfo->magasinSection->phone }}</span>
                                </li>

                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    @lang('Adresse')
                                    <span>{{ __($livraisonInfo->magasinSection->adresse) }}</span>
                                </li>

                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    @lang('Status')
                                    @if ($livraisonInfo->status != Status::COURIER_DELIVERED)
                                        <span class="badge badge--primary fw-bold">@lang('Waiting')</span>
                                    @elseif($livraisonInfo->status == Status::COURIER_DELIVERED)
                                        <span class="badge badge--success">@lang("Livré")</span>
                                    @endif
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mb-30">
                <div class="col-lg-12">
                    <div class="card border--dark">
                        <h5 class="card-header bg--dark">@lang('Livraison Details')</h5>
                        <div class="card-body">
                            <div class="table-responsive--md  table-responsive">
                                <table class="table table--light style--two">
                                    <thead>
                                        <tr>
                                            <th>@lang('Livraison Type')</th>
                                            <th>@lang('Prix')</th>
                                            <th>@lang('Qte')</th>
                                            <th>@lang('Sous-total')</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($livraisonInfo->products as $livraisonProductInfo)
                                            <tr>
                                                <td>{{ __($livraisonProductInfo->type_produit) }}</td>
                                                <td>{{ $general->cur_sym }}{{ showAmount($livraisonProductInfo->fee) }}</td>
                                                <td>{{ $livraisonProductInfo->qty }}</td>
                                                <td>{{ showAmount($livraisonProductInfo->fee) }} {{ $general->cur_sym }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mb-30">
                <div class="col-lg-12 mt-2">
                    <div class="card border--dark">
                        <h5 class="card-header bg--dark">@lang('Payment Information')</h5>
                        <div class="card-body">
                            <ul class="list-group">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    @lang('Payment Received By ')
                                    @if (!empty($livraisonInfo->paymentInfo->cooperative_id))
                                        <span>{{ __(@$livraisonInfo->paymentInfo->cooperative->name) }}</span>
                                    @else
                                        <span>@lang('N/A')</span>
                                    @endif
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    @lang('Date')
                                    @if (!empty($livraisonInfo->paymentInfo->date))
                                        <span>{{ showDateTime($livraisonInfo->date, 'd M Y') }}</span>
                                    @else
                                        <span>@lang('N/A')</span>
                                    @endif
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    @lang('Sous-total')
                                    <span>{{ showAmount($livraisonInfo->paymentInfo->amount) }}
                                        {{ __($general->cur_text) }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    @lang('Reduction')
                                    <span>
                                        {{ showAmount($livraisonInfo->paymentInfo->discount) }}
                                        {{ __($general->cur_text) }}
                                        <small class="text--danger">({{ getAmount($livraisonInfo->payment->percentage)}}%)</small>
                                    </span>
                                </li>

                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    @lang('Total')
                                    <span>{{ showAmount($livraisonInfo->paymentInfo->final_amount) }}
                                        {{ __($general->cur_text) }}</span>
                                </li>

                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    @lang('Status')
                                    @if ($livraisonInfo->paymentInfo->status == Status::PAYE)
                                        <span class="badge badge--success">@lang('Paye')</span>
                                    @else
                                        <span class="badge badge--danger">@lang('Impaye')</span>
                                    @endif
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <x-back route="{{ route('admin.livraison.info.index') }}" />
@endpush
