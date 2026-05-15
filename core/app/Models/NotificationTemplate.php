<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Kirschbaum\PowerJoins\PowerJoins;

class NotificationTemplate extends Model
{
    protected $casts = [
        'shortcodes' => 'object'
    ];

}
