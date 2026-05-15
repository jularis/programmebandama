<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\GlobalStatus;
use App\Traits\Searchable;

class ActionSocialeLocalite extends Model
{
    use HasFactory, Searchable, GlobalStatus;
    protected $table = 'action_sociale_localites';

    public function actionSociale()
    {
        return $this->belongsTo(ActionSociale::class, 'action_sociale_id');
    }
    public function localite()
    {
        return $this->belongsTo(Localite::class, 'localite_id');
    }
}
