<?php

namespace App\Models;

use App\Traits\HasCooperative;

 
class GoogleCalendarModule extends BaseModel
{

    use HasCooperative;

    protected $table = 'google_calendar_modules';

}
