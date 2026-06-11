@extends('manager.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">

            {{-- Filtres --}}
            <div class="card b-radius--10 mb-3">
                <div class="card-body">
                    <form action="">
                        <div class="d-flex flex-wrap gap-4">
                            <div class="flex-grow-1">
                                <label>@lang('N° Connaissement')</label>
                                <input type="text" name="search" value="{{ request()->search }}" class="form-control" placeholder="Rechercher...">
                            </div>
                            <div class="flex-grow-1">
                                <label>@lang('Magasin Central')</label>
                                <select name="magasin" class="form-control">
                                    <option value="">@lang('Tous')</option>
                                    @foreach ($magasins as $mag)
                                        <option value="{{ $mag->id }}" {{ request()->magasin == $mag->id ? 'selected' : '' }}>
                                            {{ $mag->nom }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="flex-grow-1">
                                <label>@lang('Campagne')</label>
                                <select name="campagne" class="form-control">
                                    <option value="">@lang('Toutes')</option>
                                    @foreach ($allcampagnes as $campagne)
                                        <option value="{{ $campagne->id }}" {{ request()->campagne == $campagne->id ? 'selected' : '' }}>
                                            {{ $campagne->nom }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="flex-grow-1">
                                <label>@lang('Type de produit')</label>
                                <select class="form-control" name="produit">
                                    <option value="">@lang('Tous')</option>
                                    <option value="Certifie" @selected(request()->produit == 'Certifie')>@lang('Certifie')</option>
                                    <option value="Ordinaire" @selected(request()->produit == 'Ordinaire')>@lang('Ordinaire')</option>
                                </select>
                            </div>
                            <div class="flex-grow-1">
                                <label>@lang('Date')</label>
                                <input name="date" type="text" class="form-control dates"
                                    placeholder="@lang('Date début - Date fin')" autocomplete="off" value="{{ request()->date }}">
                            </div>
                            <div class="flex-grow-1 align-self-end">
                                <button class="btn btn--primary w-100 h-45">
                                    <i class="fas fa-filter"></i> @lang('Filtrer')
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Cartes de totaux --}}
            <div class="row mb-3">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body d-flex align-items-center gap-3">
                            <div class="flex-shrink-0">
                                <span class="btn btn--primary btn-lg"><i class="las la-boxes"></i></span>
                            </div>
                            <div>
                                <p class="text-muted mb-0 small">@lang('Stock entrant (page)')</p>
                                <h5 class="mb-0 fw-bold">{{ number_format($totalEntrant, 2) }} kg</h5>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body d-flex align-items-center gap-3">
                            <div class="flex-shrink-0">
                                <span class="btn btn--warning btn-lg"><i class="las la-truck"></i></span>
                            </div>
                            <div>
                                <p class="text-muted mb-0 small">@lang('Stock sortant (page)')</p>
                                <h5 class="mb-0 fw-bold">{{ number_format($totalSortant, 2) }} kg</h5>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body d-flex align-items-center gap-3">
                            <div class="flex-shrink-0">
                                <span class="btn btn--success btn-lg"><i class="las la-balance-scale"></i></span>
                            </div>
                            <div>
                                <p class="text-muted mb-0 small">@lang('Disponible (page)')</p>
                                <h5 class="mb-0 fw-bold">{{ number_format(max(0, $totalEntrant - $totalSortant), 2) }} kg</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tableau --}}
            <div class="card b-radius--10">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('N° Connaissement')</th>
                                    <th>@lang('Campagne / Période')</th>
                                    <th>@lang('Magasin Central')</th>
                                    <th>@lang('Magasin Section')</th>
                                    <th>@lang('Producteur')</th>
                                    <th>@lang('Type / Certificat')</th>
                                    <th class="text-end">@lang('Entrant (kg)')</th>
                                    <th class="text-end">@lang('Sortant (kg)')</th>
                                    <th class="text-end">@lang('Disponible (kg)')</th>
                                    <th>@lang('Date livraison')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($livraisonProd as $prod)
                                    @php
                                        $disponible = max(0, (float)$prod->quantite - (float)$prod->quantite_sortant);
                                        $stock      = $prod->stockMagasinCentral;
                                    @endphp
                                    <tr>
                                        <td>
                                            <span class="fw-bold text--primary">{{ $stock->numero_connaissement ?? '—' }}</span>
                                        </td>
                                        <td>
                                            <span class="d-block">{{ $prod->campagne->nom ?? '—' }}</span>
                                            <small class="text-muted">{{ $prod->campagnePeriode->nom ?? '—' }}</small>
                                        </td>
                                        <td>
                                            <span class="fw-bold">{{ $stock->magasinCentral->nom ?? '—' }}</span>
                                        </td>
                                        <td>
                                            {{ $stock->magasinSection->nom ?? '—' }}
                                        </td>
                                        <td>
                                            <span class="fw-bold">{{ $prod->producteur->nom ?? '—' }} {{ $prod->producteur->prenoms ?? '' }}</span>
                                            @if($prod->parcelle)
                                                <br><small class="text-muted">{{ $prod->parcelle->codeParc }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge badge--primary">{{ $prod->type_produit }}</span>
                                            @if($prod->certificat)
                                                <br><small class="text-muted">{{ $prod->certificat }}</small>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            {{ number_format((float)$prod->quantite, 2) }}
                                        </td>
                                        <td class="text-end">
                                            {{ number_format((float)$prod->quantite_sortant, 2) }}
                                        </td>
                                        <td class="text-end">
                                            @if($disponible > 0)
                                                <span class="text--success fw-bold">{{ number_format($disponible, 2) }}</span>
                                            @else
                                                <span class="text-muted">0.00</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="d-block">{{ showDateTime($stock->estimate_date, 'd/m/Y') }}</span>
                                            <small class="text-muted">{{ diffForHumans($stock->estimate_date) }}</small>
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
    <a href="{{ route('manager.livraison.exportExcel.magcentralAll') }}" class="btn btn-outline--warning h-45">
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
        (function ($) {
            "use strict";
            $('.dates').datepicker({
                maxDate: new Date(),
                range: true,
                multipleDatesSeparator: "-",
                language: 'fr'
            });
            $('form select').on('change', function () {
                $(this).closest('form').submit();
            });
        })(jQuery);
    </script>
@endpush
