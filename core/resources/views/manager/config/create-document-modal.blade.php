<div class="modal-header">
    <h5 class="modal-title">@lang('Ajouter un Document Administratif')</h5>
    <button type="button" class="close" data-bs-dismiss="modal" aria-hidden="true">×</button>
</div>
<div class="modal-body">
    <div class="portlet-body">
        <x-form id="createDocumentad" method="POST" class="ajax-form">
           
        <div class="row">
        <input type="hidden" value="true" name="page_reload" id="page_reload">
            <div class="col-lg-12">
                <x-forms.text :fieldLabel="__('Ajouter un Document Administratif')"
                    :fieldPlaceholder="__('Document')" fieldName="nom" fieldId="nom"
                    fieldValue="" :fieldRequired="true" />
            </div>
        </div>
        </x-form>
       
    </div>
</div>
<div class="modal-footer">
    <x-forms.button-cancel data-bs-dismiss="modal" class="border-0 mr-3">@lang('app.cancel')</x-forms.button-cancel>
    <x-forms.button-primary id="save-documentad-setting" icon="check">@lang('app.save')</x-forms.button-primary>
</div>


<script>
    $(document).ready(function () {
        setTimeout(function () {
            $('[data-toggle="popover"]').popover();
        }, 500);
    });
  
    $('#save-documentad-setting').click(function() {
        $.easyAjax({
            container: '#createDocumentad',
            type: "POST",
            disableButton: true,
            blockUI: true,
            buttonSelector: "#save-documentad-setting",
            errorPosition: 'inline',
            url: "{{ route('manager.settings.documentad.store') }}",
            data: $('#createDocumentad').serialize(),
            success: function(response) {
                if (response.status == 'success') {
                    if (response.page_reload == 'true') {
                        window.location.reload();
                    } else {
                        $('#documentad_type_id').html(response.data);
                        $(MODAL_XL).modal('hide');
                    }
                }
            }
        })
    });
</script>
