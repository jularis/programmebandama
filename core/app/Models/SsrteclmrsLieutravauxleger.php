<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Model;
use Kirschbaum\PowerJoins\PowerJoins;

class SsrteclmrsLieutravauxleger extends Model
{
    use Searchable, GlobalStatus, PowerJoins;  

    public function ssrteclmrs()
    {
        return $this->belongsTo(Ssrteclmrs::class);
    }
     
     
}