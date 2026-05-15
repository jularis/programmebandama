<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\GlobalStatus;
use App\Traits\Searchable;
use Kirschbaum\PowerJoins\PowerJoins;

class Menage_sourceEnergie extends Model
{
    use HasFactory,Searchable, GlobalStatus, PowerJoins;
    
    protected $table = 'menage_sourceenergies';
    protected $guarded = [];

    public function menage()
    {
        return $this->belongsTo(Menage::class, 'menage_id');
    }
}
