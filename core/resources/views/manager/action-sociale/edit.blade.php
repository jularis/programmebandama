@extends('manager.layouts.app')
@section('panel')
    <div class="row mb-none-30">
        <div class="col-lg-12 mb-30">
            <div class="card">
                <div class="card-body">
                    {!! Form::model($actionSociale, [
                        'method' => 'POST',
                        'route' => ['manager.communaute.action.sociale.store', $actionSociale->id],
                        'class' => 'form-horizontal',
                        'id' => 'flocal',
                        'enctype' => 'multipart/form-data',
                    ]) !!}
                    <input type="hidden" name="id" value="{{ $actionSociale->id }}">

                    <div class="form-group row">
                        <label class="col-sm-4 control-label" for="type_projet">Type de projet:</label>
                        <div class="col-xs-12 col-sm-8">
                            <select id="type_projet" class="form-control" name="type_projet" required>
                                <option value="Hydraulique villageoise">Hydraulique villageoise</option>
                                <option value="Education"
                                    {{ old('type_projet') == 'Education' ? 'selected' : '' }}>Education</option>
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
                                value="{{ $actionSociale->titre_projet }}" required>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-4 control-label" for="description_projet">Description du Projet:</label>
                        <div class="col-xs-12 col-sm-8">
                            <textarea id="description_projet" class="form-control" name="description_projet" rows="4" required>{{ $actionSociale->description_projet }}</textarea>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-4 control-label" for="beneficiaires_projet">Bénéficiaires du projet:</label>
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
                        <label class="col-sm-4 control-label" for="beneficiaires_projet"> Autres bénéficiaires du
                            projet:</label>
                        <div class="col-xs-12 col-sm-12">
                            <table class="table table-striped table-bordered">
                                <tbody id="beneficiaire_area">

                                    <?php
        if($actionSociale->autreBeneficiaires)
        {
        $i=0;
        $a=1;
        foreach ($actionSociale->autreBeneficiaires as $data) {
           ?>
                                    <tr>
                                        <td class="row">
                                            <div class="col-xs-12 col-sm-12 bg-success">
                                                <badge class="btn  btn-outline--warning h-45 btn-sm text-white">
                                                    @lang('Autres bénéficiaires')
                                                    <?php echo $a; ?>
                                                </badge>
                                            </div>
                                            <div class="col-xs-12 col-sm-12">
                                                <div class="form-group row">
                                                    <input type="text" name="autreBeneficiaire[]"
                                                        placeholder="Autres bénéficiaires"
                                                        id="autreBeneficiaire-<?php echo $a; ?>" class="form-control"
                                                        value="<?php echo $data->libelle; ?>">
                                                </div>
                                            </div>
                                            <?php if($a>1):?>
                                            <div class="col-xs-12 col-sm-8"><button type="button" id="<?php echo $a; ?>"
                                                    class="removeRowBeneficiaire btn btn-danger btn-sm"><i
                                                        class="fa fa-minus"></i></button></div>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php
           $a++;
            $i++;
        }
    }else{
        ?>
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
                                                        value="{{ old('autreBeneficiaire') }}">
                                                </div>
                                            </div>
                                        </td>
                                    </tr>

                                    <?php
        }
        ?>

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
                                value="{{ $actionSociale->date_livraison }}" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 control-label" for="niveau_realisation">Niveau de réalisation:</label>
                        <div class="col-xs-12 col-sm-8">
                            <select id="niveau_realisation" class="form-control" name="niveau_realisation">
                                <option value="Non démarré"
                                    {{ old('niveau_realisation') == 'Non démarré' ? 'selected' : '' }}>Non démarré</option>
                                <option value="En Cours" {{ old('niveau_realisation') == 'En Cours' ? 'selected' : '' }}>
                                    En
                                    Cours</option>
                                <option value="Achevé" {{ old('niveau_realisation') == 'Achevé' ? 'selected' : '' }}>
                                    Achevé
                                </option>
                            </select>
                        </div>
                    </div>


                    <div id="date_demarrage_container" style="display:none;">
                        <div class="form-group row">
                            <label class="col-sm-4 control-label" for="date_demarrage">Date de démarrage du
                                projet:</label>
                            <div class="col-xs-12 col-sm-8">
                                <input type="date" id="date_demarrage" class="form-control" name="date_demarrage"
                                    value="{{ $actionSociale->date_demarrage }}">
                            </div>
                        </div>
                    </div>

                    <div id="date_fin_projet_container" style="display:none;">
                        <div class="form-group row">
                            <label class="col-sm-4 control-label" for="date_fin_projet">Date de fin du projet:</label>
                            <div class="col-xs-12 col-sm-8">
                                <input type="date" id="date_fin_projet" class="form-control" name="date_fin_projet"
                                    value="{{ $actionSociale->date_fin_projet }}">
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-4 control-label" for="cout_projet">Coûts du projet (En FCFA):</label>
                        <div class="col-xs-12 col-sm-8">
                            <input type="text" id="cout_projet" class="form-control" name="cout_projet"
                                value="{{ $actionSociale->cout_projet }}" required placeholder="En FCFA">
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-xs-12 col-sm-12">
                            <table class="table table-striped table-bordered">
                                <tbody id="partenaire_area">
                                    @if ($partenaires)
                                        @foreach ($partenaires as $index => $partenaire)
                                            <tr>
                                                <td class="row">
                                                    <div class="col-xs-12 col-sm-12 bg-success">
                                                        <badge class="btn  btn-outline--warning h-45 btn-sm text-white">
                                                            @lang('Partenaire impliqué') {{ $index + 1 }}
                                                        </badge>
                                                    </div>
                                                    <div class="col-xs-12 col-sm-4">
                                                        <div class="form-group row">
                                                            <label class="col-sm-4 control-label"
                                                                for="partenaire">Partenaire
                                                                impliqué:</label>
                                                            <input type="text" id="partenaire- {{ $index }}"
                                                                class="form-control"
                                                                name="partenaires[{{ $index }}][partenaire]"
                                                                placeholder="Partenaire impliqué"
                                                                value="{{ $partenaire['partenaire'] }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-xs-12 col-sm-4">
                                                        <div class="form-group row">
                                                            <label class="col-sm-4 control-label"
                                                                for="type_partenariat">Type de
                                                                partenariat:</label>
                                                            <select id="type_partenariat-{{ $index }}"
                                                                class="form-control"
                                                                name="partenaires[{{ $index }}][type_partenaire]">
                                                                <option value="">Selectionner une option</option>
                                                                <option value="Technique"
                                                                    {{ $partenaire['type_partenaire'] == 'Technique' ? 'selected' : '' }}>
                                                                    Technique</option>
                                                                <option value="Financier"
                                                                    {{ $partenaire['type_partenaire'] == 'Financier' ? 'selected' : '' }}>
                                                                    Financier</option>
                                                                <option value="Technique et Financier"
                                                                    {{ $partenaire['type_partenaire'] == 'Technique et Financier' ? 'selected' : '' }}>
                                                                    Technique et Financier</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-xs-12 col-sm-4">
                                                        <div class="form-group row">
                                                            <label class="col-sm-4 control-label"
                                                                for="montant_contribution-">Montant de la
                                                                contribution:</label>
                                                            <input type="text"
                                                                id="montant_contribution-{{ $index }}"
                                                                class="form-control"
                                                                name="partenaires[{{ $index }}][montant_contribution]"
                                                                value="{{ $partenaire['montant'] }}"
                                                                placeholder="Montant de la contribution">
                                                        </div>
                                                    </div>
                                                    <div class="col-xs-12 col-sm-8">
                                                        <button type="button" id="{{ $index }}"
                                                            class="removeRowPartenaire btn btn-danger btn-sm"><i
                                                                class="fa fa-minus"></i></button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
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
                    @php
                        $photos = json_decode($actionSociale->photos);
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
                        $documents_joints = json_decode($actionSociale->documents_joints);
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
                            <textarea id="commentaires" class="form-control" name="commentaires" rows="4"> {{ $actionSociale->commentaires }} </textarea>
                        </div>
                    </div>

                    <hr class="panel-wide">

                    <div class="form-group">
                        <button type="submit" class="btn btn--primary btn-block h-45 w-100">@lang('Envoyer')</button>
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

            $(`<div id="addMoreBox${i}" class="row pl-20 pr-20 clearfix">
            <div class="form-group my-3" style="padding: 0px;">  
            <div class="input-group mb-3"> 
                            <input name="photos[]" id="photos${i}" type="file" class="dropify" multiple="" data-height="78"/> <button type="button"
                                        class="btn btn-outline-secondary border-grey"
                                        data-toggle="tooltip" style="width: 10px;"><a href="javascript:;" class="d-flex align-items-center justify-content-center mt-5 remove-item" data-item-id="${i}" style="position: relative;top: -29px;"><i class="fa fa-times-circle f-20 text-lightest"></i></a></button>
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

            $(`<div id="addMoreBoxNew${a}" class="row pl-20 pr-20 clearfix">
            <div class="form-group my-3" style="padding: 0px;">  
            <div class="input-group mb-3"> 
                            <input name="documents_joints[]" id="documents_joints${a}" type="file" class="dropify" multiple="" data-height="78"/> <button type="button"
                                        class="btn btn-outline-secondary border-grey"
                                        data-toggle="tooltip" style="width: 10px;"><a href="javascript:;" class="d-flex align-items-center justify-content-center mt-5 remove-itemNew" data-item-id="${a}" style="position: relative;top: -29px;"><i class="fa fa-times-circle f-20 text-lightest"></i></a></button>
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
            //intrants lannee derniere
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
                    '][type_partenaire]"><option value="">Selectionner une option</option><option value="Technique">Technique</option><option value="Financier">Financier</option><option value="Technique et Financier">Technique et Financier</option></select></div></div><div class="col-xs-12 col-sm-4 pr-0"><div class="form-group"> <label class="col-sm-4 control-label" for="montant_contribution-1">Montant de la contribution:</label><input type="text" id="montant_contribution-' +
                    partenairesCount + '" class="form-control" name="partenaires[' + partenairesCount +
                    '][montant_contribution]" placeholder="Montant de la contribution"></div></div><div class="col-xs-12 col-sm-8"><button type="button" id="' +
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
    </script>
@endpush
