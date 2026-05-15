@extends('manager.layouts.app')
@section('panel')
    <?php
    use Illuminate\Support\Arr;
    use App\Models\Certification;
    use App\Models\Programme;
    
    ?>
    <div class="row mb-none-30">
        <div class="card b-radius--10 mb-3">
            <div class="card-body">
                <form action="">
                    <div class="d-flex flex-wrap gap-4">

                        <!-- <div class="flex-grow-1">
                                                                                    <label>@lang('Date')</label>
                                                                                    <input name="date" type="text" class="dates form-control"
                                                                                        placeholder="@lang('Date de debut - Date de fin')" autocomplete="off" value="{{ request()->date }}">
                                                                                </div> -->
                        <div class="flex-grow-1">
                            <label>@lang("Annee activite")</label>
                            <select name="date" class="form-control">
                                <option value="">@lang('Selectionner une date')</option>
                                @for ($i = gmdate('Y'); $i >= 2023; $i--)
                                    <option value="{{ $i }}" {{ request()->date == $i ? 'selected' : '' }}>
                                        {{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="flex-grow-1 align-self-end">
                            <button class="btn btn--primary w-100 h-45"><i class="fas fa-filter"></i>
                                @lang('Filter')</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <h3>@lang('Evalution des membres')</h3>



                <?php
                $date = request()->date ? '01/01/' . request()->date . '-12/31/' . request()->date : '01/01/' . gmdate('Y') . '-12/31/' . gmdate('Y');
                $certifications = Certification::get();
                $programmes = Programme::get();
                $producteurs = getproducteur($date);
                $sexe = array_count_values(Arr::pluck($producteurs, 'sexe'));
                
                ?>
                <table class="table table-striped table-bordered">

                    <thead class="bg--primary ">
                        <tr>
                            <th class="text-white">@lang('Membre-Adherent')</th>
                            <th class="text-white text-center">{{ request()->date ? request()->date : gmdate('Y') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>@lang('Hommes')</td>
                            <td class="text-center">{{ $sexe['Homme'] }}</td>
                        </tr>
                        <tr>
                            <td>@lang('Femmes')</td>
                            <td class="text-center">{{ $sexe['Femme'] }}</td>
                        </tr>
                        <tr>
                            <td>@lang('Total')</td>
                            <td class="text-center">{{ array_sum(array_values($sexe)) }}</td>
                        </tr>
                    </tbody>
                </table>
                <table class="table table-striped table-bordered">
                    <thead class="bg--primary ">
                        <tr>
                            <th class="text-white">@lang('Membres Ordinaires')</th>
                            <th class="text-white text-center">{{ request()->date ? request()->date : gmdate('Y') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $producteursOrdinaires = getproducteurOrdinaire($date);
                        $sexe = array_count_values(Arr::pluck($producteursOrdinaires, 'sexe'));
                        ?>
                        <tr>
                            <td>@lang('Homme')</td>
                            <td>{{ @$sexe['Homme'] ? @$sexe['Homme'] : 0 }} </td>
                        </tr>
                        <tr>
                            <td>@lang('Femme')</td>
                            <td>{{ @$sexe['Femme'] ? @$sexe['Femme'] : 0 }}</td>
                        </tr>
                        <tr>
                            <td>@lang('Total')</td>
                            <td>{{ array_sum(array_values($sexe)) }}</td>
                        </tr>
                    </tbody>
                </table>
                <table class="table table-striped table-bordered">
                    <thead class="bg--primary ">
                        <tr>
                            <th class="text-white">@lang('Repartition des membres par programme') </th>
                            <th class="text-white text-center">{{ request()->date ? request()->date : gmdate('Y') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($programmes as $res)
                            <tr>
                                <td class="text-center bg--warning" colspan="2">{{ $res->libelle }}</td>
                            </tr>
                            @foreach ($certifications as $data)
                                <?php
                                $programmecert = getProducteurProgramme($date, $data->nom, $res->id);
                                ?>
                                @if ($programmecert->count())
                                    <?php $sexe = array_count_values(Arr::pluck($programmecert, 'sexe')); ?>
                                    <tr>
                                        <td><span class="badge badge-info">Certifiés {{ $data->nom }}</span></td>
                                        <td> </td>
                                    </tr>
                                    <tr>
                                        <td>@ang('Hommes')</td>
                                        <td class="text-center"> {{ @$sexe['Homme'] ? @$sexe['Homme'] : 0 }} </td>
                                    </tr>
                                    <tr>
                                        <td>@lang('Femmes')</td>
                                        <td class="text-center"> {{ @$sexe['Femme'] ? @$sexe['Femme'] : 0 }} </td>
                                    </tr>
                                    <tr>
                                        <td>@lang('Total')</td>
                                        <td class="text-center"> {{ array_sum(array_values($sexe)) }} </td>
                                    </tr>
                                @endif
                            @endforeach
                        @endforeach
                    </tbody>
                </table>


            </div>
            <div class="col-md-6">
                <h3>@lang('Evolution de la production des membres')</h3>


                <?php
                $date = request()->date ? '01/01/' . request()->date . '-12/31/' . request()->date : '01/01/' . gmdate('Y') . '-12/31/' . gmdate('Y');
                $parcelle = getparcelle($date);
                $production = getproduction($date);
                $productionOrdinaire = getproductionOrdinaire($date);
                $productionProgramme = getproductionProgramme($date);
                $productionVente = getvente($date);
                $productionVenteOrdinaire = getventeOrdinaire($date);
                $productionVenteProgramme = getventeProgramme($date);
                $productionAntrePartenaire = getautreProduction($date);
                $chiffreCcb = getChiffreCcb($date);
                ?>

                <table class="table table-striped table-bordered">
                    <thead class="bg--primary">
                        <tr>
                            <th class="text-white">@lang('Cacao')</th>
                            <th class="text-white text-right">{{ request()->date ? request()->date : gmdate('Y') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>@lang('Superficie ')(Ha)</td>
                            <td> {{ @$parcelle ? @$parcelle : 0 }} </td>
                        </tr>
                    </tbody>
                </table>
                <table class="table table-striped table-bordered">
                    <thead class="bg--primary ">
                        <tr>
                            <th class="text-white">@lang('Cacao Production') (Kg) </th>
                            <th class="text-white text-center">{{ request()->date ? request()->date : gmdate('Y') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($certifications as $data)
                            <?php
                            $productioncert = getproductionCertifie($date, $data->nom);
                            ?>
                            @if ($productioncert)
                                <tr>
                                    <td>@lang('Certifie') {{ $data->nom }}</td>
                                    <td>{{ $productioncert }}</td>
                                </tr>
                            @endif
                        @endforeach

                        <tr>
                            <td>@lang('Programme Durabilite') </td>
                            <td>{{ @$productionProgramme ? @$productionProgramme : 0 }}</td>
                        </tr>
                        <tr>
                            <td>@lang('Conventionnel/Ordinaire')</td>
                            <td>{{ @$productionOrdinaire ? @$productionOrdinaire : 0 }}</td>
                        </tr>
                    </tbody>
                </table>
                <table class="table table-striped table-bordered">
                    <thead class="bg--primary ">
                        <tr>
                            <th class="text-white">@lang('Cacao Vendu à CCB') (Kg) </th>
                            <th class="text-white text-center">{{ request()->date ? request()->date : gmdate('Y') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($certifications as $data)
                            <?php
                            $ventecert = getventeCertifie($date, $data->nom);
                            ?>
                            @if ($ventecert)
                                <tr>
                                    <td>@lang('Certifie') {{ $data->nom }}</td>
                                    <td>{{ $ventecert }}</td>
                                </tr>
                            @endif
                        @endforeach

                        <tr>
                            <td>@lang('Programme Durabilite') </td>
                            <td>{{ @$productionVenteProgramme ? @$productionVenteProgramme : 0 }}</td>
                        </tr>
                        <tr>
                            <td>@lang('Conventionnel/Ordinaire')</td>
                            <td>{{ @$productionVenteOrdinaire ? @$productionVenteOrdinaire : 0 }}</td>
                        </tr>
                        <tr>
                            <td>@lang("Chiffre d'affaire (F CFA)")</td>
                            <td><input id="chiffreCcb" type="number" name="chiffreCcb" value="{{ getChiffreCcb($date) }}">
                            </td>
                        </tr>
                    </tbody>
                </table>
                <table class="table table-striped table-bordered">
                    <thead class="bg--primary ">
                        <tr>
                            <th class="text-white">@lang('Cacao Vendu aux autres partenaires') (Kg) </th>
                            <th class="text-white text-center">{{ request()->date ? request()->date : gmdate('Y') }}</th>
                        </tr>
                    </thead>
                    <tbody>

                        <tr>
                            <td>@lang('Cacao') </td>
                            <td> {{ $productionAntrePartenaire }} </td>
                        </tr>
                        <tr>
                            <td>@lang("Chiffre d'affaire") (F CFA)</td>
                            <td><input id="chiffreAutrePartenaire" type="number" name="chiffreAutrePartenaire"
                                    value="{{ getChiffreAutrePartenaire($date) }}"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <x-confirmation-modal />
    </div>
@endsection

@push('style')
    <style type="text/css">
        table {
            border-collapse: collapse;
            margin: 20px 0;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        th,
        td {
            padding: 12px;
            text-align: center;
            border: 1px solid #ddd;
        }

        th {
            background-color: #4caf50;
            color: #fff;
        }

        caption {
            font-size: 1.5em;
            font-weight: bold;
            margin-bottom: 10px;
        }
    </style>
@endpush
@push('style-lib')
    <link rel="stylesheet" href="{{ asset('assets/fcadmin/css/vendor/datepicker.min.css') }}">
@endpush
@push('script')
    <script src="{{ asset('assets/fcadmin/js/vendor/datepicker.min.js') }}"></script>
    <script src="{{ asset('assets/fcadmin/js/vendor/datepicker.fr.js') }}"></script>
    <script src="{{ asset('assets/fcadmin/js/vendor/datepicker.en.js') }}"></script>
@endpush
@push('script')
    <script>
        $('.dates').datepicker({
            maxDate: new Date(),
            range: true,
            multipleDatesSeparator: "-",
            language: 'fr'
        });
        $('form select').on('change', function() {
            $(this).closest('form').submit();
        });

        $('#chiffreCcb').on('blur', function() {
            var chiffreCcb = $(this).val();
            var currentYear = new Date().getFullYear();
            var token = "{{ csrf_token() }}";

            $.ajax({
                url: '{{ route('manager.presentation-coop.store') }}', // Remplacez par l'URL de votre API
                method: 'POST',
                data: { // Remplacez par l'ID de la coopérative
                    '_token': token,
                    montant: chiffreCcb,
                    date: currentYear
                },
                success: function(response) {
                    console.log(response);
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.log(textStatus, errorThrown);
                }
            });
        });
        $('#chiffreAutrePartenaire').on('blur', function() {
            var chiffrePartenaire = $(this).val();
            var currentYear = new Date().getFullYear();
            var token = "{{ csrf_token() }}";
            $.ajax({
                url: '{{ route('manager.presentation-coop.chiffreAffairePartenaire') }}',
                method: 'POST',
                data: {
                    '_token': token,
                    montant: chiffrePartenaire,
                    date: currentYear
                },
                success: function(response) {
                    console.log(response);
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.log(textStatus, errorThrown);
                }
            });
        });
    </script>
@endpush
