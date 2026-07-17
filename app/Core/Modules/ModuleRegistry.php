<?php

namespace App\Core\Modules;

use App\Models\Module;
use App\Models\SchoolModule;
use App\Support\TenantCache;
use Illuminate\Support\Collection;
use InvalidArgumentException;

class ModuleRegistry
{
    /** @return list<string> */
    public static function catalogSlugs(): array
    {
        return [
            Module::SLUG_ACCOUNTANT,
            Module::SLUG_HR,
            Module::SLUG_GRADING,
        ];
    }

    public static function findBySlug(string $slug): ?Module
    {
        return Module::query()->where('slug', $slug)->first();
    }

    public static function findBySlugOrFail(string $slug): Module
    {
        $module = static::findBySlug($slug);

        if (! $module) {
            throw new InvalidArgumentException("Unknown module [{$slug}].");
        }

        return $module;
    }

    public static function schoolHas(int $schoolId, string $slug): bool
    {
        return in_array($slug, static::enabledSlugsForSchool($schoolId), true);
    }

    /** @return list<string> */
    public static function enabledSlugsForSchool(int $schoolId): array
    {
        return TenantCache::remember('modules:enabled', now()->addMinutes(10), function () use ($schoolId) {
            return SchoolModule::query()
                ->where('school_id', $schoolId)
                ->whereIn('status', [Module::STATUS_ACTIVE, Module::STATUS_TRIAL])
                ->where(function ($q) {
                    $q->whereNull('ends_at')->orWhere('ends_at', '>', now());
                })
                ->where(function ($q) {
                    $q->whereNull('starts_at')->orWhere('starts_at', '<=', now());
                })
                ->with('module:id,slug')
                ->get()
                ->pluck('module.slug')
                ->filter()
                ->values()
                ->all();
        }, $schoolId);
    }

    public static function forgetSchoolCache(int $schoolId): void
    {
        TenantCache::forget('modules:enabled', $schoolId);
    }

    public static function enableForSchool(
        int $schoolId,
        string $slug,
        string $status = Module::STATUS_ACTIVE,
        ?\DateTimeInterface $startsAt = null,
        ?\DateTimeInterface $endsAt = null
    ): SchoolModule {
        $module = static::findBySlugOrFail($slug);

        $row = SchoolModule::query()->updateOrCreate(
            [
                'school_id' => $schoolId,
                'module_id' => $module->id,
            ],
            [
                'status'    => $status,
                'starts_at' => $startsAt ?? now(),
                'ends_at'   => $endsAt,
            ]
        );

        static::forgetSchoolCache($schoolId);

        return $row;
    }

    public static function disableForSchool(int $schoolId, string $slug): void
    {
        $module = static::findBySlugOrFail($slug);

        SchoolModule::query()
            ->where('school_id', $schoolId)
            ->where('module_id', $module->id)
            ->update([
                'status'  => Module::STATUS_DISABLED,
                'ends_at' => now(),
            ]);

        static::forgetSchoolCache($schoolId);
    }

    /** @return Collection<int, Module> */
    public static function allModules(): Collection
    {
        return Module::query()->orderBy('name')->get();
    }
}
