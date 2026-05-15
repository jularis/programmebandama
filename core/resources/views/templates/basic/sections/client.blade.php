
@php
    $client = getContent('client.content', true);
    $clients = getContent('client.element',null,false,true);
@endphp
 <section class="client-section client-section bg--title-overlay bg_fixed bg_img pt-120 pb-120" data-background="{{getImage('assets/images/frontend/client/'. @$client->data_values->background_image, '1920x1280')}}">
     <div class="container">
         <div class="client-slider">
             <div class="sync1 owl-theme owl-carousel">
                @foreach($clients as $value)
                    <div class="client__content">
                        <p>
                           {{__($value->data_values->testimonial)}}
                        </p>
                        <div class="ratings">
                            @if(is_int((int)$value->data_values->rating))
                            @for ($i=1; $i <=$value->data_values->rating;$i++)
                            <span>
                                <i class="las la-star"></i>
                             </span>
                            @endfor
                            @endif
                        </div>
                        <h5 class="title text--white">{{__($value->data_values->name)}}</h5>
                        <span class="designation">{{__($value->data_values->designation)}}</span>
                    </div>
                @endforeach
             </div>
             <div class="sync2 owl-theme owl-carousel">
               @foreach($clients as $value)
                 <div class="client__thumb">
                     <img src="{{getImage('assets/images/frontend/client/'. $value->data_values->client_image, '120x120')}}" alt="@lang('client')">
                 </div>
               @endforeach
             </div>
         </div>
     </div>
 </section>

