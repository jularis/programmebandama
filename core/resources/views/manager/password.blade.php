@extends('manager.layouts.app')
@section('panel')

    <div class="row mb-none-30">
        <div class="col-lg-3 col-md-3 mb-30">

            <div class="card b-radius--5 overflow-hidden">
                <div class="card-body p-0">
                    <div class="d-flex p-3 bg--primary">
                        <div class="avatar avatar--lg">
                            <img src="{{ getImage(getFilePath('userProfile').'/'. $user->image,getFileSize('userProfile'))}}" alt="@lang('Image')">
                        </div>
                        <div class="ps-3">
                            <h4 class="text--white">{{__($user->fullname)}}</h4>
                        </div>
                    </div>
                    <ul class="list-group">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Nom')
                            <span class="fw-bold">{{ __($user->fullname) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang("Nom d'utilisateur")
                            <span  class="fw-bold">{{ __($user->username) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Email')
                            <span  class="fw-bold">{{ $user->email }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-lg-9 col-md-9 mb-30">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-50 border-bottom pb-2">@lang('Modification du mot de passe')</h5>

                    <form action="{{ route('manager.password.update.data') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="form-group">
                            <label>@lang("Mot de passe")</label>
                            <input class="form-control" type="password" name="old_password" required>
                        </div>

                        <div class="form-group">
                            <label>@lang('Nouveau mot de passe')</label>
                            <input class="form-control" type="password" name="password" required>
                        </div>

                        <div class="form-group">
                            <label>@lang('Confirmer le mot de passe')</label>
                            <input class="form-control" type="password" name="password_confirmation" required>
                        </div>
                        <button type="submit" class="btn btn--primary w-100 btn-lg h-45">@lang('Envoyer')</button>
                    </form>
                </div>
            </div>
        </div>
    </div>


@endsection

@push('breadcrumb-plugins')
<div class="d-flex flex-wrap justify-content-end gap-2 align-items-center">
    <a href="{{route('manager.profile')}}" class="btn btn-sm btn-outline--primary" ><i class="las la-user"></i>@lang('Profile Setting')</a>
</div>
@endpush
