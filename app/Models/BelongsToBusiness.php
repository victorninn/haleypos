<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

/**
 * Apply automatic tenant (business_id) scoping on any model that uses it.
 * The current tenant is taken from the authenticated user OR from a
 * runtime override set by middleware.
 */
trait BelongsToBusiness
{
    public static function bootBelongsToBusiness(): void
    {
        static::addGlobalScope('business', function (Builder $builder) {
            $businessId = static::resolveCurrentBusinessId();
            if ($businessId !== null) {
                $builder->where($builder->getModel()->getTable().'.business_id', $businessId);
            }
        });

        static::creating(function (Model $model) {
            if (empty($model->business_id)) {
                $businessId = static::resolveCurrentBusinessId();
                if ($businessId !== null) {
                    $model->business_id = $businessId;
                }
            }
        });
    }

    public function business(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public static function resolveCurrentBusinessId(): ?int
    {
        if (app()->bound('tenant.business_id')) {
            return (int) app('tenant.business_id');
        }
        if (Auth::check() && Auth::user()->business_id) {
            return (int) Auth::user()->business_id;
        }
        return null;
    }

    public function scopeWithoutBusinessScope(Builder $query): Builder
    {
        return $query->withoutGlobalScope('business');
    }
}
