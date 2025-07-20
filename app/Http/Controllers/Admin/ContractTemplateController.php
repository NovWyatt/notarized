<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContractTemplate;
use App\Models\ContractType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ContractTemplateController extends Controller
{
    /**
     * Display a listing of contract templates
     */
    public function index(Request $request): View
    {
        $query = ContractTemplate::with(['contractType'])
            ->withCount('contracts');

        // Filter by contract type
        if ($request->filled('contract_type_id')) {
            $query->where('contract_type_id', $request->contract_type_id);
        }

        // Filter by status
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        // Search by name
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $templates     = $query->ordered()->paginate(15);
        $contractTypes = ContractType::active()->get();

        return view('admin.contract-templates.index', compact('templates', 'contractTypes'));
    }

    /**
     * Show the form for creating a new contract template
     */
    public function create(): View
    {
        $contractTypes = ContractType::active()->get();
        return view('admin.contract-templates.create', compact('contractTypes'));
    }

    /**
     * Store a newly created contract template
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'              => [
                'required',
                'string',
                'max:255',
                Rule::unique('contract_templates')->where(function ($query) use ($request) {
                    return $query->where('contract_type_id', $request->contract_type_id);
                }),
            ],
            'contract_type_id'  => 'required|exists:contract_types,id',
            'content'           => 'required|string',
            'template_settings' => 'nullable|array',
            'template_info'     => 'nullable|array',
            'default_clauses'   => 'nullable|array',
            'is_active'         => 'boolean',
            'sort_order'        => 'integer|min:0',
        ]);

        // Encode arrays to JSON
        $validated['template_settings'] = json_encode($validated['template_settings'] ?? ContractTemplate::DEFAULT_SETTINGS);
        $validated['template_info']     = json_encode($validated['template_info'] ?? []);
        $validated['default_clauses']   = json_encode($validated['default_clauses'] ?? []);

        try {
            DB::beginTransaction();

            $template = ContractTemplate::create($validated);

            DB::commit();

            return redirect()
                ->route('admin.contract-templates.show', $template)
                ->with('success', 'Template hợp đồng đã được tạo thành công.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra khi tạo template: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified contract template
     */
    public function show(ContractTemplate $contractTemplate): View
    {
        $contractTemplate->load(['contractType', 'contracts' => function ($query) {
            $query->latest()->take(10);
        }]);

        return view('admin.contract-templates.show', compact('contractTemplate'));
    }

    /**
     * Show the form for editing the specified contract template
     */
    public function edit(ContractTemplate $contractTemplate): View
    {
        $contractTypes = ContractType::active()->get();
        return view('admin.contract-templates.edit', compact('contractTemplate', 'contractTypes'));
    }

    /**
     * Update the specified contract template
     */
    public function update(Request $request, ContractTemplate $contractTemplate): RedirectResponse
    {
        $validated = $request->validate([
            'name'              => [
                'required',
                'string',
                'max:255',
                Rule::unique('contract_templates')->where(function ($query) use ($request) {
                    return $query->where('contract_type_id', $request->contract_type_id);
                })->ignore($contractTemplate->id),
            ],
            'contract_type_id'  => 'required|exists:contract_types,id',
            'content'           => 'required|string',
            'template_settings' => 'nullable|array',
            'template_info'     => 'nullable|array',
            'default_clauses'   => 'nullable|array',
            'is_active'         => 'boolean',
            'sort_order'        => 'integer|min:0',
        ]);

        // Encode arrays to JSON
        $validated['template_settings'] = json_encode($validated['template_settings'] ?? []);
        $validated['template_info']     = json_encode($validated['template_info'] ?? []);
        $validated['default_clauses']   = json_encode($validated['default_clauses'] ?? []);

        try {
            DB::beginTransaction();

            $contractTemplate->update($validated);

            DB::commit();

            return redirect()
                ->route('admin.contract-templates.show', $contractTemplate)
                ->with('success', 'Template hợp đồng đã được cập nhật thành công.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra khi cập nhật template: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified contract template
     */
    public function destroy(ContractTemplate $contractTemplate): JsonResponse
    {
        if (! $contractTemplate->canBeDeleted()) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể xóa template này vì đã có hợp đồng sử dụng.',
            ], 422);
        }

        try {
            DB::beginTransaction();

            $contractTemplate->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Template đã được xóa thành công.',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xóa template: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Toggle active status of contract template
     */
    public function toggleStatus(ContractTemplate $contractTemplate): JsonResponse
    {
        try {
            $contractTemplate->update([
                'is_active' => ! $contractTemplate->is_active,
            ]);

            return response()->json([
                'success'   => true,
                'is_active' => $contractTemplate->is_active,
                'message'   => $contractTemplate->is_active
                ? 'Template đã được kích hoạt.'
                : 'Template đã được vô hiệu hóa.',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Duplicate a contract template
     */
    public function duplicate(ContractTemplate $contractTemplate): RedirectResponse
    {
        try {
            DB::beginTransaction();

            $newTemplate             = $contractTemplate->replicate();
            $newTemplate->name       = $contractTemplate->name . ' (Bản sao)';
            $newTemplate->is_active  = false;
            $newTemplate->sort_order = 0;
            $newTemplate->save();

            DB::commit();

            return redirect()
                ->route('admin.contract-templates.edit', $newTemplate)
                ->with('success', 'Template đã được sao chép thành công. Vui lòng kiểm tra và chỉnh sửa.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra khi sao chép template: ' . $e->getMessage());
        }
    }

    /**
     * Update sort order of templates
     */
    public function updateOrder(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'orders'              => 'required|array',
            'orders.*.id'         => 'required|exists:contract_templates,id',
            'orders.*.sort_order' => 'required|integer|min:0',
        ]);

        try {
            DB::beginTransaction();

            foreach ($validated['orders'] as $order) {
                ContractTemplate::where('id', $order['id'])
                    ->update(['sort_order' => $order['sort_order']]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Thứ tự template đã được cập nhật.',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Preview template content with sample data
     */
    public function preview(ContractTemplate $contractTemplate): View
    {
        // Sample data for preview
        $sampleData = [
            'current_date'      => now()->format('d/m/Y'),
            'contract_number'   => 'HĐ001/2025',
            'transaction_value' => '1,000,000,000 VNĐ',
            'notary_fee'        => '500,000 VNĐ',
            'notary_number'     => 'CC001/2025',
            'book_number'       => 'Sổ 01',
        ];

        $previewContent = $contractTemplate->generateContent($sampleData);

        return view('admin.contract-templates.preview', compact('contractTemplate', 'previewContent'));
    }

    /**
     * Get templates by contract type (for AJAX)
     */
    public function getByContractType(Request $request): JsonResponse
    {
        $contractTypeId = $request->get('contract_type_id');

        $templates = ContractTemplate::active()
            ->byType($contractTypeId)
            ->ordered()
            ->select('id', 'name', 'sort_order')
            ->get();

        return response()->json($templates);
    }

    /**
     * Export template content
     */
    public function export(ContractTemplate $contractTemplate, Request $request): \Symfony\Component\HttpFoundation\Response
    {
        $format = $request->get('format', 'html');

        $content  = $contractTemplate->content;
        $filename = str_replace(' ', '_', $contractTemplate->name);

        switch ($format) {
            case 'html':
                return response($content)
                    ->header('Content-Type', 'text/html')
                    ->header('Content-Disposition', "attachment; filename=\"{$filename}.html\"");

            case 'txt':
                $textContent = strip_tags($content);
                return response($textContent)
                    ->header('Content-Type', 'text/plain')
                    ->header('Content-Disposition', "attachment; filename=\"{$filename}.txt\"");

            default:
                return back()->with('error', 'Định dạng xuất không được hỗ trợ.');
        }
    }

    /**
     * Import template from file
     */
    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'template_file'    => 'required|file|mimes:html,txt|max:2048',
            'name'             => 'required|string|max:255',
            'contract_type_id' => 'required|exists:contract_types,id',
        ]);

        try {
            $file    = $request->file('template_file');
            $content = file_get_contents($file->getRealPath());

            $template = ContractTemplate::create([
                'name'              => $request->name,
                'contract_type_id'  => $request->contract_type_id,
                'content'           => $content,
                'template_settings' => json_encode(ContractTemplate::DEFAULT_SETTINGS),
                'template_info'     => json_encode([]),
                'default_clauses'   => json_encode([]),
                'is_active'         => false,
                'sort_order'        => 0
            ]);

            return redirect()
                ->route('admin.contract-templates.edit', $template)
                ->with('success', 'Template đã được import thành công. Vui lòng kiểm tra và chỉnh sửa.');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra khi import template: ' . $e->getMessage());
        }
    }

    /**
     * Generate template settings based on contract type
     */
    public function generateSettings(Request $request): JsonResponse
    {
        $contractTypeId = $request->get('contract_type_id');

        if (! $contractTypeId) {
            return response()->json(['error' => 'Contract type ID is required'], 400);
        }

        $contractType = ContractType::find($contractTypeId);

        if (! $contractType) {
            return response()->json(['error' => 'Contract type not found'], 404);
        }

        // Generate suggested settings based on contract type
        $suggestedSettings = ContractTemplate::DEFAULT_SETTINGS;
        $suggestedInfo     = [
            'current_user'   => auth()->user()->name ?? 'Công chứng viên',
            'current_date'   => now()->format('d/m/Y'),
            'office_name'    => 'Văn phòng công chứng Nguyễn Thị Như Trang',
            'office_address' => '320, đường ĐT743A, khu phố Trung Thắng, phường Bình Thắng, thành phố Dĩ An, tỉnh Bình Dương',
            'province'       => 'tỉnh Bình Dương',
        ];
        $suggestedClauses = [];

        // Customize based on contract type name
        $typeName = strtolower($contractType->name);

        if (str_contains($typeName, 'mua bán') || str_contains($typeName, 'buy') || str_contains($typeName, 'sale')) {
            $suggestedClauses[] = [
                'title'       => 'Điều khoản thanh toán',
                'content'     => 'Bên mua có trách nhiệm thanh toán đầy đủ số tiền {{transaction_value}} theo lịch trình đã thỏa thuận.',
                'order'       => 1,
                'is_required' => true,
            ];
            $suggestedClauses[] = [
                'title'       => 'Điều khoản giao nhận',
                'content'     => 'Bên bán có trách nhiệm giao tài sản cho bên mua theo đúng thời gian và địa điểm đã thỏa thuận.',
                'order'       => 2,
                'is_required' => true,
            ];
        } elseif (str_contains($typeName, 'thuê') || str_contains($typeName, 'lease') || str_contains($typeName, 'rent')) {
            $suggestedSettings['show_transaction_value'] = false; // Không hiển thị giá trị giao dịch cho hợp đồng thuê
            $suggestedClauses[]                          = [
                'title'       => 'Thời hạn thuê',
                'content'     => 'Thời hạn thuê được xác định từ ngày ký hợp đồng và có thể được gia hạn theo thỏa thuận của các bên.',
                'order'       => 1,
                'is_required' => true,
            ];
            $suggestedClauses[] = [
                'title'       => 'Tiền thuê và phương thức thanh toán',
                'content'     => 'Bên thuê có trách nhiệm thanh toán tiền thuê định kỳ theo thỏa thuận.',
                'order'       => 2,
                'is_required' => true,
            ];
        } elseif (str_contains($typeName, 'chuyển nhượng') || str_contains($typeName, 'transfer')) {
            $suggestedClauses[] = [
                'title'       => 'Điều kiện chuyển nhượng',
                'content'     => 'Việc chuyển nhượng phải tuân thủ đầy đủ các quy định của pháp luật hiện hành.',
                'order'       => 1,
                'is_required' => true,
            ];
        }

        return response()->json([
            'template_settings' => $suggestedSettings,
            'template_info'     => $suggestedInfo,
            'default_clauses'   => $suggestedClauses,
        ]);
    }

    /**
     * Validate template name uniqueness within contract type (AJAX)
     */
    public function validateName(Request $request): JsonResponse
    {
        $name           = $request->get('name');
        $contractTypeId = $request->get('contract_type_id');
        $templateId     = $request->get('template_id');

        $query = ContractTemplate::where('name', $name)
            ->where('contract_type_id', $contractTypeId);

        if ($templateId) {
            $query->where('id', '!=', $templateId);
        }

        $exists = $query->exists();

        return response()->json([
            'available' => ! $exists,
            'message'   => $exists ? 'Tên template đã tồn tại trong loại hợp đồng này' : 'Tên template có thể sử dụng',
        ]);
    }

    /**
     * Show statistics for templates
     */
    public function statistics(): JsonResponse
    {
        $stats = [
            'total_templates'          => ContractTemplate::count(),
            'active_templates'         => ContractTemplate::active()->count(),
            'inactive_templates'       => ContractTemplate::where('is_active', false)->count(),
            'templates_with_contracts' => ContractTemplate::has('contracts')->count(),
            'most_used_templates'      => ContractTemplate::withCount('contracts')
                ->orderBy('contracts_count', 'desc')
                ->limit(5)
                ->get(['id', 'name', 'contracts_count']),
            'templates_by_type'        => ContractTemplate::with('contractType')
                ->selectRaw('contract_type_id, count(*) as count')
                ->groupBy('contract_type_id')
                ->get(),
        ];

        return response()->json($stats);
    }
}
