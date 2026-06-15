<?php

namespace App\Models\Concerns;

use App\Models\Scopes\SchoolScope;
use App\Support\CrossTenantSecurityLog;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

trait BelongsToSchool
{
    public function initializeBelongsToSchool(): void
    {
        if (property_exists($this, 'fillable') && is_array($this->fillable)) {
            $this->fillable = array_values(array_diff($this->fillable, ['school_id']));
        }
    }

    public function scopeForSchool(Builder $query, ?int $schoolId = null): Builder
    {
        $schoolId ??= auth()->user()?->school_id;

        if ($schoolId) {
            $query->where($query->getModel()->getTable() . '.school_id', $schoolId);
        }

        return $query;
    }

    protected static function bootBelongsToSchool(): void
    {
        static::addGlobalScope(new SchoolScope);

        static::creating(function (Model $model) {
            if (! empty($model->school_id)) {
                return;
            }

            if (auth()->check() && auth()->user()->school_id) {
                $model->school_id = auth()->user()->school_id;
            }
        });
    }

    public static function findForSchool(int $schoolId, int|string $id): ?static
    {
        return static::withoutGlobalScopes()
            ->where('school_id', $schoolId)
            ->find($id);
    }

    public static function findForSchoolOrFail(int $schoolId, int|string $id): static
    {
        return static::withoutGlobalScopes()
            ->where('school_id', $schoolId)
            ->findOrFail($id);
    }

    /** Persist with an explicit tenant key (queue / observer safe). */
    public static function createForSchool(int $schoolId, array $attributes): static
    {
        $model = new static($attributes);
        $model->school_id = $schoolId;
        $model->save();

        return $model;
    }

    /** Route model binding scoped to the authenticated user's school. */
    public function resolveRouteBinding($value, $field = null)
    {
        $field ??= $this->getRouteKeyName();

        if (auth()->check() && auth()->user()->school_id) {
            $schoolId = auth()->user()->school_id;

            $scoped = static::query()
                ->where($this->getTable().'.school_id', $schoolId)
                ->where($field, $value)
                ->first();

            if ($scoped) {
                return $scoped;
            }

            $existsElsewhere = static::withoutGlobalScopes()
                ->where($field, $value)
                ->where('school_id', '!=', $schoolId)
                ->exists();

            if ($existsElsewhere) {
                CrossTenantSecurityLog::routeBindingProbe(static::class, $value);
            }
        }

        return static::query()
            ->where($field, $value)
            ->firstOrFail();
    }
}
