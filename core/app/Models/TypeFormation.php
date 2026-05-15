<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Model;
use Kirschbaum\PowerJoins\PowerJoins;

class TypeFormation extends Model
{
    use Searchable, GlobalStatus, PowerJoins;

    protected $table = 'type_formations';

    public function themes(){
        return $this->hasMany(ThemesFormation::class, 'type_formation_id');
    }
     
}