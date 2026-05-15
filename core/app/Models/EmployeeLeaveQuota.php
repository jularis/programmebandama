<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
 
class EmployeeLeaveQuota extends BaseModel
{

    protected $guarded = ['id'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function leaveType(): BelongsTo
    {
        return $this->belongsTo(LeaveType::class, 'leave_type_id');
    }

}
