<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Model;
use Kirschbaum\PowerJoins\PowerJoins;

class FormationStaffVisiteur extends Model
{
    use Searchable, GlobalStatus, PowerJoins; 

    protected $table="formation_staff_visiteurs";

    public function formationStaff()
    {
        return $this->belongsTo(FormationStaff::class);
    } 
     
     
}