<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tenant extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_name',
        'slug',
        'domain',
        'logo',
        'commercial_register',
        'tax_number',
        'phone',
        'email',
        'address',
        'currency_code',
        'plan_id',
        'subscription_status',
        'trial_ends_at',
        'subscription_starts_at',
        'subscription_ends_at',
        'theme_settings',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'theme_settings' => 'array',
            'trial_ends_at' => 'datetime',
            'subscription_starts_at' => 'datetime',
            'subscription_ends_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function branches(): HasMany
    {
        return $this->hasMany(Branch::class);
    }

    public function mainBranch()
    {
        return $this->hasOne(Branch::class)->where('is_main', true);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class)->latest();
    }

    /**
     * Check if the tenant's subscription or trial is currently active.
     */
    public function isSubscriptionActive(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->subscription_status === 'trial') {
            return $this->trial_ends_at ? $this->trial_ends_at->isFuture() : true;
        }

        if ($this->subscription_status === 'active') {
            return $this->subscription_ends_at ? $this->subscription_ends_at->isFuture() : true;
        }

        return false;
    }

    /**
     * Get remaining days in trial or subscription.
     */
    public function remainingDays(): int
    {
        $targetDate = $this->subscription_status === 'trial' ? $this->trial_ends_at : $this->subscription_ends_at;
        if (!$targetDate) {
            return 0;
        }

        return max(0, (int) ceil(now()->diffInDays($targetDate, false)));
    }

    /**
     * Theme configuration with emerald green defaults
     */
    public function getThemeSettings(): array
    {
        $defaults = [
            'palette' => 'emerald',
            'primary_color' => '#0F5132',       // Deep Royal Emerald Green
            'primary_hover' => '#0B3B24',       // Darker Emerald
            'primary_light' => '#D1E7DD',       // Soft Mint
            'accent_gold'   => '#D97706',       // Warm Brass / Amber Gold
            'sidebar_bg'    => '#062E20',       // Dark Slate Forest
            'sidebar_text'  => '#E2E8F0',
            'body_bg'       => '#F4F8F6',       // Crisp Mint / Slate Light
        ];

        return array_merge($defaults, $this->theme_settings ?? []);
    }
}

