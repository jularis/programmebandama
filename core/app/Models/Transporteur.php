<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use App\Traits\HasCooperative;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Model;
use Kirschbaum\PowerJoins\PowerJoins;

class Transporteur extends Model
{
    use Searchable, GlobalStatus, PowerJoins, HasCooperative; 

    public function cooperative()
    {
        return $this->belongsTo(Cooperative::class,'cooperative_id');
    }

    public function entreprise()
    {
        return $this->belongsTo(Entreprise::class,'entreprise_id');
    }
 
 
}