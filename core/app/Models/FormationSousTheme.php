<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kirschbaum\PowerJoins\PowerJoins;
use App\Traits\GlobalStatus;
use App\Traits\Searchable;

class FormationSousTheme extends Model
{
    use HasFactory, PowerJoins, GlobalStatus, Searchable;

    protected $table = 'formation_sous_themes';
}
