<?php

namespace App\Models\Scopes;

use App\Support\PlatformContext;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Auth;

class SchoolScope implements Scope
{
    
    public function apply(Builder $builder, Model $model): void
    {
        if (PlatformContext::isActive() && PlatformContext::schoolId()) {
            $builder->where(
                $model->getTable().'.school_id',
                PlatformContext::schoolId()
            );

            return;
        }

        if (Auth::check() && Auth::user()->school_id) {
            $builder->where(
                $model->getTable() . '.school_id',
                Auth::user()->school_id
            );
        }
    }
}
