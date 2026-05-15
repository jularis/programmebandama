<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;
use Kirschbaum\PowerJoins\PowerJoins;
use App\Traits\HasCooperative;

class Department extends Model
{
    use GlobalStatus, hasCooperative;
 
}