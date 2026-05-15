@extends('manager.layouts.app')
@section('panel')
    <div class="row mb-none-30">
        <div class="col-lg-12 mb-30">
            <div class="card">
                <div class="card-body">
                    {!! Form::model($communauteSociale, [
                        'method' => 'POST',
                        'route' => ['manager.communaute.activite.communautaire.store', $communauteSociale->id],
                        'class' => 'form-horizontal',
                        'id' => 'flocal',
                        'enctype' => 'multipart/form-data',
                    ]) !!}
                    <input type="hidden" name="id" value="{{ $communauteSociale->id }}">
                    <div class="form-group row">
                        <label class="col-sm-4 control-label" for="localite_projet">Localité:</label>
                        <div class="col-xs-12 col-sm-8">
                            <select class="form-control select2-multi-select" name="localite[]" id="localite" required
                                multiple>
                                <option value="">@lang('Selectionner une option')</option>
                                @foreach ($localites as $localite)
                                    <option value="{{ $localite->id }}" @selected(in_array($localite->id, $dataLocalite))>
                                        {{ $localite->nom }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <?php echo Form::label(__('Bénéficiaires Membres'), null, ['class' => 'col-sm-4 control-label']); ?>
                        <div class="col-xs-12 col-sm-8">
                            <select class="form-control select2-multi-select" name="producteur[]" id="producteur" multiple
                                required>
                                <option value="">@lang('Selectionner une option')</option>
                                @foreach ($producteurs as $producteur)
                                    <option value="{{ $producteur->id }}"
                                        data-chained="{{ $producteur->localite_id ?? '' }}" @selected(old('producteur'))>
                                        {{ stripslashes($producteur->nom) }} {{ stripslashes($producteur->prenoms) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 control-label" for="titre_projet">Titre du projet:</label>
                        <div class="col-xs-12 col-sm-8">
                            <input type="text" value= "{{ $communauteSociale->titre_projet }}" id="titre_projet"
                                class="form-control" name="titre_projet" required>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-4 control-label" for="description_projet">Description du Projet:</label>
                        <div class="col-xs-12 col-sm-8">
                            <textarea id="description_projet" class="form-control" name="description_projet" rows="4" required>{{ $communauteSociale->description_projet }}</textarea>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-4 control-label" for="type_projet">Type de projet:</label>
                        <div class="col-xs-12 col-sm-8">
                            <select id="type_projet" class="form-control" name="type_projet" required>
                                <option value="Autonomisation des femmes"
                                    {{ old('type_projet') == 'Autonomisation des femmes' ? 'selected' : '' }}>Autonomisation
                                    des femmes</option>
                                <option value="Nutrition familiale"
                                    {{ old('type_projet') == 'Nutrition familiale' ? 'selected' : '' }}>Nutrition familiale
                                </option>
                                <option value="Diversification des sources de revenus"
                                    {{ old('type_projet') == 'Diversification des sources de revenus' ? 'selected' : '' }}>
                                    Diversification des sources de
                                    revenus</option>
                                <option value="Education familiale"
                                    {{ old('type_projet') == 'Education familiale' ? 'selected' : '' }}>Education familiale
                                </option>
                                <option value="Santé" {{ old('type_projet') == 'Santé' ? 'selected' : '' }}>Santé</option>
                                <option value="Education financière"
                                    {{ old('type_projet') == 'Education financière' ? 'selected' : '' }}>Education
                                    financière</option>
                            </select>
                        </div>
                    </div>
                  
                    <div class="form-group row">
                        <label class="col-sm-4 control-label" for="beneficiaires_projet">Bénéficiaires du Projet:</label>
                        <div class="col-xs-12 col-sm-8">
                            <select id="beneficiaires_projet" class="form-control" name="beneficiaires_projet" required>
                                <option value="Membres" {{ old('beneficiaires_projet') == 'Membres' ? 'selected' : '' }}>
                                    Membres</option>
                                <option value="Non - Membres"
                                    {{ old('beneficiaires_projet') == 'Non - Membres' ? 'selected' : '' }}>Non - Membres
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-4 control-label" for="niveau_realisation">Niveau de réalisation:</label>
                        <div class="col-xs-12 col-sm-8">
                            <select id="niveau_realisation" class="form-control" name="niveau_realisation">
                                <option value="Non démarré"
                                    {{ old('niveau_realisation') == 'Non démarré' ? 'selected' : '' }}>Non démarré</option>
                                <option value="En Cours" {{ old('niveau_realisation') == 'En Cours' ? 'selected' : '' }}>En
                                    Cours</option>
                                <option value="Achevé" {{ old('niveau_realisation') == 'Achevé' ? 'selected' : '' }}>Achevé
                                </option>
                            </select>
                        </div>
                    </div>


                    <div id="date_demarrage_container" style="display:none;">
                        <div class="form-group row">
                            <label class="col-sm-4 control-label" for="date_demarrage">Date de démarrage du projet:</label>
                            <div class="col-xs-12 col-sm-8">
                                <input type="date" id="date_demarrage" class="form-control" name="date_demarrage"
                                    value="{{ $communauteSociale->date_demarrage }}">
                            </div>
                        </div>
                    </div>

                    <div id="date_fin_projet_container" style="display:none;">
                        <div class="form-group row">
                            <label class="col-sm-4 control-label" for="date_fin_projet">Date de fin du projet:</label>
                            <div class="col-xs-12 col-sm-8">
                                <input type="date" id="date_fin_projet" class="form-control" name="date_fin_projet"
                                    value="{{ $communauteSociale->date_fin_projet }}">
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-4 control-label" for="cout_projet">Coûts du projet:</label>
                        <div class="col-xs-12 col-sm-8">
                            <input type="text" id="cout_projet" value="{{ $communauteSociale->cout_projet }}"
                                class="form-control" name="cout_projet" required>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-4 control-label" for="date_livraison">Date de la livraison:</label>
                        <div class="col-xs-12 col-sm-8">
                            <input type="date" id="date_livraison" value="{{ $communauteSociale->date_livraison }}"
                                class="form-control" name="date_livraison">
                        </div>
                    </div>

                    @php
                        $photos = json_decode($communauteSociale->photos);
                        $a = 1;
                    @endphp
                    <label class="col-sm-4 control-label" for="photos">Photos:</label>
                    @if ($photos)
                        <div class="form-group row">
                            @foreach ($photos as $photo)
                                <div class="col-xs-12 col-sm-4"></div>
                                <div class="col-xs-12 col-sm-8 mt-3">
                                    <input type="file" id="photos{{ $a }}" class="form-control dropify-fr"
                                        name="photos[{{ $a }}]" accept="image/*" multiple=""
                                        class="dropify" data-height="70"
                                        data-default-file="{{ asset('core/storage/app/' . $photo) }}"
                                        data-allowed-file-extensions="jpg jpeg png">
                                </div>
                                <div id="insertBefore"></div>
                                @php
                                    $a++;
                                @endphp
                            @endforeach
                            {{-- <div id="insertBefore"></div>
                            <div class="row px-lg-4 px-md-4 px-3 pb-3 pt-0 mb-3  mt-2">
                                <div class="col-md-12">
                                    <a class="f-15 f-w-500" href="javascript:;" id="add-item"><i
                                            class="icons icon-plus font-weight-bold mr-1"></i> @lang('app.add')</a>
                                </div>
                            </div> --}}
                        </div>
                    @endif
                    @php
                        $documents_joints = json_decode($communauteSociale->documents_joints);
                        $b = 1;
                    @endphp
                    <label class="col-sm-4 control-label" for="documents_joints">Documents joints:</label>
                    @if ($documents_joints)
                        <div class="form-group row">
                            @foreach ($documents_joints as $document_joint)
                                <div class="col-xs-12 col-sm-4"></div>
                                <div class="col-xs-12 col-sm-8 mt-3">
                                    <input type="file" id="documents_joints{{ $b }}"
                                        class="form-control dropify-fr" name="documents_joints[{{ $b }}]"
                                        accept="application/pdf" multiple="" class="dropify" data-height="70"
                                        data-default-file="{{ asset('core/storage/app/' . $document_joint) }}"
                                        data-allowed-file-extensions="pdf">
                                </div>
                                <div id="insertBeforeNew"></div>
                                @php
                                    $b++;
                                @endphp
                            @endforeach
                        </div>
                    @endif

                    <div class="form-group row">
                        <label class="col-sm-4 control-label" for="commentaires">Commentaires:</label>
                        <div class="col-xs-12 col-sm-8">
                            <textarea id="commentaires" class="form-control" name="commentaires" rows="4"> {{ $communauteSociale->commentaires }} </textarea>
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
    <x-back route="{{ route('manager.communaute.activite.communautaire.index') }}" />
@endpush

@push('script')
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/daterangepicker.css') }}">
    <script src="{{ asset('assets/vendor/jquery/daterangepicker.min.js') }}"></script>
    <script type="text/javascript">
        document.getElementById('niveau_realisation').addEventListener('change', function() {
            var dateFinProjetContainer = document.getElementById('date_fin_projet_container');
            if (this.value === 'En Cours' || this.value === 'Achevé') {
                dateFinProjetContainer.style.display = 'block';
            } else {
                dateFinProjetContainer.style.display = 'none';
            }
        });

        $(document).ready(function() {
            var producteursSelected = "{{ implode(',', $producteursSelected) }}";
            //idée de ce bloque de code c'est de remplire l'objet optionParproducteur avec les producteurs provenant de la base de données
            var optionParProducteur = new Object();
            $("#producteur option").each(function() {
                var curreentArray = optionParProducteur[($(this).data('chained'))] ? optionParProducteur[($(
                        this)
                    .data('chained'))] : [];
                curreentArray[$(this).val()] = $(this).text().trim();
                Object.assign(optionParProducteur, {
                    [$(this).data('chained')]: curreentArray
                });

                if (producteursSelected.split(',').includes($(this).val()) && producteursSelected != "") {
                    $(this).val($(this).data('chained') + "-" + $(this).val());
                    $(this).attr('selected', 'selected');
                } else $(this).remove();
            });
            console.log(optionParProducteur);

            $('#localite').change(function() {
                var localite = $(this).val();
                $("#producteur").empty();
                var optionsHtml2 = "";
                $(this).find('option:selected').each(function() {
                    //console.log($(this).val());
                    optionsHtml2 = updateproducteur(optionsHtml2, $(this).val(),
                        optionParProducteur);
                })
            });
        });

        function updateproducteur(optionsHtml2, id, optionParProducteur) {
            var optionsHtml = optionsHtml2
            if (id != '' && optionParProducteur[id]) {
                optionParProducteur[id].forEach(function(key, element) {
                    optionsHtml += '<option value="' + id + '-' + element + '">' + key + '</option>';
                });
                $("#producteur").html(optionsHtml);
            }
            return optionsHtml;
        }
    </script>
@endpush
