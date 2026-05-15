<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Model;
use Kirschbaum\PowerJoins\PowerJoins;
use App\Scopes\ActiveScope;
use App\Traits\CustomFieldsTrait;
use App\Traits\HasCooperative;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeDetail extends Model
{
    use Searchable, GlobalStatus, PowerJoins,CustomFieldsTrait, HasCooperative;

    //protected $guarded = [];


    protected $casts = [
        'joining_date' => 'datetime',
        'last_date' => 'datetime',
        'date_of_birth' => 'datetime',
        'calendar_view	' => 'array',
    ];

    protected $with = ['designation','cooperative', 'department'];

    protected $appends = ['upcoming_birthday'];

    const CUSTOM_FIELD_MODEL = 'App\Models\EmployeeDetail';

    public function getUpcomingBirthdayAttribute()
    {
        if (is_null($this->date_of_birth)) {
            return null;
        }

        $dob = Carbon::parse(now('H:i')->year . '-' . $this->date_of_birth->month . '-' . $this->date_of_birth->day);

        if ($dob->isPast()) {
            $dob->addYear();
        }

        return $dob->toDateString();
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id')->withoutGlobalScope(ActiveScope::class);
    }

    public function reportingTo()
    {
        return $this->belongsTo(User::class, 'reporting_to')->withoutGlobalScope(ActiveScope::class);
    }

    public function designation()
    {
        return $this->belongsTo(Designation::class, 'designation_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }
    public function lead()
    {
        return $this->hasOne(Lead::class, 'user_id');
    }

    public function attendance()
    {
        return $this->hasMany(Attendance::class, 'user_id');
    }
    public function leaves()
    {
        return $this->hasMany(Leave::class, 'user_id');
    }
    public function shifts()
    {
        return $this->hasMany(EmployeeShiftSchedule::class, 'user_id');
    }
    public function userBadge()
    {
        $itsYou = ' <span class="ml-2 badge badge-secondary pr-1">' . __('app.itsYou') . '</span>';

        if (auth()->user() && auth()->user()->id == $this->id) {
            return $this->name . $itsYou;
        }

        return $this->name;
    }
     
}