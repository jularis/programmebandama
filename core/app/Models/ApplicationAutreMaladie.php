<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\GlobalStatus;
use App\Traits\Searchable;
use Kirschbaum\PowerJoins\PowerJoins;

class ApplicationAutreMaladie extends Model
{
    use HasFactory, Searchable, GlobalStatus, PowerJoins;

    protected $table = 'application_autre_maladies';

    public function application()
    {
        return $this->belongsTo(Application::class, 'application_id');
    }
}
