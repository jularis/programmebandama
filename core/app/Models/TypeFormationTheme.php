<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kirschbaum\PowerJoins\PowerJoins;
use App\Traits\GlobalStatus;
use App\Traits\Searchable;

class TypeFormationTheme extends Model
{
    use HasFactory, PowerJoins, GlobalStatus, Searchable;

    protected $table = 'type_formation_themes';

    public function suiviFormation()
    {
        return $this->belongsTo(SuiviFormation::class, 'suivi_formation_id');
    }

    public function typeFormation()
    {
        return $this->belongsTo(TypeFormation::class, 'type_formation_id');
    }

    public function themeFormation()
    {
        return $this->belongsTo(ThemesFormation::class, 'theme_formation_id');
    }
}
