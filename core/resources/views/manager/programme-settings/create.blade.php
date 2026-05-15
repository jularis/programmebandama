@extends('manager.layouts.app')
@section('panel')
<x-setting-sidebar :activeMenu="$activeSettingMenu" />
    <x-setting-card> 
      
    <x-slot name="header">
                <div class="s-b-n-header" id="tabs">
                    <h2 class="mb-0 p-20 f-21 font-weight-normal text-capitalize border-bottom-grey">
                        @lang($pageTitle)</h2>
                </div>
            </x-slot> 

      <div class="col-lg-12 col-md-12 ntfcn-tab-content-left w-100 p-4 ">
      <div class="card">
        <div class="card-body"> 
          @method('POST')
        <div class="form-group row">
              <?php echo Form::label(__('Nom du programme'), null, ['class' => 'control-label col-xs-12 col-sm-4']); ?>
                <div class="col-xs-12 col-sm-8">
                  <?php echo Form::text('libelle', null, array('placeholder' => __('Nom du programme'),'class' => 'form-control', 'required')); ?>
                </div>
            </div>
            
            <div class="form-group">
              <button type="submit" id="save-form" class="btn btn--primary w-100 h-45"> @lang('app.save')</button>
            </div>
          
      </div>
    </div>   
  </x-setting-card>
@endsection

@push('breadcrumb-plugins')
    <x-back route="{{ route('manager.settings.durabilite-settings.index') }}" />
@endpush

@push('script')
  <script type="text/javascript">
   $('#save-form').click(function () {
            var url = "{{ route('manager.settings.durabilite-settings.store') }}";
             
            $.easyAjax({
                url: url,
                container: '#editSettings',
                type: "POST",
                disableButton: true,
                blockUI: true,
                redirect: true,
                buttonSelector: "#save-form",
                data: $('#editSettings').serialize(),
                success: function (response) {
                    if (response.status == 'success') {
                        window.location.href = response.redirectUrl;
                    }
                }
            })
        });
  </script>
@endpush