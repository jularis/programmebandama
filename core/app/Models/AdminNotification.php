<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Kirschbaum\PowerJoins\PowerJoins;

class AdminNotification extends Model
{
    public function user()
    {
    	return $this->belongsTo(User::class);
    }
}
