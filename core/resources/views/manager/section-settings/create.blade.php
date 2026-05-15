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
                        <label class="col-xs-12 col-sm-4">@lang('Select Cooperative')</label>
                        <div class="col-xs-12 col-sm-8">
                            <select class="form-control" name="cooperative_id" required>
                                @foreach ($cooperatives as $cooperative)
                                    <option value="{{ $cooperative->id }}" @selected(old('cooperative'))>
                                        {{ __($cooperative->name) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <?php echo Form::label(__('Nom de la section'), null, ['class' => 'control-label col-xs-12 col-sm-4']); ?>
                        <div class="col-xs-12 col-sm-8">
                            <?php echo Form::text('libelle', null, ['placeholder' => __('Nom de la section'), 'class' => 'form-control', 'required']); ?>
                        </div>
                    </div>
                    <div class="form-group row">
                        <?php echo Form::label(__('Région'), null, ['class' => 'control-label col-xs-12 col-sm-4']); ?>
                        <div class="col-xs-12 col-sm-8">
                            <?php echo Form::text('region', null, ['placeholder' => __('Région'), 'class' => 'form-control']); ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <?php echo Form::label(__('Sous Préfecture'), null, ['class' => 'control-label col-xs-12 col-sm-4']); ?>
                        <div class="col-xs-12 col-sm-8">
                            <?php echo Form::text('sousPrefecture', null, ['placeholder' => __('Sous préfecture'), 'class' => 'form-control']); ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <button type="submit" id="save-form" class="btn btn--primary w-100 h-45">
                            @lang('app.save')</button>
                    </div>

                </div>
            </div>
    </x-setting-card>
@endsection

@push('breadcrumb-plugins')
    <x-back route="{{ route('manager.settings.section-settings.index') }}" />
@endpush

@push('script')
    <script type="text/javascript">
        $('#save-form').click(function() {
            var url = "{{ route('manager.settings.section-settings.store') }}";

            $.easyAjax({
                url: url,
                container: '#editSettings',
                type: "POST",
                disableButton: true,
                blockUI: true,
                redirect: true,
                buttonSelector: "#save-form",
                data: $('#editSettings').serialize(),
                success: function(response) {
                    if (response.status == 'success') {
                        window.location.href = response.redirectUrl;
                    }
                }
            })
        });
    </script>
@endpush
