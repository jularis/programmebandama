<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Model;
use Kirschbaum\PowerJoins\PowerJoins;

class MagasinSection extends Model
{
    use Searchable, GlobalStatus, PowerJoins;
    public function user()
    {
        return $this->belongsTo(User::class,'staff_id');
    }

    public function section()
    {
        return $this->belongsTo(Section::class,'section_id');
    }
 
}