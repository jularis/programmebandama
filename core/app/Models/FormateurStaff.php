<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kirschbaum\PowerJoins\PowerJoins;
use App\Traits\GlobalStatus;
use App\Traits\Searchable;

class FormateurStaff extends Model
{
    use HasFactory, PowerJoins, GlobalStatus, Searchable;
    protected $table = "formateur_staffs";
    public function entreprise()
    {
        return $this->belongsTo(Entreprise::class, 'entreprise_id', 'id');
    }
    public function formations()
    {
        return $this->belongsToMany(FormationStaff::class, 'formation_staff_formateurs', 'formateur_staff_id', 'formation_staff_id');
    }

    public function suivi_formations(){
        return $this->belongsToMany(SuiviFormation::class, 'formation_producteur_formateurs', 'formateur_staff_id', 'suivi_formation_id');
    }
}
