<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Searchable;
use Kirschbaum\PowerJoins\PowerJoins;

class SuiviConnaissementUsine extends Model
{

    use Searchable, GlobalStatus, PowerJoins;
    protected $table="suivi_connaissement_usines";

    public function connaissement()
    {
        return $this->belongsTo(Connaissement::class, 'connaissement_id');
    }
     
}