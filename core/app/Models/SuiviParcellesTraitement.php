<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\GlobalStatus;
use App\Traits\Searchable;
use Kirschbaum\PowerJoins\PowerJoins;


class SuiviParcellesTraitement extends Model
{
    use HasFactory,Searchable, GlobalStatus, PowerJoins;

    protected $table = 'suivi_parcelles_traitements';

    public function suiviParcelle()
    {
        return $this->belongsTo(SuiviParcelle::class,'suivi_parcelle_id');
    }
}
