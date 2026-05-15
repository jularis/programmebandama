<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Model;
use Kirschbaum\PowerJoins\PowerJoins;

class SuiviParcelle extends Model
{
    use Searchable, GlobalStatus, PowerJoins;

    //protected $guarded = [];

    public function campagne()
    {
        return $this->belongsTo(Campagne::class);
    }

    public function parcelle()
    {
        return $this->belongsTo(Parcelle::class);
    }
    public function ombrage()
    {
        return $this->hasMany(SuiviParcellesOmbrage::class, 'suivi_parcelle_id', 'id');
    }
    public function animal()
    {
        return $this->hasMany(SuiviParcellesAnimal::class, 'suivi_parcelle_id', 'id');
    }
    public function agroforesterie()
    {
        return $this->hasMany(SuiviParcellesAgroforesterie::class, 'suivi_parcelle_id', 'id');
    }
    public function insectes(){
        return $this->hasMany(SuiviParcellesInsecteAmi::class, 'suivi_parcelle_id', 'id');
    }
    
    public function autreParasites(){
        return $this->hasMany(SuiviParcellesAutreParasite::class, 'suivi_parcelle_id', 'id');
    }
    public function traitements(){
        return $this->hasMany(SuiviParcellesTraitement::class, 'suivi_parcelle_id', 'id');
    }
    public function pesticidesAnneDerniere(){
        return $this->hasMany(SuiviParcellesPesticideAnneDerniere::class, 'suivi_parcelle_id', 'id');
    }
    public function intrantsAnneDerniere(){
        return $this->hasMany(SuiviParcellesIntrantAnneeDerniere::class, 'suivi_parcelle_id', 'id');
    }
    public function parasites(){
        return $this->hasMany(SuiviParcellesParasite::class, 'suivi_parcelle_id', 'id');
    }
}