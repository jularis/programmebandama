<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EnqueteMenageRaisonRefus extends Model
{
    protected $table = 'enquete_menage_raison_refus';
    protected $guarded = [];

    public function menage()
    {
        return $this->belongsTo(EnqueteMenage::class, 'enquete_menage_id');
    }
}
