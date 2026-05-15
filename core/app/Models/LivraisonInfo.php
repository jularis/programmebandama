<?php

namespace App\Models;

use App\Constants\Status;
use App\Traits\GlobalStatus;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Model;
use Kirschbaum\PowerJoins\PowerJoins;

class LivraisonInfo extends Model
{
    use Searchable, GlobalStatus, PowerJoins;
    public function cooperative()
    {
        return $this->belongsTo(Cooperative::class, 'cooperative_id');
    }

    public function senderStaff()
    {
        return $this->belongsTo(User::class, 'sender_staff_id');
    }

    public function receiverStaff()
    {
        return $this->belongsTo(User::class, 'receiver_staff_id');
    }

    public function receiverCooperative()
    {
        return $this->belongsTo(Cooperative::class, 'receiver_cooperative_id');
    }
    public function magasinSection()
    {
        return $this->belongsTo(MagasinSection::class, 'receiver_magasin_section_id');
    }
    public function campagne()
    {
        return $this->belongsTo(Campagne::class, 'campagne_id');
    }

    public function senderCooperative()
    {
        return $this->belongsTo(Cooperative::class, 'sender_cooperative_id');
    }

    public function paymentInfo()
    {
        return $this->hasOne(LivraisonPayment::class, 'livraison_info_id');
    }

    public function livraisonDetail()
    {
        return $this->hasMany(LivraisonProduct::class, 'livraison_info_id');
    }

    public function scopeQueue()
    {
        return $this->where('sender_cooperative_id', auth()->user()->cooperative_id)->where('status', Status::COURIER_QUEUE);
    }

    public function scopeDispatched()
    {
        return $this->where('sender_cooperative_id', auth()->user()->cooperative_id)->where('status', Status::COURIER_DISPATCH);
    }

    public function scopeUpcoming()
    {
        return $this->where('receiver_cooperative_id', auth()->user()->cooperative_id)->where('status', Status::COURIER_UPCOMING);
    }

    public function scopeDeliveryQueue()
    {
        return $this->where('receiver_cooperative_id', auth()->user()->cooperative_id)->where('status', Status::COURIER_DELIVERYQUEUE);
    }

    public function scopeDelivered()
    {
        return $this->where('receiver_cooperative_id', auth()->user()->cooperative_id)->where('status', Status::COURIER_DELIVERED);
    }

    public function products()
    {
        return $this->hasMany(LivraisonProduct::class, 'livraison_info_id', 'id');
    }
    public function scelles()
    {
        return $this->hasMany(LivraisonScelle::class, 'livraison_info_id', 'id');
    }
    public function payment()
    {
        return $this->hasOne(LivraisonPayment::class, 'livraison_info_id', 'id');
    }
}