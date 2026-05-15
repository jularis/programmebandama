<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\GlobalStatus;
use App\Traits\Searchable;
use Kirschbaum\PowerJoins\PowerJoins;

class SuiviParcellesIntrantAnneeDerniere extends Model
{
    use HasFactory,Searchable, GlobalStatus, PowerJoins;

    protected $table = 'suivi_parcelles_intrant_annee_dernieres';

    public function suiviParcelle()
    {
        return $this->belongsTo(SuiviParcelle::class,'suivi_parcelle_id');
    }
}
