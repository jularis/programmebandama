<div class="modal-header">
    <h5 class="modal-title">@lang('app.addNew') @lang('modules.leaves.leaveType')</h5>
    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i> </button>
</div>
<div class="modal-body">
    <div class="portlet-body">
        <!-- <x-form id="createLeave" method="POST" class="ajax-form"> -->
        <form action="{{route('manager.leaveType.store')}}" method="POST">
        @csrf
            <div class="tabs border-bottom-grey">
                <div class="nav" id="nav-tab">
                    <a class="nav-item nav-link f-15 type active" data-toggle="tab" href="#personal" role="tab" aria-controls="nav-type" aria-selected="true">@lang('app.general')</a>
                    <a class="nav-item nav-link f-15 type" data-toggle="tab" href="#promotion" role="tab" aria-controls="nav-type" aria-selected="true">@lang('modules.leaves.entitlement')</a>
                    <a class="nav-item nav-link f-15 type" data-toggle="tab" href="#vacation" role="tab" aria-controls="nav-type" aria-selected="true">@lang('modules.leaves.applicability')</a>
                </div>
            </div>

            <div class="tab-content" id="tab-content">
                    <div class="tab-pane active" id="personal">

                        <input type="hidden" value="true" name="page_reload" id="page_reload">
                        <h3 class="heading-h3 mt-4">@lang('app.general')</h3>
                        
                        <div class="row">
            
                            <div class="col-lg-4">
                                <x-forms.text :fieldLabel="__('modules.leaves.leaveType')"
                                    :fieldPlaceholder="__('placeholders.leaveType')" fieldName="type_name" fieldId="type_name"
                                    fieldValue="" :fieldRequired="true" />
                            </div>
            
                            <div class="col-lg-4">
                                <x-forms.select fieldId="paid" :fieldLabel="__('modules.leaves.leavePaidStatus')" fieldName="paid" search="true" :popover="__('messages.leave.paidStatus')">
                                    <option value="1">@lang('app.paid')</option>
                                    <option value="0">@lang('app.unpaid')</option>
                                </x-forms.select>
                            </div>
            
                            <div class="col-lg-4">
                                <x-forms.number :fieldLabel="__('modules.leaves.noOfLeaves')"
                                    fieldName="leave_number" fieldId="leave_number" fieldValue="0" fieldRequired="true" minValue="0" :popover="__('messages.leave.noOfLeaves')"/>
                            </div>
            
            
                            <div class="col-lg-4">
                                <x-forms.number :fieldLabel="__('modules.leaves.monthLimit')"
                                    fieldName="monthly_limit" fieldId="monthly_limit" fieldValue="0"
                                    fieldRequired="true" :fieldHelp="__('modules.leaves.monthLimitInfo')" minValue="0"
                                    :popover="__('messages.leave.monthlyLimit')"/>
                            </div>
            
                            <div class="col-lg-4">
                                <div class="form-group my-3">
                                    <x-forms.label fieldId="colorselector"  
                                        :fieldLabel="__('modules.sticky.colors')">
                                    </x-forms.label>
                                    <x-forms.input-group id="colorpicker">
                                        <input type="text" class="form-control height-35 f-14"
                                            placeholder="{{ __('placeholders.colorPicker') }}" name="color" id="colorselector">
            
                                        <x-slot name="append">
                                            <span class="input-group-text height-35 colorpicker-input-addon"><i></i></span>
                                        </x-slot>
                                    </x-forms.input-group>
                                </div>
                            </div>
                        </div>
                    
                        
                    </div>
                    <div class="tab-pane" id="promotion">
                        <h3 class="heading-h3 mt-4">@lang('modules.leaves.entitlement')</h3>
                        
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group my-3">
                                    <div class="d-flex align-items-center">
                                        <label class="f-14 text-dark-grey mb-12 mt-2 mr-1">@lang('modules.leaves.effectiveAfter')</label>
                                        &nbsp;<i class="fa fa-question-circle text-dark-grey" data-toggle="popover" data-placement="top" data-html="true"
                                                data-content="{{__('messages.leave.effectiveAfter')}}" data-trigger="hover"></i>
                                        <div class="col-md-4 ml-2">
                                            <x-forms.input-group>
                                                <input type="number" class="form-control height-35 f-14" name="effective_after" id="effective_after" min="0">
                                                <x-slot name="append">
                                                    <select name="effective_type" class="select-picker form-control">
                                                        <option value="day">@lang('app.day')</option>
                                                        <option value="month">@lang('app.month')</option>
                                                    </select>
                                                </x-slot>
                                            </x-forms.input-group>
                                        </div>
                                        <label class="f-14 text-dark-grey mb-12 mt-2">@lang('modules.leaves.ofJoining')</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <div class="d-flex mt-3">
                                        <x-forms.checkbox :fieldLabel="__('modules.leaves.allowedProbation')"
                                                fieldName="allowed_probation" fieldId="allowed_probation" checked="true" fieldValue="1" fieldRequired="true"
                                                :popover="__('messages.leave.allowedProbation')"/>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group my-3">
                                    <div class="d-flex align-items-center">
                                        <label class="f-14 mb-12 mt-2 text-dark-grey mr-1">@lang('modules.leaves.unusedLeaves')</label>
                                        &nbsp;<i class="fa fa-question-circle text-dark-grey" data-toggle="popover" data-placement="top" data-html="true"
                                                data-content="{{__('messages.leave.unusedLeave')}}" data-trigger="hover"></i>
                                        <div class="col-md-4">
                                            <x-forms.input-group>
                                                <select name="unused_leave" class="select-picker form-control">
                                                    <option value="carry forward">@lang('modules.leaves.carryForward')</option>
                                                    <option value="lapse">@lang('modules.leaves.lapse')</option>
                                                    <option value="paid">@lang('app.paid')</option>
                                                </select>
                                            </x-forms.input-group>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <div class="d-flex mt-3">
                                        <x-forms.checkbox :fieldLabel="__('modules.leaves.allowedNotice')"
                                                fieldName="allowed_notice" fieldId="allowed_notice"  checked="true" fieldValue="1" fieldRequired="true"
                                                :popover="__('messages.leave.allowedNotice')"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="vacation">
                        <h3 class="heading-h3 mt-4">@lang('modules.leaves.applicability')</h3>
               
                        <div class="row">
                            <div class="col-lg-4">
                                <div class="form-group my-3">
                                    <x-forms.label fieldId="gender" :fieldLabel="__('modules.employees.gender')" fieldRequired="true">
                                    </x-forms.label>
                                    &nbsp;<i class="fa fa-question-circle text-dark-grey" data-toggle="popover" data-placement="top" data-html="true"
                                            data-content="{{__('messages.leave.gender')}}" data-trigger="hover"></i>
                                    <select class="form-control" multiple name="gender[]"
                                        id="gender">
                                        <option value="Homme" selected>@lang('app.male')</option>
                                        <option value="Femme" selected>@lang('app.female')</option> 
                                </select>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group my-3">
                                    <x-forms.label fieldId="marital_status" :fieldLabel="__('modules.employees.maritalStatus')" fieldRequired="true">
                                    </x-forms.label>
                                    &nbsp;<i class="fa fa-question-circle text-dark-grey" data-toggle="popover" data-placement="top" data-html="true"
                                            data-content="{{__('messages.leave.maritalStatus')}}" data-trigger="hover"></i>
                                    <select class="form-control" multiple name="marital_status[]"
                                        id="marital_status">
                                        <option value="married" selected>@lang('modules.leaves.married')</option>
                                        <option value="unmarried" selected>@lang('modules.leaves.unmarried')</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group my-3">
                                    <x-forms.label fieldId="department" :fieldLabel="__('app.department')" fieldRequired="true">
                                    </x-forms.label>
                                    &nbsp;<i class="fa fa-question-circle text-dark-grey" data-toggle="popover" data-placement="top" data-html="true"
                                            data-content="{{__('messages.leave.department')}}" data-trigger="hover"></i>
                                    <select class="form-control" multiple name="department[]"
                                            id="department">
                                        @foreach ($teams as $team)
                                            <option value="{{ $team->id }}" selected>{{ $team->department }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group my-3">
                                    <x-forms.label fieldId="designation" :fieldLabel="__('app.designation')" fieldRequired="true">
                                    </x-forms.label>
                                    &nbsp;<i class="fa fa-question-circle text-dark-grey" data-toggle="popover" data-placement="top" data-html="true"
                                            data-content="{{__('messages.leave.designation')}}" data-trigger="hover"></i>
                                    <select class="form-control" multiple name="designation[]"
                                            id="designation">
                                        @foreach ($designations as $designation)
                                            <option value="{{ $designation->id }}" selected>{{ $designation->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            
                        </div>
                    </div>

            </div>
            <div class="modal-footer">
 
    <x-forms.button-cancel data-dismiss="modal" class="border-0 mr-3">@lang('app.cancel')</x-forms.button-cancel>
    <!-- <x-forms.button-primary id="save-leave-setting" icon="check">@lang('app.save')</x-forms.button-primary> -->
    <button type="submit" class="btn-primary rounded f-14 p-2" >
            <svg class="svg-inline--fa fa-check fa-w-16 mr-1" aria-hidden="true" focusable="false" data-prefix="fa" data-icon="check" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" data-fa-i2svg=""><path fill="currentColor" d="M173.898 439.404l-166.4-166.4c-9.997-9.997-9.997-26.206 0-36.204l36.203-36.204c9.997-9.998 26.207-9.998 36.204 0L192 312.69 432.095 72.596c9.997-9.997 26.207-9.997 36.204 0l36.203 36.204c9.997 9.997 9.997 26.206 0 36.204l-294.4 294.401c-9.998 9.997-26.207 9.997-36.204-.001z"></path></svg><!-- <i class="fa fa-check mr-1"></i> Font Awesome fontawesome.com -->
        Enregistrer
</button>
</div>
        </form>
        <!-- </x-form> -->
       
    </div>
</div>
 