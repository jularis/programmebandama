<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Model;
use Kirschbaum\PowerJoins\PowerJoins;

class Questionnaire extends Model
{
    use Searchable, GlobalStatus, PowerJoins; 

    public function categorieQuestion()
    {
        return $this->belongsTo(CategorieQuestionnaire::class,'categorie_questionnaire_id');
    }
    
     
}