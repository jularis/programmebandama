<div class="modal-header">
    <h5 class="modal-title">{{ $pageTitle }}</h5>
    <button type="button" class="close" data-bs-dismiss="modal" aria-hidden="true">×</button>
</div>
<div class="modal-body">
    <div class="portlet-body">
        <x-form id="createEntreprise" method="POST" class="ajax-form">
            <div class="row">
                <input type="hidden" value="true" name="page_reload" id="page_reload">
                <div class="col-lg-12">
                    <x-forms.text :fieldLabel="__('Nom de l\'entreprise')" :fieldPlaceholder="__('Nom de l\'entreprise')" fieldName="nom_entreprise"
                        fieldId="nom_entreprise" fieldValue="" :fieldRequired="true" />
                </div>
            </div>

            <div class="row">
                <input type="hidden" value="true" name="page_reload" id="page_reload">
                <div class="col-lg-12">
                    <x-forms.text :fieldLabel="__('Adresse mail de l\'entreprise')" :fieldPlaceholder="__('Adresse mail de l\'entreprise')" fieldName="mail_entreprise"
                        fieldId="mail_entreprise" fieldValue="" :fieldRequired="true" />
                </div>
            </div>

            <div class="row">
                <input type="hidden" value="true" name="page_reload" id="page_reload">
                <div class="col-lg-12">
                    <x-forms.number :fieldLabel="__('Téléphone de l\'entreprise')" :fieldPlaceholder="__('Téléphone de l\'entreprise')" fieldName="telephone_entreprise"
                        fieldId="telephone_entreprise" minValue="10" maxValue="10" fieldValue="" :fieldRequired="true" />
                </div>
            </div>
            <div class="row">
                <input type="hidden" value="true" name="page_reload" id="page_reload">
                <div class="col-lg-12">
                    <x-forms.text :fieldLabel="__('Adresse de l\'entreprise')" :fieldPlaceholder="__('Adresse de l\'entreprise')" fieldName="adresse_entreprise"
                        fieldId="adresse_entreprise" fieldValue="" :fieldRequired="true" />
                </div>
            </div>
        </x-form>

    </div>
</div>
<div class="modal-footer">
    <x-forms.button-cancel data-bs-dismiss="modal" class="border-0 mr-3">@lang('app.cancel')</x-forms.button-cancel>
    <x-forms.button-primary id="save-entreprise" icon="check">@lang('app.save')</x-forms.button-primary>
</div>


<script>
    $(document).ready(function() {
        setTimeout(function() {
            $('[data-toggle="popover"]').popover();
        }, 500);
    });

    $('#save-entreprise').click(function() {
        $.easyAjax({
            container: '#createEntreprise',
            type: "POST",
            disableButton: true,
            blockUI: true,
            buttonSelector: "#save-entreprise",
            errorPosition: 'inline',
            url: "{{ route('manager.settings.entreprise.store') }}",
            data: $('#createEntreprise').serialize(),
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
