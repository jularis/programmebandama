<?php

namespace App\Models;

use App\Traits\HasCooperative;
use Illuminate\Database\Eloquent\Factories\HasFactory;

 
class CooperativeAddress extends BaseModel
{

    use HasFactory, HasCooperative;

    protected $fillable = ['address', 'is_default', 'location', 'tax_number', 'tax_name', 'longitude', 'latitude'];

    public static function defaultAddress()
    {
        return CooperativeAddress::where('is_default', 1)->first();
    }

}
