@extends('manager.layouts.app')


@section('panel')
    
<div class="content-wrapper">
    @include($view ?? 'manager.employees.ajax.create')
</div>

@endsection
