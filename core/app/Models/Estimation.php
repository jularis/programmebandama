<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Model;
use Kirschbaum\PowerJoins\PowerJoins;

class Estimation extends Model
{
    use Searchable, GlobalStatus, PowerJoins;

    public function campagne()
    {
        return $this->belongsTo(Campagne::class);
    }

    public function parcelle()
    {
        return $this->belongsTo(Parcelle::class);
    }
     
     
}