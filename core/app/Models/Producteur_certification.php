<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\GlobalStatus;
use App\Traits\Searchable;
use Kirschbaum\PowerJoins\PowerJoins;


class Producteur_certification extends Model
{
    use HasFactory,HasFactory,Searchable, GlobalStatus, PowerJoins;

    protected $table = 'producteur_certifications';

    public function producteur()
    {
        return $this->belongsTo(Producteur::class,'producteur_id');
    }
}
