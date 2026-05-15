<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Model;
use Kirschbaum\PowerJoins\PowerJoins;

class SsrteclmrsTravauxdangereux extends Model
{
    use Searchable, GlobalStatus, PowerJoins; 
    protected $table="ssrteclmrs_travauxdangereux";

    public function ssrteclmrs()
    {
        return $this->belongsTo(Ssrteclmrs::class);
    }
     
     
}