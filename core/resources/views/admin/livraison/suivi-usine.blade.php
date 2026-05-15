@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card b-radius--10">
                <div class="card-body">
                    <div class="mb-4 text-center">
                        <h3>@lang('Suivi du Connaissement N°') {{ $livraison->numeroCU }}</h3>
                        <p class="text-muted mb-0">{{ __(@$livraison->cooperative->name) }}</p>
                    </div>

                    <form id="suiviForm" method="POST">
                        @csrf
                        <input type="hidden" name="id" value="{{ $id }}">

                        <div class="row gy-4">
                            <div class="col-md-6">
                                <div class="card border">
                                    <div class="card-body">
                                        <h5>@lang('Etape 01 - Pont Bascule')</h5>
                                        <label>@lang('Quantite livree')</label>
                                        <input type="number" name="step1" id="step1" value="{{ @$suivi->step1 }}" class="form-control suivi-field">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card border">
                                    <div class="card-body">
                                        <h5>@lang('Etape 02 - Magasin Brousse')</h5>
                                        <textarea name="step2" id="step2" rows="4" maxlength="500" class="form-control suivi-field">{{ @$suivi->step2 }}</textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card border">
                                    <div class="card-body">
                                        <h5>@lang('Etape 03')</h5>
                                        <textarea name="step3" id="step3" rows="4" maxlength="500" class="form-control suivi-field">{{ @$suivi->step3 }}</textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card border">
                                    <div class="card-body">
                                        <h5>@lang('Etape 04')</h5>
                                        <textarea name="step4" id="step4" rows="4" maxlength="500" class="form-control suivi-field">{{ @$suivi->step4 }}</textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card border">
                                    <div class="card-body">
                                        <h5>@lang('Etape 05')</h5>
                                        <textarea name="step5" id="step5" rows="4" maxlength="500" class="form-control suivi-field">{{ @$suivi->step5 }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <x-back route="{{ route('admin.livraison.usine.connaissement') }}" />
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";

            $('.suivi-field').on('change blur', function() {
                $.ajax({
                    type: 'POST',
                    url: "{{ route('admin.livraison.usine.suivi.store') }}",
                    data: $('#suiviForm').serialize(),
                    success: function(response) {
                        $('#step1').val(response.step1);
                        $('#step2').val(response.step2);
                        $('#step3').val(response.step3);
                        $('#step4').val(response.step4);
                        $('#step5').val(response.step5);
                    }
                });
            });
        })(jQuery);
    </script>
@endpush
