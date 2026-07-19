<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EnqueteMenageEnfantRaisonNonScolarisation extends Model
{
    protected $table = 'enquete_menage_enfant_raisons_non_scolarisation';
    protected $guarded = [];

    public function enfant()
    {
        return $this->belongsTo(EnqueteMenageEnfant::class, 'enfant_id');
    }
}
