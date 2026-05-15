@extends('manager.layouts.app')
@section('panel')
    <div class="row mb-none-30">
        <div class="col-lg-12 mb-30">
            <div class="card">
                <div class="card-body">
                    {!! Form::open([
                        'route' => ['manager.agro.evaluation.store'],
                        'method' => 'POST',
                        'class' => 'form-horizontal',
                        'id' => 'flocal',
                        'enctype' => 'multipart/form-data',
                    ]) !!}

                    <div class="form-group row">
                        <label class="col-sm-4 control-label">@lang('Localite')</label>
                        <div class="col-xs-12 col-sm-8">
                            <select class="form-control" name="localite" id="localite" required>
                                <option value="">@lang('Selectionner une option')</option>
                                @foreach ($localites as $localite)
                                    <option value="{{ $localite->id }}" @selected(old('localite'))>
                                        {{ $localite->nom }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-4 control-label">@lang('Producteur')</label>
                        <div class="col-xs-12 col-sm-8">
                            <select class="form-control" name="producteur" id="producteur" required>
                                <option value="">@lang('Selectionner une option')</option>
                                @foreach ($producteurs as $producteur)
                                    <option value="{{ $producteur->id }}" data-chained="{{ $producteur->localite->id }}"
                                        @selected(old('producteur'))>
                                        {{ stripslashes($producteur->nom) }} {{ stripslashes($producteur->prenoms) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <hr class="panel-wide">
                    <div class="form-group row">
                        {{ Form::label(__("Espèce D'arbres"), null, ['class' => 'col-sm-4 control-label']) }}
                        <div class="col-xs-12 col-sm-8">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Variété</th>
                                        <th>Strate</th>
                                        <th>Quantité</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($especesarbres as $data)
                                        <tr>
                                            <td>
                                                {!! Form::hidden('especesarbre[]', $data->id, []) !!}
                                                {{ $data->nom }}
                                            </td>
                                            <td>
                                                strate {{ $data->strate }}
                                            </td>
                                            <td>
                                                {!! Form::number('quantite[]', null, ['placeholder' => __('Qté'), 'class' => 'form-control', 'min' => '0']) !!}

                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>

                            </table>
                        </div>
                    </div>
                    <hr class="panel-wide">

                    <div class="form-group row">
                        <button type="submit" class="btn btn--primary w-100 h-45"> @lang('ENREGISTRER')</button>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <x-back route="{{ route('manager.agro.evaluation.index') }}" />
@endpush
@push('style')
    <style type="text/css">
        input:not([type="radio"]),
        textarea {
            padding: 0px;
        }
    </style>
@endpush
@push('script')
    <script type="text/javascript">
        function getSuperficie() {
            let superficie = $("#parcelle").find(':selected').data('superficie');
            $('#superficie').val(superficie);
        }
        $("#parcelle").chained("#producteur");
        $("#producteur").chained("#localite");

        $('#EA1,#EA2,#EA3,#EB1,#EB2,#EB3,#EC1,#EC2,#EC3').keyup(function() {
            var EA1 = $('#EA1').val();
            var EA2 = $('#EA2').val();
            var EA3 = $('#EA3').val();
            var EB1 = $('#EB1').val();
            var EB2 = $('#EB2').val();
            var EB3 = $('#EB3').val();
            var EC1 = $('#EC1').val();
            var EC2 = $('#EC2').val();
            var EC3 = $('#EC3').val();
            var coefV1 = 1;
            var coefV2 = 0.6;
            var coefV3 = 0.2;
            var supT = $('#superficie').val();

            if (EA1 && EB1 && EC1) {
                $('#T1').val(parseInt(EA1) + parseInt(EB1) + parseInt(EC1));
            }
            if (EA2 && EB2 && EC2) {
                $('#T2').val(parseInt(EA2) + parseInt(EB2) + parseInt(EC2));
            }
            if (EA3 && EB3 && EC3) {
                $('#T3').val(parseInt(EA3) + parseInt(EB3) + parseInt(EC3));
            }

            if ($('#T1').val() && $('#T2').val() && $('#T3').val()) {
                var T1 = parseFloat($('#T1').val()) * 1;
                var T2 = parseFloat($('#T2').val()) * 0.6;
                var T3 = parseFloat($('#T3').val()) * 0.2;
                $('#V1').val(T1.toFixed(2));
                $('#V2').val(T2.toFixed(2));
                $('#V3').val(T3.toFixed(2));
            }
            if ($('#V1').val() && $('#V2').val() && $('#V3').val()) {
                var V1 = parseFloat($('#V1').val()) / 3;
                var V2 = parseFloat($('#V2').val()) / 3;
                var V3 = parseFloat($('#V3').val()) / 3;
                $('#VM1').val(V1.toFixed(2));
                $('#VM2').val(V2.toFixed(2));
                $('#VM3').val(V3.toFixed(2));
            }
            if ($('#VM1').val() && $('#VM2').val() && $('#VM3').val()) {
                var VM1 = parseFloat($('#VM1').val());
                var VM2 = parseFloat($('#VM2').val());
                var VM3 = parseFloat($('#VM3').val());
                var VT = parseFloat(VM1) + parseFloat(VM2) + parseFloat(VM3);
                $('#Q').val(VT.toFixed(2));
            }

            if ($('#Q').val()) {
                var Q = parseFloat($('#Q').val()) * 25;
                $('#RF').val(Q.toFixed(2));
            }
            if ($('#RF').val()) {
                var RF = parseFloat($('#RF').val()) * supT;
                $('#EsP').val(RF.toFixed(2));
            }

        });
    </script>
@endpush
<style>
    #myTable td {
        font-size: 0.8125rem;
        color: #5b6e88;
        font-weight: 500;
        padding: 15px 25px;
        vertical-align: middle;
        border: 1px solid #f4f4f4;
    }
</style>
