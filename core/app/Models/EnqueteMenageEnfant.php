<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EnqueteMenageEnfant extends Model
{
    protected $table = 'enquete_menage_enfants';
    protected $guarded = [];

    public function menage()
    {
        return $this->belongsTo(EnqueteMenage::class, 'enquete_menage_id');
    }

    public function raisonsNonScolarisation()
    {
        return $this->hasMany(EnqueteMenageEnfantRaisonNonScolarisation::class, 'enfant_id');
    }

    public function raisonsPasExtrait()
    {
        return $this->hasMany(EnqueteMenageEnfantRaisonPasExtrait::class, 'enfant_id');
    }

    public function situationsPfte()
    {
        return $this->hasMany(EnqueteMenageEnfantSituationPfte::class, 'enfant_id');
    }

    public function raisonsTravailAbus()
    {
        return $this->hasMany(EnqueteMenageEnfantRaisonTravailAbus::class, 'enfant_id');
    }

    public function mesuresEnfant()
    {
        return $this->hasMany(EnqueteMenageEnfantMesureEnfant::class, 'enfant_id');
    }

    public function mesuresMenage()
    {
        return $this->hasMany(EnqueteMenageEnfantMesureMenage::class, 'enfant_id');
    }

    public function mesuresCommunaute()
    {
        return $this->hasMany(EnqueteMenageEnfantMesureCommunaute::class, 'enfant_id');
    }

    public function suivis()
    {
        return $this->hasMany(SuiviEnfantTravailleur::class, 'enfant_id')->latest('id');
    }
}
