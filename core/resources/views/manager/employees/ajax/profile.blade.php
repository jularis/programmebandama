<script src="{{ asset('assets/vendor/jquery/Chart.min.js') }}"></script>
<style>
    .card-img {
        width: 120px;
        height: 120px;
    }

    .card-img img {
        width: 120px;
        height: 120px;
        object-fit: cover;
    }
    .appreciation-count {
        top: -6px;
        right: 10px;
    }

</style>
@php

$showFullProfile = true;
 
@endphp
 

<div class="d-lg-flex">

    <div class="w-100 py-0 py-lg-3 py-md-0">
        <!-- ROW START -->
        <div class="row">
            <!--  USER CARDS START -->
            <div class="col-lg-12 col-md-12 mb-4 mb-xl-0 mb-lg-4 mb-md-0">
                <div class="row">
                    <div class="col-xl-7 col-md-6 mb-4 mb-lg-0">

                        <x-cards.user :image="$employee->image">
                            <div class="row">
                                <div class="col-10">
                                    <h4 class="card-title f-15 f-w-500 text-darkest-grey mb-0">
                                        {{ $employee->salutation . ' ' . $employee->lastname.' '.$employee->firstname }}
                                        @isset($employee->country)
                                            <x-flag :country="$employee->country" />
                                        @endisset
                                    </h4>
                                </div>
                                
                                    <div class="col-2 text-right">
                                        <div class="dropdown">
                                            <button class="btn f-14 px-0 py-0 text-dark-grey dropdown-toggle"
                                                type="button" data-toggle="dropdown" aria-haspopup="true"
                                                aria-expanded="false">
                                                <i class="fa fa-ellipsis-h"></i>
                                            </button>

                                            <div class="dropdown-menu dropdown-menu-right border-grey rounded b-shadow-4 p-0"
                                                aria-labelledby="dropdownMenuLink" tabindex="0">
                                                <a class="dropdown-item openRightModal"
                                                    href="{{ route('manager.employees.edit', $employee->id) }}">@lang('app.edit')</a>
                                            </div>
                                        </div>
                                    </div>
                            

                            </div>

                            <p class="f-12 font-weight-normal text-dark-grey mb-0">
                                {{ !is_null($employee->employeeDetail) && !is_null($employee->employeeDetail->designation) ? $employee->employeeDetail->designation->name : '' }}
                                &bull;
                                {{ isset($employee->employeeDetail) && !is_null($employee->employeeDetail->department) && !is_null($employee->employeeDetail->department) ? $employee->employeeDetail->department->department : '' }}
                            </p>

                            @if ($employee->status == '1')
                                <p class="card-text f-11 text-lightest">@lang('app.lastLogin')

                                    @if (!is_null($employee->last_login))
                                        {{ $employee->last_login->timezone(cooperative()->timezone)->translatedFormat('Y-m-d' . ' ' . cooperative()->time_format) }}
                                    @else
                                        --
                                    @endif
                                </p>

                            @else
                                <p class="card-text f-12 text-lightest">
                                    <x-status :value="__('app.inactive')" color="red" />
                                </p>
                            @endif

                             
                        </x-cards.user>

                        @if ($employee->employeeDetail->about_me != '')
                            <x-cards.data :title="__('app.about')" class="mt-4">
                                <div>{{ $employee->employeeDetail->about_me }}</div>
                            </x-cards.data>
                        @endif


                        <x-cards.data :title="__('modules.client.profileInfo')" class=" mt-4">
                            <x-cards.data-row :label="__('modules.employees.employeeId')"
                                :value="(!is_null($employee->employeeDetail) && !is_null($employee->employeeDetail->employee_id)) ? ($employee->employeeDetail->employee_id) : '--'" />

                            <x-cards.data-row :label="__('modules.employees.fullName')"
                                :value="$employee->lastname.' '.$employee->firstname" />

                            <x-cards.data-row :label="__('app.designation')"
                                :value="(!is_null($employee->employeeDetail) && !is_null($employee->employeeDetail->designation)) ? ($employee->employeeDetail->designation->name) : '--'" />

                            <x-cards.data-row :label="__('app.department')"
                                :value="(isset($employee->employeeDetail) && !is_null($employee->employeeDetail->department) && !is_null($employee->employeeDetail->department)) ? ($employee->employeeDetail->department->department) : '--'" />

                            <div class="col-12 px-0 pb-3 d-block d-lg-flex d-md-flex">
                                <p class="mb-0 text-lightest f-14 w-30 d-inline-block text-capitalize">
                                    @lang('modules.employees.gender')</p>
                                <p class="mb-0 text-dark-grey f-14 w-70">
                                    <x-gender :gender='$employee->genre' />
                                </p>
                            </div>


                            @php
                                $currentyearJoiningDate = \Carbon\Carbon::parse(now(cooperative()->timezone)->year.'-'.$employee->employeeDetail->joining_date->translatedFormat('m-d'));
                                if ($currentyearJoiningDate->copy()->endOfDay()->isPast()) {
                                    $currentyearJoiningDate = $currentyearJoiningDate->addYear();
                                }
                                $diffInHoursJoiningDate = now(cooperative()->timezone)->floatDiffInHours($currentyearJoiningDate, false);
                            @endphp

                            <x-cards.data-row :label="__('modules.employees.workAnniversary')" :value="(!is_null($employee->employeeDetail) && !is_null($employee->employeeDetail->joining_date)) ? (($diffInHoursJoiningDate > -23 && $diffInHoursJoiningDate <= 0) ? __('app.today') : $currentyearJoiningDate->longRelativeToNowDiffForHumans()) : '--'" />

                            <x-cards.data-row :label="__('modules.employees.dateOfBirth')"
                                              :value="(!is_null($employee->employeeDetail) && !is_null($employee->employeeDetail->date_of_birth)) ? $employee->employeeDetail->date_of_birth->translatedFormat('d F') : '--'" />

                            @if ($showFullProfile)
                                <x-cards.data-row :label="__('app.email')" :value="$employee->email" />

                                <x-cards.data-row :label="__('app.mobile')"
                                :value="$employee->mobile_with_phonecode" />
 
                                <x-cards.data-row :label="__('app.address')"
                                    :value="$employee->employeeDetail->address ?? '--'" />

                                <x-cards.data-row :label="__('app.skills')"
                                    :value="$employee->skills() ? implode(', ', $employee->skills()) : '--'" />
 
                                <x-cards.data-row :label="__('modules.employees.probationEndDate')"
                                :value="$employee->employeeDetail->probation_end_date ? Carbon\Carbon::parse($employee->employeeDetail->probation_end_date)->translatedFormat('Y-m-d') : '--'" />

                                <x-cards.data-row :label="__('modules.employees.noticePeriodStartDate')"
                                :value="$employee->employeeDetail->notice_period_start_date ? Carbon\Carbon::parse($employee->employeeDetail->notice_period_start_date)->translatedFormat('Y-m-d') : '--'" />

                                <x-cards.data-row :label="__('modules.employees.noticePeriodEndDate')"
                                :value="$employee->employeeDetail->notice_period_end_date ? Carbon\Carbon::parse($employee->employeeDetail->notice_period_end_date)->translatedFormat('Y-m-d') : '--'" />

                                <x-cards.data-row :label="__('modules.employees.maritalStatus')"
                                :value="$employee?->employeeDetail?->marital_status ? __('modules.leaves.' . $employee->employeeDetail->marital_status) : '--'" />

                                <x-cards.data-row :label="__('modules.employees.marriageAnniversaryDate')"
                                :value="$employee->employeeDetail->marriage_anniversary_date ? Carbon\Carbon::parse($employee->employeeDetail->marriage_anniversary_date)->translatedFormat('d F') : '--'" />

                                <x-cards.data-row :label="__('modules.employees.employmentType')"
                                :value="$employee?->employeeDetail?->employment_type ? __('modules.employees.' . $employee?->employeeDetail?->employment_type) : '--'" />

                                @if($employee->employeeDetail->employment_type == 'internship')
                                    <x-cards.data-row :label="__('modules.employees.internshipEndDate')"
                                    :value="$employee->employeeDetail->internship_end_date ? Carbon\Carbon::parse($employee->employeeDetail->internship_end_date)->translatedFormat('Y-m-d') : '--'" />
                                @endif

                                @if($employee->employeeDetail->employment_type == 'on_contract')
                                    <x-cards.data-row :label="__('modules.employees.contractEndDate')"
                                    :value="$employee->employeeDetail->contract_end_date ? Carbon\Carbon::parse($employee->employeeDetail->contract_end_date)->translatedFormat('Y-m-d') : '--'" />
                                @endif

                                <x-cards.data-row :label="__('modules.employees.joiningDate')"
                                :value="(!is_null($employee->employeeDetail) && !is_null($employee->employeeDetail->joining_date)) ? $employee->employeeDetail->joining_date->translatedFormat('Y-m-d') : '--'" />


                                {{-- Custom fields data --}}
                                <x-forms.custom-field-show :fields="$fields" :model="$employee->employeeDetail"></x-forms.custom-field-show>

                            @endif

                        </x-cards.data>


                    </div>

                    <div class="col-xl-5 col-lg-6 col-md-6">
 

                        <x-cards.data class="mb-4">
                            <div class="d-flex justify-content-between">
                                    <div class="col-6">
                                        <p class="f-14 text-dark-grey">@lang('modules.employees.reportingTo')</p>
                                        @if ($employee->employeeDetail->reportingTo)
                                            <x-employee :user="$employee->employeeDetail->reportingTo" />
                                        @else
                                        --
                                        @endif
                                    </div>

                                @if ($employee->reportingTeam)
                                    <div class="col-6">
                                        <p class="f-14 text-dark-grey">@lang('modules.employees.reportingTeam')</p>
                                        @if (count($employee->reportingTeam) > 0)
                                            @if (count($employee->reportingTeam) > 1)
                                                @foreach ($employee->reportingTeam as $item)
                                                    <div class="taskEmployeeImg rounded-circle mr-1">
                                                        <a href="{{ route('manager.employees.show', $item->user->id) }}">
                                                            <img data-toggle="tooltip" data-original-title="{{ $item->user->name }}"
                                                                src="{{ $item->user->image_url }}">
                                                        </a>
                                                    </div>
                                                @endforeach
                                            @else
                                                @foreach ($employee->reportingTeam as $item)
                                                    <x-employee :user="$item->user" />
                                                @endforeach
                                            @endif

                                        @else
                                            --
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </x-cards.data>

                        @if ($showFullProfile)
                            <div class="row">
                                 
                                    <div class="col-xl-6 col-sm-12 mb-4">
                                        <x-cards.widget :title="__('modules.dashboard.lateAttendanceMark')"
                                            :value="$lateAttendance" :info="__('modules.dashboard.thisMonth')"
                                            icon="map-marker-alt" />
                                    </div>
                                 
                                    <div class="col-xl-6 col-sm-12 mb-4">
                                        <x-cards.widget :title="__('modules.dashboard.leavesTaken')" :value="$leavesTaken"
                                            :info="__('modules.dashboard.thisMonth')" icon="sign-out-alt" />
                                    </div>
                                
                            </div> 
                        @endif

                    </div>
                </div>
            </div>
            <!--  USER CARDS END -->

        </div>
        <!-- ROW END -->
    </div>
</div>
