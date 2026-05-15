<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kirschbaum\PowerJoins\PowerJoins;
use App\Traits\GlobalStatus;
use App\Traits\Searchable;

class Entreprise extends Model
{
    use HasFactory, PowerJoins, GlobalStatus, Searchable;

    protected $table = "entreprises";

    public function formateurs()
    {
        return $this->hasMany(FormateurStaff::class, 'entreprise_id');
    }

    public function formateurExternes(){
        return $this->hasMany(FormateurStaff::class, 'entreprise_id');
    }
}
