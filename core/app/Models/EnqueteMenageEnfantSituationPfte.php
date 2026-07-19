<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EnqueteMenageEnfantSituationPfte extends Model
{
    protected $table = 'enquete_menage_enfant_situations_pfte';
    protected $guarded = [];

    public function enfant()
    {
        return $this->belongsTo(EnqueteMenageEnfant::class, 'enfant_id');
    }
}
