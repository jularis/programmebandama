<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\GlobalStatus;
use App\Traits\Searchable;
use Kirschbaum\PowerJoins\PowerJoins;


class ActiviteCommunautaireBeneficiaire extends Model
{
    use HasFactory, Searchable, GlobalStatus, PowerJoins;

    protected $table = 'activite_communautaire_beneficiaires';

    public function activiteCommunautaire()
    {
        return $this->belongsTo(ActiviteCommunautaire::class, 'activite_communautaire_id');
    }
    public function localite()
    {
        return $this->belongsTo(Localite::class, 'localite_id');
    }
    public function producteur()
    {
        return $this->belongsTo(Producteur::class, 'producteur_id');
    }
}
