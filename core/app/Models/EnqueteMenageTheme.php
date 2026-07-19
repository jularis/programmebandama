<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EnqueteMenageTheme extends Model
{
    protected $table = 'enquete_menage_themes';
    protected $guarded = [];

    public function menage()
    {
        return $this->belongsTo(EnqueteMenage::class, 'enquete_menage_id');
    }
}
