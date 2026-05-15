<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Model;
use Kirschbaum\PowerJoins\PowerJoins;

class AgroapprovisionnementSection extends Model
{
    use Searchable, GlobalStatus, PowerJoins;

    
    public function campagne()
    {
        return $this->belongsTo(Campagne::class);
    }

    public function section()
    {
        return $this->belongsTo(Section::class,'section_id','id');
    }

    public function especes()
    {
        return $this->hasMany(AgroapprovisionnementSection::class, 'agroapprovisionnement_section_id', 'id');
    }

    public function especesSection()
    {
        return $this->hasMany(AgroapprovisionnementSectionEspece::class, 'agroapprovisionnement_section_id', 'id');
    }
  

}