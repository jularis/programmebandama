<?php

namespace App\Traits;

use App\Models\Cooperative;
use App\Scopes\CooperativeScope;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait HasCooperative
{

    protected static function booted()
    {
        static::addGlobalScope(new CooperativeScope());
    }

    public function cooperative(): BelongsTo
    {
        return $this->belongsTo(Cooperative::class);
    }

}
