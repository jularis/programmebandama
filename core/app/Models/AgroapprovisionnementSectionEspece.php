<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Model;
use Kirschbaum\PowerJoins\PowerJoins;

class AgroapprovisionnementSectionEspece extends Model
{
    use Searchable, GlobalStatus, PowerJoins; 
    protected $table="agroapprovisionnement_section_especes";

    public function agroapprovisionnementSection()
    {
        return $this->belongsTo(AgroapprovisionnementSection::class);
    }
    
    public function agroespecesarbre()
    {
        return $this->belongsTo(Agroespecesarbre::class,'agroespecesarbre_id');
    }
    
}