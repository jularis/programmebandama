@extends('manager.layouts.app')
@section('panel')
    <div class="row mb-none-30">
        <div class="col-lg-12 mb-30">
            <div class="card">
                <div class="card-body">
                    {!! Form::model($inspection, [
                        'method' => 'POST',
                        'route' => ['manager.suivi.inspection.store', $inspection->id],
                        'class' => 'form-horizontal',
                        'id' => 'flocal',
                        'enctype' => 'multipart/form-data',
                    ]) !!}
                    <input type="hidden" name="id" value="{{ $inspection->id }}">

                    <div class="form-group row">
                        <label class="col-sm-4 control-label">@lang('Selectionner une localite')</label>
                        <div class="col-xs-12 col-sm-8">
                            <select class="form-control" name="localite" id="localite" required>
                                <option value="">@lang('Selectionner une option')</option>
                                @foreach ($localites as $localite)
                                    <option value="{{ $localite->id }}" @selected($localite->id == $inspection->producteur->localite->id)>
                                        {{ $localite->nom }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-4 control-label">@lang('Selectionner un producteur')</label>
                        <div class="col-xs-12 col-sm-8">
                            <select class="form-control" name="producteur" id="producteur" required>
                                <option value="">@lang('Selectionner une option')</option>
                                @foreach ($producteurs as $producteur)
                                    <option value="{{ $producteur->id }}" data-chained="{{ $producteur->localite->id }}"
                                        @selected($producteur->id == $inspection->producteur_id)>
                                        {{ stripslashes($producteur->nom) }} {{ stripslashes($producteur->prenoms) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 control-label">@lang('Inspecteur')</label>
                        <div class="col-xs-12 col-sm-8">
                            <select class="form-control" name="encadreur" id="encadreur" required>
                                <option value="">@lang('Selectionner une option')</option>
                                @foreach ($staffs as $staff)
                                    <option value="{{ $staff->id }}" @selected($staff->id == $inspection->formateur_id)>
                                        {{ $staff->lastname }} {{ $staff->firstname }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <hr class="panel-wide">
                    <div class="form-group row">
                        <table class="table-bordered table-striped" id="myTable">
                            <tbody>
                                <?php

              $note = 0;
              $total=0;
              foreach($categoriequestionnaire as $catquest){
                ?>
                                <tr>
                                    <td colspan="3"><strong><?php echo $catquest->titre; ?></strong></td>
                                </tr>
                                <?php 
               
              foreach($inspection->reponses as $q) {
                if($q->categorie_questionnaire_id == $catquest->id)
                {
                   ?>


                                <tr>
                                    <td><?php echo $q->nom; ?>
                                    </td>
                                    <td>

                                        <select class="form-control" class="notation" id="reponse-<?php echo $q->id; ?>"
                                            name="reponse[<?php echo $q->id; ?>]" required>
                                            <option value="0"> </option>
                                            <?php
                          foreach($notations as $not)
                          {
                            
                               ?>
                                            <option value="<?php echo $not->point; ?>" @selected($not->point == $q->pivot->notation)>
                                                <?php echo $not->nom; ?></option>
                                            <?php
                          }
                         ?>
                                        </select>
                                        </td>
                                        <td>
                                            {{ $q->commentaire }}
                                    </td>
                                </tr>


                                <?php 
                  } 
                }
              }
              ?>
                            </tbody>
                        </table>
                    </div>
                    <hr class="panel-wide">
                    <div class="form-group row">
                        <?php echo Form::label(__('Note'), null, ['class' => 'col-sm-4 control-label']); ?>
                        <div class="col-xs-12 col-sm-8">
                            <?php echo Form::number('note', $inspection->note, ['placeholder' => __('00'), 'class' => 'form-control note', 'id' => 'note', 'readonly' => 'readonly']); ?>
                        </div>
                    </div>
                    <div class="form-group row">
                        {{ Form::label(__("Date d'évaluation"), null, ['class' => 'col-sm-4 control-label']) }}
                        <div class="col-xs-12 col-sm-8">
                            {!! Form::date('date_evaluation', null, [
                                'placeholder' => __("Date d'évaluation"),
                                'class' => 'form-control',
                                'id' => 'anneeCreation-1',
                                'required',
                            ]) !!}
                        </div>
                    </div>
                    <div class="form-group row">
                        {{ Form::label(__('Quel est votre production de l\' année précedente ?'), null, ['class' => 'col-sm-4 control-label']) }}
                        <div class="col-xs-12 col-sm-8">
                            {!! Form::number('production', null, [
                                'placeholder' => __('Production de l\'année précedente (en Kg)'),
                                'class' => 'form-control',
                                'id' => 'production',
                                'required',
                            ]) !!}
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
    <x-back route="{{ route('manager.suivi.inspection.index') }}" />
@endpush
@push('script')
    <script type="text/javascript">
        $(document).ready(function() {


            $('#flocal').change(function() {
                update_amounts();
            });

        });

        function update_amounts() {
            var sum = 0;

            $('#myTable > tbody  > tr').each(function() {
                var qty = $(this).find('option:selected').val();

                if (qty == "-1" || qty == "0" || qty == "1" || qty == "2") {
                    sum = parseFloat(sum) + parseFloat(qty);
                }

            });
            $('#note').val(sum);
            //just update the total to sum
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
            padding: 15px 25px;
            vertical-align: middle;
            border: 1px solid #f4f4f4;
            min-width: 200px;
        }
    </style>
