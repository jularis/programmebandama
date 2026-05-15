@extends('manager.layouts.master')

@section('content')
    <!-- page-wrapper start -->
    <div class="page-wrapper default-version">
        @include('manager.partials.sidenav')
        @include('manager.partials.topnav')
        @yield('filter-section')
        <div class="body-wrapper">
            <div class="bodywrapper__inner"> 
                @include('manager.partials.breadcrumb') 
                @yield('panel')
            </div><!-- bodywrapper__inner end -->
        </div><!-- body-wrapper end -->
    </div>
@endsection
