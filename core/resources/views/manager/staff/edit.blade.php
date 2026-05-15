@extends('manager.layouts.app')
@section('panel')
    <div class="row mb-none-30">
        <div class="col-lg-12 col-md-12 mb-30">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('manager.staff.store') }}" method="POST" id="flocal">
                        @csrf

                        <input type="hidden" name="id" value="{{ $staff->id }}">
                        <div class="row">
                            <div class="form-group col-lg-4">
                                <label>@lang('Section')</label>
                                <input id="chkall" type="checkbox"> @lang('Selectionner tout')
                                <select class="form-control select-picker selectAll" name="section[]" id="section" multiple required>
                                   
                                    @foreach ($sections as $section)
                                        <option value="{{ $section->id }}" @selected(in_array($section->id, $userSection))>
                                            {{ __($section->libelle) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-lg-4">
                                <label>@lang('Selectionner une Localite')</label>
                                <input id="chkall2" type="checkbox"> @lang('Selectionner tout')
                                <select class="form-control select2-multi-select selectAll" id="localite" name="localite[]" multiple
                                    required> 
                                    @foreach ($localites as $localite)
                                        @php
                                            if(!in_array($localite->id, $userLocalite))
                                            {
                                                continue;
                                            }
                                             
                                        @endphp
                                        <option value="{{ $localite->id }}" data-chained="{{ $localite->section->id }}"
                                            selected>
                                            {{ __($localite->nom) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-lg-4">
                                <label for="rolePermission" class="form-label">Role</label>
                                <select class="form-control select-picker" name="role[]" multiple required>
                                    <option value="">Selectionner un rôle</option>
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->id }}"
                                            {{ in_array($role->name, $userRole) ? 'selected' : '' }}>
                                            {{ $role->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            

                        </div>
                        <div class="row">
                        <div class="form-group col-lg-4">
                                <label>@lang('Type de compte')</label>
                                <select class="form-control" name="type_compte" required>
                                    <option value="web" @selected('web' == $staff->type_compte)>Web</option>
                                    <option value="mobile" @selected('mobile' == $staff->type_compte)>Mobile</option>
                                    <option value="mobile-web" @selected('mobile-web' == $staff->type_compte)>Mobile & Web</option>
                                </select>
                            </div>
                            <div class="form-group col-lg-4">
                                <label>@lang('Nom de famille')</label>
                                <input type="text" class="form-control" value="{{ __($staff->lastname) }}"
                                    name="lastname" required>
                            </div>
                            <div class="form-group col-lg-4">
                                <label>@lang('Prenom(s)')</label>
                                <input type="text" class="form-control" name="firstname"
                                    value="{{ __($staff->firstname) }}" required>
                            </div>
                            

                        </div>

                        <div class="row">
                            
                            <div class="form-group col-lg-4">
                                <label>@lang('Email Adresse')</label>
                                <input type="email" class="form-control" name="email" value="{{ $staff->email }}"
                                    required>
                            </div>
                            <div class="form-group col-lg-4">
                                <label>@lang('Adresse')</label>
                                <input type="text" class="form-control" name="adresse" value="{{ $staff->adresse }}">
                            </div>
                            <div class="form-group col-lg-4">
                                <label>@lang('Contact')</label>
                                <input type="text" class="form-control" name="mobile" value="{{ $staff->mobile }}"
                                    required>
                            </div>
                            
                        </div>
                        <div class="row">
                        <div class="form-group col-lg-4">
                                <label>@lang("Nom d'utilisateur")</label>
                                <input type="text" class="form-control" name="username"
                                    value="{{ __($staff->username) }}" required>
                            </div>
                            <div class="form-group col-lg-4">
                                <label>@lang('Mot de passe')</label>
                                <input type="password" class="form-control" name="password">
                            </div>

                            <div class="form-group col-lg-4">
                                <label>@lang('Confirm Password')</label>
                                <input type="password" class="form-control" name="password_confirmation">
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn--primary w-100 h-45"> @lang('Envoyer')</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <x-back route="{{ route('manager.staff.index') }}" /> 
@endpush
@push('script')
<script type="text/javascript"> 

  $('#section').change(function(){

var urlsend='{{ route("manager.staff.getLocalite") }}';

  $.ajax({
            type:'get',
            url: urlsend,
            data: $('#flocal').serialize(),
            success:function(html){
            $('#localite').html(html);  
            }

        });
});

    </script>
@endpush