<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    protected $fillable = [
        'name',
        'description',
        'created_by',
        'guard_name',
    ];

    protected $casts = [
        'created_by' => 'integer',
    ];

    /**
     * Force a guard if none is supplied and
     * completely bypass Spatie's global uniqueness check.
     * We enforce per-tenant uniqueness ourselves (and at DB level).
     */
    public static function create(array $attributes = [])
    {
        // default guard
        $attributes['guard_name'] = $attributes['guard_name']
            ?? config('auth.defaults.guard', 'web');

        // optional: app-level per-tenant duplicate guard (nice error message)
        $exists = static::query()
            ->where('name', $attributes['name'] ?? null)
            ->where('guard_name', $attributes['guard_name'])
            ->when(isset($attributes['created_by']), fn(Builder $q) => $q->where('created_by', $attributes['created_by'])
            )
            ->exists();

        if ($exists) {
            throw new \InvalidArgumentException(
                "Role `{$attributes['name']}` already exists for this organization."
            );
        }

        // go straight to Eloquent create (skip Spatie's ensureUniqueRole)
        return static::query()->create($attributes);
    }

    /** Handy scope if you often need to query by org */
    public function scopeForCreator(Builder $query, int $creatorId): Builder
    {
        return $query->where('created_by', $creatorId);
    }
}
