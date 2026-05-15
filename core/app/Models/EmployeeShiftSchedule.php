<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
 
class EmployeeShiftSchedule extends BaseModel
{

    use HasFactory;

    protected $casts = [
        'date' => 'datetime',
        'shift_start_time' => 'datetime',
        'shift_end_time' => 'datetime',
    ];

    protected $appends = ['file_url', 'download_file_url'];

    protected $guarded = ['id'];

    protected $with = ['shift'];

    public function getFileUrlAttribute()
    {
        return ($this->file) ? asset_url_local_s3('employee-shift-file/'. $this->id.'/' . $this->file) : '';
    }

    public function getDownloadFileUrlAttribute()
    {
        return ($this->file) ? asset_url_local_s3('employee-shift-file/'. $this->id.'/' . $this->file) : null;
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function shift()
    {
        return $this->belongsTo(EmployeeShift::class, 'employee_shift_id');
    }

    public function requestChange()
    {
        return $this->hasOne(EmployeeShiftChangeRequest::class, 'shift_schedule_id');
    }

    public function pendingRequestChange()
    {
        return $this->hasOne(EmployeeShiftChangeRequest::class, 'shift_schedule_id')->where('status', 'waiting');
    }

}
