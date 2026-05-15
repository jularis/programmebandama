<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Searchable;
use App\Traits\GlobalStatus;
use App\Traits\HasCooperative;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Kirschbaum\PowerJoins\PowerJoins;


class Remorque extends Model
{
    use HasFactory,Searchable, GlobalStatus, PowerJoins,HasCooperative;

    protected $table = 'remorques';

    public function cooperative(): BelongsTo
    {
        return $this->belongsTo(Cooperative::class,'cooperative_id');
    }
}
