@extends('manager.layouts.app')
@section('panel')
    <div class="row mb-none-30">
        <div class="col-lg-12 mb-30">
            <div class="card">
                <div class="card-body">
                    {!! Form::model($estimation, [
                        'method' => 'POST',
                        'route' => ['manager.traca.estimation.store', $estimation->id],
                        'class' => 'form-horizontal',
                        'id' => 'flocal',
                        'enctype' => 'multipart/form-data',
                    ]) !!}
                    <input type="hidden" name="id" value="{{ $estimation->id }}">

                    <div class="form-group row">
                        <label class="col-sm-4 control-label">@lang('Selectionner une localite')</label>
                        <div class="col-xs-12 col-sm-8">
                            <select class="form-control" name="localite" id="localite" required disabled>
                                <option value="">@lang('Selectionner une option')</option>
                                @foreach ($localites as $localite)
                                    <option value="{{ $localite->id }}" @selected($localite->id == $estimation->parcelle->producteur->localite->id)>
                                        {{ $localite->nom }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-4 control-label">@lang('Selectionner un producteur')</label>
                        <div class="col-xs-12 col-sm-8">
                            <select class="form-control" name="producteur" id="producteur" required disabled>
                                <option value="">@lang('Selectionner une option')</option>
                                @foreach ($producteurs as $producteur)
                                    <option value="{{ $producteur->id }}" data-chained="{{ $producteur->localite->id }}"
                                        @selected($producteur->id == $estimation->parcelle->producteur_id)>
                                        {{ stripslashes($producteur->nom) }} {{ stripslashes($producteur->prenoms) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        {{ Form::label(__('Culture de la estimation'), null, ['class' => 'col-sm-4 control-label required']) }}
                        <div class="col-xs-12 col-sm-8">
                            <input type="text" name="culture" placeholder="Café, Cacao"
                                class="form-control @error('culture') is-invalid @enderror"
                                value="{{ old('culture') ?? $estimation->culture }}" required disabled>
                        </div>
                    </div>
                    <div class="form-group row">
                        <?php echo Form::label(__('Type de déclaration superficie'), null, ['class' => 'col-sm-4 control-label required']); ?>
                        <div class="col-xs-12 col-sm-8">
                            <?php echo Form::select('typedeclaration', ['Verbale' => __('Verbale'), 'GPS' => __('GPS')], null, ['class' => 'form-control typedeclaration', 'id' => 'typedeclaration', 'required','disabled']); ?>
                        </div>
                    </div>
                    <div class="form-group row">
                        {{ Form::label(__('Information GPS de la estimation'), null, ['class' => 'col-sm-4 control-label']) }}
                        <div class="col-xs-12 col-sm-12">
                            <table class="table table-bordered">
                                <tbody id="product_area">

                                    <tr>
                                        <td class="row">

                                            <div class="col-xs-12 col-sm-12">
                                                <div class="form-group row">
                                                    {{ Form::label(__('Superficie'), null, ['class' => 'col-sm-4 control-label required']) }}
                                                    {!! Form::text('superficie', null, [
                                                        'placeholder' => __('Superficie'),
                                                        'class' => 'form-control superficie',
                                                        'id' => 'superficie-1',
                                                        'required','disabled'
                                                    ]) !!}

                                                </div>
                                            </div>

                                            <div class="col-xs-12 col-sm-12">
                                                <div class="form-group row">
                                                    {{ Form::label(__('Latitude'), null, ['class' => 'col-sm-4 control-label']) }}
                                                    {!! Form::text('latitude', null, [
                                                        'placeholder' => __('Latitude'),
                                                        'class' => 'form-control',
                                                        'id' => 'latitude-1','disabled'
                                                    ]) !!}

                                                </div>
                                            </div>
                                            <div class="col-xs-12 col-sm-12">
                                                <div class="form-group row">
                                                    {{ Form::label(__('Longitude'), null, ['class' => 'col-sm-4 control-label']) }}
                                                    {!! Form::text('longitude', null, [
                                                        'placeholder' => __('Longitude'),
                                                        'class' => 'form-control',
                                                        'id' => 'longitude-1','disabled'
                                                    ]) !!}
                                                </div>
                                            </div>
                                            <div class="col-xs-12 col-sm-12">
                                                <div class="form-group row">
                                                    {{ Form::label(__('Année de création'), null, ['class' => 'col-sm-4 control-label required']) }}
                                                    {!! Form::number('anneeCreation', old('anneeCreation') ?? $estimation->anneeCreation, [
                                                        'placeholder' => __('Année de création'),
                                                        'class' => 'form-control text4',
                                                        'id' => 'anneeCreation-1',
                                                        'required',
                                                        'min' => 1990,
                                                        'max' => gmdate('Y') - 5,
                                                        'disabled'
                                                    ]) !!}
                                                </div>
                                            </div>

                                        </td>
                                    </tr>

                                </tbody>

                            </table>
                        </div>
                    </div>

                    <hr class="panel-wide">

                    <div class="form-group row">

                        <?php echo Form::label(__('Fichier KML ou GPX existant'), null, ['class' => 'col-sm-4 control-label']); ?>
                        <div class="col-xs-12 col-sm-8">
                            <input type="file" name="fichier_kml_gpx" class="form-control dropify-fr" disabled>
                        </div>
                    </div>
                    <hr class="panel-wide">


                    {{-- <div class="form-group">
                        <button type="submit" class="btn btn--primary btn-block h-45 w-100">@lang('Envoyer')</button>
                    </div> --}}
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <x-back route="{{ route('manager.traca.estimation.index') }}" />
@endpush

@push('script')
    <script type="text/javascript">
        //$('#localite').change(function() {
            //$("#producteur").chained("#localite");
        //});
    </script>
@endpush
