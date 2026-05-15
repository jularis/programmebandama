@extends('manager.layouts.app')
@section('panel')
    <link rel="stylesheet" href="{{ asset('assets/fcadmin/css/vendor/datepicker.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/fcadmin/css/tokens.css') }}">
    <div class="row mb-none-30">
        <div class="col-lg-12 col-md-12 mb-30">
            <div class="card">
                <form action="{{ route('manager.livraison.magcentral.store') }}" id="flocal" method="POST">
                    <div class="card-body">
                        @csrf
                        <div class="row">
                            <input type="hidden" name="code" value="{{ $code }}">
                            <div class="col-lg-3 form-group">
                                <label for="">@lang('N° Connaissement USINE')</label>
                                <div class="input-group">
                                    <span class="input-group-text">{{ $code }}</span>
                                    <input name="lastcode" value="" type="number" autocomplete="off" class="form-control" required>

                                </div>
                            </div>
                            <div class="col-lg-3 form-group">
                                <label for="">@lang('Date de livraison')</label>
                                <div class="input-group">
                                    <input name="estimate_date" value="{{ old('estimate_date') }}" type="text"
                                        autocomplete="off" class="form-control dates" placeholder="Date de livraison"
                                        required>
                                    <span class="input-group-text"><i class="las la-calendar"></i></span>
                                </div>
                            </div>

                            <div class="col-lg-3 form-group">
                                <label for="">@lang('Types de produit')</label>
                                <div class="input-group">
                                    <select class="form-control select-picker" name="type" id="type"
                                        required>
                                        <option value>@lang('Selectionner une option')</option>
                                        <option value="{{ __('Certifie') }}" @selected(old('type') == 'Certifie')>
                                            {{ __('Certifie') }}</option>
                                        <option value="{{ __('Ordinaire') }}" @selected(old('type') == 'Ordinaire')>
                                            {{ __('Ordinaire') }}</option>
                                    </select>
                                </div>
                            </div>
                             <div class="col-lg-3 form-group certif">
                                <label for="">@lang('Certificat')</label>
                                <div class="input-group">
                                <select class="form-control" name="certificat" id="certificat"
                                required>
                                <option value="">@lang('Selectionner un certificat')</option>
                                <option value="Rainforest"
                                    {{ in_array('Rainforest', old('certificat', [])) ? 'selected' : '' }}>Rainforest
                                </option>
                                <option value="Fairtrade"
                                    {{ in_array('Fairtrade', old('certificat', [])) ? 'selected' : '' }}>Fairtrade
                                </option>
                                <option value="BIO" {{ in_array('BIO', old('certificat', [])) ? 'selected' : '' }}>
                                    BIO
                                </option>
                                <option value="Autre" {{ in_array('Autre', old('certificat', [])) ? 'selected' : '' }}>
                                    Autre
                                </option>
                            </select>
                                </div>
                            </div>

                              <div class="col-lg-6">
                            <label for="">@lang("Campagne")</label>
                                <select class="form-control" id="campagne" name="campagne" required>
                                    <option value="">@lang('Selectionner une campagne')</option>
                                    @foreach ($allcampagnes as $campagne)
                                        <option value="{{ $campagne->id }}"  @selected(old('campagne')==$campagne->id)>
                                            {{ __($campagne->nom) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-6">
                            <label for="">@lang("Période")</label>
                                <select class="form-control" id="periode" name="periode" required>
                                    <option value="">@lang('Selectionner une periode')</option>
                                    @foreach ($allperiodes as $periode)
                                        <option value="{{ $periode->id }}"
                                            data-debut="{{$periode->periode_debut}}"
                                            data-fin="{{$periode->periode_fin}}"
                                            data-prix="{{$periode->prix_champ }}"
                                             @selected(old('periode')==$periode->id)
                                            data-chained="{{ $periode->campagne_id}}"
                                            >
                                            {{ __($periode->nom) }}</option>
                                    @endforeach
                                </select>
                            </div>



                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="card border--primary mt-3">
                                    <h5 class="card-header bg--primary  text-white">@lang('Information Expéditeur(Magasin Central)')</h5>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="form-group col-lg-12">
                                                <label>@lang('Selectionner un Magasin Central')</label>
                                                <select class="form-control" name="magasin_central" id="magasin_central"
                                                    onchange="getReceiver()" required>
                                                    <option value>@lang('Selectionner une option')</option>
                                                    @foreach ($magCentraux as $magasin)
                                                        <option value="{{ $magasin->id }}"
                                                            data-name ="{{ $magasin->user->lastname }} {{ $magasin->user->firstname }}"
                                                            data-phone ="{{ $magasin->user->mobile }}"
                                                            data-email ="{{ $magasin->user->email }}"
                                                            data-adresse ="{{ $magasin->user->adresse }}"
                                                            @selected(old('magasin_central') == $magasin->id)>{{ __($magasin->nom) }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group col-lg-6">
                                                <label>@lang('Nom')</label>
                                                <input type="text" class="form-control" name="receiver_name"
                                                    id="receiver_name" value="{{ old('receiver_name') }}" readonly
                                                    required>
                                            </div>
                                            <div class="form-group col-lg-6">
                                                <label>@lang('Contact')</label>
                                                <input type="text" class="form-control" name="receiver_phone"
                                                    id="receiver_phone" value="{{ old('receiver_phone') }}" readonly
                                                    required>
                                            </div>
                                        </div>
                                        <div class="row">

                                            <div class="form-group col-lg-12">
                                                <label>@lang('Email')</label>
                                                <input type="email" class="form-control" name="receiver_email"
                                                    id="receiver_email" value="{{ old('receiver_email') }}" readonly
                                                    required>
                                            </div>
                                            <div class="form-group col-lg-12">
                                                <label>@lang('Adresse')</label>
                                                <input type="text" class="form-control" name="receiver_address"
                                                    id="receiver_address" value="{{ old('receiver_address') }}" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="card border--primary mt-3">
                                    <h5 class="card-header bg--primary  text-white">@lang('Information Transporteur')</h5>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="form-group col-lg-6">
                                                <?php echo Form::label(__('Entreprise'), null, ['class' => 'control-label']); ?>
                                                <div class="input-group mb-3">
                                                    <?php echo Form::select('entreprise_id', $entreprises, null, ['placeholder' => __('Selectionner une option'), 'class' => 'form-control', 'id' => 'entreprise', 'required' => 'required']); ?>
                                                    <button type="button"
                                                        class="btn btn-outline-secondary border-grey add-entreprise"
                                                        data-toggle="tooltip"
                                                        data-original-title="Ajouter un transporteur"><i
                                                            class="las la-plus"></i></button>
                                                </div>

                                            </div>

                                            <div class="form-group col-lg-6">
                                                <?php echo Form::label(__('Transporteur'), null, ['class' => 'control-label']); ?>
                                                <div class="input-group mb-3">
                                                    <select class="form-control" name="sender_transporteur"
                                                        id="sender_transporteur" required>
                                                        <option value="">@lang('Selectionner une option')</option>
                                                        @foreach ($transporteurs as $transporteur)
                                                            <option value="{{ $transporteur->id }}"
                                                                data-chained="{{ $transporteur->entreprise_id }}"
                                                                @selected(old('transporteur'))>
                                                                {{ $transporteur->nom }} {{ $transporteur->prenoms }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <button type="button"
                                                        class="btn btn-outline-secondary border-grey add-transporteur"
                                                        data-toggle="tooltip"
                                                        data-original-title="Ajouter un transporteur"><i
                                                            class="las la-plus"></i></button>
                                                </div>
                                            </div>

                                            <div class="form-group col-lg-6">
                                                <label>@lang('Selectionner un Véhicule')</label>
                                                <select class="form-control" name="sender_vehicule" id="sender_vehicule"
                                                    required>
                                                    <option value>@lang('Selectionner une option')</option>
                                                    @foreach ($vehicules as $vehicule)
                                                        <option value="{{ $vehicule->id }}" @selected(old('sender_vehicule') == $vehicule->id)>
                                                            {{ __($vehicule->marque->nom) }}({{ __($vehicule->vehicule_immat) }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="form-group col-lg-6">
                                                <label>@lang('Selectionner un Remorque')</label>
                                                <select class="form-control" name="sender_remorque" id="sender_remorque"
                                                    required>
                                                    <option value>@lang('Selectionner une option')</option>
                                                    @foreach ($remorques as $remorque)
                                                        <option value="{{ $remorque->id }}" @selected(old('sender_remorque') == $remorque->id)>
                                                            {{ __($remorque->remorque_immat) }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                        </div>


                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-30">
                            <div class="col-lg-12">
                                <div class="card border--primary mt-3">
                                    <h5 class="card-header bg--primary text-white">@lang('Information de Livraison')
                                    </h5>
                                    <div class="card-body">
                                        <div class="row" id="">
                                            <div class="form-group row">
                                                <?php echo Form::label(__('Numéros de lot'), null, ['class' => 'col-sm-2 control-label required']); ?>
                                                <div class="col-xs-12 col-sm-10">
                                                    <?php echo Form::select('connaissement_id[]', [], null, ['class' => 'form-control producteurs select2', 'id' => 'producteurs', 'required', 'multiple']); ?>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <?php echo Form::label(null, null, ['class' => 'col-sm-2 control-label']); ?>
                                                <div class="col-xs-12 col-sm-10">
                                                    <table class="table table-striped table-bordered">
                                                        <thead>
                                                            <tr>
                                                                <th colspan="2">@lang('Producteur')</th>
                                                                <th>@lang('Type')</th>
                                                                <th>@lang('Quantité(Kg)')</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="listeprod">

                                                        </tbody>

                                                    </table>
                                                </div>
                                            </div>

                                        </div>
                                        <div class="border-line-area">
                                            <h6 class="border-line-title">@lang('Resume')</h6>
                                        </div>


                                        <div class=" d-flex justify-content-end mt-2">
                                            <div class="col-md-5 d-flex justify-content-between">
                                                <span class="fw-bold">@lang('Poids(Kg)'):</span>
                                                <div> <input type="number" name="poidsnet" id="poidsnet"
                                                        class="form-control" readonly required /></div>
                                            </div>

                                        </div>
                                        <div class=" d-flex justify-content-end mt-2">
                                            <div class="col-md-5 d-flex justify-content-between">
                                                <span class="fw-bold">@lang('Nombre de sacs'):</span>
                                                <div> <input type="number" name="nombresacs" id="nombresacs"
                                                        class="form-control" required /></div>
                                            </div>

                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>


                        <button type="submit" class="btn btn--primary mt-25 h-45 w-100 Submitbtn">
                            @lang('Envoyer')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    </div>
@endsection

@push('script')
    <script type="text/javascript">
        $("#periode").chained("#campagne");
    </script>
@endpush

@push('script')
    <script src="{{ asset('assets/fcadmin/js/vendor/datepicker.min.js') }}"></script>
    <script src="{{ asset('assets/fcadmin/js/vendor/datepicker.en.js') }}"></script>
    <script src="{{ asset('assets/fcadmin/js/vendor/datepicker.fr.js') }}"></script>
    <script src="{{ asset('assets/fcadmin/js/tokens.js') }}"></script>
    <script>
        "use strict";
        $('#producteurs').select2();
        $(".scelleOld").select2({
            tags: true
        });
        $("#sender_transporteur").chained("#entreprise");

        function getReceiver() {
            let name = $("#magasin_central").find(':selected').data('name');
            let phone = $("#magasin_central").find(':selected').data('phone');
            let email = $("#magasin_central").find(':selected').data('email');
            let adresse = $("#magasin_central").find(':selected').data('adresse');
            $('#receiver_name').val(name);
            $('#receiver_phone').val(phone);
            $('#receiver_email').val(email);
            $('#receiver_address').val(adresse);
        }

        function getDriver() {
            let name = $("#sender_transporteur").find(':selected').data('name');
            let phone = $("#sender_transporteur").find(':selected').data('phone');
            let email = $("#sender_transporteur").find(':selected').data('email');
            $('#transporteur_name').val(name);
            $('#transporteur_phone').val(phone);
            $('#transporteur_email').val(email);
        }

        function getSender() {
            let idmag = $("#sender_magasin").find(':selected').data('id');
            let name = $("#sender_magasin").find(':selected').data('name');
            let phone = $("#sender_magasin").find(':selected').data('phone');
            let email = $("#sender_magasin").find(':selected').data('email');
            let adresse = $("#sender_magasin").find(':selected').data('adresse');
            $('#sender_name').val(name);
            $('#sender_phone').val(phone);
            $('#sender_email').val(email);
            $('#sender_address').val(adresse);

        }
        $('body').on('click', '.add-transporteur', function() {
            var url = "{{ route('manager.settings.transporteurModal.index') }}";

            $(MODAL_XL + ' ' + MODAL_HEADING).html('...');
            $.ajaxModal(MODAL_XL, url);
            $(MODAL_XL).modal('show');
        });
        $('body').on('click', '.add-entreprise', function() {
            var url = "{{ route('settings.entrepriseModal.index') }}";

            $(MODAL_XL + ' ' + MODAL_HEADING).html('...');
            $.ajaxModal(MODAL_XL, url);
            $(MODAL_XL).modal('show');
        });
        $('#magasin_central,#type,#certificat').change('keyup change blur', function() {
            $('#producteurs').html('');
            var typecert = $('#type').val();
            if(typecert=='Ordinaire'){
            $("#certificat").attr('hidden', 'hidden');
            $(".certif").attr('hidden', 'hidden');
        }else{
            $("#certificat").removeAttr('hidden');
            $(".certif").removeAttr('hidden');
        }
            $.ajax({
                type: 'GET',
                url: "{{ route('manager.livraison.magcentral.get.producteur') }}",
                data: $('#flocal').serialize(),
                success: function(html) {

                    $('#producteurs').html(html);
                    $('#listeprod').html('');

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

        function getproducteur() {

            $.ajax({
                type: 'GET',
                url: "{{ route('manager.livraison.magcentral.get.listeproducteur') }}",
                data: $('#flocal').serialize(),
                success: function(html) {
                    $('#listeprod').html(html.results);
                    $('#poidsnet').val(html.total);
                }
            });
        }
        $('#flocal').change('keyup change blur', function() {
            update_amounts();
        });

        function update_amounts() {
            var sum = 0;
            var sumsacs = 0;

            $('#listeprod > tr').each(function() {

                var qty = $(this).find('.quantity').val();
                var qtysacs = $(this).find('.nbsacs').val();
                sum = parseFloat(sum) + parseFloat(qty);
                sumsacs = parseFloat(sumsacs) + parseFloat(qtysacs);

            });
            $('#poidsnet').val(sum);
            /*$('#nombresacs').val(sumsacs);
            $("#nombresacs").attr({
                "max": sumsacs,
                "min": 0
            }); */
        }

        $('.dates').datepicker({
            language: 'fr',
            dateFormat: 'yyyy-mm-dd'
            //minDate: new Date()
        });
    </script>
@endpush

@push('style')
    <style>
        .border-line-area {
            position: relative;
            text-align: center;
            z-index: 1;
        }

        .border-line-area::before {
            position: absolute;
            content: '';
            top: 50%;
            left: 0;
            width: 100%;
            height: 1px;
            background-color: #e5e5e5;
            z-index: -1;
        }

        .border-line-title {
            display: inline-block;
            padding: 3px 10px;
            background-color: #fff;
        }
    </style>
@endpush
