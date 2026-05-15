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
                                <th>@lang('Manage-Staff')</th>
                                <th>@lang('Login at')</th>
                                <th>@lang('IP')</th>
                                <th>@lang('Location')</th>
                                <th>@lang('Browser | OS')</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($loginLogs as $log)
                                <tr>
                                    <td>
                                        <span class="fw-bold d-block">{{ @$log->user->fullname }}</span>
                                        @if (@$log->user->user_type=='manager')
                                            <span class="small d-block"> <a href="{{ route('admin.cooperative.manager.edit',@$log->user->id) }}"><span>@</span>{{ @$log->user->username }}</a> </span>
                                        @else
                                            <span class="small d-block"> <a href="{{ route('admin.staff.index')."?search=".@$log->user->username}}"><span>@</span>{{ @$log->user->username }}</a> </span>
                                        @endif
                                        <span class="d-block">{{ __(ucfirst(@$log->user->user_type)) }}</span>
                                    </td>
                                    <td>
                                        {{showDateTime($log->created_at) }} <br> {{diffForHumans($log->created_at) }}
                                    </td>
                                    <td>
                                        <span class="fw-bold">
                                        <a href="{{route('admin.report.login.ipHistory',[$log->user_ip])}}">{{ $log->user_ip }}</a>
                                        </span>
                                    </td>
                                    <td>{{ __($log->city) }} <br> {{ __($log->country) }}</td>
                                    <td>{{ __($log->browser) }} <br> {{ __($log->os) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                </tr>
                            @endforelse

                            </tbody>
                        </table><!-- table end -->
                    </div>
                </div>
                @if ($loginLogs->hasPages())
                <div class="card-footer py-4">
                    {{ paginateLinks($loginLogs) }}
                </div>
                @endif
            </div><!-- card end -->
        </div>


    </div>
@endsection



@push('breadcrumb-plugins')
    @if(request()->routeIs('admin.report.login.history'))

        <x-search-form placeholder="Enter Username"/>

    @endif
@endpush
@if(request()->routeIs('admin.report.login.ipHistory'))
    @push('breadcrumb-plugins')
    <div class="d-flex flex-wrap justify-content-end gap-2 align-items-center">
        <a href="https://www.ip2location.com/{{ $ip }}" target="_blank" class="btn btn--primary">@lang('Lookup IP') {{ $ip }}</a>
    </div>
    @endpush
@endif
