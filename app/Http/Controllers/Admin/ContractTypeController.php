<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContractType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ContractTypeController extends Controller
{
    /**
     * Display a listing of contract types
     */
    public function index(Request $request): View
    {
        $query = ContractType::withCount(['templates', 'templates as active_templates_count' => function ($q) {
            $q->where('is_active', true);
        }]);

        // Filter by status
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        // Search by name or description
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        $contractTypes = $query->ordered()->paginate(15);

        return view('admin.contract-types.index', compact('contractTypes'));
    }

    /**
     * Show the form for creating a new contract type
     */
    public function create(): View
    {
        return view('admin.contract-types.create');
    }

    /**
     * Store a newly created contract type
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255|unique:contract_types,name',
            'description' => 'nullable|string',
            'is_active'   => 'boolean',
            'sort_order'  => 'integer|min:0',
        ]);

        try {
            DB::beginTransaction();

            $contractType = ContractType::create($validated);

            DB::commit();

            return redirect()
                ->route('admin.contract-types.show', $contractType)
                ->with('success', 'Loại hợp đồng đã được tạo thành công.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra khi tạo loại hợp đồng: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified contract type
     */
    public function show(ContractType $contractType): View
    {
        $contractType->load(['templates' => function ($query) {
            $query->with(['contracts' => function ($q) {
                $q->latest()->take(5);
            }])->latest()->take(10);
        }]);

        return view('admin.contract-types.show', compact('contractType'));
    }

    /**
     * Show the form for editing the specified contract type
     */
    public function edit(ContractType $contractType): View
    {
        return view('admin.contract-types.edit', compact('contractType'));
    }

    /**
     * Update the specified contract type
     */
    public function update(Request $request, ContractType $contractType): RedirectResponse
    {
        $validated = $request->validate([
            'name'        => [
                'required',
                'string',
                'max:255',
                Rule::unique('contract_types', 'name')->ignore($contractType->id),
            ],
            'description' => 'nullable|string',
            'is_active'   => 'boolean',
            'sort_order'  => 'integer|min:0',
        ]);

        try {
            DB::beginTransaction();

            $contractType->update($validated);

            DB::commit();

            return redirect()
                ->route('admin.contract-types.show', $contractType)
                ->with('success', 'Loại hợp đồng đã được cập nhật thành công.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra khi cập nhật loại hợp đồng: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified contract type
     */
    public function destroy(ContractType $contractType): JsonResponse
    {
        if (! $contractType->canBeDeleted()) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể xóa loại hợp đồng này vì đã có template hoặc hợp đồng sử dụng.',
            ], 422);
        }

        try {
            DB::beginTransaction();

            $contractType->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Loại hợp đồng đã được xóa thành công.',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xóa loại hợp đồng: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Toggle active status of contract type
     */
    public function toggleStatus(ContractType $contractType): JsonResponse
    {
        try {
            $contractType->update([
                'is_active' => ! $contractType->is_active,
            ]);

            return response()->json([
                'success'   => true,
                'is_active' => $contractType->is_active,
                'message'   => $contractType->is_active
                ? 'Loại hợp đồng đã được kích hoạt.'
                : 'Loại hợp đồng đã được vô hiệu hóa.',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update sort order of contract types
     */
    public function updateOrder(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'orders'              => 'required|array',
            'orders.*.id'         => 'required|exists:contract_types,id',
            'orders.*.sort_order' => 'required|integer|min:0',
        ]);

        try {
            DB::beginTransaction();

            foreach ($validated['orders'] as $order) {
                ContractType::where('id', $order['id'])
                    ->update(['sort_order' => $order['sort_order']]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Thứ tự loại hợp đồng đã được cập nhật.',
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
     * Show templates for a specific contract type
     */
    public function templates(ContractType $contractType): View
    {
        $templates = $contractType->templates()
            ->withCount('contracts')
            ->ordered()
            ->paginate(10);

        return view('admin.contract-types.templates', compact('contractType', 'templates'));
    }

    /**
     * Get all active contract types for select dropdown (AJAX)
     */
    public function getActiveTypes(Request $request): JsonResponse
    {
        $contractTypes = ContractType::active()
            ->ordered()
            ->select('id', 'name', 'description')
            ->get();

        return response()->json($contractTypes);
    }

    /**
     * Duplicate a contract type with all its templates
     */
    public function duplicate(ContractType $contractType): RedirectResponse
    {
        try {
            DB::beginTransaction();

            // Duplicate contract type
            $newContractType             = $contractType->replicate();
            $newContractType->name       = $contractType->name . ' (Bản sao)';
            $newContractType->is_active  = false;
            $newContractType->sort_order = 0;
            $newContractType->save();

            // Duplicate all templates of this contract type
            $templates = $contractType->templates;
            foreach ($templates as $template) {
                $newTemplate                   = $template->replicate();
                $newTemplate->contract_type_id = $newContractType->id;
                $newTemplate->name             = $template->name . ' (Bản sao)';
                $newTemplate->is_active        = false;
                $newTemplate->sort_order       = 0;
                $newTemplate->save();
            }

            DB::commit();

            return redirect()
                ->route('admin.contract-types.edit', $newContractType)
                ->with('success', "Loại hợp đồng đã được sao chép thành công với {$templates->count()} template. Vui lòng kiểm tra và chỉnh sửa.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra khi sao chép loại hợp đồng: ' . $e->getMessage());
        }
    }

    /**
     * Show statistics for contract types
     */
    public function statistics(): JsonResponse
    {
        $stats = [
            'total_types'          => ContractType::count(),
            'active_types'         => ContractType::active()->count(),
            'inactive_types'       => ContractType::where('is_active', false)->count(),
            'types_with_templates' => ContractType::has('templates')->count(),
            'types_with_contracts' => ContractType::whereHas('templates.contracts')->count(),
            'most_used_types'      => ContractType::withCount(['templates', 'templates.contracts as contracts_count'])
                ->orderBy('contracts_count', 'desc')
                ->limit(5)
                ->get(['id', 'name', 'templates_count', 'contracts_count']),
        ];

        return response()->json($stats);
    }

    /**
     * Bulk actions for contract types
     */
    public function bulkAction(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'action' => 'required|in:activate,deactivate,delete',
            'ids'    => 'required|array',
            'ids.*'  => 'exists:contract_types,id',
        ]);

        $action       = $validated['action'];
        $ids          = $validated['ids'];
        $successCount = 0;
        $errors       = [];

        try {
            DB::beginTransaction();

            foreach ($ids as $id) {
                $contractType = ContractType::find($id);

                if (! $contractType) {
                    $errors[] = "Không tìm thấy loại hợp đồng với ID: {$id}";
                    continue;
                }

                switch ($action) {
                    case 'activate':
                        $contractType->update(['is_active' => true]);
                        $successCount++;
                        break;

                    case 'deactivate':
                        $contractType->update(['is_active' => false]);
                        $successCount++;
                        break;

                    case 'delete':
                        if ($contractType->canBeDeleted()) {
                            $contractType->delete();
                            $successCount++;
                        } else {
                            $errors[] = "Không thể xóa loại hợp đồng '{$contractType->name}' vì đã có template sử dụng.";
                        }
                        break;
                }
            }

            DB::commit();

            $message = $successCount > 0 ? "Đã thực hiện thành công {$successCount} thao tác." : '';
            if (! empty($errors)) {
                $message .= ' Lỗi: ' . implode(', ', $errors);
            }

            return response()->json([
                'success'       => $successCount > 0,
                'message'       => $message,
                'success_count' => $successCount,
                'errors'        => $errors,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage(),
            ], 500);
        }
    }
}
