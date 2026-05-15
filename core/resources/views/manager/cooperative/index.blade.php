@extends('manager.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card b-radius--10 ">
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                <th>@lang('Code Coop')</th>
                                <th>@lang('Code Coop App')</th>
                                    <th>@lang('Nom')</th>
                                    <th>@lang('Email')</th>
                                    <th>@lang('Contact')</th>
                                    <th>@lang('Adresse')</th>
                                    <th>@lang('Utilisateurs Mobile')</th>
                                    <th>@lang('Utilisateurs Web')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($cooperatives as $cooperative)
                                    <tr>
                                    <td>
                                            <span>{{ __($cooperative->codeCoop) }}</span>
                                        </td>
                                        <td>
                                            <span>{{ __($cooperative->codeApp) }}</span>
                                        </td>
                                        <td>
                                            <span>{{ __($cooperative->name) }}</span>
                                        </td>
                                        <td>
                                            <span>{{ $cooperative->email }}</span>
                                        </td>
                                        <td>
                                            <span>{{ $cooperative->phone }}</span>
                                        </td>
                                        <td>
                                            <span>{{ __($cooperative->address) }}</span>
                                        </td>
                                        <td>
                                            <span>{{ __($cooperative->mobile) }}</span>
                                        </td>
                                        <td>
                                            <span>{{ __($cooperative->web) }}</span>
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
                @if ($cooperatives->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($cooperatives) }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection


@push('breadcrumb-plugins')
    <x-search-form placeholder="Search here" />
@endpush
