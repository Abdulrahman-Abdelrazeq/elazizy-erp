<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Main ERP Dashboard
     */
    public function index()
    {
        $user = Auth::user();
        $tenant = $user->tenant;

        $branches = collect();
        $activeBranch = null;

        if ($tenant) {
            $branches = Branch::where('tenant_id', $tenant->id)->where('is_active', true)->get();
            $activeBranchId = session('active_branch_id', $user->branch_id);
            $activeBranch = $branches->firstWhere('id', $activeBranchId) ?? $branches->firstWhere('is_main', true) ?? $branches->first();
        }

        // Stats summary
        $stats = [
            'branches_count' => $branches->count(),
            'users_count' => $tenant ? User::where('tenant_id', $tenant->id)->count() : User::count(),
            'subscription_days' => $tenant ? $tenant->remainingDays() : 365,
            'subscription_status' => $tenant ? $tenant->subscription_status : 'active',
            'plan_name' => $tenant && $tenant->plan ? $tenant->plan->name : 'باقة شاملة',
        ];

        return view('dashboard.index', compact('user', 'tenant', 'branches', 'activeBranch', 'stats'));
    }

    /**
     * Update Theme and Color Palette
     */
    public function updateTheme(Request $request)
    {
        $validated = $request->validate([
            'palette' => 'required|string|in:emerald,navy,burgundy,teal,custom',
            'primary_color' => 'nullable|string|max:20',
            'accent_gold' => 'nullable|string|max:20',
        ]);

        $palettes = [
            'emerald' => [
                'palette' => 'emerald',
                'name' => 'الزمردي الملكي الفاخر (افتراضي)',
                'primary_color' => '#0F5132',       // أخضر زمردي راقي
                'primary_hover' => '#0B3B24',
                'primary_light' => '#D1E7DD',
                'accent_gold'   => '#D97706',       // ذهبي عنبري
                'sidebar_bg'    => '#062E20',       // غابة ليلية زمردية
                'sidebar_text'  => '#E2E8F0',
                'body_bg'       => '#F4F8F6',
            ],
            'navy' => [
                'palette' => 'navy',
                'name' => 'الكحلي الملوكي والأزرق النيلي',
                'primary_color' => '#1E3A8A',       // أزرق كحلي ملكي
                'primary_hover' => '#172554',
                'primary_light' => '#DBEAFE',
                'accent_gold'   => '#F59E0B',
                'sidebar_bg'    => '#0F172A',       // كحلي فحمي داكن
                'sidebar_text'  => '#E2E8F0',
                'body_bg'       => '#F8FAFC',
            ],
            'burgundy' => [
                'palette' => 'burgundy',
                'name' => 'العنابي الراقي والبرونزي',
                'primary_color' => '#831843',       // عنابي ملكي فاخر
                'primary_hover' => '#500724',
                'primary_light' => '#FCE7F3',
                'accent_gold'   => '#D97706',
                'sidebar_bg'    => '#2A0818',       // عنابي داكن
                'sidebar_text'  => '#F5D0FE',
                'body_bg'       => '#FAF5F8',
            ],
            'teal' => [
                'palette' => 'teal',
                'name' => 'التيل المحيطي العصري',
                'primary_color' => '#0F766E',       // تيل محيطي
                'primary_hover' => '#115E59',
                'primary_light' => '#CCFBF1',
                'accent_gold'   => '#F59E0B',
                'sidebar_bg'    => '#042F2E',       // تيل داكن
                'sidebar_text'  => '#E2E8F0',
                'body_bg'       => '#F0FDFA',
            ],
        ];

        $paletteKey = $validated['palette'];
        $themeSettings = $palettes[$paletteKey] ?? $palettes['emerald'];

        if ($paletteKey === 'custom' && !empty($validated['primary_color'])) {
            $themeSettings = [
                'palette' => 'custom',
                'name' => 'مخصص',
                'primary_color' => $validated['primary_color'],
                'primary_hover' => $validated['primary_color'],
                'primary_light' => '#E2E8F0',
                'accent_gold'   => $validated['accent_gold'] ?? '#D97706',
                'sidebar_bg'    => '#1E293B',
                'sidebar_text'  => '#F8FAFC',
                'body_bg'       => '#F8FAFC',
            ];
        }

        // Store in tenant settings if tenant user
        $user = Auth::user();
        if ($user && $user->tenant) {
            $user->tenant->update([
                'theme_settings' => $themeSettings,
            ]);
        }

        // Also store in session for instantaneous preview
        session(['theme_settings' => $themeSettings]);

        return back()->with('success', 'تم تحديث ألوان وهوية النظام بنجاح!');
    }
}

