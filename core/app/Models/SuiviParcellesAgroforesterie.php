<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Model;
use Kirschbaum\PowerJoins\PowerJoins;

class SuiviParcellesAgroforesterie extends Model
{
    use Searchable, GlobalStatus, PowerJoins; 

    public function suiviParcelle()
    {
        return $this->belongsTo(SuiviParcelle::class);
    }
     
     
}