<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Model;
use Kirschbaum\PowerJoins\PowerJoins;

class LivraisonPrime extends Model
{
    use Searchable, GlobalStatus, PowerJoins;

    public function livraisonInfo()
    {
        return $this->belongsTo(LivraisonInfo::class, 'livraison_info_id');
    }
    
    public function parcelle()
    {
        return $this->belongsTo(Parcelle::class);
    }
    public function campagnePeriode()
    {
        return $this->belongsTo(CampagnePeriode::class, 'campagne_periode_id');
    }
    
    public function campagne()
    {
        return $this->belongsTo(Campagne::class);
    }
     
}