<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Kirschbaum\PowerJoins\PowerJoins;

class User_localite extends Model
{
    public function section(){
        return $this->belongsTo(Section::class,'section_id');
    }
    public function localite(){
        return $this->belongsTo(Localite::class,'localite_id');
    }
    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }
}
