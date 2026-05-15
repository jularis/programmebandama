@extends('manager.layouts.app')
@section('panel')
    <div class="row mb-none-30">
        <div class="col-lg-12 mb-30">
            <div class="card">
                <div class="card-body">
                    {!! Form::open([
                        'route' => ['manager.communaute.action.sociale.store'],
                        'method' => 'POST',
                        'class' => 'form-horizontal',
                        'id' => 'flocal',
                        'enctype' => 'multipart/form-data',
                    ]) !!}


                    <div class="form-group row">
                        <label class="col-sm-4 control-label" for="type_projet">Type de projet:</label>
                        <div class="col-xs-12 col-sm-8">
                            <select id="type_projet" class="form-control" name="type_projet" required>
                                <option value="Hydraulique villageoise">Hydraulique villageoise</option>
                                <option value="Education"
                                    {{ old('type_projet') == 'Hydrolique villageois' ? 'selected' : '' }}>Education</option>
                                <option value="Voirie" {{ old('type_projet') == 'Voirie' ? 'selected' : '' }}>Voirie
                                </option>
                                <option value="Electricité" {{ old('type_projet') == 'Electricité' ? 'selected' : '' }}>
                                    Electricité</option>
                                <option value="Santé" {{ old('type_projet') == 'Santé' ? 'selected' : '' }}>Santé</option>
                                <option value="Equipement rural"
                                    {{ old('type_projet') == 'Equipement rural' ? 'selected' : '' }}>Equipement rural
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-4 control-label" for="titre_projet">Titre du projet:</label>
                        <div class="col-xs-12 col-sm-8">
                            <input type="text" id="titre_projet" class="form-control" name="titre_projet"
                                value="{{ old('titre_projet') }}" required>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-4 control-label" for="description_projet">Description du Projet:</label>
                        <div class="col-xs-12 col-sm-8">
                            <textarea id="description_projet" class="form-control" name="description_projet" rows="4" required>{{ old('titre_projet') }}</textarea>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-4 control-label" for="beneficiaires_projet">Bénéficiaires du projet:</label>
                        <div class="col-xs-12 col-sm-8">
                            <select class="form-control select2-multi-select" name="beneficiaires_projet[]"
                                id="beneficiaires_projet" required multiple>
                                <option value="">@lang('Selectionner une option')</option>
                                @foreach ($localites as $localite)
                                    <option value="{{ $localite->id }}" @selected(old('localite'))>
                                        {{ $localite->nom }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 control-label" for="beneficiaires_projet"> Autres bénéficiaires du
                            projet:</label>
                        <div class="col-xs-12 col-sm-8">
                            <table class="table table-striped table-bordered">
                                <tbody id="beneficiaire_area">
                                    <tr>
                                        <td class="row">
                                            <div class="col-xs-12 col-sm-12 bg-success">
                                                <badge class="btn  btn-outline--warning h-45 btn-sm text-white">
                                                    @lang('Autres bénéficiaires')
                                                </badge>
                                            </div>
                                            <div class="col-xs-12 col-sm-12">
                                                <div class="form-group row">
                                                    <input type="text" id="autreBeneficiaire-1" class="form-control mt-3"
                                                        name="autreBeneficiaire[]" placeholder="Autres bénéficiaires"
                                                        value="{{ old('partenaire') }}">
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot style="background: #e3e3e3;">
                                    <tr>

                                        <td colspan="3">
                                            <button id="addRowBeneficiare" type="button" class="btn btn-success btn-sm"><i
                                                    class="fa fa-plus"></i></button>
                                        </td>
                                    <tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-4 control-label" for="date_livraison">Date de la livraison:</label>
                        <div class="col-xs-12 col-sm-8">
                            <input type="date" id="date_livraison" class="form-control" name="date_livraison"
                                value="{{ old('date_livraison') }}" required>
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
                            <input type="text" id="cout_projet" class="form-control" name="cout_projet"
                                value="{{ old('cout_projet') }}" required placeholder="En FCFA">
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-xs-12 col-sm-12">
                            <table class="table table-striped table-bordered">
                                <tbody id="partenaire_area">
                                    <tr>
                                        <td class="row">
                                            <div class="col-xs-12 col-sm-12 bg-success">
                                                <badge class="btn  btn-outline--warning h-45 btn-sm text-white">
                                                    @lang('Partenaire impliqué')
                                                </badge>
                                            </div>
                                            <div class="col-xs-12 col-sm-4">
                                                <div class="form-group row">
                                                    <label class="col-sm-4 control-label" for="partenaire">Partenaire
                                                        impliqué:</label>
                                                    <input type="text" id="partenaire-1" class="form-control"
                                                        name="partenaires[0][partenaire]"
                                                        placeholder="Partenaire impliqué"
                                                        value="{{ old('partenaire') }}">
                                                </div>
                                            </div>
                                            <div class="col-xs-12 col-sm-4">
                                                <div class="form-group row">
                                                    <label class="col-sm-4 control-label" for="type_partenariat">Type de
                                                        partenariat:</label>
                                                    <select id="type_partenariat-1" class="form-control"
                                                        name="partenaires[0][type_partenaire]">
                                                        <option value="">Selectionner une option</option>
                                                        <option value="Technique">Technique</option>
                                                        <option value="Financier">Financier</option>
                                                        <option value="Technique et Financier">Technique et Financier
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-xs-12 col-sm-4">
                                                <div class="form-group row">
                                                    <label class="col-sm-4 control-label"
                                                        for="montant_contribution-1">Montant de la
                                                        contribution (En FCFA):</label>
                                                    <input type="text" id="montant_contribution-1"
                                                        class="form-control" name="partenaires[0][montant_contribution]"
                                                        placeholder="Montant de la contribution (En FCFA)">
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot style="background: #e3e3e3;">
                                    <tr>

                                        <td colspan="3">
                                            <button id="addRowPartenaire" type="button"
                                                class="btn btn-success btn-sm"><i class="fa fa-plus"></i></button>
                                        </td>
                                    <tr>
                                </tfoot>
                            </table>
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
                            <textarea id="commentaires" class="form-control" name="commentaires" rows="4">{{ old('commentaires') }}</textarea>
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
    <x-back route="{{ route('manager.communaute.action.sociale.index') }}" />
@endpush

@push('script')
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/daterangepicker.css') }}">
    <script src="{{ asset('assets/vendor/jquery/daterangepicker.min.js') }}"></script>
    <script type="text/javascript">
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
                            <input name="photos[]" id="photos${i}" type="file" class="dropify" multiple="" data-height="78" /> 
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

        $(document).ready(function() {

            var partenairesCount = $("#partenaire_area tr").length;

            $(document).on('click', '#addRowPartenaire', function() {

                var html_table = '<tr>';
                html_table +=
                    '<td class="row"><div class="col-xs-12 col-sm-12 bg-success"><badge class="btn  btn-outline--warning h-45 btn-sm text-white">Partenaire impliqué ' +
                    partenairesCount +
                    '</badge></div><div class="col-xs-12 col-sm-4 pr-0"><div class="form-group"><label class="col-sm-4 control-label" for="partenaire">Partenaire impliqué:</label><input type="text" id="partenaire-' +
                    partenairesCount + '" class="form-control"name="partenaires[' + partenairesCount +
                    '][partenaire]" placeholder="Partenaire impliqué"></div></div> <div class="col-xs-12 col-sm-4"><div class="form-group row pr-0"><label class="col-sm-4 control-label" for="type_partenariat">Type de partenariat:</label> <select id="type_partenariat-' +
                    partenairesCount + '" class="form-control" name="partenaires[' + partenairesCount +
                    '][type_partenaire]"><option value="">Selectionner une option</option><option value="Technique">Technique</option><option value="Financier">Financier</option><option value="Technique et Financier">Technique et Financier</option></select></div></div><div class="col-xs-12 col-sm-4 pr-0"><div class="form-group"> <label class="col-sm-4 control-label" for="montant_contribution-1">Montant de la contribution (En FCFA):</label><input type="text" id="montant_contribution-' +
                    partenairesCount + '" class="form-control" name="partenaires[' + partenairesCount +
                    '][montant_contribution]" placeholder="Montant de la contribution(En FCFA)"></div></div><div class="col-xs-12 col-sm-8"><button type="button" id="' +
                    partenairesCount +
                    '" class="removeRowPartenaire btn btn-danger btn-sm"><i class="fa fa-minus"></i></button></td>';
                html_table += '</tr>';
                //---> End create table tr

                partenairesCount = parseInt(partenairesCount) + 1;
                $('#partenaire_area').append(html_table);
            });

            $(document).on('click', '.removeRowPartenaire', function() {
                var row_id = $(this).attr('id');
                if (row_id == $("#partenaire_area tr").length - 1) {
                    $(this).parents('tr').remove();
                    partenairesCount = parseInt(partenairesCount) - 1;
                }
            });

            //beneficiaires
            var beneficiairesCount = $("#beneficiaire_area tr").length + 1;
            $(document).on('click', '#addRowBeneficiare', function() {

                //---> Start create table tr
                var html_table = '<tr>';
                html_table +=
                    '<td class="row"><div class="col-xs-12 col-sm-12 bg-success"><badge class="btn  btn-outline--warning h-45 btn-sm text-white">Autres bénéficiaires ' +
                    beneficiairesCount +
                    '</badge></div><div class="col-xs-12 col-sm-12"><div class="form-group"><input placeholder="Autres bénéficiaires" class="form-control" id="autreBeneficiaire-' +
                    beneficiairesCount +
                    '" name="autreBeneficiaire[]" type="text"></div></div><div class="col-xs-12 col-sm-12"><button type="button" id="' +
                    beneficiairesCount +
                    '" class="removeRowBeneficiaire btn btn-danger btn-sm"><i class="fa fa-minus"></i></button></div></td>';

                html_table += '</tr>';
                //---> End create table tr

                beneficiairesCount = parseInt(beneficiairesCount) + 1;
                $('#beneficiaire_area').append(html_table);

            });

            $(document).on('click', '.removeRowBeneficiaire', function() {
                var row_id = $(this).attr('id');
                if (row_id == $("#beneficiaire_area tr").length) {
                    $(this).parents('tr').remove();
                    beneficiairesCount = parseInt(beneficiairesCount) - 1;
                }
            });


        });

        $('#producteurs').change(function() {
            $.ajax({
                type: 'GET',
                url: "{{ route('manager.livraison.magcentral.get.listeproducteur') }}",
                data: $('#flocal').serialize(),
                success: function(html) {
                    $('#listeprod').html(html.results);
                    $('#poidsnet').val(html.total);

                }
            });
        });
    </script>
@endpush
