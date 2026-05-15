<?php
namespace App\Models;
use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;
use Kirschbaum\PowerJoins\PowerJoins;

class InspectionQuestionnaire extends Model
{

    use GlobalStatus;
 
    public function inspection()
    {
        return $this->belongsTo(Inspection::class, 'inspection_id');
    }
    public function questionnaire()
    {
        return $this->belongsTo(Questionnaire::class, 'questionnaire_id');
    }
    
}