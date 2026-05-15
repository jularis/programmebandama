@extends('manager.layouts.app')
@section('panel')
<link  rel="stylesheet" href="{{asset('assets/fcadmin/css/vendor/datepicker.min.css')}}">
<link  rel="stylesheet" href="{{asset('assets/fcadmin/css/tokens.css')}}">
<div class="row mb-none-30">
    <div class="col-lg-12 col-md-12 mb-30">
        <div class="card">
            <form action="{{route('manager.livraison.store')}}" method="POST">
                <div class="card-body">
                    @csrf
                    <div class="row">
                        <div class="col-lg-4 form-group">
                            <label for="">@lang("Date de livraison")</label>
                            <div class="input-group">
                                <input name="estimate_date" value="{{ old('estimate_date') }}" type="text" autocomplete="off"  class="form-control dates" placeholder="Date de livraison" required>
                                <span class="input-group-text"><i class="las la-calendar"></i></span>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <label for="">@lang("Campagne")</label>
                                <select class="form-control" id="campagne" name="campagne">
                                    <option value="">@lang('Selectionner une campagne')</option>
                                    @foreach ($allcampagnes as $campagne)
                                        <option value="{{ $campagne->id }}"  @selected(old('campagne')==$campagne->id)>
                                            {{ __($campagne->nom) }}</option>
                                    @endforeach
                                </select>
                            </div>
<div class="col-lg-4">
                            <label for="">@lang("Période")</label>
                                <select class="form-control" id="periode" name="periode">
                                    <option value="">@lang('Selectionner une periode')</option>
                                    @foreach ($allperiodes as $periode)
                                        <option value="{{ $periode->id }}"  @selected(old('periode')==$periode->id)
                                            data-chained="{{ $periode->campagne_id}}"
                                            >
                                            {{ __($periode->nom) }}</option>
                                    @endforeach
                                </select>
                            </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-6">
                            <div class="card border--primary mt-3">
                                <h5 class="card-header bg--primary  text-white">@lang('Information Expéditeur(Délégué)')</h5>
                                <div class="card-body">
                                    <div class="row">
                                    <div class="form-group col-lg-12">
                                            <label>@lang('Selectionner un staff')</label>
                                            <select class="form-control" name="sender_staff" id="sender_staff" onchange="getSender()" required>
                                                <option value>@lang('Selectionner une option')</option>
                                                @foreach($staffs as $staff)
                                                <option value="{{$staff->id}}"
                                                data-name ="{{$staff->lastname}} {{$staff->firstname}}"
                                                data-phone ="{{$staff->mobile}}"
                                                data-email ="{{$staff->email}}"
                                                data-adresse ="{{$staff->adresse}}"
                                                @selected(old('sender_staff')==$staff->id)>{{__($staff->lastname)}} {{__($staff->firstname)}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group col-lg-6">
                                            <label>@lang('Nom')</label>
                                            <input type="text" class="form-control" name="sender_name"
                                            id="sender_name"
                                                value="{{old('sender_name')}}" readonly required>
                                        </div>
                                        <div class=" form-group col-lg-6">
                                            <label>@lang('Contact')</label>
                                            <input type="text" class="form-control" value="{{old('sender_phone')}}"
                                                name="sender_phone"
                                                id="sender_phone"
                                                readonly required>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-lg-12">
                                            <label>@lang('Email')</label>
                                            <input type="email" class="form-control" name="sender_email"
                                            id="sender_email"
                                                value="{{old('sender_email')}}" readonly required>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-lg-12">
                                            <label>@lang('Adresse')</label>
                                            <input type="text" class="form-control" name="sender_address"
                                            id="sender_address"
                                                value="{{old('sender_address')}}" readonly >
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="card border--primary mt-3">
                                <h5 class="card-header bg--primary  text-white">@lang('Information Destinataire(Magasin de Section)')</h5>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="form-group col-lg-12">
                                            <label>@lang('Selectionner un Magasin de section')</label>
                                            <select class="form-control" name="magasin_section" id="magasin_section" onchange="getReceiver()" required>
                                                <option value>@lang('Selectionner une option')</option>
                                                @foreach($magasins as $magasin)
                                                <option value="{{$magasin->id}}"
                                                data-name ="{{$magasin->user->lastname}} {{$magasin->user->firstname}}"
                                                data-phone ="{{$magasin->user->mobile}}"
                                                data-email ="{{$magasin->user->email}}"
                                                data-adresse ="{{$magasin->user->adresse}}"
                                                @selected(old('magasin_section')==$magasin->id)>{{__($magasin->nom)}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group col-lg-6">
                                            <label>@lang('Nom')</label>
                                            <input type="text" class="form-control" name="receiver_name"
                                            id="receiver_name"
                                                value="{{old('receiver_name')}}" readonly required>
                                        </div>
                                        <div class="form-group col-lg-6">
                                            <label>@lang('Contact')</label>
                                            <input type="text" class="form-control" name="receiver_phone"
                                            id="receiver_phone"
                                                value="{{old('receiver_phone')}}" readonly required>
                                        </div>
                                    </div>
                                    <div class="row">

                                        <div class="form-group col-lg-12">
                                            <label>@lang('Email')</label>
                                            <input type="email" class="form-control" name="receiver_email"
                                            id="receiver_email"
                                                value="{{old('receiver_email')}}" readonly required>
                                        </div>
                                        <div class="form-group col-lg-12">
                                            <label>@lang('Adresse')</label>
                                            <input type="text" class="form-control" name="receiver_address"
                                            id="receiver_address"
                                                value="{{old('receiver_address')}}" readonly
                                                >
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
                                    <button type="button" class="btn btn-sm btn-outline-light float-end addUserData"><i
                                            class="la la-fw la-plus"></i>@lang('Ajouter un nouveau élément')
                                    </button>
                                </h5>
                                <div class="card-body">
                                    <div class="row" id="addedField">

                                    </div>
                                    <div class="border-line-area">
                                        <h6 class="border-line-title">@lang('Resume')</h6>
                                    </div>


                                    <div class=" d-flex justify-content-end mt-2">
                                        <div class="col-md-3  d-flex justify-content-between">
                                            <span class="fw-bold">@lang('Total'):</span>
                                            <div> <span class="total">0</span> {{$general->cur_sym}}</div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>


                    <button type="submit" class="btn btn--primary mt-25 h-45 w-100 Submitbtn"> @lang('Envoyer')</button>
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
<script src="{{asset('assets/fcadmin/js/vendor/datepicker.min.js')}}"></script>
<script src="{{asset('assets/fcadmin/js/vendor/datepicker.en.js')}}"></script>
<script src="{{asset('assets/fcadmin/js/tokens.js')}}"></script>
<script>
    "use strict";
    $(".scelleOld").select2({tags: true });

    function getReceiver() {
        let name = $("#magasin_section").find(':selected').data('name');
        let phone = $("#magasin_section").find(':selected').data('phone');
        let email = $("#magasin_section").find(':selected').data('email');
        let adresse = $("#magasin_section").find(':selected').data('adresse');
        $('#receiver_name').val(name);
        $('#receiver_phone').val(phone);
        $('#receiver_email').val(email);
        $('#receiver_address').val(adresse);
    }
    function getSender() {
        let name = $("#sender_staff").find(':selected').data('name');
        let phone = $("#sender_staff").find(':selected').data('phone');
        let email = $("#sender_staff").find(':selected').data('email');
        let adresse = $("#sender_staff").find(':selected').data('adresse');
        $('#sender_name').val(name);
        $('#sender_phone').val(phone);
        $('#sender_email').val(email);
        $('#sender_address').val(adresse);
    }
    function getCertificat(id){
        let type = $("#type-" + id).find(':selected').val();
        if(type=='Ordinaire'){
            $("#certificat-" + id).attr('hidden', 'hidden');
        }else{
            $("#certificat-" + id).removeAttr('hidden');

        }

    }
    function getParcelle(id){

        let prod = $("#producteur-" + id).find(':selected').data('id');

        $.ajax({
                type:'get',
                url: "{{ route('manager.livraison.get.parcelle') }}",
                data: {
                    'id': prod
                },
                success:function(html){
                  if(html)
                  {
                  $("#parcelle-"+ id).html(html);
                  }else{
                    $("#parcelle-"+ id).html('<option disabled selected value="">Parcelle</option>');
                  }

                }
            });

            $.ajax({
                type:'get',
                url: "{{ route('manager.livraison.get.certificat') }}",
                data: {
                    'id': prod
                },
                success:function(html){
                  if(html)
                  {
                  $("#certificat-"+ id).html(html);
                  $("#type-"+ id).html('<option value="Certifie">Certifie</option><option value="Ordinaire">Ordinaire</option>');
                  }else{
                    $("#certificat-"+ id).html('<option disabled selected value="">Certificat</option>');
                    $("#type-"+ id).html('<option value="Ordinaire">Ordinaire</option>');
                  }

                }
            });

    }

    function getProducteur(){

        let prod = $("#statut").val();

        $.ajax({
                type:'get',
                url: "{{ route('manager.livraison.get.producteur') }}",
                data: {
                    'id': prod
                },
                success:function(html){
                  if(html)
                  {
                  $(".producteur").html(html);
                  }else{
                    $(".producteur").html('<option disabled selected value="">Producteur</option>');
                  }

                }
            });

    }

    (function ($) {


        $('.addUserData').on('click', function () {

            let count = $("#addedField select").length;
            let length=$("#addedField").find('.single-item').length;

            let html = `
            <div class="row single-item gy-2">
                <div class="col-md-3">
                    <select class="form-control selected_type " name="items[${length}][producteur]" required id='producteur-${length}' onchange=getParcelle(${length})>
                        <option disabled selected value="">@lang('Producteur')</option>
                        @foreach($producteurs as $producteur)
                            <option value="{{$producteur->id}}" data-id="{{$producteur->id}}" data-price="{{ $periode->prix_champ}} ?? 0 ">{{__(stripslashes($producteur->nom))}} {{__(stripslashes($producteur->prenoms))}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <select class="form-control" name="items[${length}][parcelle]" required id="parcelle-${length}">

                    </select>
                </div>
                <div class="col-md-2">
                <select class="form-control" name="items[${length}][type]" id="type-${length}" required  onchange=getCertificat(${length})>

                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-control" name="items[${length}][certificat]" id="certificat-${length}">

                    </select>
                </div>
                <div class="col-md-2">
                    <div class="input-group mb-3">
                        <input type="number" class="form-control quantity" placeholder="@lang('Qte')" disabled name="items[${length}][quantity]"  required>
                        <span class="input-group-text unit">Kg</span>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="input-group">
                        <input type="text"  class="form-control single-item-amount" placeholder="@lang('Entrer le prix')" name="items[${length}][amount]" required readonly>
                        <span class="input-group-text">{{__($general->cur_text)}}</span>
                    </div>
                </div>
                <div class="col-md-1">
                    <button class="btn btn--danger w-100 removeBtn w-100 h-45" type="button">
                        <i class="fa fa-times"></i>
                    </button>
                </div>
                <br><hr class="panel-wide">
            </div>`;
            $('#addedField').append(html,$('.scelle').tokens())
        });

        $('#addedField').on('change', '.selected_type', function (e) {
            let unit = $(this).find('option:selected').data('unit');
            let parent = $(this).closest('.single-item');
            $(parent).find('.quantity').attr('disabled', false);
            $(parent).find('.unit').html(`${unit || 'Kg'}`);
            calculation();
        });

        $('#addedField').on('click', '.removeBtn', function (e) {
            let length=$("#addedField").find('.single-item').length;
            if(length <= 1){
                notify('warning',"@lang('Au moins un élément est requis')");
            }else{
                $(this).closest('.single-item').remove();
            }
            calculation();
        });

        let discount=0;

        $('.discount').on('input',function (e) {
            this.value = this.value.replace(/^\.|[^\d\.]/g, '');

             discount=parseFloat($(this).val() || 0);
             if(discount >=100){
                discount=100;
                notify('warning',"@lang('La réduction ne peut être supérieure à 100 %')");
                $(this).val(discount);
             }
            calculation();
        });

        $('#addedField').on('input', '.quantity', function (e) {
            this.value = this.value.replace(/^\.|[^\d\.]/g, '');

            let quantity = $(this).val();
            if (quantity <= 0) {
                quantity = 0;
            }
            quantity=parseFloat(quantity);

            let parent   = $(this).closest('.single-item');
            let price    = parseFloat($(parent).find('.selected_type option:selected').data('price') || 0);
            let subTotal = price*quantity;

            $(parent).find('.single-item-amount').val(subTotal.toFixed(0));

            calculation()
        });

        function calculation ( ) {
            let items    = $('#addedField').find('.single-item');
            let subTotal = 0;

            $.each(items, function (i, item) {
                let price = parseFloat($(item).find('.selected_type option:selected').data('price') || 0);
                let quantity = parseFloat($(item).find('.quantity').val() || 0);
                subTotal+=price*quantity;
            });

            // subTotal=parseFloat(subTotal);

            // let discountAmount = (subTotal/100)*discount;
            // let total          = subTotal-discountAmount;

            // $('.subtotal').text(subTotal.toFixed(0));
            // $('.total').text(total.toFixed(0));
            $('.total').text(subTotal.toFixed(0));
        };

        $('.dates').datepicker({
            language  : 'en',
            dateFormat: 'yyyy-mm-dd',
            maxDate   : new Date()
        });

        @if(old('items'))
            calculation();
        @endif

    })(jQuery);

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
