<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Localite_centre_sante extends Model
{
    use HasFactory;
    protected $guarded = [];
    public function localite()
    {
        return $this->belongsTo(Localite::class, 'localite_id', 'id');
    }
}
