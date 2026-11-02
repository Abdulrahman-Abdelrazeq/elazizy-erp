<?php

namespace Modules\Setting\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Setting\Entities\Tax;

class TaxController extends Controller
{
    /**
     * List taxes
     */
    public function index()
    {
        $taxes = Tax::latest()->get();
        return view('setting::taxes.index', compact('taxes'));
    }

    /**
     * Store new tax
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:100',
            'rate'        => 'required|numeric|min:0|max:100',
            'code'        => 'nullable|string|max:20',
            'type'        => 'required|in:percentage,fixed',
            'applies_to'  => 'required|in:all,sales,purchases',
            'is_default'  => 'nullable|boolean',
        ]);

        if (!empty($validated['is_default'])) {
            Tax::where('is_default', true)->update(['is_default' => false]);
        }

        Tax::create([
            'name'        => $validated['name'],
            'rate'        => $validated['rate'],
            'code'        => $validated['code'] ?? null,
            'type'        => $validated['type'],
            'applies_to'  => $validated['applies_to'],
            'is_default'  => $request->boolean('is_default'),
            'is_active'   => true,
        ]);

        return back()->with('success', 'تمت إضافة الضريبة بنجاح!');
    }

    /**
     * Update tax
     */
    public function update(Request $request, Tax $tax)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:100',
            'rate'        => 'required|numeric|min:0|max:100',
            'code'        => 'nullable|string|max:20',
            'type'        => 'required|in:percentage,fixed',
            'applies_to'  => 'required|in:all,sales,purchases',
            'is_default'  => 'nullable|boolean',
            'is_active'   => 'nullable|boolean',
        ]);

        if (!empty($validated['is_default'])) {
            Tax::where('id', '!=', $tax->id)->where('is_default', true)->update(['is_default' => false]);
        }

        $tax->update([
            'name'        => $validated['name'],
            'rate'        => $validated['rate'],
            'code'        => $validated['code'] ?? null,
            'type'        => $validated['type'],
            'applies_to'  => $validated['applies_to'],
            'is_default'  => $request->boolean('is_default'),
            'is_active'   => $request->boolean('is_active', true),
        ]);

        return back()->with('success', 'تم تعديل بيانات الضريبة بنجاح!');
    }

    /**
     * Delete tax
     */
    public function destroy(Tax $tax)
    {
        $tax->delete();
        return back()->with('success', 'تم حذف الضريبة بنجاح.');
    }
}

