@php
    $contactInfo = getContent('contactInfo.content', true);
@endphp
<header>
    <div class="header-top d-none d-md-block">
        <div class="container">
            <div class="header-top-wrapper">
                <ul class="header-contact-info">
                    <li>
                        <a href="Mailto:{{ @$contactInfo->data_values->email }}"><i class="las la-envelope"></i>
                            {{ @$contactInfo->data_values->email }}</a>
                    </li>
                    <li>
                        <a href="Tel:{{ @$contactInfo->data_values->mobile }}">
                            <i class="las la-phone"></i>{{ @$contactInfo->data_values->mobile }}
                        </a>
                    </li>
                </ul>
                @if ($general->ln)
                    <select class="lang-select ms-auto me-4 langChanage">
                        @foreach ($language as $item)
                            <option value="{{ $item->code }}" @if (session('lang') == $item->code) selected @endif>
                                {{ __($item->name) }}</option>
                        @endforeach
                    </select>
                @endif
                <div class="right-area d-none d-md-block">
                    <a href="{{ route('order.tracking') }}" class="cmn--btn btn--sm text-white me-3">
                        @lang('Order Tracking')
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="header-bottom">
        <div class="container">
            <div class="header__wrapper">
                <div class="logo" style="background: #FFFFFF;">
                    <a href="{{ route('home') }}">
                        <img src="{{ getImage(getFilePath('logoIcon') . '/logo.png') }}" alt="@lang('logo')">
                    </a>
                </div>
                <div class="header-bar ms-auto d-lg-none">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
                <div class="menu-area align-items-center ">
                    <div class="d-lg-none cross--btn">
                        <i class="las la-times"></i>
                    </div>
                    <div class="right-area d-md-none text-center mb-4">
                        @if($general->ln)
                        <select class="lang-select ms-auto m-3 langChanage">
                            @foreach ($language as $item)
                            <option value="{{ $item->code }}" @if (session('lang') == $item->code) selected @endif>
                                {{ __($item->name) }}</option>
                                @endforeach
                            </select>
                        @endif

                        <a href="{{ route('order.tracking') }}" class="cmn--btn btn--sm me-3">@lang('Order Tracking')</a>
                        <ul class="header-contact-info">
                            <li>
                                <a href="Mailto:{{ @$contactInfo->data_values->email }}">
                                    <i class="las la-envelope"></i> {{ __(@$contactInfo->data_values->email) }}
                                </a>
                            </li>
                            <li>
                                <a href="Tel:{{ @$contactInfo->data_values->mobile }}">
                                    <i class="las la-phone"></i>{{ __(@$contactInfo->data_values->mobile) }}
                                </a>
                            </li>
                        </ul>
                    </div>
                    <ul class="menu">
                        <li><a href="{{ route('pages', ['/']) }}">@lang('Home')</a></li>
                        @foreach ($pages as $data)
                            <li><a href="{{ route('pages', [$data->slug]) }}">{{ __($data->name) }}</a></li>
                        @endforeach
                        <li><a href="{{ route('pages', ['blog']) }}">@lang('Blog')</a></li>
                        <li><a href="{{ route('pages', ['contact']) }}">@lang('Contact')</a></li>

                    </ul>
                </div>
            </div>
        </div>
    </div>
</header>
