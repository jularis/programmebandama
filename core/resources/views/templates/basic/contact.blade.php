@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <div class="contact-section pt-120 pb-120">
        <div class="container">
            <div class="row align-items-center justify-content-between">
                <div class="col-lg-6 d-none d-lg-block rtl pe-xxl-50">
                    <img src="{{ getImage('assets/images/frontend/contact_us/' . @$contact->data_values->contact_image, '655x615') }}"
                        alt="@lang('contact')">
                </div>
                <div class="col-lg-6">
                    <div class="section__header">
                        <span class="section__cate">{{ __(@$contact->data_values->title) }}</span>
                        <h3 class="section__title">{{ __(@$contact->data_values->heading) }}</h3>
                        <p>
                            {{ __(@$contact->data_values->sub_heading) }}
                        </p>
                    </div>
                    <form class="contact-form" action="" method="POST" class="verify-gcaptcha">
                        @csrf
                        <div class="form-group mb-3">
                            <label>@lang('Your Nom')</label>
                            <input type="text" class="form-control form--control" name="name"
                                value="{{ old('name') }}" required="">
                        </div>
                        <div class="form-group mb-3">
                            <label>@lang('Email Adresse')</label>
                            <input type="text" class="form-control form--control" name="email"
                                value="{{ old('email') }}" required="">
                        </div>
                        <div class="form-group mb-3">
                            <label>@lang('Sujet')</label>
                            <input type="text" class="form-control form--control" name="subject"
                                value="{{ old('subject') }}" required="">
                        </div>
                        <div class="form-group mb-3">
                            <label>@lang('Your Message')</label>
                            <textarea name="message" class="form-control form--control" name="message" required="">{{ old('message') }}</textarea>
                        </div>
                        <x-captcha />

                        <div class="form-group mt-2">
                            <button class="cmn--btn btn--lg rounded" type="submit">@lang('Send Message')</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
