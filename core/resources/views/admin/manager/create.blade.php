@extends('admin.layouts.app')
@section('panel')
    <div class="row mb-none-30">
        <div class="col-lg-12 mb-30">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.cooperative.manager.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="form-group col-lg-6">
                                <label>@lang('Select Cooperative')</label>
                                <select class="form-control" name="cooperative" required>
                                    <option value="">@lang('Selectionner une option')</option>
                                    @foreach ($cooperatives as $cooperative)
                                        <option value="{{ $cooperative->id }}" @selected(old('cooperative'))>
                                            {{ __($cooperative->name) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-lg-6">
                                <label for="rolePermission" class="form-label">Role</label>
                                <select class="form-control" name="role" required> 
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->id }}">{{ $role->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row"> 
                            <div class="form-group col-lg-6">
                                <label>@lang('Nom de famille')</label>
                                <input type="text" class="form-control" name="lastname" value="{{ old('lastname') }}"
                                    required>
                            </div>
                            <div class="form-group col-lg-6">
                                <label>@lang('Prenom(s)')</label>
                                <input type="text" class="form-control" name="firstname" value="{{ old('firstname') }}"
                                    required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-lg-6">
                                <label>@lang('Email Adresse')</label>
                                <input type="email" class="form-control" name="email" value="{{ old('email') }}"
                                    required>
                            </div>

                            <div class="form-group col-lg-6">
                                <label>@lang('Contact')</label>
                                <input type="text" class="form-control" name="mobile" value="{{ old('mobile') }}"
                                    required>
                            </div>
                        </div>
                        <div class="row">
                        <div class="form-group col-lg-4">
                                <label>@lang("Nom d'utilisateur")</label>
                                <input type="text" class="form-control" name="username" value="{{ old('username') }}"
                                    required>
                            </div>
                            <div class="form-group col-lg-4">
                                <label>@lang("Mot de passe")</label>
                                <input type="password" class="form-control" name="password" required>
                            </div>

                            <div class="form-group col-lg-4">
                                <label>@lang('Confirm Password')</label>
                                <input type="password" class="form-control" name="password_confirmation" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn--primary w-100 h-45"> @lang('Envoyer')</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <x-back route="{{ route('admin.cooperative.manager.index') }}" />
@endpush
