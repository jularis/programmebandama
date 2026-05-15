<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Kirschbaum\PowerJoins\PowerJoins;

class Frontend extends Model
{
    protected $casts = [
        'data_values' => 'object'
    ];

    public static function scopeGetContent($data_keys)
    {
        return Frontend::where('data_keys', $data_keys);
    }
}
