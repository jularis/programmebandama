<!-- meta tags and other links -->
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ $general->siteName($pageTitle ?? '404 | Page non trouvée') }}</title>
  <link rel="shortcut icon" type="image/png" href="{{getImage(getFilePath('logoIcon') .'/favicon.png')}}">
  <!-- bootstrap 4  -->
  <link rel="stylesheet" href="{{ asset('assets/global/css/bootstrap.min.css') }}">
  <!-- dashdoard main css -->
  <link rel="stylesheet" href="{{ asset('assets/errors/css/main.css') }}">
</head>
  <body>


  <!-- error-404 start -->
  <div class="error">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-7 text-center">
          <img src="{{ asset('assets/errors/images/error-404.png') }}" alt="@lang('image')">
          <h2><b>@lang('404')</b> @lang('Page non trouvée')</h2>
          <p>@lang('La page que vous recherchez ne sort pas ou une autre erreur s\'est produite') <br> @lang('or temporarily unavailable.')</p>
          <a href="{{ route('home') }}" class="cmn-btn mt-4">@lang('Aller à l\'Accueil')</a>
        </div>
      </div>
    </div>
  </div>
  <!-- error-404 end -->


  </body>
</html>
