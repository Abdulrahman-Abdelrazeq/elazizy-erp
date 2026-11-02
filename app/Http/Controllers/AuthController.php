<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Show the login form
     */
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    /**
     * Handle login request
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ], [
            'email.required' => 'البريد الإلكتروني مطلوب',
            'email.email' => 'صيغة البريد الإلكتروني غير صحيحة',
            'password.required' => 'كلمة المرور مطلوبة',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();

            if (!$user->is_active) {
                Auth::logout();
                return back()->withErrors(['email' => 'هذا الحساب تم تعطيله، برجاء مراجعة الإدارة.']);
            }

            // Set active branch in session
            if ($user->branch_id) {
                session(['active_branch_id' => $user->branch_id]);
            } elseif ($user->tenant_id) {
                $mainBranch = Branch::where('tenant_id', $user->tenant_id)->where('is_main', true)->first();
                if ($mainBranch) {
                    session(['active_branch_id' => $mainBranch->id]);
                }
            }

            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'email' => 'بيانات الدخول غير صحيحة، يرجى التأكد من البريد الإلكتروني وكلمة المرور.',
        ])->onlyInput('email');
    }

    /**
     * Switch active branch
     */
    public function switchBranch(Request $request)
    {
        $branchId = $request->input('branch_id');
        $user = Auth::user();

        if ($user && $user->tenant_id) {
            $branch = Branch::where('tenant_id', $user->tenant_id)->where('id', $branchId)->first();
            if ($branch) {
                session(['active_branch_id' => $branch->id]);
                return back()->with('success', 'تم التحويل إلى فرع: ' . $branch->name);
            }
        }

        return back()->with('error', 'الفرع غير متاح');
    }

    /**
     * Handle logout
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('info', 'تم تسجيل الخروج بنجاح.');
    }
}

