@extends('manager.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
        <div class="card b-radius--10 mb-3">
                <div class="card-body">
                    <form action="">
                        <div class="d-flex flex-wrap gap-4">
                            <input type="hidden" name="table" value="applications"/>
                            <div class="flex-grow-1">
                                <label>@lang('Recherche par Mot(s) cle(s)')</label>
                                <input type="text" name="search" value="{{ request()->search }}" class="form-control">
                            </div>
                            <div class="flex-grow-1">
                                <label>@lang('Localite')</label>
                                <select name="localite" class="form-control">
                                    <option value="">@lang('Toutes')</option>
                                    @foreach($localites as $local)
                                    <option value="{{ $local->id }}">{{ $local->nom }}</option>
                                    @endforeach 
                                </select>
                            </div> 
                            <div class="flex-grow-1">
                                <label>@lang('Date')</label>
                                <input name="date" type="text" class="dates form-control" placeholder="@lang('Date de debut - Date de fin')" autocomplete="off" value="{{ request()->date }}">
                            </div>
                            <div class="flex-grow-1 align-self-end">
                                <button class="btn btn--primary w-100 h-45"><i class="fas fa-filter"></i> @lang('Filter')</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card b-radius--10 ">
                <div class="card-body  p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr> 
                                    <th>@lang('Localite')</th> 
                                    <th>@lang('Producteur')</th>
                                    <th>@lang('Parcelle')</th> 
                                    <th>@lang("Date application")</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($applications as $application)
                                    <tr>
                                        <td>
                                            <span class="fw-bold">{{ $application->parcelle->producteur->localite->nom }}</span>
                                        </td>
                                        <td> 
                                            <span class="small">
                                            {{ $application->parcelle->producteur->nom }} {{ $application->parcelle->producteur->prenoms }}
                                            </span>
                                        </td>
                                        <td>
                                            <span> <a href="{{ route('manager.suivi.application.edit', $application->id) }}">
                                                    <span>@</span>{{ $application->parcelle->codeParc }}
                                                </a></span>
                                        </td> 
                                        <td>
                                            <span class="d-block">{{ showDateTime($application->date_application) }}</span>
                                            <span>{{ diffForHumans($application->date_application) }}</span>
                                        </td>
                                        <td> @php echo $application->statusEstim; @endphp </td>
                                        <td>
                                         
                                            <button type="button" class="btn btn-sm btn-outline--primary" data-bs-toggle="dropdown" aria-expanded="false"><i
                                                    class="las la-ellipsis-v"></i>@lang('Action')
                                             </button>
                                            <div class="dropdown-menu p-0">
                                                <a href="{{ route('manager.suivi.application.edit', $application->id) }}"
                                                    class="dropdown-item"><i class="la la-pen"></i>@lang('Editer')</a> 
                                                <a href="{{ route('manager.suivi.application.show', $application->id) }}"
                                                    class="dropdown-item"><i class="las la-file-invoice"></i>@lang('Détail')</a>
                                                @if ($application->status == Status::DISABLE)
                                                    <button type="button" class="confirmationBtn  dropdown-item"
                                                        data-action="{{ route('manager.suivi.application.status', $application->id) }}"
                                                        data-question="@lang('Are you sure to enable this application?')">
                                                        <i class="la la-eye"></i> @lang('Activé')
                                                    </button>
                                                @else
                                                    <button type="button" class="confirmationBtn dropdown-item"
                                                        data-action="{{ route('manager.suivi.application.status', $application->id) }}"
                                                        data-question="@lang('Are you sure to disable this application?')">
                                                        <i class="la la-eye-slash"></i> @lang('Désactivé')
                                                    </button>
                                                @endif 
                                                
                                            </div>
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
                @if ($applications->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($applications) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div id="typeModel" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Importation Application Phyto')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i> </button>
                </div>
                <form action="{{ route('manager.suivi.application.uploadcontent') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <p>@lang('Fichier dexemple à utiliser') :<a href="{{ asset('assets/phyto-import-exemple.xlsx') }}"
                                target="_blank">@lang('phyto-import-exemple.xlsx')</a></p>

                        <div class="form-group row">
                            {{ Form::label(__('Fichier(.xls, .xlsx)'), null, ['class' => 'control-label col-sm-4']) }}
                            <div class="col-xs-12 col-sm-8 col-md-8">
                                <input type="file" name="uploaded_file" accept=".xls, .xlsx"
                                    class="form-control dropify-fr" placeholder="Choisir une image" id="image" required>
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
    
    <a href="{{ route('manager.suivi.application.create') }}" class="btn  btn-outline--primary h-45 addNewCooperative">
        <i class="las la-plus"></i>@lang("Ajouter nouveau")
    </a>
    <a class="btn btn-outline--danger h-45 addType"><i class="las la-cloud-upload-alt"></i>@lang('Importation Phyto')</a>
    <a href="{{ route('manager.suivi.application.exportExcel.applicationAll') }}" class="btn  btn-outline--warning h-45"><i class="las la-cloud-download-alt"></i> @lang('Exporter en Excel')</a>
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
        (function($) {
            "use strict";

            $('.addType').on('click', function() {
                $('#typeModel').modal('show');
            });

            $('.dates').datepicker({
                maxDate:new Date(),
                range:true,
                multipleDatesSeparator:"-",
                language:'en'
            });

            let url=new URL(window.location).searchParams;
            if(url.get('localite') != undefined && url.get('localite') != ''){
                $('select[name=localite]').find(`option[value=${url.get('localite')}]`).attr('selected',true);
            }
            if(url.get('payment_status') != undefined && url.get('payment_status') != ''){
                $('select[name=payment_status]').find(`option[value=${url.get('payment_status')}]`).attr('selected',true);
            }

        })(jQuery)
    </script>
@endpush
