@extends('admin.layouts.app')
@section('panel')
<div class="row">
    <div class="col-lg-12">
        <div class="card b-radius--10 ">
            <div class="card-body p-0">
                <div class="table-responsive--sm table-responsive">
                    <table class="table table--light style--two">
                        <tr>
                           <th width="1%">No</th>
                           <th>Name</th>
                           <th width="3%" colspan="3">Action</th>
                        </tr>
                          @foreach ($roles as $key => $role)
                            <tr>
                                <td>{{ $role->id }}</td>
                                <td>{{ $role->name }}</td>
                                <td>
                                    <a class="btn btn-sm btn-outline--info" href="{{ route('admin.roles.show', $role->id) }}">Voir</a>
                                    <a class="btn btn-sm btn-outline--primary" href="{{ route('admin.roles.edit', $role->id) }}">Modifier</a>
                                    {!! Form::open(['method' => 'DELETE','route' => ['admin.roles.destroy', $role->id],'style'=>'display:inline']) !!}
                                    {!! Form::submit('Supprimer', ['class' => 'btn btn-outline-danger btn-sm']) !!}
                                    {!! Form::close() !!}
                                </td>
                            </tr>
                          @endforeach
                      </table>
                   
                </div>
            </div>
            @if ($roles->hasPages())
                <div class="card-footer py-4">
                    {{ paginateLinks($roles) }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
@push('breadcrumb-plugins')
    <x-search-form placeholder="Search here..." />
    <a href="{{route('admin.roles.create')}}" class="btn  btn-outline--primary h-45 addNewRole">
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