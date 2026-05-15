@extends('manager.layouts.app')
@section('panel')
    <div class="row mb-none-30">
        <div class="col-lg-12 mb-30">
            <div class="card">
                <div class="card-body">
                    {!! Form::open([
                        'route' => ['manager.communaute.activite.communautaire.store'],
                        'method' => 'POST',
                        'class' => 'form-horizontal',
                        'id' => 'flocal',
                        'enctype' => 'multipart/form-data',
                    ]) !!}

                    <div class="form-group row">
                        <label class="col-sm-4 control-label" for="localite_projet">Localité:</label>
                        <div class="col-xs-12 col-sm-8">
                            <select class="form-control select2-multi-select" name="localite[]" id="localite" required
                                multiple>
                                <option value="">@lang('Selectionner une option')</option>
                                @foreach ($localites as $localite)
                                    <option value="{{ $localite->id }}" @selected(old('localite'))>
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
                            <input type="text" id="titre_projet" class="form-control" name="titre_projet" required>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-4 control-label" for="description_projet">Description du Projet:</label>
                        <div class="col-xs-12 col-sm-8">
                            <textarea id="description_projet" class="form-control" name="description_projet" rows="4" required></textarea>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-4 control-label" for="type_projet">Type de projet:</label>
                        <div class="col-xs-12 col-sm-8">
                            <select id="type_projet" class="form-control" name="type_projet" required>
                                <option value="Autonomisation des femmes">Autonomisation des femmes</option>
                                <option value="Nutrition familiale">Nutrition familiale</option>
                                <option value="Diversification des sources de revenus">Diversification des sources de
                                    revenus</option>
                                <option value="Education familiale">Education familiale</option>
                                <option value="Santé">Santé</option>
                                <option value="Education financière">Education financière</option>
                            </select>
                        </div>
                    </div>



                    {{-- <div class="form-group row">
                        <label class="col-sm-4 control-label" for="beneficiaires_projet">Bénéficiaires du Projet:</label>
                        <div class="col-xs-12 col-sm-8">
                            <select id="beneficiaires_projet" class="form-control" name="beneficiaires_projet" required>
                                <option value="Membres">Membres</option>
                                <option value="Non - Membres">Non - Membres</option>
                            </select>
                        </div>
                    </div> --}}



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
                                    value="{{ old('date_demarrage') }}">
                            </div>
                        </div>
                    </div>

                    <div id="date_fin_projet_container" style="display:none;">
                        <div class="form-group row">
                            <label class="col-sm-4 control-label" for="date_fin_projet">Date de fin du projet:</label>
                            <div class="col-xs-12 col-sm-8">
                                <input type="date" id="date_fin_projet" class="form-control" name="date_fin_projet"
                                    value="{{ old('date_fin_projet') }}">
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-4 control-label" for="cout_projet">Coûts du projet (En FCFA):</label>
                        <div class="col-xs-12 col-sm-8">
                            <input type="text" id="cout_projet" class="form-control" name="cout_projet" placeholder="(En FCFA)" required>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-4 control-label" for="date_livraison">Date de la livraison:</label>
                        <div class="col-xs-12 col-sm-8">
                            <input type="date" id="date_livraison" class="form-control" name="date_livraison" required>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-4 control-label" for="photos">Photos:</label>
                        <div class="col-xs-12 col-sm-8">
                            <input type="file" id="photos1" class="form-control dropify-fr" name="photos[]"
                                accept="image/*" multiple="" class="dropify" data-height="70">
                                <div id="insertBefore"></div>
                        </div>
                        
                        <!--  ADD ITEM START-->
                        <div class="row px-lg-4 px-md-4 px-3 pb-3 pt-0 mb-3  mt-2">
                            <div class="col-md-12">
                                <a class="f-15 f-w-500" href="javascript:;" id="add-item"><i
                                        class="icons icon-plus font-weight-bold mr-1"></i> @lang('app.add')</a>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 control-label" for="documents_joints">Documents joints:</label>
                        <div class="col-xs-12 col-sm-8">
                            <input type="file" id="documents_joints1" class="form-control dropify-fr"
                                name="documents_joints[]" multiple="" class="dropify" data-height="70">
                                <div id="insertBeforeNew"></div>
                        </div>

                        
                        <!--  ADD ITEM START-->
                        <div class="row px-lg-4 px-md-4 px-3 pb-3 pt-0 mb-3  mt-2">
                            <div class="col-md-12">
                                <a class="f-15 f-w-500" href="javascript:;" id="add-itemNew"><i
                                        class="icons icon-plus font-weight-bold mr-1"></i> @lang('app.add')</a>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-4 control-label" for="commentaires">Commentaires:</label>
                        <div class="col-xs-12 col-sm-8">
                            <textarea id="commentaires" class="form-control" name="commentaires" rows="4"></textarea>
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
            var dateFinProjet = document.getElementById('date_fin_projet');
            var dateDemarrage = document.getElementById('date_demarrage');
            var dateFinProjetContainer = document.getElementById('date_fin_projet_container');
            var dateDemarrageContainer = document.getElementById('date_demarrage_container');

            if (this.value === 'En Cours' || this.value === 'Achevé') {
                dateFinProjetContainer.style.display = 'block';
                dateDemarrageContainer.style.display = 'block';
                dateFinProjet.required = true; // Rend le champ requis
                dateDemarrage.required = true; // Rend le champ requis
            } else {
                dateFinProjetContainer.style.display = 'none';
                dateDemarrageContainer.style.display = 'none';
                dateFinProjet.required = false; // Rend le champ non requis
                dateDemarrage.required = false; // Rend le champ non requis
            }
        });

        $('.dropify').dropify();
        $('.dropify-fr').dropify();
        // Add More Inputs
        var $insertBefore = $('#insertBefore');
        var $insertBeforeNew = $('#insertBeforeNew');
        var $insertBeforeAutre = $('#insertBeforeAutre');
        var i = 1;
        var a = 1;
        var b = 1;
        $('#add-item').click(function() {
            i += 1;

            $(`<div id="addMoreBox${i}" class="row pl-20 pr-20 clearfix" style="padding-top: 10px;">
            <div class="col-md-11" style="padding:0px;"> 
                            <input name="photos[]" id="photos${i}" type="file" class="dropify" multiple="" data-height="78"/> 
                            </div>
                            <div class="col-md-1" style="padding:0px;">
                            <button type="button"
                                        class="btn btn-outline-secondary border-grey"
                                        data-toggle="tooltip" style="height: 91.2px;"><a href="javascript:;" class="d-flex align-items-center justify-content-center mt-5 remove-item" data-item-id="${i}" style="position: relative;top: -29px;"><i class="fa fa-times-circle f-20 text-lightest"></i></a></button>
                                        </div></div> `)
                .insertBefore($insertBefore);

            $(".dropify").dropify();
            // Recently Added date picker assign 
        });
        // Remove fields
        $('body').on('click', '.remove-item', function() {
            var index = $(this).data('item-id');
            $('#addMoreBox' + index).remove();
        });

        $('#add-itemNew').click(function() {
            a += 1;

            $(`<div id="addMoreBoxNew${a}" class="row pl-20 pr-20 clearfix" style="padding-top: 10px;">
            <div class="col-md-11" style="padding:0px;"> 
                            <input name="documents_joints[]" id="documents_joints${a}" type="file" class="dropify" multiple="" data-height="78"/> 
                            </div>
                            <div class="col-md-1" style="padding:0px;">
                            <button type="button"
                                        class="btn btn-outline-secondary border-grey"
                                        data-toggle="tooltip" style="height: 91.2px;"><a href="javascript:;" class="d-flex align-items-center justify-content-center mt-5 remove-itemNew" data-item-id="${a}" style="position: relative;top: -29px;"><i class="fa fa-times-circle f-20 text-lightest"></i></a></button>
                                        </div></div> `)
                .insertBefore($insertBeforeNew);

            $(".dropify").dropify();
            // Recently Added date picker assign 
        });
        $('body').on('click', '.remove-itemNew', function() {
            var index = $(this).data('item-id');
            $('#addMoreBoxNew' + index).remove();
        });

        $(document).ready(function() {

            var optionParProducteur = new Object();
            $("#producteur option").each(function() {
                //on assigne les producteurs à lobjet optionParProducteur
                var curreentArray = optionParProducteur[($(this).data('chained'))] ? optionParProducteur[($(
                        this)
                    .data('chained'))] : [];
                curreentArray[$(this).val()] = $(this).text().trim();
                Object.assign(optionParProducteur, {
                    [$(this).data('chained')]: curreentArray
                });
                $(this).remove();
            });

            $('#localite').change(function() {
                var localite = $(this).val();
                $("#producteur").empty();
                var optionsHtml2 = "";
                $(this).find('option:selected').each(function() {
                    console.log($(this).val());
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

        //     $('input:file').on('change', function(){
        //     allFiles = $(this)[0].files;
        //     for(var i = 0; allFiles.length > i; i++){
        //         var eachFile = allFiles[i],
        //         fileData = new FormData();
        //         fileData.append('file', eachFile);
        //         $.ajax({
        //             url: link,
        //             type: "POST",
        //             datatype:'script',
        //             data: fileData,
        //             contentType: false,
        //             processData:false,
        //             success: function(result){
        //                 console.log(result);
        //             }
        //         })
        //     }
        // })
    </script>
@endpush
