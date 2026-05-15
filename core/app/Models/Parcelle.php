<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Model;
use Kirschbaum\PowerJoins\PowerJoins; 

class Parcelle extends Model
{
    use Searchable, GlobalStatus, PowerJoins;
    
    protected $guarded = ['section','localite',];

    public function producteur()
    {
        return $this->belongsTo(Producteur::class);
    }
    
    public function parcelleTypeProtections()
    {
        return $this->hasMany(Parcelle_type_protection::class,'parcelle_id');
    }
    public function agroespeceabre_parcelles()
    {
        return $this->hasMany(agroespeceabre_parcelle::class,'parcelle_id');
    }
    public function varietes()
    {
        return $this->hasMany(VarieteParcelle::class,'parcelle_id');
    }

    public function autreAgroespecesarbreParcelles()
    {
        return $this->hasMany(AutreAgroespecesarbreParcelle::class,'parcelle_id');
    }
}