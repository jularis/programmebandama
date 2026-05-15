@extends('manager.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
        <div class="card b-radius--10 mb-3">
                <div class="card-body">
                    <form action="">
                        <div class="d-flex flex-wrap gap-4">
                            <input type="hidden" name="table" value="ssrteclmrs"/>
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
                                    <th>@lang('Code du membre')</th>
                                    <th>@lang('Nom du membre')</th>
                                    <th>@lang('Prenoms du membre')</th> 
                                    <th>@lang('Genre')</th>
                                    <th>@lang('Lien de parente')</th> 
                                    <th>@lang("Date Enquete")</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($ssrteclmrs as $data)
                                    <tr>
                                        <td>
                                            <span class="fw-bold">{{ $data->producteur->localite->nom }}</span>
                                        </td>
                                        <td> 
                                            <span class="small">
                                            {{ $data->producteur->nom }} {{ $data->producteur->prenoms }}
                                            </span>
                                        </td> 
                                        <td>
                                            <span> <a href="{{ route('manager.suivi.ssrteclmrs.edit', $data->id) }}">
                                                    <span>@</span>{{ $data->codeMembre }}
                                                </a></span>
                                        </td>
                                        <td>
                                            <span>{{ $data->nomMembre }}</span>
                                        </td>
                                        <td>
                                            <span>{{ $data->prenomMembre }}</span>
                                        </td>
                                        <td>
                                            <span>{{ $data->sexeMembre }}</span>
                                        </td>
                                        <td>
                                            <span>{{ $data->lienParente }}</span>
                                        </td>
                                        <td>
                                            <span class="d-block">{{ showDateTime($data->date_enquete) }}</span>
                                            <span>{{ diffForHumans($data->date_enquete) }}</span>
                                        </td>
                                        <td> @php echo $data->statusBadge; @endphp </td>
                                        <td>
                                         
                                            <button type="button" class="btn btn-sm btn-outline--primary" data-bs-toggle="dropdown" aria-expanded="false"><i
                                                    class="las la-ellipsis-v"></i>@lang('Action')
                                             </button>
                                            <div class="dropdown-menu p-0">
                                                <a href="{{ route('manager.suivi.ssrteclmrs.edit', $data->id) }}"
                                                    class="dropdown-item"><i class="la la-pen"></i>@lang('Editer')</a> 
                                                <a href="{{ route('manager.suivi.ssrteclmrs.show', $data->id) }}"
                                                    class="dropdown-item"><i class="las la-file-invoice"></i>@lang('Détail')</a>
                                                @if ($data->status == Status::DISABLE)
                                                    <button type="button" class="confirmationBtn  dropdown-item"
                                                        data-action="{{ route('manager.suivi.ssrteclmrs.status', $data->id) }}"
                                                        data-question="@lang('Are you sure to enable this ssrteclmrs?')">
                                                        <i class="la la-eye"></i> @lang('Active')
                                                    </button>
                                                @else
                                                    <button type="button" class="confirmationBtn dropdown-item"
                                                        data-action="{{ route('manager.suivi.ssrteclmrs.status', $data->id) }}"
                                                        data-question="@lang('Are you sure to disable this ssrteclmrs?')">
                                                        <i class="la la-eye-slash"></i> @lang('Désactive')
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
                @if($ssrteclmrs->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($ssrteclmrs) }}
                    </div>
                @endif
            </div>
        </div>
    </div>
    <x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')
   
    <a href="{{ route('manager.suivi.ssrteclmrs.create') }}" class="btn  btn-outline--primary h-45 addNewCooperative">
        <i class="las la-plus"></i>@lang("Ajouter nouveau")
    </a>
    <a href="{{ route('manager.suivi.ssrteclmrs.exportExcel.ssrteclmrsAll') }}" class="btn  btn-outline--warning h-45"><i class="las la-cloud-download-alt"></i> @lang('Exporter en Excel')</a>
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

        $('form select').on('change', function(){
    $(this).closest('form').submit();
});
    </script>
@endpush
