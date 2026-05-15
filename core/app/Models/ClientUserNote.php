<?php

namespace App\Models;

use App\Traits\HasCooperative;

/**
 * App\Models\ClientUserNote
 *
 * @property int $id
 * @property int $user_id
 * @property int $client_note_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|ClientUserNote newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ClientUserNote newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ClientUserNote query()
 * @method static \Illuminate\Database\Eloquent\Builder|ClientUserNote whereClientNoteId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientUserNote whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientUserNote whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientUserNote whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientUserNote whereUserId($value)
 * @property int|null $cooperative_id
 * @property-read \App\Models\Cooperative|null $cooperative
 * @method static \Illuminate\Database\Eloquent\Builder|ClientUserNote whereCooperativeId($value)
 * @mixin \Eloquent
 */
class ClientUserNote extends BaseModel
{

    use HasCooperative;

    protected $table = 'client_user_notes';
    protected $fillable = ['user_id', 'client_note_id'];

}
