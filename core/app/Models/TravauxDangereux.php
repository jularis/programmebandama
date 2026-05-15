<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;
use Kirschbaum\PowerJoins\PowerJoins;

class TravauxDangereux extends Model
{
    use GlobalStatus, PowerJoins;
    protected $table="travaux_dangereux";
}