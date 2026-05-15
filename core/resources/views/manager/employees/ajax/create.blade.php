<link rel="stylesheet" href="{{ asset('assets/vendor/css/tagify.css') }}"> 
<style>
.dropify-wrapper{
    height: 84px;width: 89%;padding: 0px;margin: 0px;
}
    </style>
<div class="row">
    <div class="col-sm-12">
        <x-form id="save-employee-data-form" enctype="multipart/form-data" :action="route('manager.employees.store')">

            <div class="add-client bg-white rounded">
                <h4 class="mb-0 p-20 f-21 font-weight-normal text-capitalize border-bottom-grey">
                    @lang('modules.employees.accountDetails')</h4>
                <div class="row p-20">
                    <div class="col-lg-12">
                        <div class="row">
                          
                            <div class="col-lg-6 col-md-6">
                                <x-forms.text fieldId="lastname" :fieldLabel="__('modules.employees.employeeName')"
                                    fieldName="lastname" fieldRequired="true" :fieldPlaceholder="__('placeholders.name')">
                                </x-forms.text>
                            </div>
                            <div class="col-lg-6 col-md-6">
                                <x-forms.text fieldId="firstname" :fieldLabel="__('Prenoms')"
                                    fieldName="firstname" fieldRequired="true" :fieldPlaceholder="__('Prenoms')">
                                </x-forms.text>
                            </div>
                            <div class="col-lg-4 col-md-6">
                        <x-forms.select fieldId="gender" :fieldLabel="__('modules.employees.gender')"
                            fieldName="gender">
                            <option value="Homme">@lang('app.male')</option>
                            <option value="Femme">@lang('app.female')</option> 
                        </x-forms.select>
                    </div>
                            <div class="col-lg-4 col-md-6">
                                <x-forms.text fieldId="email" :fieldLabel="__('modules.employees.employeeEmail')"
                                    fieldName="email" fieldRequired="true" :fieldPlaceholder="__('placeholders.email')">
                                </x-forms.text>
                            </div>
                            <div class="col-lg-4 col-md-6">
                                <x-forms.datepicker fieldId="date_of_birth" :fieldLabel="__('modules.employees.dateOfBirth')"
                                    fieldName="date_of_birth" :fieldPlaceholder="__('placeholders.date')" />
                            </div>
                            <div class="col-lg-4 col-md-6">
                                <x-forms.label class="my-3" fieldId="category_id"
                                    :fieldLabel="__('app.department')" fieldRequired="true">
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
                            <div class="col-lg-4 col-md-6">
                                <x-forms.label class="my-3" fieldId="category_id"
                                    :fieldLabel="__('app.designation')" fieldRequired="true">
                                </x-forms.label>
                                <x-forms.input-group>
                                    <select class="form-control" name="designation"
                                        id="employee_designation"> 
                                        <option value="">--</option>
                                        @foreach ($designations as $designation)
                                            <option value="{{ $designation->id }}" data-chained="{{ $designation->parent_id }}">{{ $designation->name }}</option>
                                        @endforeach
                                    </select>
                                </x-forms.input-group>
                            </div>
                            
                            <div class="col-lg-4 col-md-6">
                        <x-forms.file allowedFileExtensions="png jpg jpeg svg bmp" class="mr-0 mr-lg-2 mr-md-2 cropper"
                            :fieldLabel="__('modules.profile.profilePicture')" fieldName="image" fieldId="image"
                            fieldHeight="119" :popover="__('messages.fileFormat.ImageFile')" />
                    </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-3 col-md-6">
                        <x-forms.select fieldId="country" :fieldLabel="__('app.country')" fieldName="country"
                            search="true">
                            @foreach ($countries as $item)
                                <option data-tokens="{{ $item->iso3 }}" data-phonecode = "{{$item->phonecode}}"
                                    data-content="<span class='flag-icon flag-icon-{{ strtolower($item->iso) }} flag-icon-squared'></span> {{ $item->nicename }}"
                                    value="{{ $item->id }}">{{ $item->nicename }}</option>
                            @endforeach
                        </x-forms.select>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <x-forms.label class="my-3" fieldId="mobile"
                            :fieldLabel="__('app.mobile')"></x-forms.label>
                        <x-forms.input-group style="margin-top:-4px">


                            <x-forms.select fieldId="country_phonecode" fieldName="country_phonecode"
                                search="true">

                                @foreach ($countries as $item)
                                    <option data-tokens="{{ $item->name }}"
                                            data-content="{{$item->flagSpanCountryCode()}}"
                                            value="{{ $item->phonecode }}">{{ $item->phonecode }}
                                    </option>
                                @endforeach
                            </x-forms.select>

                            <input type="tel" class="form-control height-35 f-14" placeholder="@lang('placeholders.mobile')"
                                name="mobile" id="mobile">
                        </x-forms.input-group>
                    </div>
                    
                    <div class="col-lg-3 col-md-6">
                        <x-forms.datepicker fieldId="joining_date" :fieldLabel="__('modules.employees.joiningDate')"
                            fieldName="joining_date" :fieldPlaceholder="__('placeholders.date')" fieldRequired="true"
                            :fieldValue="now(cooperative()->timezone)->format('Y-m-d')" />
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <x-forms.select fieldId="reporting_to" :fieldLabel="__('modules.employees.reportingTo')"
                            fieldName="reporting_to" :fieldPlaceholder="__('placeholders.date')" search="true">
                            <option value="">--</option>
                            @foreach ($employees as $item)
                                <x-user-option :user="$item" />
                            @endforeach
                        </x-forms.select>
                    </div> 

                    <div class="col-md-12">
                        <div class="form-group my-3">
                            <x-forms.textarea class="mr-0 mr-lg-2 mr-md-2" :fieldLabel="__('app.address')"
                                fieldName="address" fieldId="address" :fieldPlaceholder="__('placeholders.address')">
                            </x-forms.textarea>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group my-3">
                            <x-forms.textarea class="mr-0 mr-lg-2 mr-md-2" :fieldLabel="__('app.about')"
                                fieldName="about_me" fieldId="about_me" fieldPlaceholder="">
                            </x-forms.textarea>
                        </div>
                    </div>
                    <div class="col-md-4">
                    <label class="control-label">@lang('Contrats de travail')</label>
                        <div class="form-group my-3">   
                            <input name="contrat_travail[]" id="contrat_travail1" type="file" multiple="" class="dropify" data-height="70"/> 
                        </div>
                        <div id="insertBefore"></div> 
                        <!--  ADD ITEM START-->
                <div class="row px-lg-4 px-md-4 px-3 pb-3 pt-0 mb-3  mt-2">
                    <div class="col-md-12">
                        <a class="f-15 f-w-500" href="javascript:;" id="add-item"><i
                                class="icons icon-plus font-weight-bold mr-1"></i> @lang('app.add')</a>
                    </div>
                </div>
                <!--  ADD ITEM END-->
                    </div>
                    <div class="col-md-4">

                    <label class="control-label">@lang('Fiches de poste')</label>
                        <div class="form-group my-3">   
                            <input name="fiche_poste[]" id="fiche_poste1" type="file" multiple="" class="dropify" data-height="70"/> 
                        </div>
                        <div id="insertBeforeNew"></div> 
                        <!--  ADD ITEM START-->
                <div class="row px-lg-4 px-md-4 px-3 pb-3 pt-0 mb-3  mt-2">
                    <div class="col-md-12">
                        <a class="f-15 f-w-500" href="javascript:;" id="add-itemNew"><i
                                class="icons icon-plus font-weight-bold mr-1"></i> @lang('app.add')</a>
                    </div>
                </div>
                <!--  ADD ITEM END--> 
                    </div>
                    <div class="col-md-4">

                    <label class="control-label">@lang("Autres documents(CVs, CNI,...)")</label>
                        <div class="form-group my-3">   
                            <input name="autre_document[]" id="autre_document1" type="file" multiple="" class="dropify" data-height="70"/> 
                        </div>
                        <div id="insertBeforeAutre"></div> 
                        <!--  ADD ITEM START-->
                <div class="row px-lg-4 px-md-4 px-3 pb-3 pt-0 mb-3  mt-2">
                    <div class="col-md-12">
                        <a class="f-15 f-w-500" href="javascript:;" id="add-itemAutre"><i
                                class="icons icon-plus font-weight-bold mr-1"></i> @lang('app.add')</a>
                    </div>
                </div>
                <!--  ADD ITEM END--> 
                    </div>
                </div>

                <h4 class="mb-0 p-20 f-21 font-weight-normal text-capitalize border-top-grey">
                    @lang('modules.client.clientOtherDetails')</h4>
                <div class="row p-20">
                        

                    <div class="col-md-12">
                        <x-forms.text fieldId="tags" :fieldLabel="__('app.skills')" fieldName="tags"
                            :fieldPlaceholder="__('placeholders.skills')" />
                    </div>
 
                    <div class="col-lg-3 col-md-6">
                        <x-forms.datepicker fieldId="probation_end_date" :fieldLabel="__('modules.employees.probationEndDate')"
                            fieldName="probation_end_date" :fieldPlaceholder="__('placeholders.date')"
                            :popover="__('messages.probationEndDate')"/>
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <x-forms.datepicker fieldId="notice_period_start_date" :fieldLabel="__('modules.employees.noticePeriodStartDate')"
                            fieldName="notice_period_start_date" :fieldPlaceholder="__('placeholders.date')"
                            :popover="__('messages.noticePeriodStartDate')"/>
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <x-forms.datepicker fieldId="notice_period_end_date" :fieldLabel="__('modules.employees.noticePeriodEndDate')"
                            fieldName="notice_period_end_date" :fieldPlaceholder="__('placeholders.date')"
                            :popover="__('messages.noticePeriodEndDate')"/>
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <x-forms.select fieldId="employment_type" :fieldLabel="__('modules.employees.employmentType')"
                            fieldName="employment_type" fieldRequired="true" :fieldPlaceholder="__('placeholders.date')">
                            <option value="">--</option>
                            <option value="CDI">@lang('CDI')</option>
                            <option value="CDD">@lang('CDD')</option>
                            <option value="Stage">@lang('Stage')</option>
                            <option value="Interim">@lang('Interim')</option>
                            <option value="Consultance">@lang('Consultance')</option>
                            <option value="Journalier">@lang('Journalier')</option>
                        </x-forms.select>
                    </div>

                    <div class="col-lg-3 col-md-6 d-none internship-date">
                        <x-forms.datepicker fieldId="internship_end_date" :fieldLabel="__('modules.employees.internshipEndDate')"
                            fieldName="internship_end_date" :fieldPlaceholder="__('placeholders.date')"/>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <x-forms.datepicker fieldId="contract_end_date" :fieldLabel="__('modules.employees.contractEndDate')"
                            fieldName="contract_end_date" :fieldPlaceholder="__('placeholders.date')"/>
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <x-forms.select fieldId="marital_status" :fieldLabel="__('modules.employees.maritalStatus')"
                            fieldName="marital_status" :fieldPlaceholder="__('placeholders.date')">
                            <option value="unmarried">@lang('modules.leaves.unmarried')</option>
                            <option value="married">@lang('modules.leaves.married')</option>
                        </x-forms.select>
                    </div>

                    <div class="col-lg-3 col-md-6 d-none marriage_date">
                        <x-forms.datepicker fieldId="marriage_anniversary_date" :fieldLabel="__('modules.employees.marriageAnniversaryDate')"
                            fieldName="marriage_anniversary_date" :fieldPlaceholder="__('placeholders.date')"/>
                    </div>

                    <input type ="hidden" name="add_more" value="false" id="add_more" />

                </div>
                 
                <x-form-actions>
                <x-form-button id="save-employee-form" class="mr-3 btn btn-primary" icon="check">
                        @lang('Enregistrer')
                    </x-form-button>
                    
                    <x-forms.button-cancel class="border-0 " data-dismiss="modal">@lang('app.cancel')
                    </x-forms.button-cancel>

                </x-form-actions>
            </div>
        </x-form>

    </div>
</div>

<script src="{{ asset('assets/vendor/jquery/tagify.min.js') }}"></script> 
<script>
    $(document).ready(function() { 
        $("#employee_designation").chained("#employee_department");
        $('.dropify').dropify();
        $('.dropify-fr').dropify();
        $('.custom-date-picker').each(function(ind, el) {
            datepicker(el, {
                position: 'bl',
                ...datepickerConfig
            });
        });

        datepicker('#joining_date', {
            position: 'bl',
            ...datepickerConfig
        });

        datepicker('#probation_end_date', {
            position: 'bl',
            ...datepickerConfig
        });

        datepicker('#notice_period_start_date', {
            position: 'bl',
            ...datepickerConfig
        });

        datepicker('#notice_period_end_date', {
            position: 'bl',
            ...datepickerConfig
        });

        datepicker('#marriage_anniversary_date', {
            position: 'bl',
            ...datepickerConfig
        });

        datepicker('#date_of_birth', {
            position: 'bl',
            maxDate: new Date(),
            ...datepickerConfig
        });

        datepicker('#internship_end_date', {
            position: 'bl',
            ...datepickerConfig
        });

        datepicker('#contract_end_date', {
            position: 'bl',
            ...datepickerConfig
        });

        $('#marital_status').change(function(){
            var value = $(this).val();
            if(value == 'married') {
                $('.marriage_date').removeClass('d-none');
            }
            else {
                $('.marriage_date').addClass('d-none');
            }
        })

        $('#employment_type').change(function(){
            var value = $(this).val();
            if(value == 'on_contract') {
                $('.contract-date').removeClass('d-none');
            }
            else {
                $('.contract-date').addClass('d-none');
            }

            if(value == 'internship') {
                $('.internship-date').removeClass('d-none');
            }
            else {
                $('.internship-date').addClass('d-none');
            }
        })
        var input = document.querySelector('input[name=tags]'),
            // init Tagify script on the above inputs
            tagify = new Tagify(input);

        $('#save-more-employee-form').click(function() {

            $('#add_more').val(true);

            const url = "{{ route('manager.employees.store') }}";
            var data = $('#save-employee-data-form').serialize();
            saveEmployee(data, url, "#save-more-employee-form");


        });

        $('#save-employee-form').click(function() {

            const url = "{{ route('manager.employees.store') }}";
            var data = $('#save-employee-data-form').serialize();
             
            saveEmployee(data, url, "#save-employee-form");

        });

        function saveEmployee(data, url, buttonSelector) {

            $.easyAjax({
                url: url,
                container: '#save-employee-data-form',
                type: "POST",
                disableButton: true,
                blockUI: true,
                buttonSelector: buttonSelector,
                file: true,
                data: data,
                success: function(response) {
                    

                    if (response.status == 'success') {
                        if ($(MODAL_XL).hasClass('show')) {
                            $(MODAL_XL).modal('hide');
                            window.location.reload();
                        }
                        else if(response.add_more == true) {

                            var right_modal_content = $.trim($(RIGHT_MODAL_CONTENT).html());

                            if(right_modal_content.length) {

                                $(RIGHT_MODAL_CONTENT).html(response.html.html);
                                $('#add_more').val(false);
                            }
                            else {

                                $('.content-wrapper').html(response.html.html);
                                init('.content-wrapper');
                                $('#add_more').val(false);
                            }

                        }
                        else {

                            window.location.href = response.redirectUrl;

                        }

                        if (typeof showTable !== 'undefined' && typeof showTable === 'function') {
                            showTable();
                        }

                    }

                }
            });
        }

        $('#random_password').click(function() {
            const randPassword = Math.random().toString(36).substr(2, 8);

            $('#password').val(randPassword);
        });

        $('#designation-setting-add').click(function() {
            const url = "{{ route('designations.create') }}";
            $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
            $.ajaxModal(MODAL_LG, url);
        })

        $('.department-setting').click(function() {
            const url = "{{ route('departments.create') }}";
            $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
            $.ajaxModal(MODAL_LG, url);
        });

        $('#country').change(function(){
            var phonecode = $(this).find(':selected').data('phonecode');
            $('#country_phonecode').val(phonecode);
            $('.select-picker').selectpicker('refresh');
        });


        init(RIGHT_MODAL);
    });

    function checkboxChange(parentClass, id) {
        var checkedData = '';
        $('.' + parentClass).find("input[type= 'checkbox']:checked").each(function() {
            checkedData = (checkedData !== '') ? checkedData + ', ' + $(this).val() : $(this).val();
        });
        $('#' + id).val(checkedData);
    }

 // Add More Inputs
 var $insertBefore = $('#insertBefore');
 var $insertBeforeNew = $('#insertBeforeNew');
 var $insertBeforeAutre = $('#insertBeforeAutre');
        var i = 1;
        var a = 1;
        var b = 1;
 $('#add-item').click(function() {
            i += 1;
             
            $(`<div id="addMoreBox${i}" class="row pl-20 pr-20 clearfix">
            <div class="form-group my-3" style="padding: 0px;">  
            <div class="input-group mb-3"> 
                            <input name="contrat_travail[]" id="contrat_travail${i}" type="file" class="dropify" multiple="" data-height="78"/> <button type="button"
                                        class="btn btn-outline-secondary border-grey"
                                        data-toggle="tooltip" style="width: 10px;"><a href="javascript:;" class="d-flex align-items-center justify-content-center mt-5 remove-item" data-item-id="${i}" style="position: relative;top: -29px;"><i class="fa fa-times-circle f-20 text-lightest"></i></a></button>
                                        </div></div> `)
                .insertBefore($insertBefore);

                 $(".dropify").dropify(); 
            // Recently Added date picker assign 
        });

        // Remove fields
        $('body').on('click', '.remove-item', function() {
            var index = $(this).data('item-id');
            $('#addMoreBox' + index).remove();
        });
        $('#add-itemNew').click(function() {
            a += 1;
             
            $(`<div id="addMoreBoxNew${a}" class="row pl-20 pr-20 clearfix">
            <div class="form-group my-3" style="padding: 0px;">  
            <div class="input-group mb-3"> 
                            <input name="fiche_poste[]" id="fiche_poste${a}" type="file" class="dropify" multiple="" data-height="78"/> <button type="button"
                                        class="btn btn-outline-secondary border-grey"
                                        data-toggle="tooltip" style="width: 10px;"><a href="javascript:;" class="d-flex align-items-center justify-content-center mt-5 remove-itemNew" data-item-id="${a}" style="position: relative;top: -29px;"><i class="fa fa-times-circle f-20 text-lightest"></i></a></button>
                                        </div></div> `)
                .insertBefore($insertBeforeNew);

                 $(".dropify").dropify(); 
            // Recently Added date picker assign 
        });
        $('body').on('click', '.remove-itemNew', function() {
            var index = $(this).data('item-id');
            $('#addMoreBoxNew' + index).remove();
        });
        $('#add-itemAutre').click(function() {
            b += 1;
             
            $(`<div id="addMoreBoxAutre${b}" class="row pl-20 pr-20 clearfix">
            <div class="form-group my-3" style="padding: 0px;">  
            <div class="input-group mb-3"> 
                            <input name="autre_document[]" id="autre_document${b}" type="file" class="dropify" multiple="" data-height="78"/> <button type="button"
                                        class="btn btn-outline-secondary border-grey"
                                        data-toggle="tooltip" style="width: 10px;"><a href="javascript:;" class="d-flex align-items-center justify-content-center mt-5 remove-itemAutre" data-item-id="${b}" style="position: relative;top: -29px;"><i class="fa fa-times-circle f-20 text-lightest"></i></a></button>
                                        </div></div> `)
                .insertBefore($insertBeforeAutre);

                 $(".dropify").dropify(); 
            // Recently Added date picker assign 
        });
        $('body').on('click', '.remove-itemAutre', function() {
            var index = $(this).data('item-id');
            $('#addMoreBoxAutre' + index).remove();
        });
</script>
