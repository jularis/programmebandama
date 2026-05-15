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
            <div class="card b-radius--10 ">
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Theme')</th>
                                    <th>@lang('Nom')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Last Update')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($sousThemeFormation as $sousTheme)
                                    <tr>
                                        <td>
                                            <span>{{ __($sousTheme->themeFormation->nom) }}</span>
                                        </td>
                                        <td>
                                            <span>{{ __($sousTheme->nom) }}</span>
                                        </td>
                                        <td>
                                            @php
                                                echo $sousTheme->statusBadge;
                                            @endphp
                                        </td>

                                        <td>
                                            <span class="d-block">{{ showDateTime($sousTheme->updated_at) }}</span>
                                            <span>{{ diffForHumans($sousTheme->updated_at) }}</span>
                                        </td>

                                        <td>
                                            <button type="button" class="btn btn-sm btn-outline--primary  update"
                                                data-id="{{ $sousTheme->id }}" data-nom="{{ $sousTheme->nom }}"
                                                data-typeformation="{{ $sousTheme->themeFormation->type_formation_id }}"
                                                data-themeformation="{{ $sousTheme->theme_formation_id }}">
                                                <i class="las la-pen"></i>@lang('Edit')</button>

                                            @if ($sousTheme->status == Status::DISABLE)
                                                <button type="button"
                                                    class="btn btn-sm btn-outline--success confirmationBtn"
                                                    data-action="{{ route('manager.settings.sousThemeFormation.status', $sousTheme->id) }}"
                                                    data-question="@lang('Etes-vous sûr de vouloir activer ce sousTheme de formation?')">
                                                    <i class="la la-eye"></i> @lang('Active')
                                                </button>
                                            @else
                                                <button type="button"
                                                    class="btn btn-sm btn-outline--danger confirmationBtn"
                                                    data-action="{{ route('manager.settings.sousThemeFormation.status', $sousTheme->id) }}"
                                                    data-question="@lang('Etes-vous sûr de vouloir désactiver ce sousTheme de formation?')">
                                                    <i class="la la-eye-slash"></i>@lang('Désactive')
                                                </button>
                                            @endif
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
                @if ($sousThemeFormation->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($sousThemeFormation) }}
                    </div>
                @endif
            </div>
        </div>
    </x-setting-card>
    <div id="typeModel" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Ajouter un sousTheme de Formation')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i> </button>
                </div>
                <form action="{{ route('manager.settings.sousThemeFormation.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name='id'>

                        <div class="form-group row">
                            <label class="col-sm-4 control-label">@lang('Type de Formation')</label>
                            <div class="col-xs-12 col-sm-8">
                                <select class="form-control" id="typeFormation" name="typeFormation" required>
                                    <option value="">@lang('Selectionner une option')</option>
                                    @foreach ($typeFormation as $type)
                                        <option value="{{ $type->id }}" @selected(old('typeFormation'))>
                                            {{ __($type->nom) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-4 control-label">@lang('Theme de Formation')</label>
                            <div class="col-xs-12 col-sm-8">
                                <select class="form-control" name="themeFormation" id="themeFormation" required>
                                    <option value="">@lang('Selectionner une option')</option>
                                    @foreach ($themeFormation as $theme)
                                        <option value="{{ $theme->id }}" data-chained="{{ $theme->typeFormation->id }}"
                                            @selected(old('themeFormation'))>
                                            {{ __($theme->nom) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            {{ Form::label(__('Nom du sousTheme de Formation'), null, ['class' => 'control-label col-sm-4']) }}
                            <div class="col-xs-12 col-sm-8 col-md-8">
                                {!! Form::text('nom', null, [
                                    'placeholder' => __('Sous theme de formation'),
                                    'class' => 'form-control',
                                    'required',
                                ]) !!}
                            </div>
                        </div>


                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn--primary w-100 h-45 ">@lang('Envoyer')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <x-confirmation-modal />
    <div id="updateType" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Modifier un sous thème de Formation')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i> </button>
                </div>
                <form action="{{ route('manager.settings.sousThemeFormation.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name='id'>

                        <div class="form-group row">
                            <label class="col-sm-4 control-label">@lang('Type de Formation')</label>
                            <div class="col-xs-12 col-sm-8">
                                <select class="form-control" id="typeFormation2" name="typeFormation" required>
                                    <option value="">@lang('Selectionner une option')</option>
                                    @foreach ($typeFormation as $type)
                                        <option value="{{ $type->id }}" @selected(old('typeFormation'))>
                                            {{ __($type->nom) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-4 control-label">@lang('Theme de Formation')</label>
                            <div class="col-xs-12 col-sm-8">
                                <select class="form-control" name="themeFormation" id="themeFormation2" required>
                                    <option value="">@lang('Selectionner une option')</option>
                                    @foreach ($themeFormation as $theme)
                                        <option value="{{ $theme->id }}"
                                            data-chained="{{ $theme->typeFormation->id }}">
                                            {{ __($theme->nom) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            {{ Form::label(__('Nom du sousTheme de Formation'), null, ['class' => 'control-label col-sm-4']) }}
                            <div class="col-xs-12 col-sm-8 col-md-8">
                                {!! Form::text('nom', null, [
                                    'placeholder' => __('Sous theme de formation'),
                                    'class' => 'form-control',
                                    'required',
                                ]) !!}
                            </div>
                        </div>


                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn--primary w-100 h-45 ">@lang('Envoyer')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <button class="btn btn-sm btn-outline--primary addType"><i class="las la-plus"></i>@lang('Ajouter nouveau')</button>
@endpush


@push('script')
    <script type="text/javascript">
        $("#themeFormation").chained("#typeFormation");
         
    </script>
    <script>
        (function($) {
            "use strict";
            $('.addType').on('click', function() {
                $('#typeModel').modal('show');
            });
            $('.update').on('click', function() {
                var modal = $('#updateType');
                modal.find('input[name=id]').val($(this).data('id'));
                modal.find('input[name=nom]').val($(this).data('nom'));
                modal.find('select[name=typeFormation]').val($(this).data('typeformation'));
                modal.find('select[name=themeFormation]').val($(this).data('themeformation'));
                $('#updateType').modal('show');
            });

            $('#typeFormation2').on('change', function() {
                $("#themeFormation2").chained("#typeFormation2");
            });

        })(jQuery);
    </script>
@endpush
