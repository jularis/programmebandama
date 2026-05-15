<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Model;
use Kirschbaum\PowerJoins\PowerJoins;

class EmployeeAutredocument extends Model
{
    use Searchable, GlobalStatus, PowerJoins; 

    protected $table="employee_autre_documents";
     
}