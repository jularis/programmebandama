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
            <div class="card b-radius--10 ">
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">

                        <input type="hidden" name="id" value="{{ $cooperative->id }}">
                        <input type="hidden" name="codeApp" value="{{ $cooperative->codeApp }}">

                        <div class="modal-body">
                            <div class="form-group">
                                <label>@lang('Statut juridique')</label>
                                <select name="statut_juridique" class="form-control">
                                    <option value="">@lang('Tous')</option>
                                    <option value="SCOOPS"
                                        {{ $cooperative->statut_juridique == 'SCOOPS' ? 'selected' : '' }}>@lang('SCOOPS')</option>
                                    <option value="COOP CA"
                                        {{ $cooperative->statut_juridique == 'COOP CA' ? 'selected' : '' }}>@lang('COOP CA')</option>

                                </select>
                            </div>
                            <div class="form-group">
                                <label>@lang('Nom coop')</label>
                                <input type="text" class="form-control" name="name" value="{{ $cooperative->name }}"
                                    readonly required>
                            </div>

                            <div class="form-group">
                                <label>@lang('Sigle coop')</label>
                                <input type="text" class="form-control" name="codeCoop"
                                    value="{{ $cooperative->codeCoop }}" required>
                            </div>
                            <div class="form-group">
                                <label>@lang('N°RSC')</label>
                                <input type="text" class="form-control" name="numRSC" value="{{ $cooperative->numRSC }}"
                                    required>
                            </div>
                            <div class="form-group">
                                <label>@lang('Compte Contribuable ')</label>
                                <input type="text" class="form-control" name="numCompteContribuable"
                                    value="{{ $cooperative->numCompteContribuable }}" required>
                            </div> 
                            <div class="form-group">
                                <label>@lang('Secteur activite')</label>
                                <select name="secteurActivite" class="form-control">
                                    <option value="">@lang('Selectionner une option')</option>
                                    <option value="collecte"
                                        {{ $cooperative->secteurActivite == 'collecte' ? 'selected' : '' }}>@lang('Collecte')
                                    </option>
                                    <option value="Achat vente"
                                        {{ $cooperative->secteurActivite == 'Achat vente' ? 'selected' : '' }}>@lang('Achat vente')
                                    </option>
                                    <select>
                            </div>
                            <div class="form-group">
                                <label>@lang('Historique de la coopérative')</label>
                                <textarea type="text" class="form-control editor" value="{{ $cooperative->historique }}"
                                    name="historique" required>{{ $cooperative->historique }}</textarea>
                            </div>
                            <div class="form-group">
                                <label>@lang('Mission')</label>
                                <textarea type="text" class="form-control editor" value="{{ $cooperative->mission }}"
                                    name="mission" required>{{ $cooperative->mission }}</textarea>
                            </div>
                            <div class="form-group">
                                <label>@lang('Vision')</label>
                                <textarea type="text" class="form-control editor" value="{{ $cooperative->vision }}"
                                    name="vision" required>{{ $cooperative->vision }}</textarea>
                            </div>

                            <div class="form-group">
                                <label>@lang('Adresse Email coop')</label>
                                <input type="email" class="form-control" value="{{ $cooperative->email }}" name="email"
                                    required>
                            </div>

                            <div class="form-group">
                                <label>@lang('Contacts')</label>
                                <input type="text" class="form-control" value="{{ $cooperative->phone }}" name="phone"
                                    required>
                            </div>

                            <div class="form-group">
                                <label>@lang('Adresse postale de la coopérative')</label>
                                <input type="text" class="form-control" name="postal"
                                    value="{{ $cooperative->postal }}" required>
                            </div>

                            <div class="form-group">
                                <label>@lang('Adresse de la coopérative')</label>
                                <input type="text" class="form-control" name="address"
                                    value="{{ $cooperative->address }}" required>
                            </div>

                            <div class="form-group">
                                <label>@lang('Région')</label>
                                <input type="text" class="form-control" name="region"
                                    value="{{ $cooperative->region }}" required>
                            </div>
                            <div class="form-group">
                                <label>@lang('Departement')</label>
                                <input type="text" class="form-control" name="departement"
                                    value="{{ $cooperative->departement }}" required>
                            </div>
                            <div class="form-group">
                                <label>@lang('Ville')</label>
                                <input type="text" class="form-control" name="ville"
                                    value="{{ $cooperative->ville }}" required>
                            </div>
                            <hr class="panel-wide">
                            <div class="form-group">
                                <label>@lang('Année de creation')</label>
                                <input type="number" class="form-control years" min="1960"
                                    max="{{ gmdate('Y') }}" name="annee_creation"
                                    value="{{ $cooperative->annee_creation }}">
                            </div>
                            <div class="form-group">
                                <label>@lang("Date d'uniformisation OHADA")</label>
                                <input type="date" class="form-control" name="dateOHADA"
                                    value="{{ $cooperative->dateOHADA }}">
                            </div>

                            <div class="form-group">
                                <label>@lang('Code CCC')</label>
                                <input type="text" class="form-control phone " name="code_ccc"
                                    value="{{ $cooperative->code_ccc }}" required>
                            </div>
                            <div class="form-group">
                                <label>@lang('Nombre sections creation')</label>
                                <input type="number" class="form-control" name="nb_membres_creation"
                                    value="{{ $cooperative->nb_membres_creation }}">
                            </div>
                            <div class="form-group">
                                <label>@lang('Nombre sections creation')</label>
                                <input type="text" class="form-control" name="nb_sections_creation"
                                    value="{{ $cooperative->nb_sections_creation }}">
                            </div>
                            <div class="form-group">
                                <label>@lang('Nombre de membres actuel')</label>
                                <input type="number" class="form-control" name="nb_membres_actuel"
                                    value="{{ $cooperative->nb_membres_actuel }}">
                            </div>
                            <div class="form-group">
                                <label>@lang('Nombre de sections actuel')</label>
                                <input type="text" class="form-control" name="nb_sections_actuel"
                                    value="{{ $cooperative->nb_sections_actuel }}">
                            </div>
                            <div class="form-group">
                                <label>@lang('Nombre de PCA qui se sont succédés depuis la creation')</label>
                                <input type="text" class="form-control" name="nb_pca_creation"
                                    value="{{ $cooperative->nb_pca_creation }}">
                            </div>
                            <hr class="panel-wide">
                            <div class="form-group">
                                <label>@lang('Longitude')</label>
                                <input type="text" class="form-control" name="longitude"
                                    value="{{ $cooperative->longitude }}" required>
                            </div>
                            <div class="form-group">
                                <label>@lang('Latitude')</label>
                                <input type="text" class="form-control" name="latitude"
                                    value="{{ $cooperative->latitude }}" required>
                            </div>
                            <hr class="panel-wide">
                            <div class="form-group">
                                <label>@lang('Utilisateurs Mobile')</label>
                                <input type="number" class="form-control" name="mobile"
                                    value="{{ $cooperative->mobile }}" readonly required>
                            </div>
                            <div class="form-group">
                                <label>@lang('Utilisateurs Web')</label>
                                <input type="number" class="form-control" name="web"
                                    value="{{ $cooperative->web }}" readonly required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" id="save-form"
                                class="btn btn--primary w-100 h-45">@lang('app.save')</button>
                        </div>
                    </div>
                </div>

            </div><!-- card end -->
        </div>
    </x-setting-card>
@endsection

@push('script')
<script src="{{ asset('assets/ckeditor/ckeditor.js') }}"></script>
<script src="{{ asset('assets/ckeditor/adapters/jquery.js') }}"></script>
    <script>
        $('#save-form').click(function() {
            var url = "{{ route('manager.settings.cooperative-settings.update', $cooperative->id) }}";

            $.easyAjax({
                url: url,
                container: '#editSettings',
                type: "POST",
                disableButton: true,
                blockUI: true,
                buttonSelector: "#save-form",
                data: $('#editSettings').serialize(),
            })
        });
        $('body').on('click', '.add-instance', function() {
            var url = "{{ route('manager.settings.instance.index') }}";

            $(MODAL_XL + ' ' + MODAL_HEADING).html('...');
            $.ajaxModal(MODAL_XL, url);
            $(MODAL_XL).modal('show');
        });

        $('body').on('click', '.add-documentad', function() {
            var url = "{{ route('manager.settings.documentad.index') }}";

            $(MODAL_XL + ' ' + MODAL_HEADING).html('...');
            $.ajaxModal(MODAL_XL, url);
            $(MODAL_XL).modal('show');
        });
    </script>
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
 