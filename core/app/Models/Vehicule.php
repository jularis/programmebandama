<?php

namespace App\Models;

use App\Traits\Searchable;
use App\Traits\GlobalStatus;
use App\Traits\HasCooperative;
use Kirschbaum\PowerJoins\PowerJoins;
use Illuminate\Database\Eloquent\Model;

class Vehicule extends Model
{
    use Searchable, GlobalStatus, PowerJoins,HasCooperative; 
    
    public function marque()
    {
        return $this->belongsTo(Marque::class,'marque_id');
    }

    public function cooperative()
    {
        return $this->belongsTo(Cooperative::class,'cooperative_id');
    }
 
}