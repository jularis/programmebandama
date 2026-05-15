@extends('manager.layouts.app')

@push('styles')
    <style>
        .attendance-total {
            width: 10%;
        }

        .table .thead-light th,
        .table tr td,
        .table h5 {
            font-size: 12px;
        }
        .mw-250{
            min-width: 125px;
        }
    </style>
@endpush

@section('filter-section')
<div id="filter-bloc">
    <x-filters.filter-box>
        <div class="select-box d-flex py-2 pr-2 border-right-grey border-right-grey-sm-0" style="width: 20%;">
            <p class="mb-0 pr-2 f-14 text-dark-grey d-flex align-items-center">@lang('Employé')</p>
            <div class="select-status">
                <select class="form-control select-picker" name="user_id" id="user_id" data-live-search="true"
                        data-size="8">
                  
                        <option value="all">@lang('Tous')</option>
                 
                    @forelse ($employees as $item)
                        <x-user-option :user="$item" :selected="request('employee_id') == $item->id"></x-user-option>
                    @empty
                        <x-user-option :user="user()"></x-user-option>
                    @endforelse
                </select>
            </div>
        </div>

        
            <div class="select-box d-flex py-2 px-lg-2 px-md-2 px-0 border-right-grey border-right-grey-sm-0" style="width: 20%;">
                <p class="mb-0 pr-2 f-14 text-dark-grey d-flex align-items-center">@lang('Departement')</p>
                <div class="select-status">
                    <select class="form-control select-picker" name="department" id="department" data-live-search="true"
                            data-size="8">
                        <option value="all">@lang('Tous')</option>
                        @foreach ($departments as $department)
                            <option value="{{ $department->id }}">{{ $department->department }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="select-box d-flex py-2 px-lg-2 px-md-2 px-0 border-right-grey border-right-grey-sm-0" style="width: 20%;">
                <p class="mb-0 pr-2 f-14 text-dark-grey d-flex align-items-center">@lang('Designation')</p>
                <div class="select-status">
                    <select class="form-control select-picker" name="designation" id="designation" data-live-search="true"
                            data-size="8">
                        <option value="all">Tous</option>
                        @foreach ($designations as $designation)
                            <option value="{{ $designation->id }}">{{ $designation->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        

        <div class="select-box d-flex py-2 px-lg-2 px-md-2 px-0 border-right-grey border-right-grey-sm-0" style="width: 15%;">
            <p class="mb-0 pr-2 f-14 text-dark-grey d-flex align-items-center">@lang('Mois')</p>
            <div class="select-status">
                <select class="form-control select-picker" name="month" id="month" data-live-search="true"
                        data-size="8">
                    <x-forms.months :selectedMonth="$month" fieldRequired="true"/>
                </select>
            </div>
        </div>

        <div class="select-box d-flex py-2 px-lg-2 px-md-2 px-0 border-right-grey border-right-grey-sm-0" style="width: 15%;">
            <p class="mb-0 pr-2 f-14 text-dark-grey d-flex align-items-center">@lang('Annee')</p>
            <div class="select-status">
                <select class="form-control select-picker" name="year" id="year" data-live-search="true" data-size="8">
                    @for ($i = $year; $i >= $year - 4; $i--)
                        <option @if ($i == $year) selected @endif value="{{ $i }}">{{ $i }}</option>
                    @endfor
                </select>
            </div>
        </div>

        <!-- RESET START -->
        <div class="select-box d-flex py-1 px-lg-2 px-md-2 px-0" style="width: 10%;">
            <x-forms.button-secondary class="btn-xs d-none" id="reset-filters" icon="times-circle">
                @lang('Effacer')
            </x-forms.button-secondary>
        </div>
        <!-- RESET END -->

    </x-filters.filter-box>
</div>
@endsection
 
@section('panel')
    <!-- CONTENT WRAPPER START -->
    <div class="content-wrapper px-4">

        <div class="d-grid d-lg-flex d-md-flex action-bar">
            <div id="table-actions" class="flex-grow-1 align-items-center">
                 
                    <x-forms.link-primary :link="route('manager.hr.attendances.create')" class="mr-3 float-left"
                                          icon="plus">
                        @lang('Marquer une Presence')
                    </x-forms.link-primary>
                
                <x-forms.button-secondary id="export-all" class="mr-3 mb-2 mb-lg-0" icon="file-export">
                    @lang('Exporter')
                </x-forms.button-secondary>
                  
            </div>

            <div class="btn-group mt-2 mt-lg-0 mt-md-0 ml-0 ml-lg-3 ml-md-3" role="group">
                <a href="{{ route('manager.hr.attendances.index') }}" class="btn btn-secondary f-14 btn-active"
                   data-toggle="tooltip"
                   data-original-title="@lang('app.summary')"><i class="side-icon fa fa-list-ul"></i></a>

                <a href="{{ route('manager.hr.attendances.by_member') }}" class="btn btn-secondary f-14" data-toggle="tooltip"
                   data-original-title="@lang('modules.attendance.attendanceByMember')"><i
                        class="side-icon fa fa-person"></i></a>

                <a href="{{ route('manager.hr.attendances.by_hour') }}" class="btn btn-secondary f-14" data-toggle="tooltip"
                   data-original-title="@lang('modules.attendance.attendanceByHour')"><i class="fa fa-clock"></i></a>

                @if(attendance_setting() !=null)
                    <a href="{{ route('manager.hr.attendances.by_map_location') }}" class="btn btn-secondary f-14"
                       data-toggle="tooltip" data-original-title="@lang('modules.attendance.attendanceByLocation')"><i
                            class="fa fa-map-marked-alt"></i></a>
                @endif

            </div>
        </div>

        <!-- Task Box Start -->
        <x-cards.data class="mt-3">
            <div class="row">
               <div class="col-md-12">
                <span class="f-w-500 mr-1">@lang('Note'):</span> <i class="fa fa-star text-warning"></i> <i
                    class="fa fa-arrow-right text-lightest f-11 mx-1"></i> @lang('Ferie') &nbsp;|&nbsp;<i
                    class="fa fa-calendar-week text-red"></i> <i class="fa fa-arrow-right text-lightest f-11 mx-1"></i>
                @lang('Jours off') &nbsp;|&nbsp;
                <i class="fa fa-check text-primary"></i> <i class="fa fa-arrow-right text-lightest f-11 mx-1"></i>
                @lang('Presence') &nbsp;|&nbsp; <i class="fa fa-star-half-alt text-red"></i> <i
                    class="fa fa-arrow-right text-lightest f-11 mx-1"></i>
                @lang('Demi-Journee') &nbsp;|&nbsp; <i class="fa fa-exclamation-circle text-primary"></i> <i
                    class="fa fa-arrow-right text-lightest f-11 mx-1"></i>
                @lang('Tard') &nbsp;|&nbsp; <i class="fa fa-times text-lightest"></i> <i
                    class="fa fa-arrow-right text-lightest f-11 mx-1"></i>
                @lang('Absence') &nbsp;|&nbsp; <i class="fa fa-plane-departure text-danger"></i> <i
                    class="fa fa-arrow-right text-lightest f-11 mx-1"></i>
                @lang('Voyage')

            </div>
            </div>

            <div class="row">
                <div class="col-md-12" id="attendance-data"></div>
            </div>
        </x-cards.data>
        <!-- Task Box End -->
    </div>
    <!-- CONTENT WRAPPER END -->


@endsection
 
@push('script')
<script>

$('#user_id, #department, #designation, #month, #year').on('change', function () {
    if ($('#user_id').val() != "all") {
        $('#reset-filters').removeClass('d-none');
        showTable();
    } else if ($('#department').val() != "all") {
        $('#reset-filters').removeClass('d-none');
        showTable();
    } else if ($('#designation').val() != "all") {
        $('#reset-filters').removeClass('d-none');
        showTable();
    } else if ($('#month').val() != "all") {
        $('#reset-filters').removeClass('d-none');
        showTable();
    } else if ($('#year').val() != "all") {
        $('#reset-filters').removeClass('d-none');
        showTable();
    } else {
        $('#reset-filters').addClass('d-none');
        showTable();
    }
});

$('#reset-filters').click(function () {
    $('#filter-form')[0].reset();
    $('.filter-box .select-picker').selectpicker("refresh");
    $('#reset-filters').addClass('d-none');
    showTable();
});

function showTable(loading = true) {

    var year = $('#year').val();
    var month = $('#month').val();
 
    var userId = $('#user_id').val();
    var department = $('#department').val();
    var designation = $('#designation').val();

    //refresh counts
    var url = "{{ route('manager.hr.attendances.index') }}";

    var token = "{{ csrf_token() }}";

    $.easyAjax({
        data: {
            '_token': token,
            year: year,
            month: month,
            department: department,
            designation: designation,
            userId: userId
        },
        url: url,
        blockUI: loading,
        container: '.content-wrapper',
        success: function (response) {
            $('#attendance-data').html(response.data);
        }
    });

}


$('#attendance-data').on('click', '.view-attendance', function () {
    var attendanceID = $(this).data('attendance-id');
    var url = "{{ route('manager.hr.attendances.show', ':attendanceID') }}";
    url = url.replace(':attendanceID', attendanceID);
    alert(url)
    $(MODAL_XL + ' ' + MODAL_HEADING).html('...');
    $.ajaxModal(MODAL_XL, url);
    $(MODAL_XL).modal('show'); 
});

$('#attendance-data').on('click', '.edit-attendance', function (event) {
    var attendanceDate = $(this).data('attendance-date');
    var userData = $(this).closest('tr').children('td:first');
    var userID = $(this).data('user-id');
    var year = $('#year').val();
    var month = $('#month').val();

    var url = "{{ route('manager.hr.attendances.mark', [':userid',':day',':month',':year']) }}";
    url = url.replace(':userid', userID);
    url = url.replace(':day', attendanceDate);
    url = url.replace(':month', month);
    url = url.replace(':year', year);
   
    $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
    $.ajaxModal(MODAL_XL, url);
 $(MODAL_XL).modal('show'); 
});

function editAttendance(id) {
    var url = "{{ route('manager.hr.attendances.edit', [':id']) }}";
    url = url.replace(':id', id);

    $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
    $.ajaxModal(MODAL_LG, url);
    $(MODAL_LG).modal('show'); 
}

function addAttendance(userID) {
    var date = $('#date').val();
    const attendanceDate = date.split("-");
    let dayTime = attendanceDate[2];
    dayTime = dayTime.split(' ');
    let day = dayTime[0];
    let month = attendanceDate[1];
    let year = attendanceDate[0];

    var url = "{{ route('manager.hr.attendances.add-user-attendance', [':userid', ':day', ':month', ':year']) }}";
    url = url.replace(':userid', userID);
    url = url.replace(':day', day);
    url = url.replace(':month', month);
    url = url.replace(':year', year);

    $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
    $.ajaxModal(MODAL_LG, url);
    $(MODAL_LG).modal('show'); 
}

showTable(true);

$('#export-all').click(function () {
    var year = $('#year').val();
    var month = $('#month').val();
    var department = $('#department').val();
    var designation = $('#designation').val();
    var userId = $('#user_id').val();

    var url =
        "{{ route('manager.hr.attendances.export_all_attendance', [':year', ':month', ':userId', ':department', ':designation']) }}";
    url = url.replace(':year', year).replace(':month', month).replace(':userId', userId).replace(':department', department).replace(':designation', designation);
    window.location.href = url;

});
</script>
@endpush