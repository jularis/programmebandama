<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\GlobalStatus;
use App\Traits\Searchable;

class ActionSocialePartenaire extends Model
{
    use HasFactory, GlobalStatus, Searchable;

    protected $guarded = [];

    public function actionSociale()
    {
        return $this->belongsTo(ActionSociale::class);
    }
}
