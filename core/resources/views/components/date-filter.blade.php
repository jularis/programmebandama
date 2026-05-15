@props(['placeholder'=>'Search...'])
<form action="" method="GET">
    <div class="input-group">
        <input name="date" type="text" data-range="true" data-multiple-dates-separator=" - " data-language="en" class="datepicker-here form-control bg--white" data-position='bottom right' placeholder="{{ __($placeholder) }}" autocomplete="off" value="{{ request()->date }}">
        <button class="btn btn--primary input-group-text" type="submit"><i class="fa fa-search"></i></button>
    </div>
</form>

@push('style-lib')
    <link rel="stylesheet" href="{{ asset('assets/fcadmin/css/vendor/datepicker.min.css') }}">
@endpush
 
@push('script')
<script src="{{ asset('assets/fcadmin/js/vendor/datepicker.min.js') }}"></script>
    <script src="{{ asset('assets/fcadmin/js/vendor/datepicker.en.js') }}"></script>
    <script>
        (function($) {
            "use strict";
            if (!$('.datepicker-here').val()) {
                $('.datepicker-here').datepicker();
            }
        })(jQuery)
    </script>
@endpush
