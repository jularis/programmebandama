<?php

namespace App\Models;

use App\Scopes\ActiveScope;
use App\Traits\HasCooperative;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Model;

class LeaveType extends BaseModel
{
 
    use HasCooperative;
     
    protected $table='leave_types';

    public function leaves()
    {
        return $this->hasMany(Leave::class, 'leave_type_id');
    }

    public function leavesCount()
    {
        return $this->hasOne(Leave::class, 'leave_type_id')
            ->selectRaw('leave_type_id, count(*) as count, SUM(if(duration="half day", 1, 0)) AS halfday')
            ->groupBy('leave_type_id');
    }

    public static function byUser($user, $leaveTypeId = null, $status = array('approved'), $leaveDate = null)
    {
        if (!is_null($leaveDate)) {
            $leaveDate = Carbon::createFromFormat('Y-m-d', $leaveDate);

        }
        else {
            $leaveDate = Carbon::createFromFormat('d-m-Y', '01-'.cooperative()->year_starts_from.'-'.now('Africa/Abidjan')->year)->startOfMonth();
        }

        if (!$user instanceof User) {
            $user = User::withoutGlobalScope(ActiveScope::class)->withOut('clientDetails')->findOrFail($user);
        }

        $setting = cooperative();

        if (isset($user->employee[0])) {
        
           
                $leaveTypes = LeaveType::with(['leavesCount' => function ($q) use ($user, $status, $leaveDate) {
                    $q->where('leaves.user_id', $user->id);
                    $q->whereBetween('leaves.leave_date', [$leaveDate->copy()->toDateString(), $leaveDate->copy()->addYear()->toDateString()]);
                    $q->whereIn('leaves.status', $status);
                }])->select('leave_types.*', 'employee_details.notice_period_start_date', 'employee_details.probation_end_date',
                'employee_details.department_id as employee_department', 'employee_details.designation_id as employee_designation',
                'employee_details.marital_status as maritalStatus', 'users.genre as usergender', 'employee_details.joining_date')
                ->join('employee_leave_quotas', 'employee_leave_quotas.leave_type_id', 'leave_types.id')
                ->join('users', 'users.id', 'employee_leave_quotas.user_id')
                ->join('employee_details', 'employee_details.user_id', 'users.id')->where('users.id', $user->id);
            
            if (!is_null($leaveTypeId)) {
                $leaveTypes = $leaveTypes->where('leave_types.id', $leaveTypeId);
            }

            return $leaveTypes->get();
        }

        return collect();

    }

    public static function leaveTypeCodition($leave, $userRole)
    {
        $currentDate = Carbon::now()->format('Y-m-d');

        if(!is_null($leave->effective_type) && !is_null($leave->effective_after)){
            $effectiveDate = $leave->effective_type == 'days' ? Carbon::parse($leave->joining_date)->addDays($leave->effective_after)->format('Y-m-d') : Carbon::parse($leave->joining_date)->addMonths($leave->effective_after)->format('Y-m-d');
        }

        $probation = Carbon::parse($leave->probation_end_date)->format('Y-m-d');
        $noticePeriod = Carbon::parse($leave->notice_period_start_date)->format('Y-m-d');

        if((is_null($leave->probation_end_date) || ($leave->allowed_probation == 0 && $probation < $currentDate) || $leave->allowed_probation == 1) &&
        (is_null($leave->notice_period_start_date) || ($leave->allowed_notice == 0 && $noticePeriod > $currentDate) || $leave->allowed_notice == 1) &&
        (!is_null($leave->gender) && in_array($leave->usergender, json_decode($leave->gender))) &&
        (!is_null($leave->marital_status) && in_array($leave->maritalStatus, json_decode($leave->marital_status))) &&
        (!is_null($leave->department) && in_array($leave->employee_department, json_decode($leave->department))) &&
        (!is_null($leave->designation) && in_array($leave->employee_designation, json_decode($leave->designation))) &&
        (is_null($leave->effective_after) || $currentDate > $effectiveDate)){ /** @phpstan-ignore-line */
            return true;
        }

        return false;
    }

}
