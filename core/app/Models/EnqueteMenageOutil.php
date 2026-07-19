<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EnqueteMenageOutil extends Model
{
    protected $table = 'enquete_menage_outils';
    protected $guarded = [];

    public function menage()
    {
        return $this->belongsTo(EnqueteMenage::class, 'enquete_menage_id');
    }
}
