<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Model;
use Kirschbaum\PowerJoins\PowerJoins;

class Producteur extends Model
{
    use Searchable, GlobalStatus, PowerJoins;

    protected $guarded = [];

    public function localite()
    {
        return $this->belongsTo(Localite::class, 'localite_id');
    }
    public function programme()
    {
        return $this->belongsTo(Programme::class, 'programme_id');
    }
    public function menages()
    {
        return $this->hasMany(Menage::class, 'producteur_id', 'id');
    }
    public function certifications()
    {
        return $this->hasMany(Producteur_certification::class, 'producteur_id', 'id');
    }
    public function agroevaluation()
    {
        return $this->hasMany(Agroevaluation::class, 'producteur_id');
    }
    public function parcelles()
    {
        return $this->hasMany(Parcelle::class, 'producteur_id');
    }
    public function countrie()
    {
        return $this->belongsTo(Pays::class, 'pays_id');
    }
    
    public function country(){
        return $this->belongsTo(Country::class, 'nationalite');
    }
}