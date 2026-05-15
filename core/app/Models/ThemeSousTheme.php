<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kirschbaum\PowerJoins\PowerJoins;
use App\Traits\GlobalStatus;
use App\Traits\Searchable;

class ThemeSousTheme extends Model
{
    use HasFactory, PowerJoins, GlobalStatus, Searchable;

    protected $table = 'theme_sous_themes';


    public function suiviFormation()
    {
        return $this->belongsTo(SuiviFormation::class, 'suivi_formation_id');
    }

    public function themeFormation()
    {
        return $this->belongsTo(ThemesFormation::class, 'theme_id');
    }

    public function sousThemeFormation()
    {
        return $this->belongsTo(SousThemeFormation::class, 'sous_theme_id');
    }
}
