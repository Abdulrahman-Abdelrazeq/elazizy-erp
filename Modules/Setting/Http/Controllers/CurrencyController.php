<?php

namespace Modules\Setting\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Setting\Entities\Currency;

class CurrencyController extends Controller
{
    /**
     * List currencies
     */
    public function index()
    {
        $currencies = Currency::latest()->get();
        return view('setting::currencies.index', compact('currencies'));
    }

    /**
     * Store new currency
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:100',
            'code'          => 'required|string|max:10',
            'symbol'        => 'required|string|max:15',
            'exchange_rate' => 'required|numeric|min:0.0001',
            'subunit'       => 'nullable|string|max:50',
            'is_default'    => 'nullable|boolean',
        ]);

        if (!empty($validated['is_default'])) {
            Currency::where('is_default', true)->update(['is_default' => false]);
        }

        Currency::create([
            'name'          => $validated['name'],
            'code'          => strtoupper($validated['code']),
            'symbol'        => $validated['symbol'],
            'exchange_rate' => $validated['exchange_rate'],
            'subunit'       => $validated['subunit'] ?? null,
            'is_default'    => $request->boolean('is_default'),
            'is_active'     => true,
        ]);

        return back()->with('success', 'تمت إضافة العملة بنجاح!');
    }

    /**
     * Update currency
     */
    public function update(Request $request, Currency $currency)
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:100',
            'code'          => 'required|string|max:10',
            'symbol'        => 'required|string|max:15',
            'exchange_rate' => 'required|numeric|min:0.0001',
            'subunit'       => 'nullable|string|max:50',
            'is_default'    => 'nullable|boolean',
            'is_active'     => 'nullable|boolean',
        ]);

        if (!empty($validated['is_default'])) {
            Currency::where('id', '!=', $currency->id)->where('is_default', true)->update(['is_default' => false]);
        }

        $currency->update([
            'name'          => $validated['name'],
            'code'          => strtoupper($validated['code']),
            'symbol'        => $validated['symbol'],
            'exchange_rate' => $validated['exchange_rate'],
            'subunit'       => $validated['subunit'] ?? null,
            'is_default'    => $request->boolean('is_default'),
            'is_active'     => $request->boolean('is_active', true),
        ]);

        return back()->with('success', 'تم تعديل العملة بنجاح!');
    }

    /**
     * Delete currency
     */
    public function destroy(Currency $currency)
    {
        if ($currency->is_default) {
            return back()->with('error', 'لا يمكن حذف العملة الافتراضية للنظام!');
        }

        $currency->delete();
        return back()->with('success', 'تم حذف العملة بنجاح.');
    }
}

