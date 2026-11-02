<?php

namespace Modules\Setting\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Setting\Entities\PrintTemplate;

class PrintTemplateController extends Controller
{
    /**
     * Show print templates
     */
    public function index()
    {
        $templates = PrintTemplate::all();

        // If no template exists yet for this tenant, create default thermal and A4
        if ($templates->isEmpty()) {
            PrintTemplate::create([
                'title'                => 'فاتورة كاشير حرارية (80 مم)',
                'invoice_type'         => 'sales',
                'paper_size'           => 'thermal_80mm',
                'show_logo'            => true,
                'show_qr'              => true,
                'show_tax_number'      => true,
                'show_terms'           => true,
                'terms_and_conditions' => 'البضاعة المباعة ترد وتستبدل خلال 14 يوماً بموجب الفاتورة الأصلية وبحالتها.',
                'footer_text'          => 'شكراً لتسوقكم من شركة العزيزي - نسعد بخدمتكم دائماً',
                'is_default'           => true,
            ]);

            PrintTemplate::create([
                'title'                => 'فاتورة ضريبية رسمية (A4)',
                'invoice_type'         => 'sales',
                'paper_size'           => 'A4',
                'show_logo'            => true,
                'show_qr'              => true,
                'show_tax_number'      => true,
                'show_terms'           => true,
                'terms_and_conditions' => 'تعتبر هذه الفاتورة سند استلام نظامي للبضاعة الموضحة أعلاه.',
                'footer_text'          => 'شركة العزيزي للتجارة والتوريدات - جميع الحقوق محفوظة',
                'is_default'           => false,
            ]);

            $templates = PrintTemplate::all();
        }

        return view('setting::prints.index', compact('templates'));
    }

    /**
     * Update print template
     */
    public function update(Request $request, PrintTemplate $template)
    {
        $validated = $request->validate([
            'title'                => 'required|string|max:150',
            'paper_size'           => 'required|in:A4,A5,thermal_80mm,thermal_57mm',
            'terms_and_conditions' => 'nullable|string',
            'footer_text'          => 'nullable|string|max:255',
        ]);

        $template->update([
            'title'                => $validated['title'],
            'paper_size'           => $validated['paper_size'],
            'show_logo'            => $request->boolean('show_logo'),
            'show_qr'              => $request->boolean('show_qr'),
            'show_tax_number'      => $request->boolean('show_tax_number'),
            'show_terms'           => $request->boolean('show_terms'),
            'terms_and_conditions' => $validated['terms_and_conditions'],
            'footer_text'          => $validated['footer_text'],
            'is_default'           => $request->boolean('is_default'),
        ]);

        return back()->with('success', 'تم تحديث قالب الطباعة بنجاح!');
    }
}

