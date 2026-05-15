<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\GlobalStatus;
use App\Traits\Searchable;
use Kirschbaum\PowerJoins\PowerJoins;

class Localite_jour_marche extends Model
{
    use HasFactory,Searchable, GlobalStatus, PowerJoins;
    protected $guarded = [];
    protected $table = 'localite_jour_marches';
    public function localite()
    {
        return $this->belongsTo(Localite::class, 'localite_id');
    }
}
