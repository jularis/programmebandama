<?php

namespace App\Models;

use App\Models\TypeFormation;
use App\Traits\GlobalStatus;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Model;
use Kirschbaum\PowerJoins\PowerJoins;

class ThemesFormation extends Model
{
    use Searchable, GlobalStatus, PowerJoins;

    protected $table = 'themes_formations';

    public function sousThemes()
    {
        return $this->hasMany(SousThemeFormation::class, 'theme_formation_id');
    } 

    public function typeFormation()
    {
        return $this->belongsTo(TypeFormation::class, 'type_formation_id');
    }
}