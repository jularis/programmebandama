<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VisitePlantationEnfant extends Model
{
    protected $table = 'visite_plantation_enfants';
    protected $guarded = [];

    public function visite()
    {
        return $this->belongsTo(VisitePlantation::class, 'visite_id');
    }

    public function raisonsNonScolarisation()
    {
        return $this->hasMany(VisitePlantationEnfantRaisonNonScolarisation::class, 'enfant_id');
    }

    public function raisonsPasExtrait()
    {
        return $this->hasMany(VisitePlantationEnfantRaisonPasExtrait::class, 'enfant_id');
    }

    public function situationsPfte()
    {
        return $this->hasMany(VisitePlantationEnfantSituationPfte::class, 'enfant_id');
    }

    public function raisonsTravailAbus()
    {
        return $this->hasMany(VisitePlantationEnfantRaisonTravailAbus::class, 'enfant_id');
    }

    public function mesuresEnfant()
    {
        return $this->hasMany(VisitePlantationEnfantMesureEnfant::class, 'enfant_id');
    }

    public function mesuresMenage()
    {
        return $this->hasMany(VisitePlantationEnfantMesureMenage::class, 'enfant_id');
    }

    public function mesuresCommunaute()
    {
        return $this->hasMany(VisitePlantationEnfantMesureCommunaute::class, 'enfant_id');
    }
}
