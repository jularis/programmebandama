@extends('manager.layouts.app')


@section('panel')
    
<div class="content-wrapper">
    @include('manager.employees.ajax.create')
</div>

@endsection