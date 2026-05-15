<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kirschbaum\PowerJoins\PowerJoins;
use App\Traits\GlobalStatus;
use App\Traits\Searchable;

class FormationProducteurFormateur extends Model
{
    use HasFactory, PowerJoins, GlobalStatus, Searchable;

    protected $table = "formation_producteur_formateurs";

    public function suivi_formation()
    {
        return $this->belongsTo(SuiviFormation::class, 'suivi_formation_id');
    }

    public function suivi_entreprise()
    {
        return $this->belongsTo(Entreprise::class, 'entreprise_id');
    }

    public function formateurStaff()
    {
        return $this->belongsTo(FormateurStaff::class, 'formateur_staff_id');
    }
}
