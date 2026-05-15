@extends('manager.layouts.app')
@section('panel')
    <div class="row mb-none-30">
        <div class="col-lg-12 mb-30">
            <div class="card">
                <div class="card-body">
                    {!! Form::open([
                        'route' => ['manager.traca.parcelle.uploadkml'],
                        'method' => 'POST',
                        'class' => 'form-horizontal',
                        'id' => 'flocal',
                        'enctype' => 'multipart/form-data',
                    ]) !!}
                    <div class="form-group row">

                        <?php echo Form::label(__('Charger un Fichier KML'), null, ['class' => 'col-sm-4 control-label']); ?>
                        <div class="col-xs-12 col-sm-8">
                            <p>Fichier d'exemple à utiliser :<a href="{{ asset('assets/Nomenclature_kml.kml') }}"
                                target="_blank">@lang('Nomenclature_kml.kml')</a></p>
                                <p></p>
                                <br><br>
                            <input type="file" name="fichier_kml" class="form-control dropify">
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
    <x-back route="{{ route('manager.traca.parcelle.index') }}" />
@endpush

@push('script')
    <script type="text/javascript">
        $("#localite").chained("#section");
        $("#producteur_id").chained("#localite");
    </script>

@endpush
