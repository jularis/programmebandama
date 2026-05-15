<link rel="stylesheet" href="{{ asset('assets/vendor/css/bootstrap-colorpicker.css') }}" />

<div class="modal-header">
    <h5 class="modal-title">@lang('app.update') @lang('modules.attendance.shift')</h5>
    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><i class="las la-times"></i> </button>
</div>

<div class="modal-body">
    <div class="portlet-body">
        <!-- <x-form id="createTicket" method="PUT" class="ajax-form"> -->
        <form action="{{route('manager.employee-shifts.update', $employeeShift->id)}}" method="POST">
        @csrf
        @method('PUT')
            <div class="form-group">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="bootstrap-timepicker">
                            <x-forms.text :fieldLabel="__('modules.attendance.shiftName')" :fieldPlaceholder="__('placeholders.shiftName')" fieldName="shift_name"
                                fieldId="shift_name" fieldRequired="true" :fieldValue="$employeeShift->shift_name" />
                        </div>
                    </div>

                    <div class="col-md-6">
                        <x-forms.text :fieldLabel="__('modules.attendance.shiftShortCode')" fieldName="shift_short_code" fieldId="shift_short_code"
                            :fieldPlaceholder="__('placeholders.shiftShortCode')" :fieldValue="$employeeShift->shift_short_code" fieldRequired="true" />
                    </div>

                    <div class="col-md-6">
                        <div class="form-group my-3">
                            <x-forms.label fieldId="colorselector" :fieldLabel="__('modules.sticky.colors')">
                            </x-forms.label>
                            <x-forms.input-group id="colorpicker">
                                <input type="text" class="form-control height-35 f-14"
                                    placeholder="{{ __('placeholders.colorPicker') }}" name="color"
                                    id="colorselector">

                                <x-slot name="append">
                                    <span class="input-group-text height-35 colorpicker-input-addon"><i></i></span>
                                </x-slot>
                            </x-forms.input-group>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="bootstrap-timepicker">
                            <x-forms.text :fieldLabel="__('modules.attendance.officeStartTime')" :fieldPlaceholder="__('placeholders.hours')" fieldName="office_start_time"
                                fieldId="office_start_time" fieldRequired="true" :fieldValue="\Carbon\Carbon::createFromFormat(
                                    'H:i:s',
                                    $employeeShift->office_start_time,
                                )->translatedFormat('H:i')" />
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="bootstrap-timepicker">
                            <x-forms.text :fieldLabel="__('modules.attendance.officeEndTime')" :fieldPlaceholder="__('placeholders.hours')" fieldName="office_end_time"
                                fieldId="office_end_time" fieldRequired="true" :fieldValue="\Carbon\Carbon::createFromFormat('H:i:s', $employeeShift->office_end_time)->format(
                                    'H:i',
                                )" />
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="bootstrap-timepicker">
                            <x-forms.text :fieldLabel="__('modules.attendance.halfDayMarkTime')" :fieldPlaceholder="__('placeholders.hours')" fieldName="halfday_mark_time"
                                fieldId="halfday_mark_time" :fieldValue="!is_null($employeeShift->halfday_mark_time) ? \Carbon\Carbon::createFromFormat(
                                    'H:i:s',
                                    $employeeShift->halfday_mark_time,
                                )->translatedFormat('H:i') : ''" />
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <x-forms.number fieldName="early_clock_in" fieldId="early_clock_in"
                        :fieldLabel="__('modules.attendance.earlyClockIn')" :fieldValue="$employeeShift->early_clock_in"/>
                    </div>

                    <div class="col-lg-4">
                        <x-forms.number class="mr-0 mr-lg-2 mr-md-2" :fieldLabel="__('modules.attendance.lateMark')" fieldName="late_mark_duration"
                            fieldId="late_mark_duration" fieldRequired="true" :fieldValue="$employeeShift->late_mark_duration" />
                    </div>

                    <div class="col-lg-4">
                        <x-forms.number class="mr-0 mr-lg-2 mr-md-2" :fieldLabel="__('modules.attendance.checkininday')" fieldName="clockin_in_day"
                            fieldId="clockin_in_day" fieldRequired="true" :fieldValue="$employeeShift->clockin_in_day" />
                    </div>

                    <div class="col-lg-12">
                        <div class="form-group my-3">
                            <x-forms.label fieldId="office_open_days" :fieldLabel="__('modules.attendance.officeOpenDays')" fieldRequired="true">
                            </x-forms.label>
                            <div class="d-lg-flex d-sm-block justify-content-between ">
                                <x-forms.weeks fieldName="office_open_days[]" :checkedDays="$openDays"></x-forms.weeks>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <div class="modal-footer">
    <x-forms.button-cancel data-dismiss="modal" class="border-0 mr-3">@lang('app.cancel')</x-forms.button-cancel>
    <button type="submit" class="btn-primary rounded f-14 p-2" >
            <svg class="svg-inline--fa fa-check fa-w-16 mr-1" aria-hidden="true" focusable="false" data-prefix="fa" data-icon="check" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" data-fa-i2svg=""><path fill="currentColor" d="M173.898 439.404l-166.4-166.4c-9.997-9.997-9.997-26.206 0-36.204l36.203-36.204c9.997-9.998 26.207-9.998 36.204 0L192 312.69 432.095 72.596c9.997-9.997 26.207-9.997 36.204 0l36.203 36.204c9.997 9.997 9.997 26.206 0 36.204l-294.4 294.401c-9.998 9.997-26.207 9.997-36.204-.001z"></path></svg><!-- <i class="fa fa-check mr-1"></i> Font Awesome fontawesome.com -->
        Enregistrer
</button>
</div>
            </form>
        <!-- </x-form> -->
    </div>
</div>
 
<script src="{{ asset('assets/vendor/jquery/bootstrap-colorpicker.js') }}"></script>
<script>
    $('#colorselector').colorpicker({
        "color": "{{ $employeeShift->color }}"
    });

    $('#office_end_time, #office_start_time, #halfday_mark_time').timepicker({ 
            showMeridian: false, 
    });

    $('#early_clock_in').timepicker({ 
           showMeridian: false, 
           minuteStep: 1
       });
       $('#clockin_in_day').timepicker({ 
           showMeridian: false, 
           minuteStep: 1
       });
       $('#late_mark_duration').timepicker({ 
           showMeridian: false, 
           minuteStep: 1
       });
    // save type
    $('#save-employee-shift').click(function() {
        
        $.easyAjax({
            url: "{{ route('manager.employee-shifts.update', $employeeShift->id) }}",
            container: '#createTicket',
            type: "POST",
            blockUI: true,
            blockUI: '#save-employee-shift',
            disableButton: true,
            buttonSelector: '#save-signature',
            data: $('#createTicket').serialize(),
            success: function(response) {
                if (response.status == "success") {
                    window.location.reload();
                }
            }
        })
    });
</script>
