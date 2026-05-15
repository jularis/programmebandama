 
<link rel="stylesheet" href="{{ asset('assets/vendor/css/bootstrap-datepicker3.min.css') }}"> 
<link rel="stylesheet" href="{{ asset('assets/vendor/css/daterangepicker.css') }}">

<div class="row">
    <div class="col-sm-12">
        <x-form id="save-lead-data-form">
            <div class="add-client bg-white rounded">
                <h4 class="mb-0 p-20 f-21 font-weight-normal text-capitalize border-bottom-grey">
                    @lang('modules.leaves.assignLeave')</h4>
                <div class="row p-20">

                    <div class="col-lg-4 col-md-6">
                        @if (isset($defaultAssign))
                            <x-forms.label class="mt-3" fieldId="" :fieldLabel="__('app.name')" fieldRequired="true">
                            </x-forms.label>
                            <input type="hidden" name="user_id" id="user_id" value="{{ $defaultAssign->id }}">
                            <input type="text" value="{{ $defaultAssign->name }}"
                                class="form-control height-35 f-15 readonly-background" readonly>
                        @else
                            <x-forms.select fieldId="user_id" :fieldLabel="__('modules.messages.chooseMember')" fieldName="user_id" search="true"
                                fieldRequired="true">
                                <option value="">--</option>
                                @foreach ($employees as $employee)
                                    <x-user-option :user="$employee" />
                                @endforeach
                            </x-forms.select>
                        @endif
                    </div>

                    <div class="col-lg-4 col-md-6">
                        <x-forms.label class="mt-3" fieldId="" :fieldLabel="__('modules.leaves.leaveType')" fieldRequired="true">
                        </x-forms.label>
                        <x-forms.input-group>
                            <select class="form-control select-picker" name="leave_type_id" id="leave_type_id"
                                data-live-search="true">
                                <option value="">--</option>
                                @if (isset($leaveTypes))
                                    @foreach ($leaveTypes as $leaveType)
                                        <option value="{{ $leaveType->id }}">{{ $leaveType->type_name }}
                                        </option>
                                    @endforeach
                                @endif

                                @if (isset($leaveQuotas))
                                    @foreach ($leaveQuotas as $leave)
                                        @php
                                            $leaveType = new \App\Models\LeaveType();
                                        @endphp

                                        @if ($leave->employeeLeave > 0)
                                            @if($leaveType->leaveTypeCodition($leave, $userRole))
                                                    <option value="{{ $leave->id }}">{{ $leave->type_name }}</option>
                                            @endif
                                        @endif
                                    @endforeach
                                @endif
                            </select>

                       
                                
                       
                        </x-forms.input-group>
                    </div>

                   
                        <div class="col-lg-4 col-md-6">
                            <x-forms.select fieldId="status" :fieldLabel="__('app.status')" fieldName="status" search="true">
                                <option value="pending">@lang('app.pending')</option>
                                <option value="approved">@lang('app.approved')</option>
                            </x-forms.select>
                        </div>
                   

                    <div class="col-md-6 col-lg-8">
                        <div class="form-group my-3">
                            <label class="f-14 text-dark-grey mb-12 w-100" for="usr">@lang('modules.leaves.selectDuration')</label>
                            <div class="d-block d-lg-flex d-md-flex">
                                <x-forms.radio fieldId="duration_single" :fieldLabel="__('modules.leaves.single')" fieldName="duration"
                                    fieldValue="single" checked="true">
                                </x-forms.radio>
                                <x-forms.radio fieldId="duration_multiple" :fieldLabel="__('modules.leaves.multiple')" fieldValue="multiple"
                                    fieldName="duration"></x-forms.radio>

                                <x-forms.radio fieldId="half_day_first" :fieldLabel="__('modules.leaves.firstHalf')" fieldName="duration"
                                    fieldValue="first_half">
                                </x-forms.radio>
                                <x-forms.radio fieldId="half_day_second" :fieldLabel="__('modules.leaves.secondHalf')" fieldValue="second_half"
                                    fieldName="duration"></x-forms.radio>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6 single_date_div">
                        <x-forms.text :fieldLabel="__('app.date')" fieldName="leave_date" fieldId="single_date" :fieldPlaceholder="__('app.date')"
                            :fieldValue="now('Africa/Abidjan')->translatedFormat('Y-m-d')" />
                    </div>

                    <div class="col-lg-4 col-md-6 d-none multi_date_div">
                        <x-forms.text :fieldLabel="__('messages.selectMultipleDates')" fieldName="multi_date" fieldId="multi_date" :fieldPlaceholder="__('messages.selectMultipleDates')"
                            :fieldValue="now('Africa/Abidjan')->translatedFormat('Y-m-d')" />
                    </div>
                    <div class="col-lg-4 col-md-6 date-range-days mt-5">
                        <p id="users" class="mt-2 badge badge-secondary"></p>

                    </div>
                    <div class="col-md-12">
                        <div class="form-group my-3">
                            <x-forms.textarea class="mr-0 mr-lg-2 mr-md-2" :fieldLabel="__('modules.leaves.reason')" fieldName="reason"
                                fieldId="reason" fieldRequired="true" :fieldPlaceholder="__('placeholders.leave.reason')">
                            </x-forms.textarea>
                        </div>
                    </div>

                    <div class="col-lg-12"> 
                        
                        <x-forms.file class="mr-0 mr-lg-2 mr-md-2 cropper"
                            :fieldLabel="__('app.menu.addFile')" fieldName="file" fieldId="leave-file-upload"
                            fieldHeight="119" :popover="__('messages.leaveFileMessage')" />

                        <input type="hidden" name="leaveID" id="leaveID">
                    </div>
                    
                    <input type="hidden" name="multiStartDate" id="multiStartDate">
                    <input type="hidden" name="multiEndDate" id="multiEndDate">
                </div> 
               
                <x-form-actions>
                    <x-forms.button-primary id="save-leave-form" class="mr-3" icon="check">@lang('app.save')
                    </x-forms.button-primary>
                    <x-forms.button-cancel :link="route('manager.leaves.index')" class="border-0">@lang('app.cancel')
                    </x-forms.button-cancel>
                </x-form-actions>

            </div>
        </x-form>

    </div>
</div> 
<script src="{{ asset('assets/vendor/jquery/bootstrap-datepicker.min.js') }}"></script> 
<script src="{{ asset('assets/vendor/jquery/daterangepicker.min.js')}}" defer=""></script>

<script>
    $(document).ready(function() {
        $('.dropify').dropify();

// Translated
$('.dropify-fr').dropify({
    messages: {
        default: 'Glissez-déposez un fichier ici ou cliquez',
        replace: 'Glissez-déposez un fichier ou cliquez pour remplacer',
        remove: 'Supprimer',
        error: 'Désolé, le fichier trop volumineux'
    }
});
 
        getDate();
        const dp1 = datepicker('#single_date', {
            onSelect: function () {
                getDate();
            },
            position: 'bl',
            ...datepickerConfig
        });

        const dp2 = $('#multi_date').daterangepicker({
            linkedCalendars: false,
            multidate: true,
            todayHighlight: true,
            format: 'yyyy-mm-d'

        });

        $('#multi_date').change(function() {
            var dates = $(this).val();

            var startDate = moment(new Date(dates.split(' - ')[0]));
            var endDate = moment(new Date(dates.split(' - ')[1]));
            var totalDays = endDate.diff(startDate, 'days')+1;

            startDate = startDate.format('YYYY-MM-DD');
             
            endDate = endDate.format('YYYY-MM-DD');

            var multiDate = [];
            multiDate = [startDate, endDate];
            $('#multi_date').val(multiDate);

            $('#multiStartDate').val(startDate);
            $('#multiEndDate').val(endDate);
            $('.date-range-days').html(totalDays +' Jours sélectionnés');
        })

        $('input[type=radio][name=duration]').change(function() {
            if (this.value == 'multiple') {
                
                const dp2 = $('#multi_date').daterangepicker('clearDates').daterangepicker({
                    linkedCalendars: false,
                    multidate: true,
                    todayHighlight: true,
                    format: 'yyyy-mm-d'

                });
            }
        });

        // setMinDate($('#user_id').val());

        // $('#user_id').on('change', function(e) {
        //     setMinDate(e.target.value);
        // });

        function setMinDate(employeeID) {
            var employees = @json($employees);
            var employee = employees.filter(function(item) {
                return item.id == employeeID;
            });

            if(employees.length > 0 && employee[0] !== undefined)
            {
                var minDate = new Date(employee[0].employee_detail.joining_date);
                dp1.setMin(minDate);
                $('#multi_date').daterangepicker('setStartDate', minDate);
            }
        }

        $('#save-leave-form').click(function() {
            var dateRange = $('#multi_date').data('daterangepicker');
            startDate = dateRange.startDate.format('YYYY-MM-DD');
            endDate = dateRange.endDate.format('YYYY-MM-DD');

            var multiDate = [];
            multiDate = [startDate, endDate];
            $('#multi_date').val(multiDate);

            const url = "{{ route('manager.leaves.store') }}";

            $.easyAjax({
                url: url,
                container: '#save-lead-data-form',
                type: "POST",
                disableButton: true,
                blockUI: true,
                buttonSelector: "#save-leave-form",
                data: $('#save-lead-data-form').serialize()+'&multiStartDate='+startDate + '&multiEndDate='+endDate,
                success: function(response) {
                    if (response.status == 'success') {
                        $('#leaveID').val(response.leaveID);
                        // myDropzone.processQueue();
                        window.location.href = response.redirectUrl;
                    }
                }
            });
        });

        $('body').on('click', '.add-lead-type2', function() {
            var url = "{{ route('manager.leaveType.create') }}";
            $(MODAL_XL + ' ' + MODAL_HEADING).html('...');
            $.ajaxModal(MODAL_XL, url);
            $(MODAL_XL).modal('show');
        });

        $("input[name=duration]").click(function() {
            ($('input[name=duration]:checked').val() == "multiple") ? getDate('multiple') : getDate();

            $(this).val() == 'multiple' ? $('.multi_date_div').removeClass('d-none') : $(
                '.multi_date_div').addClass('d-none');
            $(this).val() == 'multiple' ? $('.single_date_div').addClass('d-none') : $(
                '.single_date_div').removeClass('d-none');
        });

        $('#user_id').change(function() {
            var id = $(this).val();
            if (id == '') {
                id = 0;
            }
            var url = "{{ route('manager.employee-leaves.employee_leave_types', ':id') }}";
            url = url.replace(':id', id);
            $.easyAjax({
                url: url,
                type: "GET",
                container: '#save-lead-data-form',
                blockUI: true,
                success: function(data) {
                    $('#leave_type_id').html(data.data);
                    $('#leave_type_id').selectpicker('refresh');
                }
            })
        });

        function getDate(value) {
            let url = "{{ route('manager.leaves.date') }}";
            date = $('#single_date').val();

            if (value == 'multiple') {
                date = '';
            }

            $.easyAjax({
                type: 'GET',
                url: url,
                container: '#save-lead-data-form',
                data: {
                    'date': date
                },
                success: function(response) {
                    if(response.status == 'success'){
                        if(response.users > 0 && response.users < 2){
                            $('#users').text(response.users+' @lang("modules.leaves.employeeOnLeave")');
                        }else if(response.users > 0){
                            $('#users').text(response.users+' @lang("modules.leaves.employeesOnLeave")');
                        }else{
                            $('#users').text('');
                        }
                    }
                }
            });
        };

        init(RIGHT_MODAL);
    });
</script>
