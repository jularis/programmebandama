<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EnqueteMenageEnfantRaisonPasExtrait extends Model
{
    protected $table = 'enquete_menage_enfant_raisons_pas_extrait';
    protected $guarded = [];

    public function enfant()
    {
        return $this->belongsTo(EnqueteMenageEnfant::class, 'enfant_id');
    }
}
