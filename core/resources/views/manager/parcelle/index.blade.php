@extends('manager.layouts.app')
@section('panel')
<?php use Carbon\Carbon; ?>
    <div class="row">
        <div class="col-lg-12">
            <div class="card b-radius--10 mb-3">
                <div class="card-body">
                    <form action="">
                        <div class="d-flex flex-wrap gap-4">
                            <input type="hidden" name="table" value="parcelles" />
                            <div class="flex-grow-1">
                                <label>@lang('Recherche par Mot(s) cle(s)')</label>
                                <input type="text" name="search" value="{{ request()->search }}" class="form-control">
                            </div>
                            <div class="flex-grow-1">
                                <label>@lang('Section')</label>
                                <select name="section" class="form-control select2-basic" data-live-search="true" id="section">
                                    <option value="">@lang('Toutes')</option>
                                    @foreach ($sections as $local)
                                        <option value="{{ $local->id }}"
                                            {{ request()->section == $local->id ? 'selected' : '' }}>{{ $local->libelle }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="flex-grow-1">
                                <label>@lang('Localité')</label>
                                <select name="localite" class="form-control select2-basic" id="localite">
                                    <option value="">@lang('Toutes')</option>
                                    @foreach ($localites as $local)
                                        <option value="{{ $local->id }}" data-chained="{{ $local->section_id }}"
                                            {{ request()->localite == $local->id ? 'selected' : '' }}>{{ $local->nom }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="flex-grow-1">
                                <label>@lang('Producteur')</label>
                                <select name="producteur" class="form-control select2-basic" id="producteur">
                                    <option value="">@lang('Tous')</option>
                                    @foreach ($producteurs as $local)
                                        <option value="{{ $local->id }}" data-chained="{{ $local->localite_id }}"
                                            {{ request()->producteur == $local->id ? 'selected' : '' }}>
                                            {{ stripslashes($local->nom) }} {{ stripslashes($local->prenoms) }} ({{ $local->codeProd }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="flex-grow-1">
                                <label>@lang('Statut declaration')</label>
                                <select name="typedeclaration" class="form-control">
                                    <option value="">@lang('Tous')</option>
                                    <option value="GPS" {{ request()->typedeclaration == 'GPS' ? 'selected' : '' }}>
                                        @lang('GPS')</option>
                                    <option value="Verbale" {{ request()->typedeclaration == 'Verbale' ? 'selected' : '' }}>
                                        @lang('Verbale')</option>
                                </select>
                            </div>
                            <div class="flex-grow-1">
                                <label>@lang('Date')</label>
                                <input name="date" type="text" class="dates form-control"
                                    placeholder="@lang('Date de debut - Date de fin')" autocomplete="off" value="{{ request()->date }}">
                            </div>
                            <div class="flex-grow-1 align-self-end">
                                <button class="btn btn--primary w-100 h-45"><i class="fas fa-filter"></i>
                                    @lang('Filter')</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card b-radius--10 mb-3">
                <div class="card-body">
                  
                <div class="row">
                            <div class="col-md-4">
                                <div class="alert alert-success text-center">
                                <div class="fw-bold">{{ $total_parcelle }}</div>
                                    @lang('TOTAL PARCELLES')
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="alert alert-info text-center">
                                <div class="fw-bold">{{ $total_parcelle_gps }}</div>
                                    @lang('TOTAL GPS')
                                    
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="alert alert-warning text-center">
                                <div class="fw-bold">{{ $total_parcelle_verbale }}</div>
                                    @lang('TOTAL VERBALES')
                                </div>
                            </div>
                    </div>
                </div>
            </div>
            <div class="card b-radius--10 ">
                <div class="card-body  p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Section')</th>
                                    <th>@lang('Localite')</th>
                                    <th>@lang('Code Parcelle')</th>
                                    <th>@lang('Producteur')</th>
                                    <th>@lang('Statut declaration')</th>
                                    <th>@lang('Superficie')</th>
                                    <th>@lang('Annee de creation')</th>
                                    <th>@lang('Ajoutée le')</th>
                                    <th>@lang('Etat')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($parcelles as $parcelle)
 
                                    <tr>
                                        <td>
                                            @if ($parcelle->producteur && $parcelle->producteur->localite && $parcelle->producteur->localite->section)
                                                <span
                                                    class="fw-bold">{{ $parcelle->producteur->localite->section->libelle }}</span>
                                            @else
                                                <span class="fw-bold">Pas de section</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="fw-bold">{{ $parcelle->producteur->localite->nom }}</span>
                                        </td>
                                        <td>
                                            <span> <a href="{{ route('manager.traca.parcelle.edit', $parcelle->id) }}">
                                                    <span>@</span>{{ $parcelle->codeParc }}
                                                </a></span>
                                        </td>
                                        <td>
                                            <span class="small">
                                                {{ stripslashes($parcelle->producteur->nom) }} {{ stripslashes($parcelle->producteur->prenoms) }}
                                            </span>
                                        </td>

                                        <td>
                                            <span>{{ $parcelle->typedeclaration }}</span>
                                        </td>
                                        <td>
                                            <span>{{ $parcelle->superficie }}</span>
                                        </td>
                                        <td>
                                            <span>{{ $parcelle->anneeCreation }}</span>
                                        </td>
                                        <td>
                                            <span class="d-block">{{ showDateTime($parcelle->created_at) }}</span>
                                            <span>{{ diffForHumans($parcelle->created_at) }}</span>
                                        </td>
                                        <td> @php echo $parcelle->statusBadge; @endphp </td>
                                        <td>
                                        <a href="{{ route('manager.traca.parcelle.show', $parcelle->id) }}"
                                                    class="btn btn-sm btn-outline--info"><i
                                                        class="las la-file-invoice"></i>@lang('Détail')</a>
                                        <a href="{{ route('manager.traca.parcelle.edit', $parcelle->id) }}"
                                                    class="btn btn-sm btn-outline--danger"><i class="la la-pen"></i>@lang('Editer')</a>
                                                    @if ($parcelle->status == Status::DISABLE)
                                                    <button type="button" class="btn btn-sm btn-outline--success confirmationBtn"
                                                        data-action="{{ route('manager.traca.parcelle.status', $parcelle->id) }}"
                                                        data-question="@lang('Êtes-vous sûr de vouloir activer cette parcelle?')">
                                                        <i class="la la-eye"></i> @lang('Active')
                                                    </button>
                                                @else
                                                    <button type="button" class="btn btn-sm btn-outline--warning confirmationBtn"
                                                        data-action="{{ route('manager.traca.parcelle.status', $parcelle->id) }}"
                                                        data-question="@lang('Êtes-vous sûr de vouloir désactiver cette parcelle?')">
                                                        <i class="la la-eye-slash"></i> @lang('Désactive')
                                                    </button>
                                                @endif
                                                
                                                <a href="javascript:void();"
                                                class="btn btn-sm btn-outline--danger confirmationBtn"
                                                data-action="{{ route('manager.traca.parcelle.delete', encrypt($parcelle->id)) }}"
                                                data-question="@lang('Êtes-vous sûr de vouloir supprimer cette parcelle?')"
                                                ><i
                                                    class="las la-trash"></i>@lang('Supprimer')</a>
                                            
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
                @if ($parcelles->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($parcelles) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div id="typeModel" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Importer des parcelles')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i> </button>
                </div>
                <form action="{{ route('manager.traca.parcelle.uploadcontent') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <p>Fichier d'exemple à utiliser :<a href="{{ asset('assets/parcelle-import-exemple.xlsx') }}"
                                target="_blank">@lang('parcelle-import-exemple.xlsx')</a></p>

                        <div class="form-group row">
                            {{ Form::label(__('Fichier(.xls, .xlsx)'), null, ['class' => 'control-label col-sm-4']) }}
                            <div class="col-xs-12 col-sm-8 col-md-8">
                                <input type="file" name="uploaded_file" accept=".xls, .xlsx"
                                    class="form-control dropify-fr" placeholder="Choisir une image" id="image"
                                    required>
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
@endsection

@push('breadcrumb-plugins')
    <a href="{{ route('manager.traca.parcelle.create') }}" class="btn  btn-outline--primary h-45 addNewCooperative">
        <i class="las la-plus"></i>@lang('Ajouter nouveau')
    </a>
    <a class="btn btn-outline--info h-45 addType"><i class="las la-cloud-upload-alt"></i> @lang('Importer des Parcelles')</a>
    <a href="{{ route('manager.traca.parcelle.uploadkml') }}" class="btn btn-danger h-45"><i
            class="las la-cloud-upload-alt"></i> @lang('Importer un Fichier KML')</a>
    <a href="{{ route('manager.traca.parcelle.exportExcel.parcelleAll') }}" class="btn  btn-outline--warning h-45"><i
            class="las la-cloud-download-alt"></i> @lang('Exporter en Excel')</a>
    <button type="button" class="btn btn-outline--primary h-45" data-bs-toggle="dropdown" aria-expanded="false"><i
            class="las la-map-marker"></i>@lang('Voir Mapping')
    </button>
    <div class="dropdown-menu p-0">
        <a class="dropdown-item" href="{{ route('manager.traca.parcelle.mapping') }}">@lang('Waypoints')</a>
        <a class="dropdown-item" href="{{ route('manager.traca.parcelle.mapping.polygone') }}">@lang('Polygones')</a>

    </div>
@endpush
@push('style')
    <style>
        .table-responsive {
            overflow-x: auto;
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
        $("#localite").chained("#section");
        $("#producteur").chained("#localite");
        (function($) {
            "use strict";

            $('.addType').on('click', function() {
                $('#typeModel').modal('show');
            });

            $('.dates').datepicker({
                maxDate: new Date(),
                range: true,
                multipleDatesSeparator: "-",
                language: 'fr'
            });

            let url = new URL(window.location).searchParams;
            if (url.get('localite') != undefined && url.get('localite') != '') {
                $('select[name=localite]').find(`option[value=${url.get('localite')}]`).attr('selected', true);
            }
            if (url.get('payment_status') != undefined && url.get('payment_status') != '') {
                $('select[name=payment_status]').find(`option[value=${url.get('payment_status')}]`).attr('selected',
                    true);
            }

        })(jQuery)

        $('form select').on('change', function() {
            $(this).closest('form').submit();
        });
    </script>
@endpush
