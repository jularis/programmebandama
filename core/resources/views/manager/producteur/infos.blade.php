@extends('manager.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card b-radius--10 ">
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">

                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Producteur')</th>
                                    <th>@lang('Forêt & jachere')</th>
                                    <th>@lang('superficie forêt & jachère')</th>
                                    <th>@lang('Mobile Money')</th>
                                    <th>@lang('Compte Bancaire')</th>
                                    <th>@lang('Ajouté le')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($infosproducteurs as $info)
                                    <tr>
                                        <td>
                                            <span class="fw-bold">{{ $info->producteur->nom }}
                                                {{ $info->producteur->prenoms }}</span>
                                        </td>
                                        <td>
                                            <span class="fw-bold">{{ $info->foretsjachere }}</span>
                                        </td>
                                        <td>
                                            <span class="fw-bold">{{ $info->superficie }}</span>
                                        </td>
                                       
                                        </td>
                                       
                                        </td>
                                        <td>
                                            <span class="fw-bold">{{ $info->mobileMoney }}</span>
                                        </td>
                                        <td>
                                            <span class="fw-bold">{{ $info->compteBanque }}</span>
                                        </td>
                                        <td>
                                            {{ showDateTime($info->created_at) }} <br>
                                            {{ diffForHumans($info->created_at) }}
                                        </td>
                                        
                                        <td>
                                            <a href="{{ route('manager.traca.producteur.showinfosproducteur', encrypt($info->id)) }}"
                                                class="icon-btn btn--info ml-1">@lang('Détails')</a>

                                            <a href="{{ route('manager.traca.producteur.editinfo', encrypt($info->id)) }}"
                                                class="icon-btn btn--primary ml-1">@lang('Edit')</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                    </tr>
                                @endforelse

                            </tbody>
                        </table>
                    </div>
                </div>


            </div>
        </div>
    </div>


    <div id="cooperativeModel" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Créer une info producteur')</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('manager.traca.producteur.storeinfo') }}" method="POST">
                    @csrf

                    <input type="hidden" name="producteur_id" value="{{ decrypt($id) }}" />
                    <div class="modal-body">
                        <div class="form-group row">
                            <?php echo Form::label(__('Avez-vous des forets ou jachère ?'), null, ['class' => 'col-sm-4 control-label']); ?>
                            <div class="col-xs-12 col-sm-8">
                                <?php echo Form::select('foretsjachere', ['non' => 'Non', 'oui' => 'Oui'], null, ['class' => 'form-control foretsjachere', 'required']); ?>

                            </div>
                        </div>


                        <div class="form-group row" id="superficie">
                            <?php echo Form::label(__('Superficie'), null, ['class' => 'col-sm-4 control-label']); ?>
                            <div class="col-xs-12 col-sm-8">
                                <?php echo Form::number('superficie', null, ['placeholder' => 'Nombre', 'class' => 'form-control superficie', 'min' => '0']); ?>
                            </div>
                        </div>

                        {{-- autres culture en dehors du cacao --}}
                        <div class="form-group row">
                            <?php echo Form::label(__('Avez-vous d’autres Cultures en dehors du cacao?'), null, ['class' => 'col-sm-4 control-label']); ?>
                            <div class="col-xs-12 col-sm-8">
                                <?php echo Form::select('autresCultures', ['non' => 'Non', 'oui' => 'Oui'], null, ['class' => 'form-control autresCultures', 'required']); ?>
                            </div>
                        </div>
                        <div class="form-group row">
                            <?php echo Form::label('', null, ['class' => 'col-sm-4 control-label']); ?>
                            <div class="col-xs-12 col-sm-8" id="listecultures">
                                <table class="table table-striped table-bordered">
                                    <tbody id="product_area">

                                        <tr>
                                            <td class="row">
                                                <div class="col-xs-12 col-sm-12 bg-success">
                                                    <badge class="btn  btn-outline--warning h-45 btn-sm text-white">@lang('Information Culture 1')
                                                    </badge>
                                                </div>
                                                <div class="col-xs-12 col-sm-12">
                                                    <div class="form-group row">
                                                        {{ Form::label(__('Type de culture'), null, ['class' => 'control-label']) }}
                                                        <input type="text" name="typeculture[]"
                                                            placeholder="Riz, Maïs, Igname, Banane, ..." id="typeculture-1"
                                                            class="form-control" value="{{ old('typeculture') }}">
                                                    </div>
                                                </div>

                                                <div class="col-xs-12 col-sm-12">
                                                    <div class="form-group row">
                                                        {{ Form::label(__('Superficie de culture'), null, ['class' => 'control-label']) }}
                                                        <input type="text" name="superficieculture[]"
                                                            placeholder="Superficie de culture" id="superficieculture-1"
                                                            class="form-control " value="{{ old('superficieculture') }}">
                                                    </div>
                                                </div>

                                            </td>
                                        </tr>

                                    </tbody>
                                    <tfoot style="background: #e3e3e3;">
                                        <tr>

                                            <td colspan="3">
                                                <button id="addRow" type="button" class="btn btn-success btn-sm"><i
                                                        class="fa fa-plus"></i></button>
                                            </td>
                                        <tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        {{-- autre activité en dehors du cacao --}}
                        <div class="form-group row">
                            <?php echo Form::label(__('Avez-vous d’autres activités en dehors des cultures?'), null, ['class' => 'col-sm-4 control-label']); ?>
                            <div class="col-xs-12 col-sm-8">
                                <?php echo Form::select('autreActivite', ['non' => 'Non', 'oui' => 'Oui'], null, ['class' => 'form-control autreActivite', 'required']); ?>

                            </div>
                        </div>
                        <div class="form-group row">
                            <?php echo Form::label('', null, ['class' => 'col-sm-4 control-label']); ?>
                            <div class="col-xs-12 col-sm-8" id="listeactivites">
                                <table class="table table-striped table-bordered">
                                    <tbody id="activity_area">

                                        <tr>
                                            <td class="row">
                                                <div class="col-xs-12 col-sm-12 bg-success">
                                                    <badge class="btn  btn-outline--warning h-45 btn-sm text-white">@lang('Information Activité 1')
                                                    </badge>
                                                </div>
                                                <div class="col-xs-12 col-sm-12">
                                                    <div class="form-group row">
                                                        {{ Form::label(__('Type d\'activité'), null, ['class' => 'control-label']) }}
                                                        <input type="text" name="typeactivite[]"
                                                            placeholder="Elevage, Commerce, Prestation de service, ..."
                                                            id="typeactivite-1" class="form-control"
                                                            value="{{ old('typeactivite') }}">
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                    <tfoot style="background: #e3e3e3;">
                                        <tr>
                                            <td colspan="3">
                                                <button id="addRowActivite" type="button" class="btn btn-success btn-sm"><i
                                                        class="fa fa-plus"></i></button>
                                            </td>
                                        <tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>

                        <hr class="panel-wide">

                        <div class="form-group row">
                            <?php echo Form::label(__('Avez-vous recours à une main d\'œuvre familiale  ?'), null, ['class' => 'col-sm-4 control-label']); ?>
                            <div class="col-xs-12 col-sm-8">
                                <?php echo Form::select('mainOeuvreFamilial', ['non' => 'non', 'oui' => 'oui'], null, ['class' => 'form-control mainOeuvreFamilial', 'required']); ?>
                            </div>
                        </div>

                        <div class="form-group row" id="travailleurFamilial">
                            <?php echo Form::label(__('Combien de personnes de la famille travaillent'), null, ['class' => 'col-sm-4 control-label']); ?>
                            <div class="col-xs-12 col-sm-8">
                                <?php echo Form::number('travailleurFamilial', null, ['placeholder' => 'Nombre', 'class' => 'form-control travailleurFamilial', 'min' => '0']); ?>
                            </div>
                        </div>

                        <div class="form-group row">
                            <?php echo Form::label(__('Combien de travailleurs (rémunéré) avez-vous ?'), null, ['class' => 'col-sm-4 control-label']); ?>
                            <div class="col-xs-12 col-sm-8">
                                <?php echo Form::number('travailleurs', null, ['placeholder' => 'Nombre', 'class' => 'form-control', 'min' => '0', 'required']); ?>
                            </div>
                        </div>

                        <div class="form-group row">
                            <?php echo Form::label(__('Nombre de Travailleurs Permanents (plus de 12mois)'), null, ['class' => 'col-sm-4 control-label']); ?>
                            <div class="col-xs-12 col-sm-8">
                                <?php echo Form::number('travailleurspermanents', null, ['placeholder' => 'Nombre', 'class' => 'form-control', 'min' => '0', 'required']); ?>
                            </div>
                        </div>

                        <div class="form-group row">
                            <?php echo Form::label(__('Nombre de Travailleurs temporaires'), null, ['class' => 'col-sm-4 control-label']); ?>
                            <div class="col-xs-12 col-sm-8">
                                <?php echo Form::number('travailleurstemporaires', null, ['placeholder' => 'Nombre', 'class' => 'form-control', 'min' => '0']); ?>
                            </div>
                        </div>
                        <div class="form-group row">
                            <?php echo Form::label(__('Etes vous membre de société de travail ?'), null, ['class' => 'col-sm-4 control-label']); ?>
                            <div class="col-xs-12 col-sm-8">
                                <?php echo Form::select('societeTravail', ['non' => 'Non', 'oui' => 'Oui'], null, ['class' => 'form-control societeTravail', 'required']); ?>
                            </div>
                        </div>
                        <div class="form-group row" id="societe">
                            <?php echo Form::label(__('Nombre de Personne'), null, ['class' => 'col-sm-4 control-label']); ?>
                            <div class="col-xs-12 col-sm-8">
                                <?php echo Form::number('nombrePersonne', null, ['placeholder' => 'Nombre', 'id' => 'nombrePersonne', 'class' => 'form-control nombrePersonne', 'min' => '0']); ?>
                            </div>
                        </div>

                        <hr class="panel-wide">

                        <div class="form-group row">
                            <?php echo Form::label(__('As-tu un Compte Mobile Money ?'), null, ['class' => 'col-sm-4 control-label']); ?>
                            <div class="col-xs-12 col-sm-8">
                                <?php echo Form::select('mobileMoney', ['non' => 'Non', 'oui' => 'Oui'], null, ['class' => 'form-control mobileMoney', 'required']); ?>

                            </div>
                        </div>
                        <div id="numeroCompteMM">
                            <div class="form-group row">
                                <?php echo Form::label('', null, ['class' => 'col-sm-4 control-label']); ?>
                                <div class="col-xs-12 col-sm-8" id="listeoperateurs">
                                    <table class="table table-striped table-bordered">
                                        <tbody id="compagny_area">
                                            <tr>
                                                <td class="row">
                                                    <div class="col-xs-12 col-sm-12 bg-success">
                                                        <badge class="btn  btn-outline--warning h-45 btn-sm text-white">
                                                            @lang('Information mobile monnaie 1')
                                                        </badge>
                                                    </div>
                                                    <div class="col-xs-12 col-sm-12">
                                                        <div class="form-group row">
                                                            {{ Form::label(__('Opérateur'), null, ['class' => 'control-label']) }}
                                                            <select name="operateurMM[]" id="operateurMM-1"
                                                                class="form-control">
                                                                <option value="MTN">MTN</option>
                                                                <option value="ORANGE">ORANGE</option>
                                                                <option value="MOOV">MOOV</option>
                                                                <option value="Wave">Wave</option>
                                                                <option value="Push">Push</option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="col-xs-12 col-sm-12">
                                                        <div class="form-group row">
                                                            {{ Form::label(__('Numéro'), null, ['class' => 'control-label']) }}
                                                            <input type="text" name="numeros[]"
                                                                placeholder="Numéro opérateur" id="numeros-1"
                                                                class="form-control " value="{{ old('numeros') }}">
                                                        </div>
                                                    </div>

                                                </td>
                                            </tr>
                                        </tbody>
                                        <tfoot style="background: #e3e3e3;">
                                            <tr>
                                                <td colspan="3">
                                                    <button id="addRowOperateur" type="button"
                                                        class="btn btn-success btn-sm"><i class="fa fa-plus"></i></button>
                                                </td>
                                            <tr>
                                        </tfoot>
                                    </table>

                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <?php echo Form::label(__('As-tu un compte bancaire (dans une banque) ?'), null, ['class' => 'col-sm-4 control-label']); ?>
                            <div class="col-xs-12 col-sm-8">
                                <?php echo Form::select('compteBanque', ['non' => 'Non', 'oui' => 'Oui'], null, ['class' => 'form-control compteBanque', 'required']); ?>

                            </div>
                        </div>
                        <div class="form-group row" id="nomBanque">
                            <?php echo Form::label(__('Nom de la banque'), null, ['class' => 'col-sm-4 control-label']); ?>
                            <div class="col-xs-12 col-sm-8">
                                <?php echo Form::select('nomBanque', ['Advans' => 'Advans', 'Coopec' => 'Coopec', 'Microcred' => 'Microcred', 'Autre' => 'Autre'], null, ['class' => 'form-control nomBanque']); ?>
                            </div>
                        </div>
                        <div class="form-group row" id="autreBanque">
                            <?php echo Form::label(__('Nom de la banque'), null, ['class' => 'col-sm-4 control-label']); ?>
                            <div class="col-xs-12 col-sm-8">
                                <?php echo Form::text('autreBanque', null, ['placeholder' => 'Autre Banque', 'class' => 'form-control autreBanque']); ?>
                            </div>
                        </div>
                        <hr class="panel-wide">
                        <div class="modal-footer">
                            <button type="button" class="btn btn--dark btnFermer"
                                data-dismiss="modal">@lang('Fermer')</button>
                            <button type="submit" class="btn btn--primary"><i
                                    class="fa fa-fw fa-paper-plane"></i>@lang('Enregistrer une info')</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <x-search-form placeholder="Search here ..." />
    @if (!$infosproducteurs->count())
        <a href="javascript:void(0)" class="btn  btn-outline--primary box--shadow1  h-45 addNewBrach"><i
                class="las la-plus"></i>@lang('Ajouter une info prod')</a>
    @endif
    <x-back route="{{ route('manager.traca.producteur.index') }}" />
@endpush

@push('script')
    <script>
        "use strict";
        $('.addNewBrach').on('click', function() {
            $('#cooperativeModel').modal('show');
        });


        $(document).ready(function() {
            $('#cooperativeModel .btnFermer').click(function() {
                $('#cooperativeModel').modal('hide');
            });
        });

        $(document).ready(function() {
            $('#cooperativeModel').modal({
                backdrop: 'static',
                keyboard: false
            });
        });
    </script>
    <script type="text/javascript">
        $(document).ready(function() {

            $(document).ready(function() {

                var productCount = $("#product_area tr").length + 1;
                $(document).on('click', '#addRow', function() {

                    //---> Start create table tr
                    var html_table = '<tr>';
                    html_table +=
                        '<td class="row"><div class="col-xs-12 col-sm-12 bg-success"><badge class="btn  btn-outline--warning h-45 btn-sm text-white">Information Culture ' +
                        productCount +
                        '</badge></div><div class="col-xs-12 col-sm-12"><div class="form-group row"><label for="Type de culture" class="control-label">Type de culture</label><input placeholder="Riz, Maïs, Igname, Banane, ..." class="form-control" id="typeculture-' +
                        productCount +
                        '" name="typeculture[]" type="text"></div></div><div class="col-xs-12 col-sm-12"><div class="form-group row"><label for="superficieculture" class="control-label">Superficie de culture</label><input type="text" name="superficieculture[]" placeholder="Superficie de culture" id="superficieculture-' +
                        productCount +
                        '" class="form-control " value=""></div></div><div class="col-xs-12 col-sm-12"><button type="button" id="' +
                        productCount +
                        '" class="removeRow btn btn-danger btn-sm"><i class="fa fa-minus"></i></button></div></td>';

                    html_table += '</tr>';
                    //---> End create table tr

                    productCount = parseInt(productCount) + 1;
                    $('#product_area').append(html_table);

                });

                $(document).on('click', '.removeRow', function() {

                    var row_id = $(this).attr('id');

                    // delete only last row id
                    if (row_id == $("#product_area tr").length) {

                        $(this).parents('tr').remove();

                        productCount = parseInt(productCount) - 1;

                        //    console.log($("#product_area tr").length);

                        //  productCount--;

                    }
                });

            });

            $(document).ready(function() {

                var productCount = $("#activity_area tr").length + 1;

                $(document).on('click', '#addRowActivite', function() {

                    //---> Start create table tr
                    var html_table = '<tr>';

                    html_table +=
                        '<td class="row"><div class="col-xs-12 col-sm-12 bg-success"><badge class="btn  btn-outline--warning h-45 btn-sm text-white">Information Activité ' +
                        productCount +
                        '</badge></div><div class="col-xs-12 col-sm-12"><div class="form-group"><label for="" class="control-label">Type D\'activité</label><input placeholder="Elevage, Commerce, Prestation de service, ..." class="form-control" id="typeactivite-' +
                        productCount +
                        '" name="typeactivite[]" type="text"></div></div><div class="col-xs-12 col-sm-12 col-md-12"><button type="button" id="' +
                        productCount +
                        '" class="removeRowActivite btn btn-danger btn-sm"><i class="fa fa-minus"></i></button></div></td>';

                    html_table += '</tr>';
                    //---> End create table tr

                    productCount = parseInt(productCount) + 1;


                    $('#activity_area').append(html_table);

                });

                $(document).on('click', '.removeRowActivite', function() {

                    var row_id = $(this).attr('id');

                    // delete only last row id
                    if (row_id == $("#activity_area tr").length) {

                        $(this).parents('tr').remove();

                        productCount = parseInt(productCount) - 1;

                        //    console.log($("#product_area tr").length);

                        //  productCount--;

                    }
                });


            });

            $(document).ready(function() {

                var productCount = $("#compagny_area tr").length + 1;
                $(document).on('click', '#addRowOperateur', function() {

                    //---> Start create table tr
                    var html_table = '<tr>';
                    html_table +=
                        '<td class="row"><div class="col-xs-12 col-sm-12 bg-success"><badge class="btn  btn-outline--warning h-45 btn-sm text-white">Information mobile monnaie ' +
                        productCount +
                        '</badge></div><div class="col-xs-12 col-sm-12"><div class="form-group row"><label for="Type de culture" class="control-label">Opérateur</label><select name="operateurMM[]" id="operateurMM-' +
                        productCount +
                        '" class="form-control"><option value="MTN">MTN</option><option value="ORANGE">ORANGE</option><option value="MOOV">MOOV</option><option value="Wave">Wave</option><option value="Push">Push</option></select></div></div><div class="col-xs-12 col-sm-12"><div class="form-group row"><label for="" class="control-label">Numéro</label><input type="text" name="numeros[]" placeholder="Numéro opérateur" id="numeros-' +
                        productCount +
                        '" class="form-control " value=""></div></div><div class="col-xs-12 col-sm-12"><button type="button" id="' +
                        productCount +
                        '" class="removeRowOperateur btn btn-danger btn-sm"><i class="fa fa-minus"></i></button></div></td>';
                    html_table += '</tr>';
                    //---> End create table tr

                    productCount = parseInt(productCount) + 1;
                    $('#compagny_area').append(html_table);

                });

                $(document).on('click', '.removeRowOperateur', function() {

                    var row_id = $(this).attr('id');

                    // delete only last row id
                    if (row_id == $("#compagny_area tr").length) {

                        $(this).parents('tr').remove();

                        productCount = parseInt(productCount) - 1;

                        //    console.log($("#product_area tr").length);

                        //  productCount--;

                    }
                });

            });

        });
        $('#listecultures,#gardePapiersChamps,#numeroCompteMM,#listeactivites,#nomBanque,#autreBanque,#travailleurFamilial,#societe')
            .hide();

        $('.autresCultures').change(function() {
            var autresCultures = $('.autresCultures').val();
            if (autresCultures == 'oui') {
                $('#listecultures').show('slow');
            } else {
                $('#listecultures').hide('slow');
                $('.listecultures').val('');
            }
        });
        $('.mainOeuvreFamilial').change(function() {
            var mainOeuvreFamilial = $('.mainOeuvreFamilial').val();
            if (mainOeuvreFamilial == 'oui') {
                $('#travailleurFamilial').show('slow');
                $('.travailleurFamilial').show('slow');
            } else {
                $('#travailleurFamilial').hide('slow');
                $('.travailleurFamilial').val('');
            }
        });
        $('.societeTravail').change(function() {
            var societeTravail = $('.societeTravail').val();
            if (societeTravail == 'oui') {
                $('#societe').show('slow');
                $('#nombrePersonne').prop('required', true);
            } else {
                $('#societe').hide('slow');
                $('#nombrePersonne').prop('required', false);
                $('.nombrePersonne').val('');
            }
        });

        $('.nomBanque').change(function() {
            var nomBanque = $('.nomBanque').val();
            if (nomBanque == 'Autre') {
                $('#autreBanque').show('slow');
                $('.autreBanque').show('slow');
            } else {
                $('#autreBanque').hide('slow');
                $('.autreBanque').val('');
            }
        });
        $('.compteBanque').change(function() {
            var compteBanque = $('.compteBanque').val();
            if (compteBanque == 'oui') {
                $('#nomBanque').show('slow');
                $('.nomBanque').show('slow');
            } else {
                $('#nomBanque').hide('slow');
                $('.nomBanque').val('');
            }
        });
        $('.autreActivite').change(function() {
            var autreActivite = $('.autreActivite').val();
            if (autreActivite == 'oui') {
                $('#listeactivites').show('slow');
            } else {
                $('#listeactivites').hide('slow');
                $('.listeactivites').val('');
            }
        });

        $('.mobileMoney').change(function() {
            var mobileMoney = $('.mobileMoney').val();
            if (mobileMoney == 'oui') {
                $('#numeroCompteMM').show('slow');
                $('.numeroCompteMM').css('display', 'block');
            } else {
                $('#numeroCompteMM').hide('slow');
                $('.numeroCompteMM').val('');
            }
        });
    </script>
    <script type="text/javascript">
        $('#superficie').hide();
        $('.foretsjachere').change(function() {
            var foretsjachere = $('.foretsjachere').val();
            if (foretsjachere == 'oui') {
                $('#superficie').show('slow');
            } else {
                $('#superficie').hide('slow');
                $('.superficie').val('');
            }
        });
    </script>
@endpush
