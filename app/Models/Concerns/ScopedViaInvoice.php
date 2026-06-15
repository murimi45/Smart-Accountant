<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;

trait ScopedViaInvoice
{
    protected static function bootScopedViaInvoice(): void
    {
        static::addGlobalScope('school', function (Builder $builder) {
            if (auth()->check() && auth()->user()->school_id) {
                $schoolId = auth()->user()->school_id;

                $builder->whereHas('invoice', function (Builder $query) use ($schoolId) {
                    $query->where('school_id', $schoolId);
                });
            }
        });
    }
}
