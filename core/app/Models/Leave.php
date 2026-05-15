<?php

namespace App\Models;

use App\Scopes\ActiveScope;
use App\Traits\HasCooperative;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

 
class Leave extends BaseModel
{

    use HasFactory;
    use HasCooperative;

    protected $casts = [
        'leave_date' => 'datetime',
        'approved_at' => 'datetime',
    ];
    protected $guarded = ['id'];
    protected $appends = ['date']; // Being used in attendance

    public function getDateAttribute()
    {
        return $this->leave_date->toDateString();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id')->withoutGlobalScope(ActiveScope::class)->withOut('clientDetails');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by')->withoutGlobalScope(ActiveScope::class)->withOut('clientDetails', 'role');
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(LeaveType::class, 'leave_type_id');
    }

    public function getLeavesTakenCountAttribute()
    {
        $userId = $this->user_id;
        $setting = cooperative();
        $user = User::withoutGlobalScope(ActiveScope::class)->withOut('clientDetails', 'role')->findOrFail($userId);
        $currentYearJoiningDate = Carbon::parse($user->employee[0]->joining_date->format((now(cooperative()->timezone)->year) . '-m-d'));

        if ($currentYearJoiningDate->isFuture()) {
            $currentYearJoiningDate->subYear();
        }

        $leaveFrom = $currentYearJoiningDate->copy()->toDateString();
        $leaveTo = $currentYearJoiningDate->copy()->addYear()->toDateString();

        if ($setting->leaves_start_from !== 'joining_date') {
            $leaveStartYear = Carbon::parse(now()->format((now(cooperative()->timezone)->year) . '-' . cooperative()->year_starts_from . '-01'));

            if ($leaveStartYear->isFuture()) {
                $leaveStartYear = $leaveStartYear->subYear();
            }

            $leaveFrom = $leaveStartYear->copy()->toDateString();
            $leaveTo = $leaveStartYear->copy()->addYear()->toDateString();
        }

        $fullDay = Leave::where('user_id', $userId)
            ->whereBetween('leave_date', [$leaveFrom, $leaveTo])
            ->where('status', 'approved')
            ->where('duration', '<>', 'half day')
            ->count();

        $halfDay = Leave::where('user_id', $userId)
            ->whereBetween('leave_date', [$leaveFrom, $leaveTo])
            ->where('status', 'approved')
            ->where('duration', 'half day')
            ->count();

        return ($fullDay + ($halfDay / 2));

    }

    public static function byUserCount($user, $year = null)
    {
        $setting = cooperative();

        if (!$user instanceof User) {
            $user = User::withoutGlobalScope(ActiveScope::class)->withOut('clientDetails', 'role')->findOrFail($user);
        }

        $leaveFrom = (is_null($year)) ? Carbon::createFromFormat('d-m-Y', '01-'.cooperative()->year_starts_from.'-'.now(cooperative()->timezone)->year)->startOfMonth()->toDateString() : Carbon::createFromFormat('d-m-Y', '01-'.cooperative()->year_starts_from.'-'.$year)->startOfMonth()->toDateString();
        $leaveTo = Carbon::parse($leaveFrom)->addYear()->subDay()->toDateString();

        if ($setting->leaves_start_from == 'joining_date' && isset($user->employee[0])) {
            $currentYearJoiningDate = Carbon::parse($user->employee[0]->joining_date->format((now(cooperative()->timezone)->year) . '-m-d'));

            if ($currentYearJoiningDate->isFuture()) {
                $currentYearJoiningDate->subYear();
            }

            $leaveFrom = $currentYearJoiningDate->copy()->toDateString();
            $leaveTo = $currentYearJoiningDate->copy()->addYear()->toDateString();
        }

        $fullDay = Leave::where('user_id', $user->id)
            ->whereBetween('leave_date', [$leaveFrom, $leaveTo])
            ->where('status', 'approved')
            ->where('duration', '<>', 'half day')
            ->get();

        $halfDay = Leave::where('user_id', $user->id)
            ->whereBetween('leave_date', [$leaveFrom, $leaveTo])
            ->where('status', 'approved')
            ->where('duration', 'half day')
            ->get();

        return (count($fullDay) + (count($halfDay) / 2));
    }

    public function files(): HasMany
    {
        return $this->hasMany(LeaveFile::class, 'leave_id')->orderBy('id', 'desc');
    }

}
