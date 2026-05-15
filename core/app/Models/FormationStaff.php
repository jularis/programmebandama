<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Model;
use Kirschbaum\PowerJoins\PowerJoins;

class FormationStaff extends Model
{
    use Searchable, GlobalStatus, PowerJoins;

    protected $table = "formation_staffs";

    public function cooperative()
    {
        return $this->belongsTo(Cooperative::class, 'cooperative_id');
    }
    public function moduleFormationStaff()
    {
        return $this->belongsTo(ModuleFormationStaff::class, 'module_formation_staff_id');
    }
    public function campagne()
    {
        return $this->belongsTo(Campagne::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function formationStaffModuleTheme()
    {
        return $this->hasMany(FormationStaffModuleTheme::class, 'formation_staff_id', 'id');
    }

    public function formateurs()
    {
        return $this->belongsToMany(FormateurStaff::class, 'formation_staff_formateurs', 'formation_staff_id', 'formateur_staff_id');
    }

    public function formationStaffEntrepriseFormateur()
    {
        return $this->hasMany(FormationStaffFormateur::class, 'formation_staff_id', 'id');
    }

    public function entreprises()
    {
        return $this->belongsToMany(Entreprise::class, 'formation_staff_formateurs', 'formation_staff_id', 'entreprise_id');
    }
   
}
