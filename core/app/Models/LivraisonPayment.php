<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Model;
use Kirschbaum\PowerJoins\PowerJoins;

class LivraisonPayment extends Model
{

    use Searchable, GlobalStatus, PowerJoins;

    public function livraisonInfo()
    {
        return $this->belongsTo(LivraisonInfo::class, 'livraison_info_id');
    }
    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function cooperative()
    {
        return $this->belongsTo(Cooperative::class, 'cooperative_id');
    }
    public function campagne()
    {
        return $this->belongsTo(Campagne::class, 'campagne_id');
    }
}