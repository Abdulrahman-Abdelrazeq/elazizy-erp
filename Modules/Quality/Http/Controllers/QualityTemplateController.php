<?php

namespace Modules\Quality\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Inventory\Entities\Category;
use Modules\Inventory\Entities\Item;
use Modules\Quality\Entities\QualityTemplate;
use Modules\Quality\Entities\QualityTemplateItem;

class QualityTemplateController extends Controller
{
    /**
     * List Quality Inspection Templates
     */
    public function index(Request $request)
    {
        $query = QualityTemplate::with(['item', 'category', 'items'])->latest();

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('code', 'like', "%{$s}%")
                  ->orWhere('description', 'like', "%{$s}%");
            });
        }

        $templates = $query->paginate(15)->withQueryString();

        $stats = [
            'total'         => QualityTemplate::count(),
            'incoming'      => QualityTemplate::where('type', 'incoming')->count(),
            'in_process'    => QualityTemplate::where('type', 'in_process')->count(),
            'final_product' => QualityTemplate::where('type', 'final_product')->count(),
            'project_site'  => QualityTemplate::where('type', 'project_site')->count(),
        ];

        return view('quality::templates.index', compact('templates', 'stats'));
    }

    /**
     * Show form to create new Template
     */
    public function create()
    {
        $suggestedCode = QualityTemplate::generateCode();
        $items = Item::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();

        return view('quality::templates.create', compact('suggestedCode', 'items', 'categories'));
    }

    /**
     * Store new Template
     */
    public function store(Request $request)
    {
        $tenantId = currentTenant() ? currentTenant()->id : 1;

        $validated = $request->validate([
            'code'        => 'nullable|string|max:50',
            'name'        => 'required|string|max:190',
            'type'        => 'required|in:incoming,in_process,final_product,project_site',
            'item_id'     => 'nullable|exists:items,id',
            'category_id' => 'nullable|exists:categories,id',
            'is_active'   => 'boolean',
            'description' => 'nullable|string',
        ]);

        $code = !empty($validated['code'])
            ? $validated['code']
            : QualityTemplate::generateCode($tenantId);

        $template = QualityTemplate::create([
            'tenant_id'   => $tenantId,
            'code'        => $code,
            'name'        => $validated['name'],
            'type'        => $validated['type'],
            'item_id'     => $validated['item_id'] ?? null,
            'category_id' => $validated['category_id'] ?? null,
            'is_active'   => $request->boolean('is_active', true),
            'description' => $validated['description'] ?? null,
            'created_by'  => Auth::id(),
        ]);

        return redirect()->route('quality.templates.show', $template->id)
            ->with('success', "تم إنشاء نموذج الفحص وقائمة التحقق بنجاح برقم كود: {$template->code}. يمكنك الآن إضافة بنود ومعايير الفحص.");
    }

    /**
     * Show Template with Checkpoints
     */
    public function show(QualityTemplate $template)
    {
        $template->load(['item', 'category', 'items', 'inspections.inspector', 'creator']);
        return view('quality::templates.show', compact('template'));
    }

    /**
     * Show form to edit Template
     */
    public function edit(QualityTemplate $template)
    {
        $items = Item::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();

        return view('quality::templates.edit', compact('template', 'items', 'categories'));
    }

    /**
     * Update Template
     */
    public function update(Request $request, QualityTemplate $template)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:190',
            'type'        => 'required|in:incoming,in_process,final_product,project_site',
            'item_id'     => 'nullable|exists:items,id',
            'category_id' => 'nullable|exists:categories,id',
            'is_active'   => 'boolean',
            'description' => 'nullable|string',
        ]);

        $template->update([
            'name'        => $validated['name'],
            'type'        => $validated['type'],
            'item_id'     => $validated['item_id'] ?? null,
            'category_id' => $validated['category_id'] ?? null,
            'is_active'   => $request->boolean('is_active', true),
            'description' => $validated['description'] ?? null,
        ]);

        return redirect()->route('quality.templates.show', $template->id)
            ->with('success', 'تم تحديث بيانات نموذج الفحص بنجاح.');
    }

    /**
     * Delete Template
     */
    public function destroy(QualityTemplate $template)
    {
        if ($template->inspections()->exists()) {
            return back()->with('error', 'لا يمكن حذف نموذج فحص مسجل عليه عمليات فحص وتقارير جودة سابقة.');
        }

        $template->delete();
        return redirect()->route('quality.templates.index')->with('success', 'تم حذف نموذج الفحص بنجاح.');
    }

    /**
     * Store new Checkpoint Item in Template
     */
    public function storeItem(Request $request, QualityTemplate $template)
    {
        $tenantId = currentTenant() ? currentTenant()->id : 1;

        $validated = $request->validate([
            'checkpoint_name'   => 'required|string|max:190',
            'standard_type'     => 'required|in:boolean,numeric_range,text',
            'expected_boolean'  => 'nullable|boolean',
            'min_value'         => 'nullable|numeric',
            'max_value'         => 'nullable|numeric',
            'target_value'      => 'nullable|numeric',
            'unit_of_measure'   => 'nullable|string|max:50',
            'inspection_method' => 'nullable|string|max:190',
            'is_mandatory'      => 'boolean',
        ]);

        $sortOrder = $template->items()->count() + 1;

        $template->items()->create([
            'tenant_id'         => $tenantId,
            'checkpoint_name'   => $validated['checkpoint_name'],
            'standard_type'     => $validated['standard_type'],
            'expected_boolean'  => $request->boolean('expected_boolean', true),
            'min_value'         => $validated['min_value'] ?? null,
            'max_value'         => $validated['max_value'] ?? null,
            'target_value'      => $validated['target_value'] ?? null,
            'unit_of_measure'   => $validated['unit_of_measure'] ?? null,
            'inspection_method' => $validated['inspection_method'] ?? null,
            'is_mandatory'      => $request->boolean('is_mandatory', true),
            'sort_order'        => $sortOrder,
        ]);

        return redirect()->route('quality.templates.show', $template->id)
            ->with('success', 'تمت إضافة معيار الفحص ونقطة التحقق بنجاح.');
    }

    /**
     * Delete Checkpoint Item
     */
    public function destroyItem(QualityTemplateItem $item)
    {
        $templateId = $item->template_id;
        $item->delete();

        return redirect()->route('quality.templates.show', $templateId)
            ->with('success', 'تم حذف معيار الفحص بنجاح.');
    }
}
