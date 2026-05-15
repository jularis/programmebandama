<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Model;
use Kirschbaum\PowerJoins\PowerJoins;

class SuiviFormation extends Model
{
    use Searchable, GlobalStatus, PowerJoins;

    public function localite()
    {
        return $this->belongsTo(Localite::class, 'localite_id');
    }
    public function typeFormation()
    {
        return $this->belongsTo(TypeFormation::class, 'type_formation_id', 'id');
    }
    public function campagne()
    {
        return $this->belongsTo(Campagne::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }

    public function formationProducteur()
    {
        return $this->hasMany(SuiviFormationProducteur::class, 'suivi_formation_id', 'id');
    }

    public function themeSousTheme()
    {
        return $this->hasMany(ThemeSousTheme::class, 'suivi_formation_id', 'id');
    }

    public function typeFormationTheme()
    {
        return $this->hasMany(TypeFormationTheme::class, 'suivi_formation_id', 'id');
    }

    public function entreprises(){
        return $this->belongsToMany(Entreprise::class,'formation_producteur_formateurs','suivi_formation_id','entreprise_id');
        
    }
    public function formateurs(){
        return $this->belongsToMany(FormateurStaff::class,'formation_producteur_formateurs','suivi_formation_id','formateur_staff_id');
    }
    public function suiviFormationEntrepriseFormateur(){
        return $this->hasMany(FormationProducteurFormateur::class,'suivi_formation_id','id');
    }
}