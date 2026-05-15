<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\GlobalStatus;
use App\Traits\Searchable;
use Kirschbaum\PowerJoins\PowerJoins;

class Pays extends Model
{
    use HasFactory, Searchable, GlobalStatus, PowerJoins;

    protected $guarded = [];

    protected $table = 'countries';
    
    public function producteurs()
    {
        return $this->hasMany(Producteur::class, 'pays_id');
    }
}
