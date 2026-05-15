@php
    $counter = getContent('counter.content', true);
    $counters = getContent('counter.element', false, null, true);
@endphp
<div class="counter-section pt-80 pb-80 bg--title-overlay bg_fixed bg_img"
    data-background="{{ getImage('assets/images/frontend/counter/' . @$counter->data_values->background_image, '1920x1080') }}">
    <div class="container">
        <div class="row justify-content-center g-4">
            @foreach ($counters as $value)
                <div class="col-lg-3 col-sm-6">
                    <div class="counter-item">
                        <div class="counter-header">
                            <h3 class="title rafcounter" data-counter-end="{{ $value->data_values->counter_digit }}">
                                {{ $value->data_values->counter_digit }}</h3>
                        </div>
                        <div class="counter-content">
                            {{ __($value->data_values->title) }}
                        </div>
                        <div class="icon">
                            @php echo $value->data_values->counter_icon @endphp
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
