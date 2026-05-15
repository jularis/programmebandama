<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Model;
use Kirschbaum\PowerJoins\PowerJoins;

class FormationStaffListe extends Model
{
    use Searchable, GlobalStatus, PowerJoins; 

    protected $table="formation_staff_listes";

    public function formationStaff()
    {
        return $this->belongsTo(FormationStaff::class);
    }

    public function user()
    {
        return $this->belongsTo(user::class);
    }
     
     
}