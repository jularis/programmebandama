<?php

namespace App\Models;

use App\Traits\HasCooperative;
use Illuminate\Database\Eloquent\Relations\HasMany;
 
class Team extends BaseModel
{

    use HasCooperative;
    protected $table='departments';
    protected $fillable = ['department'];

    public function members(): HasMany
    {
        return $this->hasMany(EmployeeTeam::class, 'team_id');
    }

    public function teamMembers(): HasMany
    {
        return $this->hasMany(EmployeeDetail::class, 'department_id');
    }

    public static function allDepartments()
    {
        // if (user()->permission('view_department') == 'all' || user()->permission('view_department') == 'none') {
        //     return Team::all();
        // }

        // return Team::where('added_by', user()->id)->get();
        return Team::all();
    }

    public function childs(): HasMany
    {
        return $this->hasMany(Team::class, 'parent_id');
    }

}
