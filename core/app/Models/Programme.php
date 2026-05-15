<?php

namespace App\Models;

use App\Traits\Searchable;
use App\Traits\GlobalStatus;
use Kirschbaum\PowerJoins\PowerJoins;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Programme extends Model
{
    use HasFactory,Searchable, GlobalStatus, PowerJoins;

    protected $guarded = [];

    // protected $casts = [
    //     'created_at' => 'datetime:Y-m-d',
    // ];
    protected $table = 'programmes';

    public function producteurs()
    {
        return $this->hasMany(Producteur::class, 'programme_id');
    }
}
