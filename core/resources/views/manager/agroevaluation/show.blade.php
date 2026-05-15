@extends('manager.layouts.app')
@section('panel')
    <div class="row mb-none-30">
        <div class="col-lg-12 mb-30">
            <div class="card">
                <div class="card-body">
                    {!! Form::model($evaluation, [
                        'method' => 'POST',
                        'route' => ['manager.agro.evaluation.store', $evaluation->id],
                        'class' => 'form-horizontal',
                        'id' => 'flocal',
                        'enctype' => 'multipart/form-data',
                    ]) !!}
                    <input type="hidden" name="id" value="{{ $evaluation->id }}">
                    <input type="hidden" name="producteur" value="{{ $evaluation->producteur_id }}">
                    <input type="hidden" name="localite" value="{{ $evaluation->producteur->localite->id }}">
                    <div class="form-group row">
                        <label class="col-sm-4 control-label">@lang('Localité')</label>
                        <div class="col-xs-12 col-sm-8">

                            {!! Form::text('localites', $producteurs->localite->nom, [
                                'placeholder' => __('Localite'),
                                'class' => 'form-control',
                                'readonly',
                            ]) !!}
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-4 control-label">@lang('Producteur')</label>
                        <div class="col-xs-12 col-sm-8">
                            {!! Form::text('producteurs', $producteurs->nom . ' ' . $producteurs->prenoms, [
                                'placeholder' => __('Producteur'),
                                'class' => 'form-control',
                                'readonly',
                            ]) !!}

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
                                        <?php
                                        if (in_array($data->id, $dataEspece)) {
                                            $qte = $dataQuantite[$data->id];
                                        } else {
                                            $qte = 0;
                                        }
                                        ?>
                                        <tr>
                                            <td>
                                                {!! Form::hidden('especesarbre[]', $data->id, []) !!}
                                                {{ $data->nom }}
                                            </td>
                                            <td>
                                                strate {{ $data->strate }}
                                            </td>
                                            <td>
                                                {!! Form::number('quantite[]', $qte, ['placeholder' => __('Qté'), 'class' => 'form-control', 'min' => '0','disabled']) !!}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>

                            </table>
                        </div>
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

@push('script')
    <script type="text/javascript">
        $('#localite').change(function() {
            $("#producteur").chained("#localite");
        });
    </script>
@endpush
