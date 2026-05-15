<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Model;
use Kirschbaum\PowerJoins\PowerJoins;

class Cooperative extends Model
{
    use Searchable, GlobalStatus, PowerJoins;

    public function cooperativeUsers()
    {
        return $this->hasMany(User::class, 'cooperative_id', 'id');
    }
    public function sections()
    {
        return $this->hasMany(Section::class, 'cooperative_id', 'id');
    }
    // Dans le modèle Cooperative
    public function producteurs()
    {
        return $this->hasMany(Producteur::class);
    }
}
