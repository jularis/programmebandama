<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EnqueteMenageEnfantMesureEnfant extends Model
{
    protected $table = 'enquete_menage_enfant_mesures_enfant';
    protected $guarded = [];

    public function enfant()
    {
        return $this->belongsTo(EnqueteMenageEnfant::class, 'enfant_id');
    }
}
