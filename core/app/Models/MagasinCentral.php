<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Model;
use Kirschbaum\PowerJoins\PowerJoins;

class MagasinCentral extends Model
{
    use Searchable, GlobalStatus, PowerJoins;
    protected $table="magasin_centraux";
    
    public function user()
    {
        return $this->belongsTo(User::class,'staff_id');
    }

    public function cooperative()
    {
        return $this->belongsTo(Cooperative::class,'cooperative_id');
    }
}