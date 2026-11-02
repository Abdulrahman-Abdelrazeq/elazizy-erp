<?php

namespace Modules\Setting\Entities;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanySetting extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'key',
        'value',
        'group',
    ];

    /**
     * Get a setting by key for the active tenant
     */
    public static function get(string $key, mixed $default = null, ?int $tenantId = null): mixed
    {
        $tenantId = $tenantId ?: (auth()->check() ? auth()->user()->tenant_id : session('tenant_id'));
        if (!$tenantId) {
            $firstTenant = \App\Models\Tenant::first();
            $tenantId = $firstTenant ? $firstTenant->id : null;
        }

        if (!$tenantId) {
            return $default;
        }

        $record = static::withoutGlobalScope('tenant')->where('tenant_id', $tenantId)->where('key', $key)->first();
        return $record ? $record->value : $default;
    }

    /**
     * Set/update a setting for the active tenant
     */
    public static function set(string $key, mixed $value, string $group = 'general', ?int $tenantId = null): self
    {
        $tenantId = $tenantId ?: (auth()->check() ? auth()->user()->tenant_id : session('tenant_id'));
        if (!$tenantId) {
            $firstTenant = \App\Models\Tenant::first();
            $tenantId = $firstTenant ? $firstTenant->id : null;
        }

        return static::withoutGlobalScope('tenant')->updateOrCreate(
            ['tenant_id' => $tenantId, 'key' => $key],
            ['value' => $value, 'group' => $group]
        );
    }
}
