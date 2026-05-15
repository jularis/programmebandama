@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card b-radius--10 ">
                <div class="card-body  p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Cooperative')</th>
                                    <th>@lang('Section')</th>
                                    <th>@lang('Localite')</th>
                                    <th>@lang('Code Localite')</th>
                                    <th>@lang('Type de localites')</th>
                                    <th>@lang('Ajoutée le')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($cooperativeLocalites as $localite)
                                    <tr>
                                        <td>
                                            <span class="fw-bold">{{ __($localite->section->cooperative->name) }}</span>
                                        </td>
                                        <td>
                                            <span class="fw-bold">{{ __($localite->section->libelle) }}</span>
                                        </td>
                                        <td> 
                                            <span class="small">
                                                <a href="{{ route('admin.cooperative.localite.edit', $localite->id) }}">
                                                    <span>@</span>{{$localite->nom }}
                                                </a>
                                            </span>
                                        </td>
                                        <td>
                                            <span>{{ $localite->codeLocal }}</span>
                                        </td>
                                        <td>
                                            <span>{{ $localite->type_localites }}<br>{{ $localite->sousprefecture }}</span>
                                        </td>
                                        <td>
                                            <span class="d-block">{{ showDateTime($localite->created_at) }}</span>
                                            <span>{{ diffForHumans($localite->created_at) }}</span>
                                        </td>
                                        <td> @php echo $localite->statusBadge; @endphp </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-outline--primary" data-bs-toggle="dropdown" aria-expanded="false"><i
                                                    class="las la-ellipsis-v"></i>@lang('Action')
                                             </button>
                                            <div class="dropdown-menu p-0">
                                                <a href="{{ route('admin.cooperative.localite.edit', $localite->id) }}"
                                                    class="dropdown-item"><i class="la la-pen"></i>@lang('Edit')</a>
                                               
                                                @if ($localite->status == Status::DISABLE)
                                                    <button type="button" class="confirmationBtn  dropdown-item"
                                                        data-action="{{ route('admin.cooperative.localite.status', $localite->id) }}"
                                                        data-question="@lang('Are you sure to enable this localite?')">
                                                        <i class="la la-eye"></i> @lang('Activé')
                                                    </button>
                                                @else
                                                    <button type="button" class=" confirmationBtn   dropdown-item"
                                                        data-action="{{ route('admin.cooperative.localite.status', $localite->id) }}"
                                                        data-question="@lang('Are you sure to disable this localite?')">
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
                @if ($cooperativeLocalites->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($cooperativeLocalites) }}
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <div id="typeModel" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Importer des localites')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i> </button>
                </div>
                <form action="{{ route('admin.cooperative.localite.uploadcontent') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">   
                        <p>Fichier d'exemple à utiliser :<a href="{{ asset('assets/localite-import-exemple.xlsx') }}" target="_blank">@lang('localite-import-exemple.xlsx')</a></p>
                 
                    <div class="form-group row">
                                <label class="col-sm-4 control-label">@lang('Type de Formation')</label>
                                <div class="col-xs-12 col-sm-8">
                                <select class="form-control" name="coop_id" required>
                                    <option value="">@lang('Selectionner une option')</option>
                                    @foreach($cooperatives as $coop)
                                        <option value="{{ $coop->id }}" @selected(old('cooperative'))>
                                            {{ __($coop->name) }}</option>
                                    @endforeach
                                </select>
                                </div>
                            </div> 

        <div class="form-group row">
            {{ Form::label(__('Fichier(.xls, .xlsx)'), null, ['class' => 'control-label col-sm-4']) }}
            <div class="col-xs-12 col-sm-8 col-md-8">
            <input type="file" name="uploaded_file" accept=".xls, .xlsx" class="form-control dropify-fr" placeholder="Choisir une image" id="image" required> 
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
    <x-search-form placeholder="Search here..." />
    <a href="{{ route('admin.cooperative.localite.create') }}" class="btn  btn-outline--primary h-45 addNewCooperative">
        <i class="las la-plus"></i>@lang("Ajouter nouveau")
    </a>
    <a class="btn  btn-outline--info h-45 addType"><i class="las la-cloud-upload-alt"></i> Importer des Localites</a>
@endpush
@push('style')
    <style>
        .table-responsive {
            overflow-x: auto;
        }
    </style>
@endpush
@push('script')
    <script>
        (function($) {
            "use strict";

            $('.addType').on('click', function() {
                $('#typeModel').modal('show');
            });
              

        })(jQuery)
    </script>
@endpush

