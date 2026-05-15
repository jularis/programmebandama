<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Model;
use Kirschbaum\PowerJoins\PowerJoins;

class ActionSociale extends Model
{
    use Searchable, GlobalStatus, PowerJoins;

    protected $guarded = [];

    public function cooperative()
    {
        return $this->belongsTo(Cooperative::class);
    }
    public function beneficiaires()
    {
        return $this->hasMany(ActionSocialeLocalite::class, 'action_sociale_id');
    }
    public function partenaires()
    {
        return $this->hasMany(ActionSocialePartenaire::class, 'action_sociale_id');
    }
    public function autreBeneficiaires()
    {
        return $this->hasMany(ActionSocialeAutreBeneficiaire::class, 'action_sociale_id');
    }   
}
