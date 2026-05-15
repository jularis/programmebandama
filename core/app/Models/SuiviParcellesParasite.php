<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Model;
use Kirschbaum\PowerJoins\PowerJoins;

class SuiviParcellesParasite extends Model
{
    use Searchable, GlobalStatus, PowerJoins;
    protected $tables = 'suivi_parcelles_parasites'; 

    public function suiviParcelle()
    {
        return $this->belongsTo(SuiviParcelle::class, 'suivi_parcelle_id', 'id');
    }
     
     
}