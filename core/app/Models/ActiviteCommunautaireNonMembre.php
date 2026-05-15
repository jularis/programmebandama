<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kirschbaum\PowerJoins\PowerJoins;
use App\Traits\GlobalStatus;
use App\Traits\Searchable;

class ActiviteCommunautaireNonMembre extends Model
{
    use HasFactory, PowerJoins, GlobalStatus, Searchable;
    protected $table = 'activite_communautaire_non_membres';

    public function activiteCommunautaire()
    {
        return $this->belongsTo(ActiviteCommunautaire::class, 'activite_communautaire_id');
    }
}
