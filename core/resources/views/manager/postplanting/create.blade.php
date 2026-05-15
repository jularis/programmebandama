@extends('manager.layouts.app')
@section('panel')
    <div class="row mb-none-30">
        <div class="col-lg-12 mb-30">
            <div class="card">
                <div class="card-body">
                    {!! Form::open([
                        'route' => ['manager.agro.postplanting.store'],
                        'method' => 'POST',
                        'class' => 'form-horizontal',
                        'id' => 'flocal',
                        'enctype' => 'multipart/form-data',
                        'style' => 'margin-bottom:200px;',
                    ]) !!}

                    <div class="form-group row">
                        <label class="col-sm-4 control-label">@lang('Selectionner une section')</label>
                        <div class="col-xs-12 col-sm-8">
                            <select class="form-control" name="section" id="section" required>
                                <option value="">@lang('Selectionner une option')</option>
                                @foreach ($sections as $section)
                                    <option value="{{ $section->id }}" @selected(old('section'))>
                                        {{ $section->libelle }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-4 control-label">@lang('Selectionner une localite')</label>
                        <div class="col-xs-12 col-sm-8">
                            <select class="form-control" name="localite" id="localite" required>
                                <option value="">@lang('Selectionner une option')</option>
                                @foreach ($localites as $localite)
                                    <option value="{{ $localite->id }}" data-chained="{{ $localite->section_id }}"
                                        @selected(old('localite'))>
                                        {{ $localite->nom }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-4 control-label">@lang('Selectionner un producteur')</label>
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

                    <div class="form-group">
                        <?php echo Form::label(__('ESPECES D\'ARBRE'), null, ['class' => 'col-sm-12 control-label', 'style' => 'font-weight:bold;font-size:20px;']); ?>
                        <div class="col-xs-12 col-sm-12">
                            <table class="table table-striped table-bordered">
                                <tbody id="listeespece" style="text-align: center;">

                                </tbody>
                            </table>
                        </div>
                    </div>
                    <hr class="panel-wide">
                    <!-- <div style="
    position: fixed;
    bottom: 0px;
    left: 270px;
    width: 78%;
    overflow: hidden;
    background: #e9ecef;
"> -->
                    <div class="form-group row">
                        <?php echo Form::label(__('QUANTITE RECUE'), null, ['class' => 'col-sm-4 control-label', 'style' => 'font-weight:bold;font-size:20px;']); ?>
                        <div class="col-xs-12 col-sm-8">
                            <input type="number" name="total" id="total" class="form-control" readonly
                                style="font-weight:bold; font-size:20px;" />
                        </div>
                    </div>
                    <div class="form-group row">
                        <?php echo Form::label(__('QUANTITE PLANTEE'), null, ['class' => 'col-sm-4 control-label required', 'style' => 'font-weight:bold;font-size:20px;']); ?>
                        <div class="col-xs-12 col-sm-8">
                            <input type="number" name="qteplante" id="qteplante" class="form-control" readonly
                                style="color:#FF0000; font-weight:bold; font-size:20px;" />
                        </div>
                    </div>
                    <div class="form-group row">
                        <?php echo Form::label(__('QUANTITE SURVECU'), null, ['class' => 'col-sm-4 control-label required', 'style' => 'font-weight:bold;font-size:20px;']); ?>
                        <div class="col-xs-12 col-sm-8">
                            <input type="number" name="qtesurvecue" id="qtesurvecue" class="form-control" readonly
                                style="color:#FF0000; font-weight:bold; font-size:20px;" />
                        </div>
                    </div>
                    <div class="form-group row">
                        {{ Form::label(__('Date'), null, ['class' => 'col-sm-4 control-label required']) }}
                        <div class="col-xs-12 col-sm-8">
                            <?php echo Form::date('date_planting', null, ['class' => 'form-control date_planting required']); ?>
                        </div>
                    </div>
                    <hr class="panel-wide">

                    <div class="form-group row">
                        <button type="submit" class="btn btn--primary w-100 h-45"> @lang('Envoyer')</button>
                    </div>

                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <x-back route="{{ route('manager.agro.postplanting.index') }}" />
@endpush

@push('script')
    <script type="text/javascript">
        $('#producteur').change(function() {

            var urlsend = '{{ route('manager.agro.postplanting.getAgroParcellesArbres') }}';

            $.ajax({
                type: 'GET',
                url: urlsend,
                data: $('#flocal').serialize(),
                success: function(html) {
                    $('#listeespece').html(html.tableau);
                    $("#total").val(html.total);
                    $("#qteplante").val(html.total);
                    $("#qtesurvecue").val(html.total);
                }

            });
        });

        $('#flocal').change('keyup change blur', function() {
            var total = $('#total').val();
        });

        function getQuantite(id, k, s) {
            update_amounts(id, k, s);
        }

        function update_amounts(id, k, s) {
            let total = $('#total').val();
            var sum = 0;
            let qteplante = parseInt($('#qteplante').val());
            let max = $('.quantity-' + id).attr('max');

            let quantite = 0;

            // update Quantite Survecue
            var qteCurrent = $("#qte-" + k).val();
            $("#qte2-" + k).val(qteCurrent);
            $("#qte2" + k).attr({
                "max": qteCurrent,
                "min": 0
            });
            update_survecue(id, k, s);

            $('.quantity-' + id).each(function() {
                var qty = $(this).val();
                quantite = parseInt(quantite) + parseInt(qty);
                //  if(quantite>max){
                //     $('#qte-'+k).val(0); 
                //     } 
            });

            $('.totaux').each(function() {
                var nb = $(this).val();
                // update Quantite survecue 
                sum = parseInt(sum) + parseInt(nb);
            });

            if (sum > total) {
                $('#qte-' + k).val(0);
                $('.totaux').each(function() {
                    var nb = $(this).val();
                    sum = parseInt(sum) + parseInt(nb);
                });
            } else {
                $('#qteplante').val(sum);
            }
            for (let i = 1; i < 6; i++) {
                var soustotal = 0;

                $('.st-' + i).each(function() {
                    var nb = $(this).val();
                    soustotal = parseInt(soustotal) + parseInt(nb);

                });
                $('#soustotal-' + i).val(soustotal);

            }

            $("#qteplante").attr({
                "max": total,
                "min": 0
            });
        }

        function getQuantite2(id, k, s) {
            update_survecue(id, k, s);
        }

        function update_survecue(id, k, s) {
            let total = $('#qtesurvecue').val();
            var sum = 0;
            let qtesurvecue = parseInt($('#qtesurvecue').val());
            let max = $('.quantity2-' + id).attr('max');

            let quantite = 0;
            $('.quantity2-' + id).each(function() {
                var qty = $(this).val();
                quantite = parseInt(quantite) + parseInt(qty);
                //  if(quantite>max){
                //     $('#qte-'+k).val(0); 
                //     } 
            });

            $('.totaux2').each(function() {
                var nb = $(this).val();
                sum = parseInt(sum) + parseInt(nb);
            });

            if (sum > total) {
                $('#qte-' + k).val(0);
                $('.totaux2').each(function() {
                    var nb = $(this).val();
                    sum = parseInt(sum) + parseInt(nb);
                });
            } else {
                $('#qtesurvecue').val(sum);
            }


            $("#qtesurvecue").attr({
                "max": total,
                "min": 0
            });
        }
        $("#localite").chained("#section");
        $("#producteur").chained("#localite");
    </script>
@endpush
