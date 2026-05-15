@extends('manager.layouts.app')

@push('style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/full-calendar/main.min.css') }}">
    <style>
        .filter-box {
            z-index: 1;
        }
        .fc-daygrid-day-frame {cursor:pointer;}
    </style>
@endpush

@section('filter-section')
<div id="filter-bloc">
    <x-filters.filter-box>
        <!-- SEARCH BY TASK START -->
        <div class="task-search d-flex  py-1 pr-lg-3 px-0 border-right-grey align-items-center">
            <form class="w-100 mr-1 mr-lg-0 mr-md-1 ml-md-1 ml-0 ml-lg-0">
                <div class="input-group bg-grey rounded">
                    <div class="input-group-prepend">
                        <span class="input-group-text border-0 bg-additional-grey">
                            <i class="fa fa-search f-13 text-dark-grey"></i>
                        </span>
                    </div>
                    <input type="text" class="form-control f-14 p-1 border-additional-grey" id="search-text-field"
                        placeholder="@lang('app.startTyping')">
                </div>
            </form>
        </div>
        <!-- SEARCH BY TASK END -->

        <!-- RESET START -->
        <div class="select-box d-flex py-1 px-lg-2 px-md-2 px-0">
            <x-forms.button-secondary class="btn-xs d-none" id="reset-filters" icon="times-circle">
                @lang('app.clearFilters')
            </x-forms.button-secondary>
        </div>
        <!-- RESET END -->
    </x-filters.filter-box>
    </div>
@endsection

 
@section('panel')
    <!-- CONTENT WRAPPER START -->
    <div class="content-wrapper">
        <!-- Add Task Export Buttons Start -->
        <div class="d-grid d-lg-flex d-md-flex action-bar">
            <div id="table-actions" class="flex-grow-1 align-items-center">
                 
                    <x-forms.link-primary :link="route('manager.holidays.create')"
                        data-redirect-url="{{ route('manager.holidays.index') }}" class="mr-3 openRightModal float-left"
                        icon="plus">
                        @lang('modules.holiday.addNewHoliday')
                    </x-forms.link-primary>
                    <x-forms.button-secondary icon="check" class="mr-3 float-left mb-2 mb-lg-0 mb-md-0" id="mark-holiday">
                        @lang('modules.holiday.markSunday')
                    </x-forms.button-secondary>
                
            </div>

            <div class="btn-group mt-2 mt-lg-0 mt-md-0 ml-0 ml-lg-3 ml-md-3" role="group" aria-label="Basic example">
                <a href="{{ route('manager.holidays.index') }}" class="btn btn-secondary f-14 btn-active" data-toggle="tooltip"
                    data-original-title="@lang('app.menu.calendar')"><i class="side-icon bi bi-calendar"></i></a>
                <a href="{{ route('manager.holidays.table_view') }}" class="btn btn-secondary f-14" data-toggle="tooltip"
                    data-original-title="@lang('modules.leaves.tableView')"><i class="side-icon bi bi-list-ul"></i></a>
            </div>
        </div>

        <!-- leave table Box Start -->
        <div class="d-flex flex-column w-tables rounded mt-3 bg-white">
            <x-cards.data>
                <div id="calendar"></div>
            </x-cards.data>
        </div>
        <!-- leave table End -->

    </div>
    <!-- CONTENT WRAPPER END -->

@endsection

@push('script')
    <script src="{{ asset('assets/vendor/full-calendar/main.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/full-calendar/locales-all.min.js') }}"></script>

    <script>
         $('.dropify').dropify();
        $('#search-text-field').on('change keyup',
            function() {
                if ($('#search-text-field').val() != "") {
                    $('#reset-filters').removeClass('d-none');
                    loadData();
                } else {
                    $('#reset-filters').addClass('d-none');
                    loadData();
                }
            });

        $('#reset-filters').click(function() {
            $('#filter-form')[0].reset();

            $('.filter-box .select-picker').selectpicker("refresh");
            $('#reset-filters').addClass('d-none');
            loadData();
        });

        var initialLocaleCode = 'fr';
        var calendarEl = document.getElementById('calendar');

        var calendar = new FullCalendar.Calendar(calendarEl, {
            locale: initialLocaleCode,
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
            },
            firstDay: parseInt("{{ attendance_setting()?->week_start_from }}"),
            navLinks: true, // can click day/week names to navigate views
            selectable: false,
            selectMirror: true,
            eventClick: function(arg) {
                getEventDetail(arg.event.id);
            },
            dateClick: function(info) {
                const myDate = moment(info.date).format('YYYY-MM-DD');
                addEvent(myDate);
            },
            editable: false,
            dayMaxEvents: true, // allow "more" link when too many events
            events: {
                url: "{{ route('manager.holidays.index') }}",
                extraParams: function() {
                    var searchText = $('#search-text-field').val();

                    return {
                        searchText: searchText,
                    };
                }
            },
            eventTimeFormat: {
                hour: '2-digit',
                minute: '2-digit',
                meridiem: false
            }
        });

        calendar.render();

        function loadData() {
            calendar.refetchEvents();
            calendar.destroy();
            calendar.render();
        }

        // show leave detail in right modal
        var addEvent = function(date) {
            openTaskDetail();
            let url = `{{ route('manager.holidays.create') }}?date=${date}`;

            $.easyAjax({
                url: url,
                blockUI: true,
                container: RIGHT_MODAL,
                historyPush: true,
                success: function(response) {
                    if (response.status == "success") {
                        $(RIGHT_MODAL_CONTENT).html(response.html);
                        $(RIGHT_MODAL_TITLE).html(response.title);
                    }
                },
                error: function(request, status, error) {
                    if (request.status == 403) {
                        $(RIGHT_MODAL_CONTENT).html(
                            '<div class="align-content-between d-flex justify-content-center mt-105 f-21">403 | Permission Denied</div>'
                        );
                    } else if (request.status == 404) {
                        $(RIGHT_MODAL_CONTENT).html(
                            '<div class="align-content-between d-flex justify-content-center mt-105 f-21">404 | Not Found</div>'
                        );
                    } else if (request.status == 500) {
                        $(RIGHT_MODAL_CONTENT).html(
                            '<div class="align-content-between d-flex justify-content-center mt-105 f-21">500 | Something Went Wrong</div>'
                        );
                    }
                }
            });
        }

        // show leave detail in right modal
        var getEventDetail = function(id) {
            openTaskDetail();
            var url = "{{ route('manager.holidays.show', ':id') }}";
            url = url.replace(':id', id);

            $.easyAjax({
                url: url,
                blockUI: true,
                container: RIGHT_MODAL,
                historyPush: true,
                success: function(response) {
                    if (response.status == "success") {
                        $(RIGHT_MODAL_CONTENT).html(response.html);
                        $(RIGHT_MODAL_TITLE).html(response.title);
                    }
                },
                error: function(request, status, error) {
                    if (request.status == 403) {
                        $(RIGHT_MODAL_CONTENT).html(
                            '<div class="align-content-between d-flex justify-content-center mt-105 f-21">403 | Permission Denied</div>'
                        );
                    } else if (request.status == 404) {
                        $(RIGHT_MODAL_CONTENT).html(
                            '<div class="align-content-between d-flex justify-content-center mt-105 f-21">404 | Not Found</div>'
                        );
                    } else if (request.status == 500) {
                        $(RIGHT_MODAL_CONTENT).html(
                            '<div class="align-content-between d-flex justify-content-center mt-105 f-21">500 | Something Went Wrong</div>'
                        );
                    }
                }
            });
        }

        $('body').on('click', '#mark-holiday', function() {
            var url = "{{ route('manager.holidays.mark_holiday') }}?year" + $('#year').val();
           
            $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
            $.ajaxModal(MODAL_LG, url);
            $(MODAL_LG).modal('show');
        });
    </script>
@endpush
