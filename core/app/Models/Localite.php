<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use App\Traits\HasCooperative;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Model;
use Kirschbaum\PowerJoins\PowerJoins;

class Localite extends Model
{
    use Searchable, GlobalStatus, PowerJoins; 

    public function ecoleprimaires()
    {
        return $this->hasMany(Localite_ecoleprimaire::class,'localite_id', 'id');
    }
    public function section()
    {
        return $this->belongsTo(Section::class, 'section_id');
    }
    public function producteurs()
    {
        return $this->hasMany(Producteur::class, 'localite_id', 'id');
    }
    public function centresantes()
    {
        return $this->hasMany(Localite_centre_sante::class, 'localite_id', 'id');
    }
    public function marches()
    {
        return $this->hasMany(Localite_jour_marche::class, 'localite_id', 'id');
    }
    public function eaux()
    {
        return $this->hasMany(Localite_source_eau::class, 'localite_id', 'id');
    }

    public function producteur()
    {
        return $this->hasMany(Producteur::class, 'localite_id');
    }
}