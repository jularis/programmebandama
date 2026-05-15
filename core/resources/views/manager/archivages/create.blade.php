@extends('manager.layouts.app')
@section('panel')
    <div class="row mb-none-30">
        <div class="col-lg-12 mb-30">
            <div class="card">
                <div class="card-body">
                {!! Form::open(array('route' => ['manager.archivages.store'],'method'=>'POST', 'id'=>'flocal','enctype'=>'multipart/form-data','class'=>'form-horizontal')) !!}
 

    <div class="form-group row">
        <?php echo Form::label(__('Type Archive'), null, ['class' => 'col-sm-4 control-label']); ?>
        <div class="col-xs-12 col-sm-8">
             <?php echo Form::select('type_archive_id', $type_archives, null, array('placeholder' => __('Type Archive'),'class' => 'form-control typearchives', 'id'=>'typearchives','required'=>'required')); ?>
        </div>
    </div>
    

    <hr class="panel-wide">
    <div class="form-group row">
            <?php echo Form::label(__('Titre du document'), null, ['class' => 'col-sm-4 control-label']); ?>
            <div class="col-xs-12 col-sm-8">
            <?php echo Form::text('titre', null,array('placeholder' => __('Titre du document'),'class' => 'form-control titre','required'=>'required')); ?>
        </div>
    </div>
     
<div class="form-group row">
            <?php echo Form::label(__('Résume (500 caractère maximum)'), null, ['class' => 'col-sm-4 control-label']); ?>
            <div class="col-xs-12 col-sm-8">
            <?php echo  Form::textarea('resume', null, ['id' => 'resume', 'rows' => 4, 'cols' => 54, 'style' => 'resize:none','class' => 'form-control resume','maxlength' => 500]); ?>
            <div id="count">
            <span id="current_count">0</span>
            <span id="maximum_count">/ 500</span>
        </div>
        </div>
    </div>
    <hr class="panel-wide">

    @if(request()->has('editor'))
    <a href="{{ route('manager.archivages.create') }}" class="btn btn-success">@lang('Charger un fichier')</a>
 
    <div class="form-group row">
            <?php echo Form::label(__('Contenu'), null, ['class' => 'col-sm-4 control-label']); ?>
            <div class="col-xs-12 col-sm-8">
            <?php echo  Form::textarea('content', null, ['id' => 'content', 'style' => 'resize:none','class' => 'form-control editor']); ?>
        </div>
    </div>
    @else
    <a href="{{ route('manager.archivages.create', ['editor' => 'pdf']) }}" class="btn btn-danger">@lang('Créer directement le PDF')</a>
    <div class="form-group row">
                    <?php echo Form::label(__('Joindre (PDF-Word-Excel-Jpeg) Taille maxi 2Mo par Document'), null, ['class' => 'col-sm-4 control-label']); ?>
                    <div class="col-xs-12 col-sm-8">
                    <input type="file" name="document" value="" class="form-control document dropify-fr">
		        </div>
    @endif

	</div>

    <hr class="panel-wide">

    <div class="col-xs-12 col-sm-8 text-center"><br><br>
        <button type="submit" class="btn btn-primary" id="submit">Enregister</button>
    </div>

{!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <x-back route="{{ route('manager.archivages.index') }}" />
@endpush

@push('script')
<script src="{{ asset('assets/ckeditor/ckeditor.js') }}"></script>
<script src="{{ asset('assets/ckeditor/adapters/jquery.js') }}"></script>
    <script type="text/javascript">
       $('#resume').keyup(function() {
    var characterCount = $(this).val().length,
        current_count = $('#current_count'),
        maximum_count = $('#maximum_count'),
        count = $('#count');
        current_count.text(characterCount);
});
    </script>
     <script>
  $( 'textarea.editor' ).ckeditor( {
    language: 'fr', 
});
  </script>
@endpush
