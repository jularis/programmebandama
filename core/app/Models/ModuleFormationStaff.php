<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Model;
use Kirschbaum\PowerJoins\PowerJoins;

class ModuleFormationStaff extends Model
{
    use Searchable, GlobalStatus, PowerJoins;
    protected $table="module_formation_staffs";

    public function themeFormationStaff()
    {
        return $this->hasMany(ThemeFormationStaff::class, 'module_formation_staff_id');
    }
     
}