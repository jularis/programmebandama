<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Model;
use Kirschbaum\PowerJoins\PowerJoins;

class ClasseEtude extends Model
{
    use Searchable, GlobalStatus, PowerJoins;
    protected $table='classes';
    
    public function niveau()
    {
        return $this->belongsTo(NiveauxEtude::class,'niveaux_etude_id');
    }
     
}