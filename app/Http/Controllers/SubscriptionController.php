<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Tenant;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubscriptionController extends Controller
{
    /**
     * Show subscription details and renewal options
     */
    public function index()
    {
        $user = Auth::user();
        $tenant = $user->tenant;

        if (!$tenant) {
            abort(403, 'غير مصرح لك بالوصول لإدارة الاشتراكات');
        }

        $plans = Plan::where('is_active', true)->get();
        $history = Subscription::where('tenant_id', $tenant->id)->with('plan')->latest()->get();

        return view('subscription.index', compact('user', 'tenant', 'plans', 'history'));
    }

    /**
     * Renew or upgrade subscription
     */
    public function renew(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:plans,id',
            'billing_cycle' => 'required|in:monthly,yearly',
            'payment_method' => 'required|string',
        ]);

        $user = Auth::user();
        $tenant = $user->tenant;
        $plan = Plan::findOrFail($request->plan_id);

        $price = $request->billing_cycle === 'yearly' ? $plan->price_yearly : $plan->price_monthly;
        $startsAt = Carbon::now();
        $endsAt = $request->billing_cycle === 'yearly' ? Carbon::now()->addYear() : Carbon::now()->addMonth();

        // If currently active, append to current end date
        if ($tenant->isSubscriptionActive() && $tenant->subscription_ends_at && $tenant->subscription_ends_at->isFuture()) {
            $startsAt = $tenant->subscription_ends_at;
            $endsAt = $request->billing_cycle === 'yearly' 
                ? (clone $startsAt)->addYear() 
                : (clone $startsAt)->addMonth();
        }

        // Create subscription log
        $subscription = Subscription::create([
            'tenant_id' => $tenant->id,
            'plan_id' => $plan->id,
            'billing_cycle' => $request->billing_cycle,
            'price' => $price,
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
            'status' => 'active',
            'payment_method' => $request->payment_method,
            'payment_reference' => 'REN-' . strtoupper(uniqid()),
            'notes' => 'تجديد اشتراك عبر لوحة التحكم',
        ]);

        // Update tenant subscription status
        $tenant->update([
            'plan_id' => $plan->id,
            'subscription_status' => 'active',
            'subscription_starts_at' => $tenant->subscription_starts_at ?? $startsAt,
            'subscription_ends_at' => $endsAt,
        ]);

        return redirect()->route('subscription.index')->with('success', 'تم تجديد الاشتراك بنجاح حتى تاريخ ' . $endsAt->format('Y-m-d'));
    }
}

