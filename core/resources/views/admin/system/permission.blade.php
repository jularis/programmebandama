@extends('admin.layouts.app')
@section('panel')
    <div class="row justify-content-center">
        <div class="col-xl-8">
            <div class="card b-radius--10 ">
                <div class="card-body p-0">
                    <ul class="list-group">
                         
                    </ul>
                </div>
                <div class="py-2 px-3">
                    <a href="{{ route('admin.system.permission.routes') }}" class="btn btn--primary w-100 h-45">@lang('Cliquer pour créer les permissions de routes')</a>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('style')
<style>
  .list-group-item span{
    font-size: 22px !important;
    padding: 8px 0px
  }
</style>
@endpush
