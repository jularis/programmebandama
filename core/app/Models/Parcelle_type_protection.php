<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\GlobalStatus;
use App\Traits\Searchable;
use Kirschbaum\PowerJoins\PowerJoins;

class Parcelle_type_protection extends Model
{
    use HasFactory,Searchable, GlobalStatus, PowerJoins;

    protected $table = 'parcelle_type_protections';

    public function parcelle()
    {
        return $this->belongsTo(Parcelle::class,'parcelle_id');
    }
}
