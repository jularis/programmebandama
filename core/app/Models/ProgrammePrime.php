<?php

namespace App\Models;

use App\Traits\Searchable;
use App\Traits\GlobalStatus;
use Kirschbaum\PowerJoins\PowerJoins;
use Illuminate\Database\Eloquent\Model;

class ProgrammePrime extends Model
{
    use Searchable, GlobalStatus, PowerJoins;

    public function programme()
    {
        return $this->belongsTo(Programme::class, 'programme_id');
    }
}