<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Searchable;
use Kirschbaum\PowerJoins\PowerJoins;

class LivraisonProductDetail extends Model
{

    use Searchable, GlobalStatus, PowerJoins;
    protected $table="livraison_product_details";

    public function livraisonInfo()
    {
        return $this->belongsTo(LivraisonInfo::class, 'livraison_info_id');
    }
    
    public function campagne()
    {
        return $this->belongsTo(Campagne::class, 'campagne_id');
    }
    public function campagnePeriode()
    {
        return $this->belongsTo(CampagnePeriode::class, 'campagne_periode_id');
    }
    public function parcelle()
    {
        return $this->belongsTo(Parcelle::class, 'parcelle_id');
    }
}