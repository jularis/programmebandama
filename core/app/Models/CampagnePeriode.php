<?php

namespace App\Models;

use App\Traits\Searchable;
use App\Traits\GlobalStatus;
use Kirschbaum\PowerJoins\PowerJoins;
use Illuminate\Database\Eloquent\Model;

class CampagnePeriode extends Model
{
    use Searchable, GlobalStatus, PowerJoins;

    public function campagne()
    {
        return $this->belongsTo(Campagne::class, 'campagne_id');
    }
}