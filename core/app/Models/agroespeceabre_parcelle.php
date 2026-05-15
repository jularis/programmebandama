<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\GlobalStatus;
use App\Traits\Searchable;
use Kirschbaum\PowerJoins\PowerJoins;


class agroespeceabre_parcelle extends Model
{
    use HasFactory,Searchable, GlobalStatus, PowerJoins;
    protected $table = 'agroespeceabre_parcelles';
    protected $guarded = [];
    public function agroespeceabre()
    {
        return $this->belongsTo(Agroespecesarbre::class, 'agroespeceabre_id');
    }
    public function parcelle()
    {
        return $this->belongsTo(Parcelle::class, 'parcelle_id');
    }

}
