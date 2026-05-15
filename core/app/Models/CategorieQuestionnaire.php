<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Model;
use Kirschbaum\PowerJoins\PowerJoins;

class CategorieQuestionnaire extends Model
{
    use Searchable, GlobalStatus, PowerJoins; 

    public function questions()
    {
        return $this->hasMany(Questionnaire::class, 'categorie_questionnaire_id', 'id');
    }
     
}