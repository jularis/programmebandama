<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kirschbaum\PowerJoins\PowerJoins;
use App\Traits\GlobalStatus;
use App\Traits\Searchable; 

class VarieteParcelle extends Model
{
    use HasFactory, PowerJoins, GlobalStatus, Searchable;

    protected $table = 'varieteParcelles';

    public function parcelle()
    {
        return $this->belongsTo(Parcelle::class, 'parcelle_id');
    }
}
