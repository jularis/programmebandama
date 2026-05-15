<?php

namespace App\Models;

use App\Scopes\ActiveScope;
use App\Traits\HasCooperative;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class Attendance extends BaseModel
{

    use HasCooperative;

    protected $casts = [
        'clock_in_time' => 'datetime',
        'clock_out_time' => 'datetime',
        'shift_end_time' => 'datetime',
        'shift_start_time' => 'datetime',
    ];
    protected $appends = ['clock_in_date'];
    protected $guarded = ['id'];
    protected $with = ['cooperative'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id')->withoutGlobalScope(ActiveScope::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(CooperativeAddress::class, 'location_id');
    }

    public function shift(): BelongsTo
    {
        return $this->belongsTo(EmployeeShift::class, 'employee_shift_id');
    }

    public function getClockInDateAttribute()
    {
        return $this->clock_in_time->timezone($this->cooperative->timezone)->toDateString();
    }

    public static function attendanceByDate($date)
    {
        DB::statement('SET @attendance_date = ' . $date);

        return User::withoutGlobalScope(ActiveScope::class)
            ->leftJoin(
                'attendances',
                function ($join) use ($date) {
                    $join->on('users.id', '=', 'attendances.user_id')
                        ->where(DB::raw('DATE(attendances.clock_in_time)'), '=', $date)
                        ->whereNull('attendances.clock_out_time');
                }
            )->leftJoin('employee_details', 'employee_details.user_id', '=', 'users.id')
            ->leftJoin('designations', 'designations.id', '=', 'employee_details.designation_id') 
            ->select(
                DB::raw("( select count('atd.id') from attendances as atd where atd.user_id = users.id and DATE(atd.clock_in_time)  =  '" . $date . "' and DATE(atd.clock_out_time)  =  '" . $date . "' ) as total_clock_in"),
                DB::raw("( select count('atdn.id') from attendances as atdn where atdn.user_id = users.id and DATE(atdn.clock_in_time)  =  '" . $date . "' ) as clock_in"),
                'users.id',
                'users.firstname',
                'users.lastname',
                'attendances.clock_in_ip',
                'attendances.clock_in_time',
                'attendances.clock_out_time',
                'attendances.late',
                'attendances.half_day',
                'attendances.working_from',
                'designations.name as designation_name',
                'users.image',
                DB::raw('@attendance_date as atte_date'),
                'attendances.id as attendance_id'
            )
            ->groupBy('users.id')
            ->orderBy('users.lastname', 'asc');
    }

    public static function attendanceByUserDate($userid, $date)
    {
        DB::statement('SET @attendance_date = ' . $date);

        return User::withoutGlobalScope(ActiveScope::class)
            ->leftJoin(
                'attendances',
                function ($join) use ($date) {
                    $join->on('users.id', '=', 'attendances.user_id')
                        ->where(DB::raw('DATE(attendances.clock_in_time)'), '=', $date);
                }
            )->leftJoin('employee_details', 'employee_details.user_id', '=', 'users.id')
            ->leftJoin('designations', 'designations.id', '=', 'employee_details.designation_id')
            ->select(
                DB::raw("( select count('atd.id') from attendances as atd where atd.user_id = users.id and DATE(atd.clock_in_time)  =  '" . $date . "' and DATE(atd.clock_out_time)  =  '" . $date . "' ) as total_clock_in"),
                DB::raw("( select count('atdn.id') from attendances as atdn where atdn.user_id = users.id and DATE(atdn.clock_in_time)  =  '" . $date . "' ) as clock_in"),
                'users.id',
                'users.firstname',
                'users.lastname',
                'attendances.clock_in_ip',
                'attendances.clock_in_time',
                'attendances.clock_out_time',
                'attendances.late',
                'attendances.half_day',
                'attendances.working_from',
                'designations.name as designation_name',
                'users.image',
                DB::raw('@attendance_date as atte_date'),
                'attendances.id as attendance_id'
            )
            ->where('users.id', $userid)->first();
    }

    public static function attendanceDate($date)
    {
        return User::with(['attendance' => function ($q) use ($date) {
            $q->where(DB::raw('DATE(attendances.clock_in_time)'), '=', $date);
        }])
            ->withoutGlobalScope(ActiveScope::class) 
            ->leftJoin('employee_details', 'employee_details.user_id', '=', 'users.id')
            ->leftJoin('designations', 'designations.id', '=', 'employee_details.designation_id') 
            ->select(
                'users.id',
                'users.firstname',
                'users.lastname',
                'users.image',
                'designations.name as designation_name'
            )
            ->groupBy('users.id')
            ->orderBy('users.lastname', 'asc');
    }

    public static function attendanceHolidayByDate($date)
    {
        $holidays = Holiday::all();
        $user = User::leftJoin(
            'attendances',
            function ($join) use ($date) {
                $join->on('users.id', '=', 'attendances.user_id')
                    ->where(DB::raw('DATE(attendances.clock_in_time)'), '=', $date);
            }
        )
            ->withoutGlobalScope(ActiveScope::class) 
            ->leftJoin('employee_details', 'employee_details.user_id', '=', 'users.id')
            ->leftJoin('designations', 'designations.id', '=', 'employee_details.designation_id') 
            ->select(
                'users.id',
                'users.firstname',
                'users.lastname',
                'attendances.clock_in_ip',
                'attendances.clock_in_time',
                'attendances.clock_out_time',
                'attendances.late',
                'attendances.half_day',
                'attendances.working_from',
                'users.image',
                'designations.name as job_title',
                'attendances.id as attendance_id'
            )
            ->groupBy('users.id')
            ->orderBy('users.lastname', 'asc')
            ->union($holidays)
            ->get();

        return $user;
    }

    public static function userAttendanceByDate($startDate, $endDate, $userId)
    {
         
        return Attendance::without('cooperative')
            ->join('users', 'users.id', '=', 'attendances.user_id')
            ->leftJoin('cooperative_addresses', 'cooperative_addresses.id', '=', 'attendances.location_id')
            ->where(DB::raw('DATE(attendances.clock_in_time)'), '>=', $startDate)
            ->where(DB::raw('DATE(attendances.clock_in_time)'), '<=', $endDate)
            ->where('attendances.user_id', '=', $userId)
            ->orderBy('attendances.clock_in_time', 'desc')
            ->select('attendances.*', 'users.*', 'attendances.id as aId', 'cooperative_addresses.location')
            ->get();
    }

    public static function countDaysPresentByUser($startDate, $endDate, $userId)
    {
        $totalPresent = DB::select('SELECT count(DISTINCT DATE(attendances.clock_in_time) ) as presentCount from attendances where DATE(attendances.clock_in_time) >= "' . $startDate . '" and DATE(attendances.clock_in_time) <= "' . $endDate . '" and user_id="' . $userId . '" ');

        return $totalPresent[0]->presentCount;
    }

    public static function countDaysLateByUser($startDate, $endDate, $userId)
    {
        $totalLate = Attendance::whereBetween(DB::raw('DATE(attendances.`clock_in_time`)'), [$startDate->toDateString(), $endDate->toDateString()])
            ->where('late', 'yes')
            ->where('user_id', $userId)
            ->select(DB::raw('count(DISTINCT DATE(attendances.clock_in_time) ) as lateCount'))
            ->first();

        return $totalLate->lateCount;
    }

    public static function countHalfDaysByUser($startDate, $endDate, $userId)
    {
        $halfDay1 = Attendance::whereBetween(DB::raw('DATE(attendances.`clock_in_time`)'), [$startDate, $endDate])
            ->where('user_id', $userId)
            ->where('half_day', 'yes')
            ->count();

        $halfDay2 = Leave::where('user_id', $userId)
            ->where('leave_date', '>=', $startDate)
            ->where('leave_date', '<=', $endDate)
            ->where('status', 'approved')
            ->where('duration', 'half day')
            ->select('leave_date', 'reason', 'duration')
            ->count();

        return $halfDay1 + $halfDay2;
    }

    // Get User Clock-ins by date
    public static function getTotalUserClockIn($date, $userId)
    {
        return Attendance::where(DB::raw('DATE(attendances.clock_in_time)'), $date)
            ->where('user_id', $userId)
            ->count();
    }

    public static function getTotalUserClockInWithTime($startTime, $endTime, $userId)
    {
        return Attendance::whereBetween('clock_in_time', [$startTime, $endTime])
            ->where('user_id', $userId)
            ->count();
    }

    // Attendance by User and date
    public static function attendanceByUserAndDate($date, $userId)
    {
        return Attendance::where('user_id', $userId)
            ->where(DB::raw('DATE(attendances.clock_in_time)'), '=', $date)
            ->get();
    }

    public function totalTime($startDate, $endDate, $userId, $format = null)
    {
        $attendanceActivity = Attendance::userAttendanceByDate($startDate->format('Y-m-d'), $endDate->format('Y-m-d'), $userId);

        $attendanceActivity = $attendanceActivity->reverse()->values();

        $settingStartTime = Carbon::createFromFormat('H:i:s', attendance_setting()->shift->office_start_time, $this->timezone);
        $defaultEndTime = $settingEndTime = Carbon::createFromFormat('H:i:s', attendance_setting()->shift->office_end_time, $this->timezone);

        if ($settingStartTime->gt($settingEndTime)) {
            $settingEndTime->addDay();
        }

        if ($settingEndTime->greaterThan(now()->timezone($this->timezone))) {
            $defaultEndTime = now()->timezone($this->timezone);
        }

        $totalTime = 0;

        foreach ($attendanceActivity as $key => $activity) {
            if ($key == 0) {
                $firstClockIn = $activity;
                $startTime = Carbon::parse($firstClockIn->clock_in_time)->timezone($this->timezone);
            }

            $lastClockOut = $activity;

            if (!is_null($lastClockOut->clock_out_time)) {
                $endTime = Carbon::parse($lastClockOut->clock_out_time)->timezone($this->timezone);

            }
            elseif (
                ($lastClockOut->clock_in_time->timezone($this->timezone)->format('Y-m-d') != now()->timezone($this->timezone)->format('Y-m-d'))
                && is_null($lastClockOut->clock_out_time)
                && isset($startTime)
            ) {
                $endTime = Carbon::parse($startTime->format('Y-m-d') . ' ' . attendance_setting()->shift->office_end_time, $this->timezone);

                if ($startTime->gt($endTime)) {
                    $endTime->addDay();
                }

            }
            else {
                $endTime = $defaultEndTime;
            }

            $totalTime = $totalTime + $endTime->timezone($this->timezone)->diffInMinutes($activity->clock_in_time->timezone($this->timezone), true);
        }

        if ($format == 'H:i') {
            return intdiv($totalTime, 60) . ':' . ($totalTime % 60);
        }

        if ($format == 'm') {
            return $totalTime;
        }

        /** @phpstan-ignore-next-line */
        //return CarbonInterval::formatHuman($totalTime);
    }

}
