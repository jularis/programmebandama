@extends('manager.layouts.app')
@section('panel')
@section('filter-section')
<?php use Carbon\Carbon; ?>
<div id="filter-bloc">
<x-filters.filter-box>
    <!-- CLIENT START -->
    <div class="select-box py-2 d-flex pr-2 border-right-grey border-right-grey-sm-0">
        <p class="mb-0 pr-2 f-14 text-dark-grey d-flex align-items-center">Employé(e)</p>
        <div class="select-status">
            <select class="form-control select-picker" name="employee" id="employee" data-live-search="true"
                    data-size="8"> 
                    <option value="all">Tous</option> 
                @foreach ($users as $employee)
                    <x-user-option :user="$employee"/>
                @endforeach
            </select>
        </div>
    </div>

    <!-- CLIENT END -->

    <!-- DESIGNATION START -->
    <div class="select-box d-flex py-2 px-lg-2 px-md-2 px-0 border-right-grey border-right-grey-sm-0">
        <p class="mb-0 pr-2 f-14 text-dark-grey d-flex align-items-center">Désignation</p>
        <div class="select-status">
            <select class="form-control select-picker" name="designation" id="designation">
                <option value="all">Tous</option>
                @foreach ($designations as $designation)
                    <option value="{{ $designation->id }}">{{ $designation->name }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <!-- DESIGNATION END -->


    <!-- SEARCH BY TASK START -->
    <div class="task-search d-flex  py-1 px-lg-3 px-0 border-right-grey align-items-center">
        <form class="w-100 mr-1 mr-lg-0 mr-md-1 ml-md-1 ml-0 ml-lg-0">
            <div class="input-group bg-grey rounded">
                <div class="input-group-prepend">
                    <span class="input-group-text border-0 bg-additional-grey">
                        <i class="fa fa-search f-13 text-dark-grey"></i>
                    </span>
                </div>
                <input type="text" class="form-control f-14 p-1 border-additional-grey" id="search-text-field"
                       placeholder="Tapez pour rechercher">
            </div>
        </form>
    </div>
    <!-- SEARCH BY TASK END -->

    <!-- RESET START -->
    <!-- <div class="select-box d-flex py-1 px-lg-2 px-md-2 px-0">
        <x-forms.button-secondary class="btn-xs d-none" id="reset-filters" icon="times-circle">
            Filtres
        </x-forms.button-secondary>
    </div> -->
    <!-- RESET END -->

    <!-- MORE FILTERS START -->
    <x-filters.more-filter-box>
        <div class="more-filter-items">
            <label class="f-14 text-dark-grey mb-12 text-capitalize" for="usr">Département</label>
            <div class="select-filter mb-4">
                <div class="select-others">
                    <select class="form-control" name="department" data-container="body"
                            id="department">
                        <option value="all">Tous</option>
                        @foreach ($teams as $department)
                            <option value="{{ $department->id }}">{{ $department->department }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

         

        <div class="more-filter-items">
            <label class="f-14 text-dark-grey mb-12 text-capitalize" for="usr">Statut</label>
            <div class="select-filter mb-4">
                <div class="select-others">
                    <select class="form-control" name="status" id="status" data-container="body">
                        <option value="all">Tous</option>
                        <option selected value="active">Activé</option>
                        <option value="deactive">Désactivé</option> 
                    </select>
                </div>
            </div>
        </div>

        <div class="more-filter-items">
            <label class="f-14 text-dark-grey mb-12 text-capitalize" for="usr">Genre</label>
            <div class="select-filter mb-4">
                <div class="select-others">
                    <select class="form-control" name="gender" id="gender" data-container="body">
                        <option value="all">Tous</option>
                        <option value="homme">Homme</option>
                        <option value="femme">Femme</option> 
                    </select>
                </div>
            </div>
        </div>

    </x-filters.more-filter-box>
    <!-- MORE FILTERS END -->
</x-filters.filter-box>
</div>
@endsection

 
    <!-- CONTENT WRAPPER START -->
    <div class="page-wrapper">
        <!-- Add Task Export Buttons Start -->
        <div class="d-flex justify-content-between action-bar">

            <div id="table-actions" class="d-block d-lg-flex align-items-center">
               
                    <x-forms.link-primary :link="route('manager.hr.all.employee.save')" class="mr-3" icon="plus" data-toggle="modal" data-target="#add_employee">
                        Ajouter un Employé
                    </x-forms.link-primary> 
                    <x-forms.link-secondary  :link="route('manager.hr.all.employee.save')" class="mr-3 mb-2 mb-lg-0 d-none d-lg-block"
                                            icon="file-download">
                       Exporter
                    </x-forms.link-secondary>
                    <x-forms.link-secondary  :link="route('manager.hr.all.employee.save')" class="mr-3 mb-2 mb-lg-0 d-none d-lg-block"
                                            icon="file-upload">
                       Importer
                    </x-forms.link-secondary>
               
            </div> 

        </div>
        <!-- Add Task Export Buttons End -->
        <!-- Task Box Start -->
        <div class="d-flex flex-column w-tables rounded mt-3 bg-white table-responsive">

        <table class="table table-striped custom-table datatable">
                            <thead>
                                <tr>
                                    <th>Nom & Prenom</th>
                                    <th>Employee ID</th>
                                    <th>Email</th>
                                    <th>Mobile</th>
                                    <th class="text-nowrap">Join Date</th>
                                    <th>Role</th>
                                    <th class="text-right no-sort">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($users as $items )
                                <tr>
                                    <td>
                                        <h2 class="table-avatar">
                                            <a href="{{ url('employee/profile/'.$items->user_id) }}" class="avatar">
                                            @if($items->user->image)
                                                <img alt="" src="{{ url('core/storage/app/public/'. $items->user->image) }}">
                                            @else
                                            <img alt="" src="{{ url('assets/images/avatar.png') }}">
                                            @endif
                                            </a>
                                            <a href="{{ url('employee/profile/'.$items->user_id) }}">{{ $items->user->lastname }}<span>{{ $items->user->firstname }}</span></a>
                                        </h2>
                                    </td> 
                                    <td>{{ $items->employee_matricule }}</td>

                                    <td>{{ $items->user->email }}</td>
                                    <td>{{  $items->user->mobile }}</td>
                                    <td>{{ Carbon::parse($items->joining_date)->format('d-m-Y') }}</td>
                                    <td>{{ $items->user->user_type }}</td>
                                    <td class="text-right">
                                        <div class="dropdown dropdown-action">
                                            <a href="#" class="action-icon dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="material-icons">more_vert</i></a>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                <a class="dropdown-item" href="{{ url('all/employee/view/edit/'.$items->user_id) }}"><i class="fa fa-pencil m-r-5"></i> Edit</a>
                                                <a class="dropdown-item" href="{{url('all/employee/delete/'.$items->user_id)}}"onclick="return confirm('Are you sure to want to delete it?')"><i class="fa fa-trash-o m-r-5"></i> Delete</a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>

        </div>
        <!-- Task Box End -->
    </div>
    <!-- CONTENT WRAPPER END -->
 
      
        <!-- Add Employee Modal -->
        <div id="add_employee" class="modal custom-modal fade" role="dialog">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Ajouter un employé</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <!-- <form action="{{ route('manager.hr.all.employee.save') }}" method="POST">
                           // @csrf
                     
                        </form> -->
                        <x-form id="save-data-form" :action="route('manager.hr.all.employee.save')">
        <div class="add-client">
            <div class="row p-20">
                <div class="col-lg-9">
                    <div class="row">
                        <div class="col-lg-6 col-md-6">
                            <x-label for="Matricule_Employe"></x-label>
                                <x-input name="matricule" required placeholder="e.g CXV-163">
                                </x-input>
                            </div>
                    <div class="col-lg-6 col-md-6">
                        <x-label for="Nom_Employe"></x-label>
                        <x-input name="nom" required placeholder="e.g Kouame"></x-input> 
                            </div>
                            <div class="col-lg-6 col-md-6">
                                <x-label for="Prenom Employe"></x-label>
                                <x-input name="prenom" placeholder="e.g Gildas" required></x-input>
                                
                            </div>
                            <div class="col-lg-6 col-md-6">
                                <x-forms.text fieldId="email" :fieldLabel="__('Email Employe')"
                                    fieldName="email" fieldRequired="true" :fieldPlaceholder="__('e.g johndoe@domain.com')">
                                </x-forms.text>
                            </div>
                            <div class="col-lg-4 col-md-6">
                          
                                <x-forms.datepicker fieldId="date_of_birth" :fieldLabel="__('Date de Naissance')"
                                    fieldName="date_of_birth" :fieldPlaceholder="__('Select Date')" />
                            </div>
                            <div class="col-lg-4 col-md-6">
                                <x-forms.label class="my-3" fieldId="category_id"
                                    :fieldLabel="__('Désignation')" fieldRequired="true">
                                </x-forms.label>
                                <x-forms.input-group>
                                    <select class="form-control select-picker" name="designation"
                                        id="employee_designation" data-live-search="true">
                                        <option value="">--</option>
                                        @foreach ($designations as $designation)
                                            <option value="{{ $designation->id }}">{{ $designation->name }}</option>
                                        @endforeach
                                    </select>
                                </x-forms.input-group>
                            </div>
                            <div class="col-lg-4 col-md-6">
                                <x-forms.label class="my-3" fieldId="category_id"
                                    :fieldLabel="__('Département')" fieldRequired="true">
                                </x-forms.label>
                                <x-forms.input-group>
                                    <select class="form-control select-picker" name="department"
                                        id="employee_department" data-live-search="true">
                                        <option value="">--</option>
                                        @foreach ($teams as $team)
                                            <option value="{{ $team->id }}">{{ $team->department }}</option>
                                        @endforeach
                                    </select>
                                </x-forms.input-group>
                            </div>
                            
                    </div>
                </div>

                <div class="col-lg-3">
                        <x-forms.file allowedFileExtensions="png jpg jpeg svg bmp" class="mr-0 mr-lg-2 mr-md-2 cropper"
                            :fieldLabel="__('Image de profile')" fieldName="image" fieldId="image"
                            fieldHeight="119" :popover="__('messages.fileFormat.ImageFile')" />
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <x-forms.select fieldId="country" :fieldLabel="__('Pays')" fieldName="country"
                            search="true">
                            @foreach ($countries as $item)
                                <option data-tokens="{{ $item->iso3 }}" data-phonecode = "{{$item->phonecode}}"
                                    data-content="<span class='flag-icon flag-icon-{{ strtolower($item->iso) }} flag-icon-squared'></span> {{ $item->nicename }}"
                                    value="{{ $item->id }}">{{ $item->nicename }}</option>
                            @endforeach
                        </x-forms.select>
                    </div>

                    <div class="col-lg-5 col-md-6">
                        <x-forms.label class="my-3" fieldId="mobile"
                            :fieldLabel="__('Mobile')"></x-forms.label>
                        <x-forms.input-group style="margin-top:-4px">


                            <x-forms.select fieldId="country_phonecode" fieldName="country_phonecode"
                                search="true" style="min-width: 100px;">

                                @foreach ($countries as $item)
                                    <option data-tokens="{{ $item->name }}"
                                            data-content="{{$item->flagSpanCountryCode()}}"
                                            value="{{ $item->phonecode }}">{{ $item->phonecode }}
                                    </option>
                                @endforeach
                            </x-forms.select>

                            <input type="tel" class="form-control height-35 f-14" placeholder="@lang('Contact')"
                                name="mobile" id="mobile">
                        </x-forms.input-group>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <x-forms.select fieldId="gender" :fieldLabel="__('Genre')"
                            fieldName="gender">
                            <option value="homme">Homme</option>
                            <option value="femme">Femme</option> 
                        </x-forms.select>
                    </div>
                    <div class="col-lg-6 col-md-6">
                        <x-forms.datepicker fieldId="joining_date" :fieldLabel="__('Date Entree')"
                            fieldName="joining_date" :fieldPlaceholder="__('Select Date')" fieldRequired="true" />
                    </div>
                    <div class="col-lg-6 col-md-6">
                    
                                <x-label for="Superieur(e) Hierachique"></x-label>
                                <x-forms.input-group>
                                    <select class="form-control select-picker" name="reporting_to"
                                        id="reporting_to" data-live-search="true">
                                        <option value="">--</option>
                                        @foreach ($users as $item)
                                            <x-user-option :user="$item" />
                                        @endforeach 
                                    </select>
                                </x-forms.input-group>
                         
                    </div>     
                    <div class="col-md-12">
                        <div class="form-group my-3">
                            <x-forms.textarea class="mr-0 mr-lg-2 mr-md-2" :fieldLabel="__('Adresse')"
                                fieldName="address" fieldId="address" :fieldPlaceholder="__('e.g. 132, My Street, Kingston, New York 12401')">
                            </x-forms.textarea>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group my-3">
                            <x-forms.textarea class="mr-0 mr-lg-2 mr-md-2" :fieldLabel="__('A propos')"
                                fieldName="about_me" fieldId="about_me" fieldPlaceholder="">
                            </x-forms.textarea>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <x-forms.select fieldId="employment_type" :fieldLabel="__('Type de Contrat')"
                            fieldName="employment_type" :fieldPlaceholder="__('Selectionner')">
                            <option value="">--</option>
                            <option value="plein_temps">Plein Temps</option>
                            <option value="temps_partiel">Temps Partiel</option>
                            <option value="contractuel">Contractuel</option>
                            <option value="interimaire">Interimaire</option>
                            <option value="stagiaire">Stagiaire</option>
                        </x-forms.select>
                    </div>
                    <div class="col-lg-3 col-md-6 d-none internship-date"> 
                    <x-forms.datepicker fieldId="internship_end_date" :fieldLabel="__('Date fin Interim')"
                            fieldName="internship_end_date" :fieldPlaceholder="__('Select Date')"/>
                    </div>
                    <div class="col-lg-3 col-md-6 d-none contract-date">
                        <x-forms.datepicker fieldId="contract_end_date" :fieldLabel="__('Date fin de contrat')"
                            fieldName="contract_end_date" :fieldPlaceholder="__('Select Date')"/>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <x-forms.select fieldId="marital_status" :fieldLabel="__('Statut matrimonial')"
                            fieldName="marital_status" :fieldPlaceholder="__('Selectionner')">
                            <option value="">--</option>
                            <option value="celibataire">Celibataire</option>
                            <option value="marie">Marié</option>
                        </x-forms.select>
                    </div>

                    <div class="col-lg-3 col-md-6 d-none marriage_date">
                        <x-forms.datepicker fieldId="marriage_anniversary_date" :fieldLabel="__('Date de mariage')"
                            fieldName="marriage_anniversary_date" :fieldPlaceholder="__('Selectionner')"/>
                    </div>
 
            </div>
            <x-form-actions>
              
            <x-form-button id="save-employee-form" class="mr-3 btn btn-primary" icon="check">
                        @lang('Enregistrer')
                    </x-form-button>
                     
                    <x-forms.button-cancel class="border-0 " data-dismiss="modal">@lang('Annuler')
                    </x-forms.button-cancel>

                </x-form-actions>
        </div>
        </x-form>
                    </div>
                </div>
            </div>
        </div>
        <!-- /Add Employee Modal --> 
    
@endsection 

@push('script')

<script>
        $("input:checkbox").on('click', function()
        {
            var $box = $(this);
            if ($box.is(":checked"))
            {
                var group = "input:checkbox[class='" + $box.attr("class") + "']";
                $(group).prop("checked", false);
                $box.prop("checked", true);
            }
            else
            {
                $box.prop("checked", false);
            }
        });
        $('#country').change(function(){
            var phonecode = $(this).find(':selected').data('phonecode');
            console.log(phonecode);
            $('#country_phonecode').val(phonecode);
            $('.select-picker').selectpicker('refresh'); 
        }); 
        // select auto id and email
        $('#name').on('change',function()
        {
            $('#employee_id').val($(this).find(':selected').data('employee_id'));
            $('#email').val($(this).find(':selected').data('email'));
        });
        $('#marital_status').change(function(){
            var value = $(this).val();
            if(value == 'marie') {
                $('.marriage_date').removeClass('d-none');
            }
            else {
                $('.marriage_date').addClass('d-none');
            }
        });
        $('#employment_type').change(function(){
            var value = $(this).val();
            if(value == 'contractuel') {
                $('.contract-date').removeClass('d-none');
            }
            else {
                $('.contract-date').addClass('d-none');
            }

            if(value == 'interimaire') {
                $('.internship-date').removeClass('d-none');
            }
            else {
                $('.internship-date').addClass('d-none');
            }
        });
     
    datepicker('#date_of_birth', {
            position: 'bl', 
            maxDate: new Date(),  
            ...datepickerConfig
        });
        datepicker('#marriage_anniversary_date', {
            position: 'bl',
            maxDate: new Date(), 
            ...datepickerConfig
        });
        datepicker('#contract_end_date', {
            position: 'bl',
            ...datepickerConfig 
        });
        datepicker('#internship_end_date', {
            position: 'bl',
            ...datepickerConfig 
        });
        datepicker('#joining_date', {
            position: 'bl',
            ...datepickerConfig 
        });
    </script>
@endpush