<?php

namespace App\Models;

use App\Traits\EtatSoumissionBadge;
use App\Traits\GlobalStatus;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SuiviEnfantTravailleur extends Model
{
    use Searchable, GlobalStatus, EtatSoumissionBadge, SoftDeletes;

    protected $table = 'suivi_enfant_travailleurs';
    protected $guarded = [];

    public function enfant()
    {
        return $this->belongsTo(EnqueteMenageEnfant::class, 'enfant_id');
    }

    public function actionsRemediation()
    {
        return $this->hasMany(SuiviEnfantTravailleurActionRemediation::class, 'suivi_id');
    }

    public function raisonsNonScolarisation()
    {
        return $this->hasMany(SuiviEnfantTravailleurRaisonNonScolarisation::class, 'suivi_id');
    }

    public function raisonsPasExtrait()
    {
        return $this->hasMany(SuiviEnfantTravailleurRaisonPasExtrait::class, 'suivi_id');
    }

    public function situationsPfte()
    {
        return $this->hasMany(SuiviEnfantTravailleurSituationPfte::class, 'suivi_id');
    }

    public function raisonsTravailAbus()
    {
        return $this->hasMany(SuiviEnfantTravailleurRaisonTravailAbus::class, 'suivi_id');
    }

    public function mesuresEnfant()
    {
        return $this->hasMany(SuiviEnfantTravailleurMesureEnfant::class, 'suivi_id');
    }

    public function mesuresMenage()
    {
        return $this->hasMany(SuiviEnfantTravailleurMesureMenage::class, 'suivi_id');
    }

    public function mesuresCommunaute()
    {
        return $this->hasMany(SuiviEnfantTravailleurMesureCommunaute::class, 'suivi_id');
    }

    public function themes()
    {
        return $this->hasMany(SuiviEnfantTravailleurTheme::class, 'suivi_id');
    }

    public function outils()
    {
        return $this->hasMany(SuiviEnfantTravailleurOutil::class, 'suivi_id');
    }
}
