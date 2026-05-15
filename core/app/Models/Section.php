<?php

namespace App\Models;

use App\Traits\Searchable;
use App\Traits\GlobalStatus;

use App\Traits\HasCooperative;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Section extends Model
{
    use HasFactory, Searchable, GlobalStatus;
    use HasCooperative; 

    protected $guarded = [];

    public function cooperative()
    {
        return $this->belongsTo(Cooperative::class, 'cooperative_id');
    } 
	
    public function localites()
    {
        return $this->hasMany(Localite::class, 'section_id', 'id');
    }
}
