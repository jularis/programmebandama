@extends('manager.layouts.app')

 
@section('panel')
    <!-- SETTINGS START -->
    <div class="w-100 d-flex ">

        <x-setting-sidebar :activeMenu="$activeSettingMenu" />

        <x-setting-card>
            <x-slot name="header">
                <div class="s-b-n-header" id="tabs">
                    <nav class="tabs px-4 border-bottom-grey">
                        <div class="nav" id="nav-tab" role="tablist">
                            <a class="nav-item nav-link f-15 active attendance"
                                href="{{ route('manager.settings.attendance-settings.index') }}?tab=attendance" role="tab"
                                aria-controls="nav-ticketAgents" aria-selected="true">@lang('app.menu.attendanceSettings')
                            </a>

                           
                                <a class="nav-item nav-link f-15 shift"
                                    href="{{ route('manager.settings.attendance-settings.index') }}?tab=shift" role="tab"
                                    aria-controls="nav-ticketTypes" aria-selected="true">@lang('app.menu.employeeShifts')
                                </a>
                           
                        </div>
                    </nav>
                </div>
            </x-slot>

            <x-slot name="buttons">
                <div class="row">

                    <div class="col-md-12 mb-2">
                        <x-forms.button-primary icon="plus" id="addEmployeeShift" class="shift-btn mb-2 d-none actionBtn">
                            @lang('app.addNew') @lang('modules.attendance.shift')
                        </x-forms.button-primary>
                    </div>

                </div>
            </x-slot>


            @include($view)

        </x-setting-card>

    </div>
    <!-- SETTINGS END -->
@endsection

@push('script')
    <script>

        $('.nav-item').removeClass('active');
        const activeTab = "{{ $activeTab }}";
        $('.' + activeTab).addClass('active');

        showBtn(activeTab);

        function showBtn(activeTab) {
            $('.actionBtn').addClass('d-none');
            $('.' + activeTab + '-btn').removeClass('d-none');
        }

        $("body").on("click", "#editSettings .nav a", function(event) {
            event.preventDefault();

            $('.nav-item').removeClass('active');
            $(this).addClass('active');

            const requestUrl = this.href;
            window.location.href = requestUrl
            // $.easyAjax({
            //     url: requestUrl,
            //     blockUI: true,
            //     container: "#nav-tabContent",
            //     historyPush: true,
            //     success: function(response) {
            //         if (response.status == "success") {
            //             showBtn(response.activeTab);

            //             $('#nav-tabContent').html(response.html);
            //             init('#nav-tabContent');
            //         }
            //     }
            // });
        });
    </script>
@endpush
