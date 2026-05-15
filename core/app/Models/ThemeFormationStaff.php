<?php

namespace App\Models;

use App\Models\ModuleFormationStaff;
use App\Traits\GlobalStatus;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Model;
use Kirschbaum\PowerJoins\PowerJoins;

class ThemeFormationStaff extends Model
{
    use Searchable, GlobalStatus, PowerJoins;

    protected $table="theme_formation_staffs";

    public function moduleFormationStaff()
    {
        return $this->belongsTo(ModuleFormationStaff::class, 'module_formation_staff_id');
    }
     
     
}