<?php

namespace Modules\Setting\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Setting\Entities\CompanySetting;
use Modules\Setting\Entities\Currency;
use Modules\Setting\Entities\Tax;

class SettingController extends Controller
{
    /**
     * Show general settings hub
     */
    public function index()
    {
        $tenant = Auth::user()->tenant ?: (session('tenant_id') ? Tenant::find(session('tenant_id')) : Tenant::first());
        $taxes = Tax::all();
        $currencies = Currency::all();

        // Retrieve saved operational settings or defaults
        $settings = [
            // Invoicing & Sales
            'invoice_auto_print'       => CompanySetting::get('invoice_auto_print', '1'),
            'invoice_default_type'     => CompanySetting::get('invoice_default_type', 'cash'),
            'invoice_show_tax_details' => CompanySetting::get('invoice_show_tax_details', '1'),
            'invoice_show_qr_code'     => CompanySetting::get('invoice_show_qr_code', '1'),
            'allow_negative_stock'     => CompanySetting::get('allow_negative_stock', '0'),
            'update_price_on_purchase' => CompanySetting::get('update_price_on_purchase', '1'),
            // POS
            'pos_enable_shifts'        => CompanySetting::get('pos_enable_shifts', '1'),
            'pos_auto_open_drawer'     => CompanySetting::get('pos_auto_open_drawer', '1'),
            'pos_barcode_sound'        => CompanySetting::get('pos_barcode_sound', '1'),
            // Company details
            'commercial_register'      => $tenant->commercial_register ?? '',
            'tax_number'               => $tenant->tax_number ?? '',
            'phone'                    => $tenant->phone ?? '',
            'email'                    => $tenant->email ?? '',
            'address'                  => $tenant->address ?? '',
            'default_tax_rate'         => CompanySetting::get('default_tax_rate', '15'),
        ];

        return view('setting::index', compact('tenant', 'taxes', 'currencies', 'settings'));
    }

    /**
     * Update Company Profile & Settings
     */
    public function update(Request $request)
    {
        $tenant = Auth::user()->tenant ?: (session('tenant_id') ? Tenant::find(session('tenant_id')) : Tenant::first());

        $request->validate([
            'company_name'        => 'required|string|max:200',
            'commercial_register' => 'nullable|string|max:100',
            'tax_number'          => 'nullable|string|max:100',
            'phone'               => 'nullable|string|max:50',
            'email'               => 'nullable|email|max:150',
            'address'             => 'nullable|string|max:255',
            'currency_code'       => 'nullable|string|max:10',
        ]);

        if ($tenant) {
            $tenant->update([
                'company_name'        => $request->company_name,
                'commercial_register' => $request->commercial_register,
                'tax_number'          => $request->tax_number,
                'phone'               => $request->phone,
                'email'               => $request->email,
                'address'             => $request->address,
                'currency_code'       => $request->currency_code ?? $tenant->currency_code,
            ]);
        }

        // Save boolean/operational flags
        $keys = [
            'invoice_auto_print',
            'invoice_default_type',
            'invoice_show_tax_details',
            'invoice_show_qr_code',
            'allow_negative_stock',
            'update_price_on_purchase',
            'pos_enable_shifts',
            'pos_auto_open_drawer',
            'pos_barcode_sound',
            'default_tax_rate',
        ];

        foreach ($keys as $k) {
            if ($request->has($k)) {
                CompanySetting::set($k, $request->input($k), 'operational');
            } else {
                // If it's a checkbox and absent, set to 0
                if (in_array($k, ['invoice_auto_print', 'invoice_show_tax_details', 'invoice_show_qr_code', 'allow_negative_stock', 'update_price_on_purchase', 'pos_enable_shifts', 'pos_auto_open_drawer', 'pos_barcode_sound'])) {
                    CompanySetting::set($k, '0', 'operational');
                }
            }
        }

        return back()->with('success', 'تم حفظ وتحديث إعدادات وبيانات الشركة بنجاح!');
    }
}

