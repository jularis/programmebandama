@extends('manager.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card b-radius--10 mb-3">
                <div class="card-body">
                    <form action="">
                        <div class="d-flex flex-wrap gap-4">
                            <input type="hidden" name="table" value="archivages" />
                            <div class="flex-grow-1">
                                <label>@lang('Recherche par Mot(s) cle(s)')</label>
                                <input type="text" name="search" value="{{ request()->search }}" class="form-control">
                            </div>
                            <div class="flex-grow-1">
                                <label>@lang('Type Archive')</label>
                                <select name="type_archive" class="form-control">
                                    <option value="">@lang('Toutes')</option>
                                    @foreach ($typearchives as $local)
                                        <option value="{{ $local->id }}" {{ request()->type_archive == $local->id ? 'selected' : '' }}>{{ $local->nom }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                             
                            <div class="flex-grow-1">
                                <label>@lang('Date')</label>
                                <input name="date" type="text" class="dates form-control"
                                    placeholder="@lang('Date de début - Date de fin')" autocomplete="off" value="{{ request()->date }}">
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
                                    <th>@lang('Cooperative')</th> 
                                    <th>@lang('Titre')</th>
                                    <th>@lang('Type Archive')</th>
                                    <th>@lang('Document')</th> 
									 <th>@lang('Status')</th> 
                                    <th>@lang('Ajoutée le')</th>
                                   <th>@lang('Mise a jour')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($archivages as $archivage)
                                    <tr> 
                                        <td>
                                            <span class="small">{{ $archivage->cooperative->name }}</span>
                                        </td> 

                                        <td>
                                            <span class="fw-bold">
                                                <a href="{{ route('manager.archivages.edit', $archivage->id) }}">
                                                    <span>@</span>{{ $archivage->titre }}
                                                </a>
                                            </span>
                                        </td>
                                        <td>
                                            <span>{{ $archivage->typeArchive->nom }}</span>
                                        </td>
                                        <td>
										<?php
                    $ext=substr(strrchr($archivage->document,'.'),1);
                    if(!$ext){$ext='file';}
                      ?>
                      <a href="<?php echo asset('core/storage/app/public/'.$archivage->document); ?>" target="_blank"><img src="<?php echo asset('public/images'); ?>/<?php echo $ext; ?>.png" width="30px" alt=""><i class="fa fa-download fa-2x" style="color: #05b50b;"></i></a> 
                                        </td> 
										<td> @php echo $archivage->statusBadge; @endphp </td>
                                        <td>
                                            <span class="d-block">{{ showDateTime($archivage->created_at) }}</span>
                                            <span>{{ diffForHumans($archivage->created_at) }}</span>
                                        </td>
										 <td>
                                            <span class="d-block">{{ showDateTime($archivage->updated_at) }}</span>
                                            <span>{{ diffForHumans($archivage->updated_at) }}</span>
                                        </td>
                                        
                                        <td> 
                                            <button type="button" class="btn btn-sm btn-outline--primary"
                                                data-bs-toggle="dropdown" aria-expanded="false"><i
                                                    class="las la-ellipsis-v"></i>@lang('Action')
                                            </button>
                                            <div class="dropdown-menu p-0">
                                                <a href="{{ route('manager.archivages.edit', $archivage->id) }}"
                                                    class="dropdown-item"><i class="la la-pen"></i>@lang('Editer')</a>
                                                @if ($archivage->status == Status::DISABLE)
                                                    <button type="button" class="confirmationBtn  dropdown-item"
                                                        data-action="{{ route('manager.archivages.status', $archivage->id) }}"
                                                        data-question="@lang('Are you sure to enable this archivage?')">
                                                        <i class="la la-eye"></i> @lang('Active')
                                                    </button>
                                                @else
                                                    <button type="button" class="confirmationBtn dropdown-item"
                                                        data-action="{{ route('manager.archivages.status', $archivage->id) }}"
                                                        data-question="@lang('Are you sure to disable this archivage?')">
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
                @if ($archivages->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($archivages) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

     
    <x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')
    <a href="{{ route('manager.archivages.create') }}" class="btn  btn-outline--primary h-45">
        <i class="las la-plus"></i>@lang('Ajouter une archive')
    </a> 
    <a href="{{ route('manager.archivages.export') }}" class="btn  btn-outline--warning h-45"><i
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
            if (url.get('status') != undefined && url.get('status') != '') {
                $('select[name=status]').find(`option[value=${url.get('status')}]`).attr('selected', true);
            }

        })(jQuery)
    </script>
@endpush
