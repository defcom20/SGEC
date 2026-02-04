<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait HasUuid
{
    /**
     * Boot the trait and automatically generate UUID on model creation
     */
    protected static function bootHasUuid(): void
    {
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    /**
     * Get the route key name for Laravel route model binding
     * This makes routes use UUID instead of ID
     *
     * @return string
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    /**
     * Resolve route binding using UUID
     *
     * @param mixed $value
     * @param string|null $field
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function resolveRouteBinding($value, $field = null)
    {
        return $this->where($field ?? 'uuid', $value)->firstOrFail();
    }

    /**
     * Scope to find by UUID
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $uuid
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByUuid($query, string $uuid)
    {
        return $query->where('uuid', $uuid);
    }
}
