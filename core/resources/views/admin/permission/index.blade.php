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
                                <th>@lang('Permission')</th>
                                <th>@lang('Guard Name')</th>
                                <th>@lang('Action')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($permissions as $permission)
                                <tr>
                                    <td>
                                        <span>{{ $permission->name }}</span>
                                    </td>
                                    <td>
                                        <span>{{ $permission->guard_name }}</span>
                                    </td>
                                
                                    <td>
                                   
                                        <a href="{{ route('admin.permissions.edit',$permission->id) }}"
                                            class="btn btn-sm btn-outline--primary"><i
                                                class="las la-pen"></i>@lang('Edit')
                                        </a>
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
            @if ($permissions->hasPages())
                <div class="card-footer py-4">
                    {{ paginateLinks($permissions) }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
@push('breadcrumb-plugins')
    <x-search-form placeholder="Search here..." />
    <a href="{{route('admin.permissions.create')}}" class="btn  btn-outline--primary h-45 addNewRole">
        <i class="las la-plus"></i>@lang("Ajouter nouveau")
    </a>
    <i></i>
@endpush
@push('style')
    <style>
        .table-responsive {
            overflow-x: auto;
        }
    </style>
@endpush