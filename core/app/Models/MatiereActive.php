<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kirschbaum\PowerJoins\PowerJoins;
use App\Traits\GlobalStatus;
use App\Traits\Searchable;

class MatiereActive extends Model
{
    use HasFactory, PowerJoins, GlobalStatus, Searchable;
    protected $table = 'matiere_actives';

    public function application()
    {
        return $this->belongsTo(Application::class, 'application_id');
    }
    
    public function applicationPesticide(){
        return $this->belongsTo(ApplicationPesticide::class,'application_pesticide_id');
    }
}
