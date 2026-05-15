<div class="modal-header">
    <h5 class="modal-title">{{ $pageTitle }}</h5>
    <button type="button" class="close" data-bs-dismiss="modal" aria-hidden="true">×</button>
</div>
<div class="modal-body">
    <div class="portlet-body">
        <x-form id="createFormateur" method="POST" class="ajax-form">

            <div class="row">
                <div class="col-lg-12">
                    <label for="entreprise_id" class="control-label">@lang('Entreprise')</label>
                    <select class="form-control" name="entreprise_id" id="entreprise_id" required>
                        <option value="">@lang('Choisir une entreprise')</option>
                        @foreach ($entreprises as $entreprise)
                            <option value="{{ $entreprise->id }}">{{ $entreprise->nom_entreprise }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="row">
                <input type="hidden" value="true" name="page_reload" id="page_reload">
                <div class="col-lg-12">
                    <x-forms.text :fieldLabel="__('Nom du formateur')" :fieldPlaceholder="__('Nom du formateur')" fieldName="nom_formateur" fieldId="nom_formateur"
                        fieldValue="" :fieldRequired="true" />
                </div>
            </div>
            <div class="row">
                <input type="hidden" value="true" name="page_reload" id="page_reload">
                <div class="col-lg-12">
                    <x-forms.text :fieldLabel="__('Prenoms du formateur')" :fieldPlaceholder="__('Prenom du formateur')" fieldName="prenom_formateur"
                        fieldId="prenom_formateur" fieldValue="" :fieldRequired="true" />
                </div>
            </div>
            <div class="row">
                <input type="hidden" value="true" name="page_reload" id="page_reload">
                <div class="col-lg-12">
                    <x-forms.number :fieldLabel="__('Téléphone du formateur')" :fieldPlaceholder="__('Téléphone du formateur')" fieldName="telephone_formateur"
                        fieldId="telephone_formateur" minValue="10" maxValue="10" fieldValue="" :fieldRequired="true" />
                </div>
            </div>
            <div class="row">
                <input type="hidden" value="true" name="page_reload" id="page_reload">
                <div class="col-lg-12">
                    <x-forms.text :fieldLabel="__('Domaine de compétence')" :fieldPlaceholder="__('Domaine de compétence')" fieldName="poste_formateur"
                        fieldId="poste_formateur" fieldValue="" :fieldRequired="true" />
                </div>
            </div>
        </x-form>

    </div>
</div>
<div class="modal-footer">
    <x-forms.button-cancel data-bs-dismiss="modal" class="border-0 mr-3">@lang('app.cancel')</x-forms.button-cancel>
    <x-forms.button-primary id="save-formateur-staff" icon="check">@lang('app.save')</x-forms.button-primary>
</div>


<script>
    $(document).ready(function() {
        setTimeout(function() {
            $('[data-toggle="popover"]').popover();
        }, 500);
    });

    $('#save-formateur-staff').click(function() {
        $.easyAjax({
            container: '#createFormateur',
            type: "POST",
            disableButton: true,
            blockUI: true,
            buttonSelector: "#save-formateur-staff",
            errorPosition: 'inline',
            url: "{{ route('manager.settings.formateurStaff.store') }}",
            data: $('#createFormateur').serialize(),
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
