<div class="modal-header">
    <h5 class="modal-title">@lang('modules.attendance.shift')</h5>
    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i> </button>
</div>

<div class="modal-body">
    @include('manager.attendance-settings.ajax.shift')
</div>
<div class="modal-footer">
    <x-forms.button-cancel data-dismiss="modal" class="border-0 mr-3">@lang('app.cancel')</x-forms.button-cancel>
</div>
