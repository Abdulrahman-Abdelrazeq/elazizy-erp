<?php

namespace App\Traits;

use App\Models\Tenant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToTenant
{
    /**
     * Boot the trait to attach global scope and event listeners.
     */
    protected static function bootBelongsToTenant(): void
    {
        static::creating(function ($model) {
            if (!$model->tenant_id) {
                if (auth()->check() && auth()->user()->tenant_id) {
                    $model->tenant_id = auth()->user()->tenant_id;
                } elseif (session()->has('tenant_id')) {
                    $model->tenant_id = session('tenant_id');
                } else {
                    $defaultTenant = Tenant::first();
                    $model->tenant_id = $defaultTenant ? $defaultTenant->id : 1;
                }
            }
        });

        static::addGlobalScope('tenant', function (Builder $builder) {
            if (auth()->check() && auth()->user()->tenant_id && !auth()->user()->isSuperAdmin()) {
                $builder->where($builder->getModel()->getTable() . '.tenant_id', auth()->user()->tenant_id);
            }
        });
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}

