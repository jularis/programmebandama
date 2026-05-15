<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kirschbaum\PowerJoins\PowerJoins;
use App\Traits\GlobalStatus;
use App\Traits\Searchable;

class FormationStaffFormateur extends Model
{
    use HasFactory, PowerJoins, GlobalStatus, Searchable;
    protected $table = "formation_staff_formateurs";

    public function formationStaff()
    {
        return $this->belongsTo(FormationStaff::class, 'formation_staff_id');
    }
    public function entreprise()
    {
        return $this->belongsTo(Entreprise::class, 'entreprise_id');
    }

    public function formateurStaff()
    {
        return $this->belongsTo(FormateurStaff::class, 'formateur_staff_id');
    }
}
