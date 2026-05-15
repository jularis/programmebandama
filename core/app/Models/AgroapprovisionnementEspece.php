<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Model;
use Kirschbaum\PowerJoins\PowerJoins;

class AgroapprovisionnementEspece extends Model
{
    use Searchable, GlobalStatus, PowerJoins; 

    public function agroapprovisionnement()
    {
        return $this->belongsTo(Agroapprovisionnement::class);
    }
    
    public function agroespecesarbre()
    {
        return $this->belongsTo(Agroespecesarbre::class,'agroespecesarbre_id');
    }
     
}