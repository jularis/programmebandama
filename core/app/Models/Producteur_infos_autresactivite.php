<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\GlobalStatus;
use App\Traits\Searchable;
use Kirschbaum\PowerJoins\PowerJoins;
use Illuminate\Database\Eloquent\Model;

class Producteur_infos_autresactivite extends Model
{
    use HasFactory,Searchable, PowerJoins,GlobalStatus;

    protected $guarded = [];
    protected $table = 'producteur_infos_autresactivites';

    public function producteurInfo(){

        return $this->belongsTo(Producteur_info::class,'producteur_info_id', 'id');
    }
    
}
