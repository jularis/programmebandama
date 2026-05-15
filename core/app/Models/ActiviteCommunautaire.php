<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Model;
use Kirschbaum\PowerJoins\PowerJoins;

class ActiviteCommunautaire extends Model
{
    use Searchable, GlobalStatus, PowerJoins;

    public function cooperative()
    {
        return $this->belongsTo(Cooperative::class);
    }
    public function localites()
    {
        return $this->hasMany(ActiviteCommunautaireLocalite::class, 'activite_communautaire_id');
    }

    public function beneficiaires()
    {
        return $this->hasMany(ActiviteCommunautaireBeneficiaire::class, 'activite_communautaire_id');
    }

    public function activiteCommunautaireNonMembres()
    {
        return $this->hasMany(ActiviteCommunautaireNonMembre::class, 'activite_communautaire_id');
    }
}
