@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card b-radius--10 ">
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Cooperative')</th>
                                    <th>@lang('Staff')</th>
                                    <th>@lang('Email')</th>
                                    <th>@lang('Ajouté le')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($staffs as $staff)
                                    <tr>
                                        <td>
                                            <span class="fw-bold">{{ __($staff->cooperative->name) }}</span>
                                        </td>

                                        <td>
                                            <span class="fw-bold">{{ $staff->fullname }}</span>
                                            <br>
                                            <span class="small">
                                                <span>@</span>{{ $staff->username }}
                                            </span>
                                        </td>

                                        <td>
                                            <span class="fw-bold">{{ $staff->email }}<br>{{ $staff->mobile }}</span>
                                        </td>

                                        <td>
                                            {{ showDateTime($staff->created_at) }} <br>
                                            {{ diffForHumans($staff->created_at) }}
                                        </td>

                                        <td>
                                            @php
                                                echo $staff->statusBadge;
                                            @endphp

                                        </td>
                                        <td>
                                            <a href="{{ route('admin.cooperative.localite.staff', $staff->id) }}"
                                                class="btn btn-sm btn-outline--success" target="_blank"><i
                                                    class="las la-sign-in-alt"></i>
                                                @lang('Login')</a>

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
                @if ($staffs->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($staffs) }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <x-search-form placeholder="Search here ..." />
    <x-back route="{{ route('admin.cooperative.localite.index') }}" />
@endpush
