<?php

namespace App\Models;

use App\Traits\HasCooperative;

/**
 * App\Models\Skill
 *
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $icon
 * @method static \Illuminate\Database\Eloquent\Builder|Skill newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Skill newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Skill query()
 * @method static \Illuminate\Database\Eloquent\Builder|Skill whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Skill whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Skill whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Skill whereUpdatedAt($value)
 * @property int|null $cooperative_id
 * @property-read \App\Models\Cooperative|null $cooperative
 * @method static \Illuminate\Database\Eloquent\Builder|Skill whereCooperativeId($value)
 * @mixin \Eloquent
 */
class Skill extends BaseModel
{

    use HasCooperative;

    protected $table = 'skills';
    protected $fillable = ['name'];

}
