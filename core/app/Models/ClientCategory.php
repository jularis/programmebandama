<?php

namespace App\Models;

use App\Traits\HasCooperative;

/**
 * App\Models\ClientCategory
 *
 * @property int $id
 * @property string $category_name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|ClientCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ClientCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ClientCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder|ClientCategory whereCategoryName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientCategory whereUpdatedAt($value)
 * @property int|null $cooperative_id
 * @property-read \App\Models\Cooperative|null $cooperative
 * @method static \Illuminate\Database\Eloquent\Builder|ClientCategory whereCooperativeId($value)
 * @mixin \Eloquent
 */
class ClientCategory extends BaseModel
{

    use HasCooperative;

    protected $table = 'client_categories';

}
