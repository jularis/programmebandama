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
                                <input type="text" name="search" value="{{ request()->search }}" class="form-control">
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
            <div class="card b-radius--10">
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Livraison')</th>
                                    <th>@lang('Campagne')</th>
                                    <th>@lang('Periode')</th>
                                    <th>@lang('Magasin Central')</th>
                                    <th>@lang('Transporteur')</th>
                                    <th>@lang('Vehicule')</th>
                                    <th>@lang('Type Produit')</th>
                                    <th>@lang('Quantite chargee(Kg)')</th>
                                    <th>@lang('Nombre Sacs')</th>
                                    <th>@lang('Quantite receptionnee(Kg)')</th>
                                    <th>@lang('Statut')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($stocks as $produit)
                                    <tr>
                                        <td>{{ $produit->numeroCU }}</td>
                                        <td>{{ $produit->campagne->nom }}</td>
                                        <td>{{ $produit->campagnePeriode->nom }}</td>
                                        <td>
                                            @if(@$produit->magasin_centraux_id)
                                                <span class="text--primary">{{ __($produit->magasinCentral->nom) }}</span>
                                            @else
                                                <span>@lang('N/A')</span>
                                            @endif
                                        </td>
                                        <td>{{ $produit->transporteur->nom }} {{ $produit->transporteur->prenoms }}</td>
                                        <td>{{ $produit->vehicule->marque->nom }}({{ $produit->vehicule->vehicule_immat }})</td>
                                        <td><span class="btn btn-sm btn-outline--success">{{ $produit->type_produit }}</span></td>
                                        <td>{{ $produit->quantite_livre }}</td>
                                        <td>{{ $produit->sacs_livre }}</td>
                                        <td>{{ $produit->quantite_confirme }}</td>
                                        <td>
                                            @if($produit->status == Status::COURIER_DISPATCH)
                                                <span class="badge badge--dark">@lang('En attente de reception')</span>
                                            @endif
                                            @if($produit->status == Status::COURIER_DELIVERYQUEUE)
                                                <span class="badge badge--success">@lang('Receptionnee')</span>
                                            @endif
                                            @if($produit->status == Status::COURIER_DELIVERED)
                                                <span class="badge badge--danger">@lang('Refoulee')</span><br>
                                                <p>{{ $produit->commentaire }}</p>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('manager.livraison.usine.invoice', encrypt($produit->id)) }}"
                                                class="btn btn-sm btn-outline--info">
                                                <i class="las la-file-invoice"></i> @lang('Details livraisons')
                                            </a>
                                            @if($produit->status == Status::COURIER_DELIVERYQUEUE)
                                                <a href="{{ route('manager.livraison.usine.suivi', encrypt($produit->id)) }}"
                                                    class="btn btn-sm btn-outline--primary">
                                                    <i class="las la-random"></i> @lang('Suivre cette livraison')
                                                </a>
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
@endsection

@push('breadcrumb-plugins')
    <a href="{{ route('manager.livraison.exportExcel.livraisonAll') }}" class="btn btn-outline--warning h-45">
        <i class="las la-cloud-download-alt"></i> @lang('Exporter en Excel')
    </a>
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

            $('.dates').datepicker({
                maxDate: new Date(),
                range: true,
                multipleDatesSeparator: "-",
                language: 'fr'
            });

            $('form select').on('change', function() {
                $(this).closest('form').submit();
            });
        })(jQuery)
    </script>
@endpush
