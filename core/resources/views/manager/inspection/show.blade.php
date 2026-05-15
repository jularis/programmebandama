@extends('manager.layouts.app')
@section('panel')
    <style type="text/css">
        @page {
            size: landscape;
        }
    </style>
    <div class="row mb-none-30">
        <div class="col-lg-12 mb-30">
            <div class="card">
                <div class="card-body" id="printFacture">
                    {!! Form::model($inspection, [
                        'method' => 'POST',
                        'route' => ['manager.suivi.inspection.suiviStore', $inspection->id],
                        'class' => 'form-horizontal',
                        'id' => 'flocal',
                        'enctype' => 'multipart/form-data',
                    ]) !!}
                    <input type="hidden" name="id" value="{{ $inspection->id }}">
                    <div class="form-group row">
                        <label class="col-sm-4 control-label">@lang('Campagne')</label>
                        <div class="col-xs-12 col-sm-8">
                            {{ Str::replace('Campagne ', '', $inspection->campagne->nom) }}
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 control-label">@lang('Localite')</label>
                        <div class="col-xs-12 col-sm-8">
                            {{ $inspection->producteur->localite->nom }}
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-4 control-label">@lang('Producteur')</label>
                        <div class="col-xs-12 col-sm-8">
                            {{ $inspection->producteur->nom }}
                            {{ $inspection->producteur->prenoms }}({{ $inspection->producteur->codeProd }})

                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 control-label">@lang('Parcelle')</label>
                        <div class="col-xs-12 col-sm-8"> 
                            {{ $inspection->parcelle->codeParc ?? null }}

                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 control-label">@lang('Inspecteur')</label>
                        <div class="col-xs-12 col-sm-8">
                            {{ $inspection->user->lastname }} {{ $inspection->user->firstname }}

                        </div>
                    </div>

                    <hr class="panel-wide">
                    <div class="form-group row">
                        <table class="table-bordered table-striped table-responsive" id="myTable">
                            <tbody>
                                <?php

              $note = 0;
              $total=0;
              $i=1;
             $themeArray=array();

              foreach($inspection->reponsesInspection as $reponse){
                
                ?>
                                @if (!in_array($reponse->questionnaire->categorieQuestion->titre, $themeArray))
                                    <tr>
                                        <td @if ($i == 1) colspan="4" @else colspan="8" @endif
                                            style="max-width: 300px !important;text-wrap: wrap;">
                                            <strong><?php echo $reponse->questionnaire->categorieQuestion->titre; ?></strong></td>
                                        @if ($i == 1)
                                            <td style="max-width: 300px !important;text-wrap: wrap;">Recommandations</td>
                                            <td class="text-center">Délai d'exécution</td>
                                            <td class="text-center">Date de vérification</td>
                                            <td class="text-center">Statut</td>
                                        @endif
                                    </tr>
                                    @php
                                        $themeArray[] = $reponse->questionnaire->categorieQuestion->titre;
                                    @endphp
                                @endif

                                <tr>
                                    <td style="min-width: 300px !important;max-width: 300px !important;text-wrap: wrap;">
                                        <?php echo $reponse->questionnaire->nom; ?>
                                    </td>
                                    <td><?php echo $reponse->questionnaire->certificat; ?>
                                    </td>
                                    <td>
                                        <span
                                            class="badge @if ($reponse->notation == 'Conforme') badge-success @endif @if ($reponse->notation == 'Pas Conforme') badge-danger @endif @if ($reponse->notation == 'Non Applicable') badge-info @endif"><?php echo $reponse->notation; ?></span>
                                    </td>
                                    <td style="min-width: 300px !important;max-width: 300px !important;text-wrap: wrap;"> {{ $reponse->commentaire }} </td>
                                    <td>
                                        @if ($reponse->notation == 'Pas Conforme')
                                            <textarea cols="15" class="recommandation" name="recommandations[{{ $reponse->id }}]">{{ $reponse->recommandations }}</textarea>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($reponse->notation == 'Pas Conforme')
                                            <input type="date" class="delai" name="delai[{{ $reponse->id }}]"
                                                value="{{ $reponse->delai }}" min="{{ gmdate('Y-m-d') }}" />
                                        @endif
                                    </td>
                                    <td>
                                        @if ($reponse->notation == 'Pas Conforme')
                                            <input type="date" class="verification"
                                                name="date_verification[{{ $reponse->id }}]"
                                                value="{{ $reponse->date_verification }}" max="{{ gmdate('Y-m-d') }}" />
                                        @endif
                                    </td>
                                    <td>
                                        @if ($reponse->notation == 'Pas Conforme')
                                            <select class="statut" name="statuts[{{ $reponse->id }}]"
                                                class="form-control">
                                                <option value="En cours"
                                                    {{ $reponse->statuts == 'En cours' ? 'selected' : '' }}>En cours
                                                </option>
                                                <option value="Achevé"
                                                    {{ $reponse->statuts == 'Achevé' ? 'selected' : '' }}>Achevé</option>
                                                <option value="Non Débuté"
                                                    {{ $reponse->statuts == 'Non Débuté' ? 'selected' : '' }}>Non Débuté
                                                </option>
                                            </select>
                                        @endif
                                    </td>
                                </tr>
                                <?php  
                   $i++;
              }
              ?>
                            </tbody>
                        </table>
                    </div>
                    <hr class="panel-wide">

                    <div class="form-group row">
                        <?php echo Form::label(__('Taux de Conformité (%)'), null, ['class' => 'col-sm-4 control-label']); ?>
                        <div class="col-xs-12 col-sm-8">
                            {{ $inspection->note }}%
                        </div>
                    </div>
                    <div class="form-group row">
                        <?php echo Form::label(__('Total question'), null, ['class' => 'col-sm-4 control-label']); ?>
                        <div class="col-xs-12 col-sm-8">
                            {{ $inspection->total_question }}
                        </div>
                    </div>
                    <div class="form-group row">
                        <?php echo Form::label(__('Total question Conforme'), null, ['class' => 'col-sm-4 control-label']); ?>
                        <div class="col-xs-12 col-sm-8">
                            {{ $inspection->total_question_conforme }}
                        </div>
                    </div>
                    <div class="form-group row">
                        <?php echo Form::label(__('Total question Non Conforme'), null, ['class' => 'col-sm-4 control-label']); ?>
                        <div class="col-xs-12 col-sm-4">
                            {{ $inspection->total_question_non_conforme }}
                        </div>
                        <div class="col-xs-12 col-sm-4 pull-right" style="text-align:center;">
                            <h3 style="font-weight:bold;">Suivi des mesures correctives</h3>
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <td>En cours</td>
                                        <td>Non Débuté</td>
                                        <td>Achevé</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td id="nbEncours">0</td>
                                        <td id="nbNonRealise">0</td>
                                        <td id="nbRealise">0</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="form-group row">
                        <?php echo Form::label(__('Total question Non Applicable'), null, ['class' => 'col-sm-4 control-label']); ?>
                        <div class="col-xs-12 col-sm-8">
                            {{ $inspection->total_question_non_applicable }}
                        </div>
                    </div>
                    <div class="form-group row">
                        {{ Form::label(__("Date d'inspection"), null, ['class' => 'col-sm-4 control-label']) }}
                        <div class="col-xs-12 col-sm-8">
                            {{ date('d/m/Y', strtotime($inspection->date_evaluation)) }}
                        </div>
                    </div>
                    <div class="form-group row">
                        {{ Form::label(__("Etat d'approbation"), null, ['class' => 'col-sm-4 control-label']) }}
                        <div class="col-xs-12 col-sm-8">
                            @if ($inspection->approbation == 1)
                                <span class="badge badge-success">Approuvé</span>
                            @endif

                            @if ($inspection->approbation == 2)
                                <span class="badge badge-info">Non Approuvé</span>
                            @endif

                            @if ($inspection->approbation == 3)
                                <span class="badge badge-danger">Exclu</span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group row">
                        {{ Form::label(__("Date d'approbation"), null, ['class' => 'col-sm-4 control-label']) }}
                        <div class="col-xs-12 col-sm-8">
                            {{ $inspection->date_approbation }}
                        </div>
                    </div>
                    <hr class="panel-wide">


                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
    <div class="row no-print">

        @if ($inspection->approbation == 2 || $inspection->approbation == null)
            <div class="col-sm-3">
                <div class="float-sm-end" id="showApprob">
                    <a href="{{ route('manager.suivi.inspection.approbation', ['id' => $inspection->id, 'statut' => 1]) }}">
                        <button class="btn btn-outline--primary"><i
                                class="las la-check"></i></i>@lang('Approuvé')</button></a>
                </div>
            </div>
        @endif
        @if ($inspection->approbation == null)
            <div class="col-sm-3">
                <div class="float-sm-end">
                    <a href="{{ route('manager.suivi.inspection.approbation', ['id' => $inspection->id, 'statut' => 2]) }}">
                        <button class="btn btn-outline--info"><i class="las la-times"></i></i>@lang('Non Approuvé')</button>
                    </a>
                </div>
            </div>

            <div class="col-sm-3">
                <div class="float-sm-end">
                    <a href="{{ route('manager.suivi.inspection.approbation', ['id' => $inspection->id, 'statut' => 3]) }}">
                        <button class="btn btn-outline--danger"><i class="las la-trash"></i></i>@lang('Exclu')</button>
                    </a>
                </div>
            </div>
        @endif
        <div class="col-sm-3">
            <div class="float-sm-end">
                <button class="btn btn-outline--primary  printFacture"><i
                        class="las la-download"></i></i>@lang('Imprimer')</button>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <x-back route="{{ route('manager.suivi.inspection.index') }}" />
@endpush
@push('script')
    <script>
        "use strict";
        $('.printFacture').click(function() {
            $('#printFacture').printThis();
        });
    </script>
@endpush
@push('script')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#showApprob').hide();
            update_amounts();

            $('.recommandation,.delai,.verification,.statut').change('keyup change blur', function() {

                $.ajax({
                    type: 'POST',
                    url: "{{ route('manager.suivi.inspection.suiviStore') }}",
                    data: $('#flocal').serialize(),
                    success: function(html) {
                        //$('input[name=lastname]').val(html.lastname).attr("readonly",'readonly'); 
                    }
                });
                update_amounts();

            });

        });

        function update_amounts() {
            var encours = nnrealise = realise = 0;
            $('#myTable > tbody  > tr').each(function() {
                var statut = $(this).find('option:selected').val();

                if (statut == "En cours") {
                    encours += 1;
                }
                if (statut == "Non Débuté") {
                    nnrealise += 1;
                }
                if (statut == "Achevé") {
                    realise += 1;

                }

            });

            console.log('En cours: ' + encours);
            console.log('Non Débuté: ' + nnrealise);
            console.log('Achevé: ' + realise);
            $('#nbEncours').text(encours);
            $('#nbNonRealise').text(nnrealise);
            $('#nbRealise').text(realise);
            if(encours==0 && nnrealise==0){
                $('#showApprob').show();
            }else{
                $('#showApprob').hide();
            }
        }

        $("#producteur").chained("#localite");
    </script>
@endpush

@push('style')
    <style>
        #myTable td {
            font-size: 0.8125rem;
            color: #5b6e88;
            font-weight: 500;
            padding: 5px 5px;
            vertical-align: middle;
            border: 1px solid #f4f4f4;
            min-width: 100px;
        }
    </style>
