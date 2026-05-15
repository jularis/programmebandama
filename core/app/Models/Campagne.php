<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use App\Traits\HasCooperative;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Model;
use Kirschbaum\PowerJoins\PowerJoins;


class Campagne extends Model
{
    use Searchable, GlobalStatus, PowerJoins;

    public function cooperative()
    {
        return $this->belongsTo(Cooperative::class, 'cooperative_id');
    }
}