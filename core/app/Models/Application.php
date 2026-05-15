<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Model;
use Kirschbaum\PowerJoins\PowerJoins;

class Application extends Model
{
    use Searchable, GlobalStatus, PowerJoins;

    public function parcelle()
    {
        return $this->belongsTo(Parcelle::class);
    }

    public function campagne()
    {
        return $this->belongsTo(Campagne::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class,'applicateur_id');
    }
    public function applicationPesticides()
    {
        return $this->hasMany(ApplicationPesticide::class,'application_id');
    }
    public function applicationMaladies()
    {
        return $this->hasMany(ApplicationMaladie::class,'application_id');
    }
    public function matiereActives()
    {
        return $this->hasMany(MatiereActive::class,'application_id');
    }
    public function applicationAutreMaladies()
    {
        return $this->hasMany(ApplicationAutreMaladie::class,'application_id');
    }
}