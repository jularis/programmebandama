<?php

namespace App\Models;

use App\Traits\HasCooperative;
use Illuminate\Database\Eloquent\Factories\HasFactory;

 
class EmployeeShift extends BaseModel
{

    use HasFactory, HasCooperative;

    protected $guarded = ['id'];

}
