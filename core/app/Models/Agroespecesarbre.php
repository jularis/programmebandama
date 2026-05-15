<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Model;
use Kirschbaum\PowerJoins\PowerJoins;

class Agroespecesarbre extends Model
{
    use Searchable, GlobalStatus, PowerJoins;
    protected $guarded = [];

    public function agroespeceabre_parcelles()
    {
        return $this->hasMany(agroespeceabre_parcelle::class, 'agroespeceabre_id');
    }
}