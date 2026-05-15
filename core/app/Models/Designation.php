<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;
use Kirschbaum\PowerJoins\PowerJoins;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\HasCooperative;

class Designation extends Model
{
    use GlobalStatus, hasCooperative;
 
    public function members()
    {
        return $this->hasMany(EmployeeTeam::class, 'designation_id');
    }

    public function teamMembers()
    {
        return $this->hasMany(EmployeeDetail::class, 'designation_id');
    }

    public static function allDesignations()
    {
        // if (user()->permission('view_department') == 'all' || user()->permission('view_department') == 'none') {
        //     return Team::all();
        // }

        // return Team::where('added_by', user()->id)->get();
        return Designation::all();
    }

    public function childs()
    {
        return $this->hasMany(Department::class, 'parent_id','id');
    }

    public function departement()
    {
        return $this->belongsTo(Department::class, 'parent_id','id');
    }
 
}