<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kirschbaum\PowerJoins\PowerJoins;
use App\Traits\GlobalStatus;
use App\Traits\Searchable;

class ApplicationPesticide extends Model
{
    use HasFactory, PowerJoins, GlobalStatus, Searchable;
    protected $table = 'application_pesticides';

    public function application()
    {
        return $this->belongsTo(Application::class, 'application_id');
    }
    public function matiereActives()
    {
        return $this->hasMany(MatiereActive::class, 'application_pesticide_id');
    }
}
