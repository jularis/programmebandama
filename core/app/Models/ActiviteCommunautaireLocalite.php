<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\GlobalStatus;
use App\Traits\Searchable;

class ActiviteCommunautaireLocalite extends Model
{
    use HasFactory, Searchable, GlobalStatus;
    protected $table = 'activite_communautaire_localites';
    
    public function activiteCommunautaire()
    {
        return $this->belongsTo(ActiviteCommunautaire::class, 'activite_communautaire_id');
    }
    public function localite()
    {
        return $this->belongsTo(Localite::class, 'localite_id');
    }
}
