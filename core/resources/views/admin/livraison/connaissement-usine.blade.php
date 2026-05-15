@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card b-radius--10 mb-3">
                <div class="card-body">
                    <form action="">
                        <div class="d-flex flex-wrap gap-4">
                            <div class="flex-grow-1">
                                <label>@lang('Recherche')</label>
                                <input type="text" name="search" value="{{ request()->search }}" class="form-control">
                            </div>
                            <div class="flex-grow-1">
                                <label>@lang('Cooperative')</label>
                                <select name="cooperative_id" class="form-control">
                                    <option value="">@lang('Toutes')</option>
                                    @foreach ($cooperatives as $cooperative)
                                        <option value="{{ $cooperative->id }}" @selected(request()->cooperative_id == $cooperative->id)>{{ $cooperative->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="flex-grow-1">
                                <label>@lang('Magasin Central')</label>
                                <select name="magasin" class="form-control">
                                    <option value="">@lang('Tous')</option>
                                    @foreach ($magasins as $magasin)
                                        <option value="{{ $magasin->id }}" @selected(request()->magasin == $magasin->id)>
                                            {{ $magasin->nom }} @if($magasin->cooperative) - {{ $magasin->cooperative->name }} @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="flex-grow-1">
                                <label>@lang('Statut')</label>
                                <select name="status" class="form-control">
                                    <option value="">@lang('Tous')</option>
                                    <option value="{{ Status::COURIER_DISPATCH }}" @selected(request()->status == Status::COURIER_DISPATCH)>@lang('En attente de reception')</option>
                                    <option value="{{ Status::COURIER_DELIVERYQUEUE }}" @selected(request()->status == Status::COURIER_DELIVERYQUEUE)>@lang('Receptionnee')</option>
                                    <option value="{{ Status::COURIER_DELIVERED }}" @selected(request()->status == Status::COURIER_DELIVERED)>@lang('Refoulee')</option>
                                </select>
                            </div>
                            <div class="flex-grow-1">
                                <label>@lang('Date')</label>
                                <input name="date" type="text" class="form-control date"
                                    placeholder="@lang('Date de debut - Date de fin')" autocomplete="off" value="{{ request()->date }}">
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
                                    <th>@lang('Livraison')</th>
                                    <th>@lang('Cooperative')</th>
                                    <th>@lang('Campagne')</th>
                                    <th>@lang('Magasin Central')</th>
                                    <th>@lang('Transporteur')</th>
                                    <th>@lang('Vehicule')</th>
                                    <th>@lang('Type Produit')</th>
                                    <th>@lang('Quantite chargee')</th>
                                    <th>@lang('Sacs')</th>
                                    <th>@lang('Quantite receptionnee')</th>
                                    <th>@lang('Statut')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($stocks as $produit)
                                    <tr>
                                        <td>{{ $produit->numeroCU }}</td>
                                        <td>{{ __(@$produit->cooperative->name) }}</td>
                                        <td>
                                            {{ __(@$produit->campagne->nom) }}<br>
                                            <small>{{ __(@$produit->campagnePeriode->nom) }}</small>
                                        </td>
                                        <td>{{ __(@$produit->magasinCentral->nom) }}</td>
                                        <td>{{ __(@$produit->transporteur->nom) }} {{ __(@$produit->transporteur->prenoms) }}</td>
                                        <td>{{ __(@$produit->vehicule->marque->nom) }} ({{ __(@$produit->vehicule->vehicule_immat) }})</td>
                                        <td><span class="btn btn-sm btn-outline--success">{{ $produit->type_produit }}</span></td>
                                        <td>{{ showAmount($produit->quantite_livre) }} Kg</td>
                                        <td>{{ $produit->sacs_livre }}</td>
                                        <td>{{ showAmount($produit->quantite_confirme) }} Kg</td>
                                        <td>
                                            @if($produit->status == Status::COURIER_DISPATCH)
                                                <span class="badge badge--dark">@lang('En attente de reception')</span>
                                            @elseif($produit->status == Status::COURIER_DELIVERYQUEUE)
                                                <span class="badge badge--success">@lang('Receptionnee')</span>
                                            @elseif($produit->status == Status::COURIER_DELIVERED)
                                                <span class="badge badge--danger">@lang('Refoulee')</span>
                                                <br><small>{{ $produit->commentaire }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.livraison.usine.invoice', encrypt($produit->id)) }}" class="btn btn-sm btn-outline--info">
                                                <i class="las la-file-invoice"></i> @lang('Details')
                                            </a>
                                            @if($produit->status == Status::COURIER_DELIVERYQUEUE)
                                                <a href="{{ route('admin.livraison.usine.suivi', encrypt($produit->id)) }}" class="btn btn-sm btn-outline--primary">
                                                    <i class="las la-random"></i> @lang('Suivi')
                                                </a>
                                            @endif
                                            @if($produit->status == Status::COURIER_DISPATCH)
                                                <button type="button" class="btn btn-sm btn-outline--danger refoule" data-code="{{ $produit->numeroCU }}">
                                                    <i class="las la-ban"></i> @lang('Refouler')
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline--secondary delivery" data-code="{{ $produit->numeroCU }}" data-qterecept="{{ $produit->quantite_livre }}">
                                                    <i class="las la-truck"></i> @lang('Receptionner')
                                                </button>
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

    <div class="modal fade" id="deliveryBy" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Confirmation de reception')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="@lang('Fermer')">
                        <span class="fa fa-times"></span>
                    </button>
                </div>
                <form action="{{ route('admin.livraison.usine.delivery') }}" method="POST">
                    @csrf
                    <input type="hidden" name="code">
                    <div class="modal-body">
                        <p>@lang('Etes-vous sur de vouloir confirmer la reception de cette livraison ?')</p>
                        <div class="form-group">
                            <label>@lang('Quantite receptionnee')</label>
                            <input type="number" name="quantite_confirme" required class="form-control">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn--dark" data-bs-dismiss="modal">@lang('Fermer')</button>
                        <button type="submit" class="btn btn--primary">@lang('Confirmer')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="refouleBy" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Confirmation de refoulement')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="@lang('Fermer')">
                        <span class="fa fa-times"></span>
                    </button>
                </div>
                <form action="{{ route('admin.livraison.usine.refoule') }}" method="POST">
                    @csrf
                    <input type="hidden" name="code">
                    <div class="modal-body">
                        <p>@lang('Etes-vous sur de vouloir confirmer le refoulement de cette livraison ?')</p>
                        <div class="form-group">
                            <label>@lang('Commentaire')</label>
                            <textarea name="commentaire" rows="4" maxlength="500" required class="form-control"></textarea>
                        </div>
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

@push('style-lib')
    <link rel="stylesheet" href="{{ asset('assets/fcadmin/css/vendor/datepicker.min.css') }}">
@endpush
@push('script-lib')
    <script src="{{ asset('assets/fcadmin/js/vendor/datepicker.min.js') }}"></script>
    <script src="{{ asset('assets/fcadmin/js/vendor/datepicker.fr.js') }}"></script>
@endpush
@push('script')
    <script>
        (function($) {
            "use strict";

            $('.delivery').on('click', function() {
                var modal = $('#deliveryBy');
                modal.find('input[name=code]').val($(this).data('code'));
                modal.find('input[name=quantite_confirme]').val($(this).data('qterecept'));
                modal.modal('show');
            });

            $('.refoule').on('click', function() {
                var modal = $('#refouleBy');
                modal.find('input[name=code]').val($(this).data('code'));
                modal.modal('show');
            });

            $('.date').datepicker({
                maxDate: new Date(),
                range: true,
                multipleDatesSeparator: "-",
                language: 'fr'
            });
        })(jQuery);
    </script>
@endpush
