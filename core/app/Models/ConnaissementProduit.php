<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Searchable;
use Kirschbaum\PowerJoins\PowerJoins;

class ConnaissementProduit extends Model
{

    use Searchable, GlobalStatus, PowerJoins;
    protected $table="connaissement_produits";
    public function campagne()
    {
        return $this->belongsTo(Campagne::class, 'campagne_id');
    }
    public function parcelle()
    {
        return $this->belongsTo(Parcelle::class, 'parcelle_id');
    }
    public function producteur()
    {
        return $this->belongsTo(Producteur::class, 'producteur_id');
    }
    public function campagnePeriode()
    {
        return $this->belongsTo(CampagnePeriode::class, 'campagne_periode_id');
    }
     
    public function connaissement()
    {
        return $this->belongsTo(Connaissement::class, 'connaissement_id');
    } 
}