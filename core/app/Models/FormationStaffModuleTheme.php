<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\GlobalStatus;
use App\Traits\Searchable;
use Kirschbaum\PowerJoins\PowerJoins;

class FormationStaffModuleTheme extends Model
{
    use HasFactory, Searchable, GlobalStatus, PowerJoins;

    protected $table = "formation_staff_module_themes";

    public function formationStaff()
    {
        return $this->belongsTo(FormationStaff::class, 'formation_staff_id');
    }
    public function moduleFormationStaff()
    {
        return $this->belongsTo(ModuleFormationStaff::class, 'module_formation_staff_id');
    }
    public function themeFormationStaff()
    {
        return $this->belongsTo(ThemeFormationStaff::class, 'theme_formation_staff_id');
    }
}
