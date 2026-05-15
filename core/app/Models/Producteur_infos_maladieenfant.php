<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Kirschbaum\PowerJoins\PowerJoins;

class Producteur_infos_maladieenfant extends Model
{
    use PowerJoins;
    
    public function producteurInfo(){

        return $this->belongsTo(Producteur_info::class);
    }
}
