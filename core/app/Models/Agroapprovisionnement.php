<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Model;
use Kirschbaum\PowerJoins\PowerJoins;

class Agroapprovisionnement extends Model
{
    use Searchable, GlobalStatus, PowerJoins;

    
    public function campagne()
    {
        return $this->belongsTo(Campagne::class);
    }

    public function cooperative()
    {
        return $this->belongsTo(Cooperative::class);
    }

    public function especes()
    {
        return $this->hasMany(AgroapprovisionnementEspece::class, 'agroapprovisionnement_id', 'id');
    }

  

}