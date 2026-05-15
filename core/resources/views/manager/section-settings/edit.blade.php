@extends('manager.layouts.app')
@section('panel')
    <x-setting-sidebar :activeMenu="$activeSettingMenu" />
    <x-setting-card>
        <x-slot name="header">
            <div class="s-b-n-header" id="tabs">
                <h2 class="mb-0 p-20 f-21 font-weight-normal text-capitalize border-bottom-grey">
                    @lang($pageTitle)</h2>
            </div>
        </x-slot>
        <div class="col-lg-12 col-md-12 ntfcn-tab-content-left w-100 p-4 ">
            <div class="card">
                <div class="card-body">
                    {!! Form::model($section, [
                        'method' => 'POST',
                        'route' => ['manager.settings.section-settings.update', $section->id],
                        'class' => 'form-horizontal',
                        'id' => 'flocal',
                        'enctype' => 'multipart/form-data',
                    ]) !!}

                    <input type="hidden" name="id" value="{{ $section->id }}">
                    <div class="form-group row">
                        <label class="col-xs-12 col-sm-4">@lang('Select Cooperative')</label>
                        <div class="col-xs-12 col-sm-8">
                            <select class="form-control" name="cooperative_id" required>
                                @foreach ($cooperatives as $cooperative)
                                    <option value="{{ $cooperative->id }}" @selected($cooperative->id == $section->cooperative_id)>
                                        {{ __($cooperative->name) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <?php echo Form::label(__('Nom de la section'), null, ['class' => 'control-label col-xs-12 col-sm-4']); ?>
                        <div class="col-xs-12 col-sm-8">
                            <?php echo Form::text('libelle', null, ['placeholder' => __('Nom de la section'), 'class' => 'form-control', 'required']); ?>
                        </div>
                    </div>
                    <div class="form-group row">
                        <?php echo Form::label(__('Région'), null, ['class' => 'control-label col-xs-12 col-sm-4']); ?>
                        <div class="col-xs-12 col-sm-8">
                            <?php echo Form::text('region', null, ['placeholder' => __('Région'), 'class' => 'form-control']); ?>
                        </div>
                    </div>
                    <div class="form-group row">
                        <?php echo Form::label(__('Sous Préfecture'), null, ['class' => 'control-label col-xs-12 col-sm-4']); ?>
                        <div class="col-xs-12 col-sm-8">
                            <?php echo Form::text('sousPrefecture', null, ['placeholder' => __('Sous préfecture'), 'class' => 'form-control']); ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <button type="submit" id="save-form"
                            class="btn btn--primary btn-block h-45 w-100">@lang('Envoyer')</button>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
    </x-setting-card>
@endsection

@push('breadcrumb-plugins')
    <x-back route="{{ route('manager.settings.section-settings.index') }}" />
@endpush

@push('script')
    <script type="text/javascript">
        $(document).ready(function() {

            var maladiesCount = $("#maladies tr").length + 1;
            $(document).on('click', '#addRowMal', function() {

                //---> Start create table tr
                var html_table = '<tr>';
                html_table +=
                    '<td class="row"><div class="col-xs-12 col-sm-12 bg-success"><badge class="btn btn-warning btn-sm">Nom Ecole Primaire ' +
                    maladiesCount +
                    '</badge></div><div class="col-xs-12 col-sm-11"><div class="form-group"><input placeholder=" ..." class="form-control" id="nomecolesprimaires-' +
                    maladiesCount +
                    '" name="nomecolesprimaires[]" type="text"></div></div><div class="col-xs-12 col-sm-1"><button type="button" id="' +
                    maladiesCount +
                    '" class="removeRowMal btn btn-danger btn-sm"><i class="fa fa-minus"></i></button></div></td>';

                html_table += '</tr>';
                //---> End create table tr

                maladiesCount = parseInt(maladiesCount) + 1;
                $('#maladies').append(html_table);

            });

            $(document).on('click', '.removeRowMal', function() {

                var row_id = $(this).attr('id');

                // delete only last row id
                if (row_id == $("#maladies tr").length) {

                    $(this).parents('tr').remove();

                    maladiesCount = parseInt(maladiesCount) - 1;

                }
            });

        });


        if ($('.marche').val() == 'oui') {
            $('#kmmarcheproche').hide('slow');
            $('.kmmarcheproche').css('display', 'block');
        } else {
            $('#kmmarcheproche').show('slow');
        }
        $('.marche').change(function() {
            var marche = $('.marche').val();
            if (marche == 'non') {
                $('#kmmarcheproche').show('slow');
                $('.kmmarcheproche').css('display', 'block');
            } else {
                $('#kmmarcheproche').hide('slow');
            }
        });
        // CENTRE DE SANTE
        if ($('.centresante').val() == 'oui') {
            $('#kmCentresante').hide('slow');
            $('.kmCentresante').css('display', 'block');
        } else {
            $('#kmCentresante').show('slow');
        }
        $('.centresante').change(function() {
            var centresante = $('.centresante').val();
            if (centresante == 'non') {
                $('#kmCentresante').show('slow');
                $('.kmCentresante').css('display', 'block');
            } else {
                $('#kmCentresante').hide('slow');
            }
        });

        // ECOLE
        if ($('.ecole').val() == 'oui') {
            $('#nombrecole').show('slow');
            $('.kmEcoleproche').hide('slow');
            $('.nombrecole').css('display', 'block');
        } else {
            $('.kmEcoleproche').show('slow');
            $('#nombrecole').hide('slow');
            $('.kmEcoleproche').css('display', 'block');
        }

        $('.ecole').change(function() {
            var ecole = $('.ecole').val();
            if (ecole == 'oui') {
                $('#nombrecole').show('slow');
                $('.nombrecole').css('display', 'block');
                $('.kmEcoleproche').hide('slow');
            } else {
                $('#kmEcoleproche').show('slow');
                $('#nombrecole').hide('slow');
                $('.kmEcoleproche').css('display', 'block');
            }
        });

        // EAU HYDRAULIQUE
        if ($('.sourceeau').val() == 'Pompe Hydraulique Villageoise') {
            $('#etatpompehydrau').show('slow');
            $('.etatpompehydrau').css('display', 'block');
        } else {
            $('#etatpompehydrau').hide('slow');
        }
        $('.sourceeau').change(function() {
            var sourceeau = $('.sourceeau').val();
            if (sourceeau == 'Pompe Hydraulique Villageoise') {
                $('#etatpompehydrau').show('slow');
                $('.etatpompehydrau').css('display', 'block');
            } else {
                $('#etatpompehydrau').hide('slow');
            }
        });

        $('#save-form').click(function() {
            var url = "{{ route('manager.settings.section-settings.update', $section->id) }}";

            $.easyAjax({
                url: url,
                container: '#editSettings',
                type: "POST",
                disableButton: true,
                blockUI: true,
                redirect: true,
                buttonSelector: "#save-form",
                data: $('#editSettings').serialize(),
                success: function(response) {
                    if (response.status == 'success') {
                        window.location.href = response.redirectUrl;
                    }
                }
            })
        });
    </script>
@endpush
