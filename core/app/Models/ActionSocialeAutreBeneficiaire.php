<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\GlobalStatus;
use App\Traits\Searchable;
use Kirschbaum\PowerJoins\PowerJoins;

class ActionSocialeAutreBeneficiaire extends Model
{
    use HasFactory, Searchable, GlobalStatus, PowerJoins;

    protected $table = 'action_sociale_autre_beneficiaires';

    public function actionSociale()
    {
        return $this->belongsTo(ActionSociale::class, 'action_sociale_id');
    }
}
