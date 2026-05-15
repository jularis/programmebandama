<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Searchable;
use Kirschbaum\PowerJoins\PowerJoins;

class Connaissement extends Model
{

    use Searchable, GlobalStatus, PowerJoins;
    

    public function producteur()
    {
        return $this->belongsTo(Producteur::class, 'producteur_id');
    }
    
    public function campagne()
    {
        return $this->belongsTo(Campagne::class, 'campagne_id');
    }
    public function campagnePeriode()
    {
        return $this->belongsTo(CampagnePeriode::class, 'campagne_periode_id');
    }
    public function cooperative()
    {
        return $this->belongsTo(Cooperative::class, 'cooperative_id');
    }
    public function magasinSection()
    {
        return $this->belongsTo(MagasinSection::class, 'magasin_section_id');
    }
    public function magasinCentral()
    {
        return $this->belongsTo(MagasinCentral::class, 'magasin_centraux_id');
    }
    public function transporteur()
    {
        return $this->belongsTo(Transporteur::class, 'transporteur_id');
    }
    public function vehicule()
    {
        return $this->belongsTo(Vehicule::class, 'vehicule_id');
    }

    public function products()
    {
        return $this->hasMany(ConnaissementProduit::class, 'connaissement_id', 'id');
    }
}