<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Model;
use Kirschbaum\PowerJoins\PowerJoins;

class Ssrteclmrs extends Model
{
    use Searchable, GlobalStatus, PowerJoins;
    protected $table='ssrteclmrs';

    public function producteur()
    {
        return $this->belongsTo(Producteur::class);
    }
    public function localite()
    {
        return $this->belongsTo(Localite::class);
    }
    public function lieutravauxdangereux()
    {
        return $this->hasMany(SsrteclmrsLieutravauxdangereux::class, 'ssrteclmrs_id', 'id');
    }
     
    public function lieutravauxlegers()
    {
        return $this->hasMany(SsrteclmrsLieutravauxleger::class, 'ssrteclmrs_id', 'id');
    }

    public function raisonarretecoles()
    {
        return $this->hasMany(SsrteclmrsRaisonarretecole::class, 'ssrteclmrs_id', 'id');
    }

    public function travauxdangereux()
    {
        return $this->hasMany(SsrteclmrsTravauxdangereux::class, 'ssrteclmrs_id', 'id');
    }

    public function travauxlegers()
    {
        return $this->hasMany(SsrteclmrsTravauxleger::class, 'ssrteclmrs_id', 'id');
    }
}