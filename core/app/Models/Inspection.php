<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Model;
use Kirschbaum\PowerJoins\PowerJoins;

class Inspection extends Model
{
    use Searchable, GlobalStatus, PowerJoins;

    public function producteur()
    {
        return $this->belongsTo(Producteur::class);
    }
    public function parcelle()
    {
        return $this->belongsTo(Parcelle::class);
    }
    public function campagne()
    {
        return $this->belongsTo(Campagne::class);
    }
     
    public function user()
    {
        return $this->belongsTo(User::class,'formateur_id');
    }
    public function reponsesInspection(){
        return $this->hasMany(InspectionQuestionnaire::class,'inspection_id');
    }
    public function reponses(){
        return $this->belongsToMany(Questionnaire::class,'inspection_questionnaires')->withPivot('notation');
    }
}