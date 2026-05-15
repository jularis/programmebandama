@extends('admin.layouts.app')
@section('panel')
    <div class="row mb-none-30">
        <div class="col-lg-12 col-md-12 mb-30">
            <div class="card">
                <div class="card-body">
                    <h1>{{ ucfirst($role->name) }} Role</h1>
                    <div class="lead">
                        
                    </div>
                    <div class="container mt-4">
                        <h3>Permissions attribuées</h3>
                        <table class="table table-striped">
                            <thead>
                                <th scope="col" width="20%">Nom Permission</th>
                                <th scope="col" width="1%">Guard</th> 
                            </thead>
                                @forelse($rolePermissions as $permission)
                                    <tr>
                                        <td>{{ $permission->name }}</td>
                                        <td>{{ $permission->guard_name }}</td>
                                    </tr>
                                @empty
                                        <tr>
                                            <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                        </tr>
                                @endforelse
                        </table>
                        <a class="btn btn-outline--primary mt-4 w-100 h-45" href="{{ route('admin.roles.edit', $role->id) }}">Modifier</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('breadcrumb-plugins')
    <x-back route="{{ route('admin.roles.index') }}" />
@endpush