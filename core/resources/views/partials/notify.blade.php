<link rel="stylesheet" href="{{ asset('assets/global/css/iziToast.min.css') }}">
<script src="{{ asset('assets/global/js/iziToast.min.js') }}"></script>
@if(session()->has('notify'))
    @foreach(session('notify') as $msg)
        <script>
            "use strict";
            iziToast.{{ $msg[0] }}({timeout: 20000, message:"{{ __($msg[1]) }}", position: "topRight"});
        </script>
    @endforeach
@endif

@if (isset($errors) && $errors->any())
    @php
        $collection = collect($errors->all());
        $errors = $collection->unique();
    @endphp

    <script>
        "use strict";
        @foreach ($errors as $error)
        iziToast.error({
            timeout: 20000,
            message: '{{ __($error) }}',
            position: "topRight"
        });
        @endforeach
    </script>

@endif
<script>
    "use strict";
    function notify(status,message) {
        iziToast[status]({
            timeout: 20000,
            message: message,
            position: "topRight"
        });
    }
</script>

