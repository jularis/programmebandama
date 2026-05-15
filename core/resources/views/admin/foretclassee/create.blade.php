@extends('admin.layouts.app')
@section('panel')
    <div class="row mb-none-30">
        <div class="col-lg-12 mb-30">
            <div class="card">
                <div class="card-body"> 
                    {!! Form::open(array('route' => ['admin.foretclassee.store'],'method'=>'POST','class'=>'form-horizontal', 'id'=>'flocal', 'enctype'=>'multipart/form-data')) !!} 

    <div class="form-group row">

                     <?php echo Form::label(__('Charger un Fichier KML'), null, ['class' => 'col-sm-4 control-label']); ?>
                     <div class="col-xs-12 col-sm-8">
                           <input type="file" name="fichier_kml" class="form-control">
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
    <x-back route="{{ route('admin.foretclassee.index') }}" />
@endpush
 