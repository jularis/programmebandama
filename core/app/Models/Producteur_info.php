<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Model;
use Kirschbaum\PowerJoins\PowerJoins;

class Producteur_info extends Model
{
    use Searchable, PowerJoins;

    protected $guarded = [];

    public function producteur(){

        return $this->belongsTo(Producteur::class);
    }

    public function maladiesenfant()
    {
        return $this->hasMany(Producteur_infos_maladieenfant::class,'producteur_info_id', 'id');
    }

    public function typesculture()
    {
        return $this->hasMany(Producteur_infos_typeculture::class,'producteur_info_id', 'id');
    }
    public function autresactivites()
    {
        return $this->hasMany(Producteur_infos_autresactivite::class,'producteur_info_id', 'id');
    }
    public function mobiles(){
        return $this->hasMany(Producteur_infos_mobile::class,'producteur_info_id', 'id');
    }

}
