@extends('manager.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card b-radius--10 mb-3">
                <div class="card-body">
                    <form action="">
                        <div class="d-flex flex-wrap gap-4">
                            <input type="hidden" name="table" value="suivi_formaions" />
                            <div class="flex-grow-1">
                                <label>@lang('Recherche par Mot(s) cle(s)')</label>
                                <input type="text" name="search" value="{{ request()->search }}" class="form-control">
                            </div>
                            <div class="flex-grow-1">
                                <label>@lang('Localites')</label>
                                <select name="localite" class="form-control">
                                    <option value="">@lang('Toutes')</option>
                                    @foreach ($localites as $local)
                                        <option value="{{ $local->id }}">{{ $local->nom }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="flex-grow-1">
                                <label>@lang('Modules')</label>
                                <select name="module" class="form-control">
                                    <option value="">@lang('Tous')</option>
                                    @foreach ($modules as $module)
                                        <option value="{{ $module->id }}">{{ $module->nom }}</option>
                                    @endforeach
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
            <div class="card b-radius--10 ">
                <div class="card-body  p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Localite')</th>
                                    <th>@lang('Formateur Interne')</th>
                                    <th>@lang('Entreprise')</th>
                                    <th>@lang('Formateur Externe')</th>
                                    <th>@lang('Lieu')</th>
                                    <th>@lang('Modules') </th>
                                    <th>@lang('Date formation')</th>
                                    <th>@lang('Ajoutée le')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($formations as $formation)
                                    <tr>
                                        <td>
                                            <span class="fw-bold">{{ $formation->localite->nom }}</span>
                                        </td>
                                        @if($formation->user != null)
                                            <td>
                                                <span> <a href="{{ route('manager.suivi.formation.edit', $formation->id) }}">
                                                        <span>@</span>{{ $formation->user ? $formation->user->lastname : '' }}
                                                        {{ $formation->user ? $formation->user->firstname : ''}}
                                                    </a></span>
                                            </td>
                                        @else
                                            <td>@lang('Pas de formateur interne pour cette formation')</td>
                                        @endif
                                        @if($formation->formateur_externe == 'oui')
                                        <td>
                                            <span>
                                                @foreach ($formation->entreprises->unique('nom_entreprise') as $entreprise)
                                                    {{ $entreprise->nom_entreprise }}
                                                    @if (!$loop->last)
                                                        ,
                                                    @endif
                                                @endforeach
                                            </span>
                                        </td>
                                        @else
                                        <td>@lang("Pas d'entreprise externe pour cette formation")</td>
                                        @endif

                                        @if($formation->formateur_externe == 'oui')
                                        <td>
                                            <span>@</span>
                                                    @foreach ($formation->formateurs as $formateur)
                                                        {{ $formateur->nom_formateur }}
                                                        {{ $formateur->prenom_formateur }}

                                                        @if (!$loop->last)
                                                            ,
                                                        @endif
                                                    @endforeach
                                        @else
                                        <td>@lang('Pas de formateur externe pour cette formation')</td>
                                        @endif

                                        <td>
                                            <span>{{ $formation->lieu_formation }}</span>
                                        </td>

                                        <td>
                                        @if (!empty($formation->typeFormationTheme()))
                                         @php
                                         $listeModules = array();
                                            $modules = $formation->typeFormationTheme()->get();
                                         @endphp
                                                    @foreach($modules as $v) 

                                                    @if(in_array(Str::remove("\r\n",utf8_decode(Str::between($v->typeFormation->nom,"(",")"))), $listeModules))
                                                       @php continue; @endphp
                                                    @endif
                                                        <span class="badge badge--success">{{ Str::remove("\r\n",utf8_decode(Str::between($v->typeFormation->nom,"(",")")))  }}</span>
                                                        @php
                                                        $listeModules[]=Str::remove("\r\n",utf8_decode(Str::between($v->typeFormation->nom,"(",")")));
                                                        @endphp
                                                    @endforeach
                                                    
                                                @endif
                                        </td>
                                        <td>
                                            <span class="d-block">{{ showDateTime($formation->date_formation) }}</span>
                                            {{-- <span>{{ diffForHumans($formation->date_formation) }}</span> --}}
                                        </td>
                                        <td>
                                            <span class="d-block">{{ showDateTime($formation->created_at) }}</span>
                                            <span>{{ diffForHumans($formation->created_at) }}</span>
                                        </td>
                                        <td> @php echo $formation->statusBadge; @endphp </td>
                                        <td>
                                            <a href="{{ route('manager.suivi.formation.visiteur.visiteurs',$formation->id) }}"
                                                class="icon-btn btn--info ml-1">@lang('Visiteurs')</a>
                                                <a href="{{ route('manager.suivi.formation.exportExcel.formationAll',['id'=>encrypt($formation->id)]) }}" class="btn  btn-outline--success ml-1"><i
            class="las la-cloud-download-alt"></i> @lang('Exporter')</a>
                                            <button type="button" class="btn btn-sm btn-outline--primary"
                                                data-bs-toggle="dropdown" aria-expanded="false"><i
                                                    class="las la-ellipsis-v"></i>@lang('Action')
                                            </button>
                                            
                                            <div class="dropdown-menu p-0">
                                                <a href="{{ route('manager.suivi.formation.edit', $formation->id) }}"
                                                    class="dropdown-item"><i class="la la-pen"></i>@lang('Editer')</a>
                                                <a href="{{ route('manager.suivi.formation.show', $formation->id) }}"
                                                    class="dropdown-item"><i class="las la-file-invoice"></i>@lang('Détail')</a>
                                                    
                                                @if ($formation->status == Status::DISABLE)
                                                    <button type="button" class="confirmationBtn  dropdown-item"
                                                        data-action="{{ route('manager.suivi.formation.status', $formation->id) }}"
                                                        data-question="@lang('Are you sure to enable this formation?')">
                                                        <i class="la la-eye"></i> @lang('Active')
                                                    </button>
                                                @else
                                                    <button type="button" class="confirmationBtn dropdown-item"
                                                        data-action="{{ route('manager.suivi.formation.status', $formation->id) }}"
                                                        data-question="@lang('Are you sure to disable this formation?')">
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
                @if ($formations->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($formations) }}
                    </div>
                @endif
            </div>
        </div>
    </div>
    <x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')
    <x-search-form placeholder="Search here..." />
    <a href="{{ route('manager.suivi.formation.create') }}" class="btn  btn-outline--primary h-45 addNewCooperative">
        <i class="las la-plus"></i>@lang('Ajouter nouveau')
    </a>
    <a href="{{ route('manager.suivi.formation.exportExcel.formationAll') }}" class="btn  btn-outline--warning h-45"><i
            class="las la-cloud-download-alt"></i> @lang('Exporter en Excel')</a>
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
                maxDate: new Date(),
                range: true,
                multipleDatesSeparator: "-",
                language: 'fr'
            });

            let url = new URL(window.location).searchParams;
            if (url.get('localite') != undefined && url.get('localite') != '') {
                $('select[name=localite]').find(`option[value=${url.get('localite')}]`).attr('selected', true);
            }
            if (url.get('module') != undefined && url.get('module') != '') {
                $('select[name=module]').find(`option[value=${url.get('module')}]`).attr('selected', true);
            }

        })(jQuery)

        $('form select').on('change', function(){
    $(this).closest('form').submit();
});
    </script>
@endpush
