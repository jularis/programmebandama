@php
    $cooperative = getContent('cooperative.content', true);
    $cooperatives = App\Models\Cooperative::active()
        ->orderBy('name')
        ->get();
@endphp
<div class="contact-brances position-relative pt-120 pb-120 bg--title-overlay bg_img bg_fixed"
    data-background="{{ getImage('assets/images/frontend/cooperative/' . @$cooperative->data_values->background_image, '1920x1080') }}">
    <div class="container position-relative">
        <div class="row gy-5 align-items-center">
            <div class="col-lg-12">
                <div class="brances-slider-wrapper ps-xl-4">

                    <div class="section__header section__header__center text--white">
                        <span class="section__cate">{{ __(@$cooperative->data_values->title) }}</span>
                        <h3 class="section__title">{{ __(@$cooperative->data_values->heading) }}</h3>
                        <p>
                            {{ __(@$cooperative->data_values->sub_heading) }}
                        </p>
                    </div>
                    <div class="brances-slider owl-theme owl-carousel">
                        @foreach ($cooperatives as $value)
                            <div class="brance__item">
                                <h6 class="title">{{ __($value->name) }}</h6>
                                <ul class="footer__widget-contact">
                                    <li>
                                        <i class="las la-map-marker"></i> {{ __($value->address) }}
                                    </li>
                                    <li>
                                        <i class="las la-mobile"></i> @lang('Mobile'): {{ $value->phone }}
                                    </li>
                                    <li>
                                        <i class="las la-envelope"></i> {{ $value->email }}
                                    </li>
                                </ul>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
