<?php

namespace App\Models;

use App\Traits\HasCooperative;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

 
class AttendanceSetting extends BaseModel
{

    use HasCooperative;

    public function shift() 
    {
        return $this->belongsTo(EmployeeShift::class, 'default_employee_shift');
    }

}
