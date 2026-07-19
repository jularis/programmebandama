<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EnqueteMenageEnfantMesureMenage extends Model
{
    protected $table = 'enquete_menage_enfant_mesures_menage';
    protected $guarded = [];

    public function enfant()
    {
        return $this->belongsTo(EnqueteMenageEnfant::class, 'enfant_id');
    }
}
