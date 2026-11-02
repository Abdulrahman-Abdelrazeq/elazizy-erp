<?php

use App\Models\Branch;
use App\Models\Tenant;
use Illuminate\Support\Facades\Auth;

if (!function_exists('currentTenant')) {
    /**
     * Get currently active tenant
     */
    function currentTenant(): ?Tenant
    {
        if (!Auth::check()) {
            return null;
        }

        return Auth::user()->tenant;
    }
}

if (!function_exists('activeBranch')) {
    /**
     * Get currently active branch
     */
    function activeBranch(): ?Branch
    {
        if (!Auth::check() || !Auth::user()->tenant_id) {
            return null;
        }

        $branchId = session('active_branch_id', Auth::user()->branch_id);
        if ($branchId) {
            $branch = Branch::where('tenant_id', Auth::user()->tenant_id)->where('id', $branchId)->first();
            if ($branch) {
                return $branch;
            }
        }

        return Branch::where('tenant_id', Auth::user()->tenant_id)->where('is_main', true)->first();
    }
}

if (!function_exists('currentTheme')) {
    /**
     * Get active theme settings with Emerald defaults
     */
    function currentTheme(): array
    {
        $sessionTheme = session('theme_settings');
        if ($sessionTheme && is_array($sessionTheme)) {
            return $sessionTheme;
        }

        $tenant = currentTenant();
        if ($tenant && !empty($tenant->theme_settings)) {
            return $tenant->getThemeSettings();
        }

        return [
            'palette' => 'emerald',
            'name' => 'الزمردي الملكي الفاخر (افتراضي)',
            'primary_color' => '#0F5132',       // أخضر زمردي راقي
            'primary_hover' => '#0B3B24',       // زمردي داكن
            'primary_light' => '#D1E7DD',       // نعناعي ناعم
            'accent_gold'   => '#D97706',       // ذهبي عنبري دافئ
            'sidebar_bg'    => '#062E20',       // غابة ليلية زمردية فاخرة
            'sidebar_text'  => '#E2E8F0',
            'body_bg'       => '#F4F8F6',       // خلفية ناصعة ناعمة
        ];
    }
}

if (!function_exists('formatMoney')) {
    /**
     * Format currency amount with tenant symbol
     */
    function formatMoney($amount, $currency = null): string
    {
        $curr = $currency ?: (currentTenant() ? currentTenant()->currency_code : 'SAR');
        return number_format((float)$amount, 2, '.', ',') . ' ' . $curr;
    }
}

if (!function_exists('getSetting')) {
    /**
     * Get company/system setting value
     */
    function getSetting(string $key, mixed $default = null): mixed
    {
        if (class_exists(\Modules\Setting\Entities\CompanySetting::class)) {
            return \Modules\Setting\Entities\CompanySetting::get($key, $default);
        }
        return $default;
    }
}

