@extends('manager.layouts.master')
@section('content')
    <div class="login-main">
        <div class="container custom-container">
            <div class="row justify-content-center">
                <div class="col-xxl-5 col-xl-5 col-lg-6 col-md-8 col-sm-11">
                    <div class="login-area">
                        <div class="login-wrapper">
                            <div class="login-wrapper__top">
                                <h3 class="title text-white">@lang('Bienvenue sur') <strong>{{ __($general->site_name) }}</strong>
                                </h3>
                                <p class="text-white">{{ __($pageTitle) }} @lang("Tableau de bord")</p>
                            </div>
                            <div class="login-wrapper__body">
                                
                                <form action="{{ route('login') }}" method="POST"
                                    class="cmn-form mt-30 verify-gcaptcha login-form">
                                    @csrf
                                    <div class="form-group">
                                        <label>@lang("Nom d'utilisateur")</label>
                                        <input type="text" class="form-control" value="{{ old('username') }}"
                                            name="username" required>
                                    </div>
                                    <div class="form-group">
                                        <label>@lang("Mot de passe")</label>
                                        <input type="password" class="form-control" name="password" required>
                                    </div>
                                    <x-captcha />
                                    <div class="d-flex flex-wrap justify-content-between">
                                        <div class="form-check me-3">
                                            <input class="form-check-input" name="remember" type="checkbox" id="remember">
                                            <label class="form-check-label" for="remember">@lang("Se souvenir de moi")</label>
                                        </div>
                                        <a href="{{ route('manager.password.request') }}"
                                            class="forget-text">@lang("Mot de passe oublié?")</a>
                                    </div>
                                    <button type="submit" class="btn cmn-btn w-100">@lang("CONNEXION")</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
