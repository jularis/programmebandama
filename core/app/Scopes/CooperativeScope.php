<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class CooperativeScope implements Scope
{

    public function apply(Builder $builder, Model $model)
    {

        // Check if model has cooperative method which comes from HasCooperative Trait.
        // If that has method then it has cooperative otherwise it do not have cooperative id
        // and we can simply return from here
        if (!method_exists($model, 'cooperative')) {
            return $builder;
        }

        // When user is logged in
        // auth()->user() do not work in apply so we have use auth()->hasUser()
        if (auth()->hasUser()) {

            $cooperative = cooperative();

            // We are not checking if table has cooperative_id or not to avoid extra queries.
            // We need to be extra careful with migrations we have created. For all the migration when doing something with update
            // we need to add withoutGlobalScope(CooperativeScope::class)
            // Otherwise we will get the error of cooperative_id not found when application is updating or modules are installing

            if ($cooperative) {
                $builder->where($model->getTable() . '.cooperative_id', '=', $cooperative->id);
            }
        }
    }

}
