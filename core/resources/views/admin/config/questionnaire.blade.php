@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card b-radius--10 ">
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Categorie')</th>
                                    <th>@lang('Nom')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Last Update')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($questionnaire as $question)
                                    <tr>
                                    <td>
                                            <span>{{ __($question->categorieQuestion->titre) }}</span>
                                        </td> 
                                        <td style="text-align:left;">
                                            <span>{{ __($question->nom) }}</span>
                                        </td> 
                                        <td>
                                            @php
                                                echo $question->statusBadge;
                                            @endphp
                                        </td>

                                        <td>
                                            <span class="d-block">{{ showDateTime($question->updated_at) }}</span>
                                            <span>{{ diffForHumans($question->updated_at) }}</span>
                                        </td>

                                        <td>
                                            <button type="button" class="btn btn-sm btn-outline--primary  updateType"
                                                data-id="{{ $question->id }}" 
                                                data-nom="{{ $question->nom }}"
                                                data-categoriequestionnaire="{{ $question->categorie_questionnaire_id }}"><i
                                                 class="las la-pen"></i>@lang('Edit')</button>

                                            @if ($question->status == Status::DISABLE)
                                                <button type="button"
                                                    class="btn btn-sm btn-outline--success confirmationBtn"
                                                    data-action="{{ route('admin.config.questionnaire.status', $question->id) }}"
                                                    data-question="@lang('Etes-vous sûr de vouloir activer cette question?')">
                                                    <i class="la la-eye"></i> @lang('Activé')
                                                </button>
                                            @else
                                                <button type="button"
                                                    class="btn btn-sm btn-outline--danger confirmationBtn"
                                                    data-action="{{ route('admin.config.questionnaire.status', $question->id) }}"
                                                    data-question="@lang('Etes-vous sûr de vouloir désactiver cette question?')">
                                                    <i class="la la-eye-slash"></i>@lang('Désactivé')
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
                @if ($questionnaire->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($questionnaire) }}
                    </div>
                @endif
            </div>
        </div>
    </div>
    <div id="typeModel" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Ajouter une question')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i> </button>
                </div>
                <form action="{{ route('admin.config.questionnaire.store') }}" method="POST">
                    @csrf
                    <div class="modal-body"> 
                    <input type="hidden" name='id'>
                    <div class="form-group row">
                                <label class="col-sm-4 control-label">@lang('Type de Formation')</label>
                                <div class="col-xs-12 col-sm-8">
                                <select class="form-control" name="categoriequestionnaire" required>
                                    <option value="">@lang('Selectionner une option')</option>
                                    @foreach($categorieQuestion as $categorie)
                                        <option value="{{ $categorie->id }}" @selected(old('categoriequestionnaire'))>
                                            {{ __($categorie->titre) }}</option>
                                    @endforeach
                                </select>
                                </div>
                            </div> 

        <div class="form-group row">
            {{ Form::label(__('Nom de la question'), null, ['class' => 'control-label col-sm-4']) }}
            <div class="col-xs-12 col-sm-8 col-md-8">
            {!! Form::text('nom', null, array('placeholder' => __('Nom du type de formation'),'class' => 'form-control','required')) !!}
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
    <button class="btn btn-sm btn-outline--primary addType"><i class="las la-plus"></i>@lang("Ajouter nouveau")</button>
@endpush


@push('script')
    <script>
        (function($) {
            "use strict";
            $('.addType').on('click', function() {
                $('#typeModel').modal('show');
            });

            $('.updateType').on('click', function() {
                var modal = $('#typeModel'); 
                modal.find('input[name=id]').val($(this).data('id')); 
                modal.find('input[name=nom]').val($(this).data('nom')); 
                modal.find('select[name=categoriequestionnaire]').val($(this).data('categoriequestionnaire')); 
                modal.modal('show');
            });
        })(jQuery);
    </script>
@endpush
