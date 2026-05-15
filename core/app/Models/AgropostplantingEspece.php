<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Model;
use Kirschbaum\PowerJoins\PowerJoins;

class AgropostplantingEspece extends Model
{
    use Searchable, GlobalStatus, PowerJoins; 
    protected $table="agropostplanting_especes";

    public function agropostplanting()
    {
        return $this->belongsTo(Agropostplanting::class);
    }
    
    public function agroespecesarbre()
    {
        return $this->belongsTo(Agroespecesarbre::class,'agroespecesarbre_id');
    }
     
}