<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Model;
use Kirschbaum\PowerJoins\PowerJoins;

class Menage extends Model
{
    use Searchable, GlobalStatus, PowerJoins;

    public function producteur()
    {
        return $this->belongsTo(Producteur::class,);
    }

    public function menage_sourceEnergie()
    {
        return $this->hasMany(Menage_sourceEnergie::class, 'menage_id');
    }

    public function menage_ordure(){
        return $this->hasMany(Menage_ordure::class, 'menage_id');
    }
     
     
}