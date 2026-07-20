<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Casts\Attribute;

trait EtatSoumissionBadge
{
    public function etatSoumissionBadge(): Attribute
    {
        return new Attribute(
            get: fn () => $this->etatSoumission == 'Brouillon'
                ? '<span class="badge badge--warning">' . trans('Brouillon') . '</span>'
                : '<span class="badge badge--success">' . trans('Soumis') . '</span>',
        );
    }
}
