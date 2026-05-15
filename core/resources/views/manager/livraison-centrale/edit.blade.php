@extends('manager.layouts.app')
@section('panel')
    <div class="row mb-none-30">
        <div class="col-lg-12 col-md-12 mb-30">
            <div class="card">
                <form action="{{ route('manager.livraison.update', encrypt($livraisonInfo->id)) }}" method="POST">
                    <div class="card-body">
                        @csrf
                        <div class="row">
                            <div class="col-6 form-group">
                                <label for="">@lang('Date estimée de livraison')</label>
                                <div class="input-group">
                                    <input name="estimate_date"
                                        value="{{ showDateTime($livraisonInfo->estimate_date, 'Y-m-d') }}" type="text"
                                        autocomplete="off" class="form-control date" placeholder="Date estimée de livraison"
                                        required>
                                    <span class="input-group-text"><i class="las la-calendar"></i></span>
                                </div>
                            </div>
                            <div class="col-6 form-group">
                                <label for="">@lang('Paiement Status')</label>
                                <div class="input-group">
                                    <select class="form-control" required name="payment_status">
                                        <option value="0" @selected($livraisonInfo->payment->status == 0)>@lang('IMPAYE')</option>
                                        <option value="1" @selected($livraisonInfo->payment->status == 1)>@lang('PAYE')</option>
                                    </select>
                                    <span class="input-group-text"><i class="las la-money-bill-wave-alt"></i></span>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-6">
                                <div class="card border--primary mt-3">
                                    <h5 class="card-header bg--primary  text-white">@lang('Information Expéditeur')</h5>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="form-group col-lg-12">
                                                <label>@lang('Selectionner un staff')</label>
                                                <select class="form-control" name="sender_staff" id="sender_staff"
                                                    onchange="getSender()" required>
                                                    <option value>@lang('Selectionner une option')</option>
                                                    @foreach ($staffs as $staff)
                                                        <option value="{{ $staff->id }}"
                                                            data-name ="{{ $staff->lastname }} {{ $staff->firstname }}"
                                                            data-phone ="{{ $staff->mobile }}"
                                                            data-email ="{{ $staff->email }}"
                                                            data-adresse ="{{ $staff->adresse }}"
                                                            @selected($livraisonInfo->sender_staff_id == $staff->id)>{{ __($staff->lastname) }}
                                                            {{ __($staff->firstname) }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group col-lg-6">
                                                <label>@lang('Nom')</label>
                                                <input type="text" class="form-control" name="sender_name"
                                                    value="{{ $livraisonInfo->sender_name }}" id="sender_name" required>
                                            </div>
                                            <div class=" form-group col-lg-6">
                                                <label>@lang('Contact')</label>
                                                <input type="text" class="form-control"
                                                    value="{{ $livraisonInfo->sender_phone }}" name="sender_phone"
                                                    id="sender_phone" required>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-lg-12">
                                                <label>@lang('Email')</label>
                                                <input type="email" class="form-control" name="sender_email"
                                                    id="sender_email" value="{{ $livraisonInfo->sender_email }}" required>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-lg-12">
                                                <label>@lang('Adresse')</label>
                                                <input type="text" class="form-control" name="sender_address"
                                                    value="{{ $livraisonInfo->sender_address }}" id="sender_address"
                                                    required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="card border--primary mt-3">
                                    <h5 class="card-header bg--primary  text-white">@lang('Information Destinataire')</h5>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="form-group col-lg-12">
                                                <label>@lang('Selectionner un Magasin de section')</label>
                                                <select class="form-control" name="magasin_section" id="magasin_section"
                                                    onchange="getReceiver()" required>
                                                    <option value>@lang('Selectionner une option')</option>
                                                    @foreach ($magasins as $magasin)
                                                        <option value="{{ $magasin->id }}"
                                                            data-name ="{{ $magasin->user->lastname }} {{ $magasin->user->firstname }}"
                                                            data-phone ="{{ $magasin->user->mobile }}"
                                                            data-email ="{{ $magasin->user->email }}"
                                                            data-adresse ="{{ $magasin->user->adresse }}"
                                                            @selected($livraisonInfo->receiver_magasin_section_id == $magasin->id)>{{ __($magasin->nom) }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group col-lg-6">
                                                <label>@lang('Nom')</label>
                                                <input type="text" class="form-control" name="receiver_name"
                                                    id="receiver_name" value="{{ $livraisonInfo->receiver_name }}"
                                                    required>
                                            </div>
                                            <div class="form-group col-lg-6">
                                                <label>@lang('Contact')</label>
                                                <input type="text" class="form-control" name="receiver_phone"
                                                    id="receiver_phone" value="{{ $livraisonInfo->receiver_phone }}"
                                                    required>
                                            </div>
                                        </div>
                                        <div class="row">

                                            <div class="form-group col-lg-12">
                                                <label>@lang('Email')</label>
                                                <input type="email" class="form-control" name="receiver_email"
                                                    id="receiver_email" value="{{ $livraisonInfo->receiver_email }}"
                                                    required>
                                            </div>
                                            <div class="form-group col-lg-12">
                                                <label>@lang('Adresse')</label>
                                                <input type="text" class="form-control" name="receiver_address"
                                                    id="receiver_address" value="{{ $livraisonInfo->receiver_address }}"
                                                    required>
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
                                        <button type="button"
                                            class="btn btn-sm btn-outline-light float-end addUserData"><i
                                                class="la la-fw la-plus"></i>@lang('Ajouter un nouveau élément')
                                        </button>
                                    </h5>
                                    <div class="card-body">
                                        <div class="row" id="addedField">
                                            <?php $i = 0; ?>
                                            @foreach ($livraisonInfo->products as $item)
                                                <div class="row single-item gy-2">
                                                    <div class="col-md-3">
                                                        <select class="form-control selected_type"
                                                            name="items[{{ $loop->index }}][producteur]"
                                                            id='producteur-<?php echo $i; ?>'
                                                            onchange=getParcelle(<?php echo $i; ?>) required>
                                                            <option disabled selected value="">@lang('Producteurs')
                                                            </option>
                                                            @foreach ($producteurs as $producteur)
                                                                <option value="{{ $producteur->id }}"
                                                                    data-id="{{ $producteur->id }}"
                                                                    data-price={{ getAmount($campagne->prix_achat) }}
                                                                    @selected($item->parcelle->producteur->id == $producteur->id)>
                                                                    {{ __(stripslashes($producteur->nom)) }}
                                                                    {{ __($producteur->prenom) }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <select class="form-control selected_type"
                                                            name="items[{{ $loop->index }}][parcelle]"
                                                            id="parcelle-<?php echo $i; ?>" required>
                                                            <option disabled selected value="">@lang('Parcelles')
                                                            </option>
                                                            @foreach ($parcelles as $parcelle)
                                                                <option value="{{ $parcelle->id }}"
                                                                    @selected($item->parcelle->id == $parcelle->id)>{{ __('Parcelle') }}
                                                                    {{ __($parcelle->codeParc) }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <select class="form-control selected_type"
                                                            name="items[{{ $loop->index }}][type]" required>
                                                            <option disabled selected value="">@lang('Type')
                                                            </option>
                                                            <option value="{{ __('Certifie') }}"
                                                                @selected($item->type_produit == 'Certifie')>{{ __('Certifie') }}</option>
                                                            <option value="{{ __('Ordinaire') }}"
                                                                @selected($item->type_produit == 'Ordinaire')>{{ __('Ordinaire') }}</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="input-group mb-3">
                                                            <input type="number" class="form-control quantity"
                                                                value="{{ $item->qty }}"
                                                                name="items[{{ $loop->index }}][quantity]" required>
                                                            <span class="input-group-text unit"><i
                                                                    class="las la-balance-scale"></i></span>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-7">
                                                        <select class="form-control scelleOld"
                                                            name="items[{{ $loop->index }}][scelle][]" multiple>

                                                            @foreach (\App\Models\LivraisonScelle::where([['livraison_info_id', $livraisonInfo->id], ['parcelle_id', $item->parcelle_id], ['type_produit', $item->type_produit]])->get() as $option)
                                                                <option value="{{ $option->numero_scelle }}" selected>
                                                                    {{ __($option->numero_scelle) }}</option>
                                                            @endforeach

                                                        </select>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="input-group">
                                                            <input type="text" class="form-control single-item-amount"
                                                                value="{{ $item->fee }}"
                                                                name="items[{{ $loop->index }}][amount]" required
                                                                readonly>
                                                            <span
                                                                class="input-group-text">{{ __($general->cur_text) }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-1">
                                                        <button class="btn btn--danger w-100 removeBtn w-100 h-45"
                                                            type="button">
                                                            <i class="fa fa-times"></i>
                                                        </button>
                                                    </div>
                                                    <br>
                                                    <hr class="panel-wide">
                                                </div>
                                                <?php $i++; ?>
                                            @endforeach
                                        </div>
                                        <div class="border-line-area">
                                            <h6 class="border-line-title">@lang('Resume')</h6>
                                        </div>
                                        <div class="d-flex justify-content-end">
                                            <div class="col-md-3">
                                                <div class="input-group">
                                                    <span class="input-group-text">@lang('Reduction')</span>
                                                    <input type="number" name="discount"
                                                        class="form-control bg-white text-dark discount"
                                                        value="{{ $livraisonInfo->payment->percentage }}">
                                                    <span class="input-group-text">%</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class=" d-flex justify-content-end mt-2">
                                            <div class="col-md-3  d-flex justify-content-between">
                                                <span class="fw-bold">@lang('Sous-total'):</span>
                                                <div><span
                                                        class="subtotal">{{ showAmount(@$livraisonInfo->payment->amount) }}</span>
                                                    {{ $general->cur_sym }}</div>
                                            </div>
                                        </div>
                                        <div class=" d-flex justify-content-end mt-2">
                                            <div class="col-md-3  d-flex justify-content-between">
                                                <span class="fw-bold">@lang('Total'):</span>
                                                <div><span
                                                        class="total">{{ showAmount(@$livraisonInfo->payment->final_amount) }}</span>
                                                    {{ $general->cur_sym }}</div>
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
@push('breadcrumb-plugins')
    <x-back route="{{ route('manager.livraison.sent.queue') }}" />
@endpush
@push('script')
    <script src="{{ asset('assets/fcadmin/js/vendor/datepicker.min.js') }}"></script>
    <script src="{{ asset('assets/fcadmin/js/vendor/datepicker.en.js') }}"></script>
    <script src="{{ asset('assets/fcadmin/js/tokens.js') }}"></script>
@endpush

@push('style-lib')
    <link rel="stylesheet" href="{{ asset('assets/fcadmin/css/vendor/datepicker.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/fcadmin/css/tokens.css') }}">
@endpush

@push('script')
    <script>
        "use strict";
        $(".scelleOld").select2({
            tags: true
        });

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

        function getParcelle(id) {

            let prod = $("#producteur-" + id).find(':selected').data('id');

            $.ajax({
                type: 'get',
                url: "{{ route('manager.livraison.get.parcelle') }}",
                data: {
                    'id': prod
                },
                success: function(html) {
                    if (html) {
                        $("#parcelle-" + id).html(html);
                    } else {
                        $("#parcelle-" + id).html('<option disabled selected value="">Parcelle</option>');
                    }

                }
            });

        }


        (function($) {

            $('.addUserData').on('click', function() {
                let count = $("#addedField select").length;
                let length = $("#addedField").find('.single-item').length;
                let html = `
            <div class="row single-item gy-2">
                <div class="col-md-3">
                    <select class="form-control selected_type" name="items[${length}][producteur]" required id='producteur-${length}' onchange=getParcelle(${length})>
                        <option disabled selected value="">@lang('Producteur')</option>
                        @foreach ($producteurs as $producteur)
                            <option value="{{ $producteur->id }}" data-id="{{ $producteur->id }}" data-price={{ getAmount($campagne->prix_achat) }} >{{ __(stripslashes($producteur->nom)) }} {{ __(stripslashes($producteur->prenoms)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-control" name="items[${length}][parcelle]" required id="parcelle-${length}">
                        
                    </select>
                </div>
                <div class="col-md-3">
                <select class="form-control" name="items[${length}][type]" required>
                        <option disabled selected value="">@lang('Type')</option> 
                            <option value="{{ __('Certifie') }}">{{ __('Certifie') }}</option>
                            <option value="{{ __('Ordinaire') }}">{{ __('Ordinaire') }}</option>
                    </select> 
                </div>
                <div class="col-md-3">
                    <div class="input-group mb-3">
                        <input type="number" class="form-control quantity" placeholder="@lang('Qte')" disabled name="items[${length}][quantity]"  required>
                        <span class="input-group-text unit"><i class="las la-balance-scale"></i></span>
                    </div>
                </div>
                <div class="col-md-7">
                <input  type="text" class="form-control scelle" name="items[${length}][scelle][]" placeholder="@lang('Numéros du Scellé')">  
                </div>
                <div class="col-md-4">
                    <div class="input-group">
                        <input type="text"  class="form-control single-item-amount" placeholder="@lang('Entrer le Prix')" name="items[${length}][amount]" required readonly>
                        <span class="input-group-text">{{ __($general->cur_text) }}</span>
                    </div>
                </div>
                <div class="col-md-1">
                    <button class="btn btn--danger w-100 removeBtn w-100 h-45" type="button">
                        <i class="fa fa-times"></i>
                    </button>
                </div>
                <br><hr class="panel-wide">
            </div>`;
                $('#addedField').append(html, $('.scelle').tokens())
            });

            $('#addedField').on('change', '.selected_type', function(e) {
                let unit = $(this).find('option:selected').data('unit');
                let parent = $(this).closest('.single-item');
                $(parent).find('.quantity').attr('disabled', false);
                $(parent).find('.unit').html(`${unit || '<i class="las la-balance-scale"></i>'}`);
                calculation();
            });

            $('#addedField').on('click', '.removeBtn', function(e) {
                let length = $("#addedField").find('.single-item').length;
                if (length <= 1) {
                    notify('warning', "@lang('Au moins un élément est requis')");
                } else {
                    $(this).closest('.single-item').remove();
                }
                $('.discount').trigger('change');
                calculation();
            });

            let discount = 0;

            $('.discount').on('input change', function(e) {
                this.value = this.value.replace(/^\.|[^\d\.]/g, '');
                discount = parseFloat($(this).val() || 0);
                if (discount >= 100) {
                    discount = 100;
                    notify('warning', "@lang('La réduction ne peut être supérieure à 100 %')");
                    $(this).val(discount);
                }
                calculation();
            });

            $('#addedField').on('input', '.quantity', function(e) {
                this.value = this.value.replace(/^\.|[^\d\.]/g, '');

                let quantity = $(this).val();
                if (quantity <= 0) {
                    quantity = 0;
                }
                quantity = parseFloat(quantity);

                let parent = $(this).closest('.single-item');
                let price = parseFloat($(parent).find('.selected_type option:selected').data('price') || 0);
                let subTotal = price * quantity;

                $(parent).find('.single-item-amount').val(subTotal.toFixed(0));


                calculation()
            });

            function calculation() {
                let items = $('#addedField').find('.single-item');
                let subTotal = 0;

                $.each(items, function(i, item) {
                    let price = parseFloat($(item).find('.selected_type option:selected').data('price') || 0);
                    let quantity = parseFloat($(item).find('.quantity').val() || 0);
                    subTotal += price * quantity;
                });

                subTotal = parseFloat(subTotal);

                let discountAmount = (subTotal / 100) * discount;
                let total = subTotal - discountAmount;

                $('.subtotal').text(subTotal.toFixed(0));
                $('.total').text(total.toFixed(0));
            };

            $('.dates').datepicker({
                language: 'en',
                dateFormat: 'yyyy-mm-dd',
                minDate: new Date()
            });

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
