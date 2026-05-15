<?php
namespace App\Models;

use App\Traits\Searchable;
use App\Traits\GlobalStatus;

use App\Traits\HasCooperative;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Certification extends Model
{
    use HasFactory, Searchable, GlobalStatus; 
}
