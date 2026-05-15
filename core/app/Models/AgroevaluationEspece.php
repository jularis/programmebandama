<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Model;
use Kirschbaum\PowerJoins\PowerJoins;

class AgroevaluationEspece extends Model
{
    use Searchable, GlobalStatus, PowerJoins; 

    public function agroevaluation()
    {
        return $this->belongsTo(Agroevaluation::class);
    }
    
    public function agroespecesarbre()
    {
        return $this->belongsTo(Agroespecesarbre::class,'agroespecesarbre_id');
    }
     
}