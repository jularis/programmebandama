<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\GlobalStatus;
use App\Traits\Searchable;
use Kirschbaum\PowerJoins\PowerJoins;

class AutreAgroespecesarbreParcelle extends Model
{
    use HasFactory, Searchable, GlobalStatus, PowerJoins;

    protected $table = 'autre_agroespeceabre_parcelles';
    protected $guarded = [];

    public function parcelle()
    {
        return $this->belongsTo(Parcelle::class, 'parcelle_id');
    }
}
