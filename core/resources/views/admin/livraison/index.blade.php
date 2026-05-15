@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card b-radius--10 mb-3">
                <div class="card-body">
                    <form action="">
                        <div class="d-flex flex-wrap gap-4">
                            <div class="flex-grow-1">
                                <label>@lang('Numero Commande')</label>
                                <input type="text" name="search" value="{{ request()->search }}" class="form-control">
                            </div>
                            <div class="flex-grow-1">
                                <label>@lang('Status')</label>
                                <select name="status" class="form-control">
                                    <option value="">@lang('Toutes')</option>
                                    <option value="0">@lang('En attente')</option>
                                    <option value="1">@lang("Expédié")</option>
                                    <option value="1">@lang("Encours")</option>
                                    <option value="2">@lang("Reçu")</option>
                                    <option value="3">@lang('Livré')</option>
                                </select>
                            </div>
                            <div class="flex-grow-1">
                                <label>@lang("Paiement Status")</label>
                                <select name="payment_status" class="form-control">
                                    <option value="" selected>@lang('Toutes')</option>
                                    <option value="1">@lang('Paye')</option>
                                    <option value="0">@lang('Impaye')</option>
                                </select>
                            </div>
                            <div class="flex-grow-1">
                                <label>@lang('Date')</label>
                                <input name="date" type="text" class="date form-control" placeholder="@lang('Date de début - Date de fin')" autocomplete="off" value="{{ request()->date }}">
                            </div>
                            <div class="flex-grow-1 align-self-end">
                                <button class="btn btn--primary w-100 h-45"><i class="fas fa-filter"></i> @lang('Filter')</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card b-radius--10">
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang("Coopérative Expéditeur - Staff")</th>
                                    <th>@lang('Coopérative Destinataire - Magasin')</th>
                                    <th>@lang("Montant - Numéro Commande")</th>
                                    <th>@lang('Creations Date')</th>
                                    <th>@lang("Paiement Status")</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($livraisonInfos as $livraisonInfo)
                                    <tr>
                                        <td>
                                            <span class="fw-bold">{{ __($livraisonInfo->senderCooperative->name) }}</span><br>
                                            {{ __(@$livraisonInfo->senderStaff->fullname) }}
                                        </td>
                                        <td>
                                            @if ($livraisonInfo->receiver_cooperative_id)
                                                <span class="fw-bold">{{ __(@$livraisonInfo->receiverCooperative->name) }}</span>
                                            @else
                                                @lang('N/A')
                                            @endif
                                            <br>
                                            @if ($livraisonInfo->receiver_staff_id)
                                                {{ __(@$livraisonInfo->receiverStaff->fullname) }}
                                            @else
                                                <span>@lang('N/A')</span>
                                            @endif
                                        </td>

                                        <td>
                                            <span class="fw-bold">{{ showAmount(@$livraisonInfo->paymentInfo->final_amount) }}
                                                {{ __($general->cur_text) }}</span><br>
                                            <span>{{ $livraisonInfo->code }}</span>
                                        </td>

                                        <td>
                                            {{ showDateTime($livraisonInfo->created_at, 'd M Y') }}<br>{{ diffForHumans($livraisonInfo->created_at) }}
                                        </td>

                                        <td>
                                            @if (@$livraisonInfo->paymentInfo->status == Status::PAYE)
                                                <span class="badge badge--success">@lang('Paye')</span>
                                            @elseif(@$livraisonInfo->paymentInfo->status == Status::IMPAYE)
                                                <span class="badge badge--danger">@lang('Impaye')</span>
                                            @endif
                                        </td>

                                        <td>
                                            @if ($livraisonInfo->status == Status::COURIER_QUEUE)
                                                <span class="badge badge--primary">@lang('En attente')</span>
                                            @elseif($livraisonInfo->status == Status::COURIER_DISPATCH)
                                                <span class="badge badge--warning">@lang("Envoyé")</span>
                                            @elseif($livraisonInfo->status == Status::COURIER_DELIVERYQUEUE)
                                                <span class="badge badge--dark">@lang("Reçu")</span>
                                            @elseif($livraisonInfo->status == Status::COURIER_DELIVERED)
                                                <span class="badge badge--success">@lang("Livré")</span>
                                            @endif
                                        </td>

                                        <td>
                                            <a href="{{ route('admin.livraison.invoice', $livraisonInfo->id) }}"
                                                class="btn btn-sm btn-outline--info"><i class="las la-file-invoice"></i>
                                                @lang("Facture")</a>
                                            <a href="{{ route('admin.livraison.info.details', $livraisonInfo->id) }}"
                                                class="btn btn-sm btn-outline--primary"><i class="las la-info-circle"></i>
                                                @lang("Details")</a>
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
                @if ($livraisonInfos->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($livraisonInfos) }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('style-lib')
    <link rel="stylesheet" href="{{ asset('assets/fcadmin/css/vendor/datepicker.min.css') }}">
@endpush
@push('script-lib')
    <script src="{{ asset('assets/fcadmin/js/vendor/datepicker.min.js') }}"></script>
    <script src="{{ asset('assets/fcadmin/js/vendor/datepicker.en.js') }}"></script>
@endpush
@push('script')
    <script>
        (function($) {
            "use strict";

            $('.date').datepicker({
                maxDate:new Date(),
                range:true,
                multipleDatesSeparator:"-",
                language:'en'
            });

            let url=new URL(window.location).searchParams;
            if(url.get('status') != undefined && url.get('status') != ''){
                $('select[name=status]').find(`option[value=${url.get('status')}]`).attr('selected',true);
            }
            if(url.get('payment_status') != undefined && url.get('payment_status') != ''){
                $('select[name=payment_status]').find(`option[value=${url.get('payment_status')}]`).attr('selected',true);
            }

        })(jQuery)
    </script>
@endpush

