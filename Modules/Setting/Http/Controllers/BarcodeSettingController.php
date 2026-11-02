<?php

namespace Modules\Setting\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Setting\Entities\BarcodeSetting;

class BarcodeSettingController extends Controller
{
    /**
     * Show barcode settings
     */
    public function index()
    {
        $setting = BarcodeSetting::first();

        if (!$setting) {
            $setting = BarcodeSetting::create([
                'name'                => 'ملصق الباركود الافتراضي (38x25 مم)',
                'barcode_type'        => 'code128',
                'label_width'         => 38,
                'label_height'        => 25,
                'show_product_name'   => true,
                'show_price'          => true,
                'show_company_name'   => true,
                'show_barcode_number' => true,
                'is_default'          => true,
            ]);
        }

        return view('setting::barcode.index', compact('setting'));
    }

    /**
     * Update barcode settings
     */
    public function update(Request $request)
    {
        $setting = BarcodeSetting::first();

        $validated = $request->validate([
            'name'         => 'required|string|max:100',
            'barcode_type' => 'required|in:code128,ean13,qr',
            'label_width'  => 'required|numeric|min:20|max:100',
            'label_height' => 'required|numeric|min:15|max:100',
        ]);

        $setting->update([
            'name'                => $validated['name'],
            'barcode_type'        => $validated['barcode_type'],
            'label_width'         => $validated['label_width'],
            'label_height'        => $validated['label_height'],
            'show_product_name'   => $request->boolean('show_product_name'),
            'show_price'          => $request->boolean('show_price'),
            'show_company_name'   => $request->boolean('show_company_name'),
            'show_barcode_number' => $request->boolean('show_barcode_number'),
        ]);

        return back()->with('success', 'تم حفظ إعدادات وأبعاد ملصق الباركود بنجاح!');
    }
}

