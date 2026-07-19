<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kirschbaum\PowerJoins\PowerJoins;

class VisitePlantation extends Model
{
    use Searchable, GlobalStatus, PowerJoins, SoftDeletes;

    protected $table = 'visite_plantations';
    protected $guarded = [];

    public function producteur()
    {
        return $this->belongsTo(Producteur::class, 'producteur_id');
    }

    public function localite()
    {
        return $this->belongsTo(Localite::class, 'localite_id');
    }

    public function section()
    {
        return $this->belongsTo(Section::class, 'section_id');
    }

    public function enfants()
    {
        return $this->hasMany(VisitePlantationEnfant::class, 'visite_id');
    }

    public function raisonsRefus()
    {
        return $this->hasMany(VisitePlantationRaisonRefus::class, 'visite_id');
    }
}
