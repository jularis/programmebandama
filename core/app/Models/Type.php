<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;
use Kirschbaum\PowerJoins\PowerJoins;

class Type extends Model
{
    use GlobalStatus;

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
}