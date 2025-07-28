<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Models\ContractTemplate;
use App\Models\ContractType;
use App\Models\Dossier;
use App\Services\ContractService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class DossierController extends Controller
{
    protected ContractService $contractService;

    public function __construct(ContractService $contractService)
    {
        $this->contractService = $contractService;
    }

    /**
     * Hiển thị danh sách hồ sơ
     */
    public function index(Request $request)
    {
        $query = Dossier::with(['creator'])
            ->where('created_by', auth()->id())
            ->withCount('contracts');

        // Tìm kiếm
        if ($search = $request->get('search')) {
            $query->search($search);
        }

        // Lọc theo trạng thái
        if ($status = $request->get('status')) {
            $query->byStatus($status);
        }

        $dossiers = $query->latest()->paginate(20);

        // Thống kê
        $stats = [
            'total'      => Dossier::where('created_by', auth()->id())->count(),
            'draft'      => Dossier::where('created_by', auth()->id())->byStatus(Dossier::STATUS_DRAFT)->count(),
            'processing' => Dossier::where('created_by', auth()->id())->byStatus(Dossier::STATUS_PROCESSING)->count(),
            'completed'  => Dossier::where('created_by', auth()->id())->byStatus(Dossier::STATUS_COMPLETED)->count(),
            'cancelled'  => Dossier::where('created_by', auth()->id())->byStatus(Dossier::STATUS_CANCELLED)->count(),
        ];

        return view('admin.dossiers.index', compact('dossiers', 'stats'));
    }

    /**
     * Hiển thị form tạo hồ sơ mới
     */
    public function create()
    {
        return view('admin.dossiers.create');
    }

    /**
     * Lưu hồ sơ mới
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => [
                'required',
                'string',
                'max:255',
                Rule::unique('dossiers')->where(function ($query) {
                    return $query->where('created_by', auth()->id());
                }),
            ],
            'description' => 'nullable|string|max:1000',
        ]);

        try {
            $dossier = Dossier::create([
                'name'        => $validated['name'],
                'description' => $validated['description'],
                'created_by'  => auth()->id(),
                'status'      => Dossier::STATUS_DRAFT,
            ]);

            return redirect()
                ->route('admin.dossiers.show', $dossier->id)
                ->with('success', 'Hồ sơ đã được tạo thành công.');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra khi tạo hồ sơ: ' . $e->getMessage());
        }
    }

    /**
     * Hiển thị chi tiết hồ sơ với danh sách templates
     */
    public function show($id)
    {
        // Debug: Log đầu vào
        Log::info('DossierController@show START', [
            'dossier_id'  => $id,
            'user_id'     => auth()->id(),
            'request_url' => request()->fullUrl(),
        ]);

        try {
            // Tìm dossier
            $dossier = Dossier::where('id', $id)
                ->where('created_by', auth()->id())
                ->first();

            if (! $dossier) {
                Log::warning('Dossier not found, redirecting to index', [
                    'dossier_id' => $id,
                    'user_id'    => auth()->id(),
                ]);

                return redirect()
                    ->route('admin.dossiers.index')
                    ->with('error', 'Không tìm thấy hồ sơ hoặc bạn không có quyền truy cập.');
            }

            // Load creator relationship
            try {
                $dossier->load(['creator']);
                Log::info('Creator loaded successfully');
            } catch (\Exception $e) {
                Log::warning('Error loading creator: ' . $e->getMessage());
            }

            // Set default values cho các biến
            $contractTypes = collect([]);
            $litigants     = collect([]);
            $assets        = collect([]);
            $contractStats = [
                'total'       => 0,
                'draft'       => 0,
                'completed'   => 0,
                'total_value' => 0,
            ];

            Log::info('Before rendering view', [
                'dossier_id'   => $dossier->id,
                'dossier_name' => $dossier->name,
            ]);

            // Render view
            return view('admin.dossiers.show', compact(
                'dossier',
                'contractTypes',
                'litigants',
                'assets',
                'contractStats'
            ));

        } catch (\Exception $e) {
            Log::error('Exception in DossierController@show', [
                'error' => $e->getMessage(),
                'file'  => $e->getFile(),
                'line'  => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()
                ->route('admin.dossiers.index')
                ->with('error', 'Có lỗi xảy ra khi tải hồ sơ: ' . $e->getMessage());
        }
    }

    /**
     * Tạo contract từ template đã chọn
     */
    public function createContractFromTemplate(Request $request, $dossierId): RedirectResponse
    {
        $dossier = Dossier::where('id', $dossierId)
            ->where('created_by', auth()->id())
            ->firstOrFail();
        $validated = $request->validate([
            'contract_template_id'     => 'required|exists:contract_templates,id',
            'contract_date'            => 'required|date|before_or_equal:today',
            'transaction_value'        => 'nullable|numeric|min:0',
            'notary_fee'               => 'nullable|string|max:255',
            'notary_number'            => 'nullable|string|max:255',
            'book_number'              => 'nullable|string|max:255',
            'parties'                  => 'required|array|min:1',
            'parties.*.litigant_id'    => 'required|exists:litigants,id',
            'parties.*.party_type'     => 'required|string',
            'parties.*.group_name'     => 'required|string',
            'parties.*.order_in_group' => 'nullable|integer|min:1',
            'parties.*.notes'          => 'nullable|string|max:500',
            'assets'                   => 'nullable|array',
            'assets.*.asset_id'        => 'required|exists:assets,id',
            'assets.*.notes'           => 'nullable|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            $template = ContractTemplate::findOrFail($validated['contract_template_id']);

            // Tạo hợp đồng từ template
            $contract = $this->contractService->createContractFromTemplate(
                $dossier,
                $template,
                $validated,
                $validated['parties'],
                $validated['assets'] ?? []
            );

            // Auto-generate contract number
            if (empty($contract->contract_number)) {
                $contract->update([
                    'contract_number' => $contract->generateContractNumber(),
                ]);
            }

            // Cập nhật trạng thái hồ sơ
            if ($dossier->status === Dossier::STATUS_DRAFT) {
                $dossier->markAsProcessing();
            }

            DB::commit();

            return redirect()
                ->route('admin.dossiers.contracts.show', [$dossier, $contract])
                ->with('success', 'Hợp đồng đã được tạo thành công từ template.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra khi tạo hợp đồng: ' . $e->getMessage());
        }
    }

    /**
     * Hiển thị chi tiết hợp đồng
     */
    public function showContract($dossierId, $contractId): View
    {
        $dossier = Dossier::where('id', $dossierId)
            ->where('created_by', auth()->id())
            ->firstOrFail();

        $contract = Contract::where('id', $contractId)
            ->where('dossier_id', $dossier->id)
            ->firstOrFail();

        $contract->load([
            'template.contractType',
            'parties.litigant.individual',
            'parties.litigant.organization',
            'parties.litigant.creditInstitution',
            'assets.realEstate',
            'assets.movableProperty',
        ]);

        // Lấy preview nội dung
        $preview = $this->contractService->getContractPreview($contract);

        return view('admin.dossiers.show-contract', compact(
            'dossier', 'contract', 'preview'
        ));
    }

    /**
     * Xuất hợp đồng ra PDF
     */
    public function exportContractPdf($dossierId, $contractId)
    {
        $dossier = Dossier::where('id', $dossierId)
            ->where('created_by', auth()->id())
            ->firstOrFail();

        $contract = Contract::where('id', $contractId)
            ->where('dossier_id', $dossier->id)
            ->firstOrFail();

        try {
            $filePath = $this->contractService->exportToPdf($contract);

            return Storage::download($filePath, basename($filePath));

        } catch (\Exception $e) {
            return back()->with('error', 'Có lỗi xảy ra khi xuất PDF: ' . $e->getMessage());
        }
    }

    /**
     * Xuất hợp đồng ra Word
     */
    public function exportContractWord($dossierId, $contractId)
    {
        $dossier = Dossier::where('id', $dossierId)
            ->where('created_by', auth()->id())
            ->firstOrFail();

        $contract = Contract::where('id', $contractId)
            ->where('dossier_id', $dossier->id)
            ->firstOrFail();

        try {
            $filePath = $this->contractService->exportToWord($contract);

            return Storage::download($filePath, basename($filePath));

        } catch (\Exception $e) {
            return back()->with('error', 'Có lỗi xảy ra khi xuất Word: ' . $e->getMessage());
        }
    }

    /**
     * Preview template trước khi tạo contract (AJAX)
     */
    public function previewTemplate(Request $request, $dossierId): JsonResponse
    {
        // Verify user access to dossier
        Dossier::where('id', $dossierId)
            ->where('created_by', auth()->id())
            ->firstOrFail();

        $templateId = $request->get('template_id');

        if (! $templateId) {
            return response()->json(['error' => 'Template ID is required'], 400);
        }

        try {
            $template = ContractTemplate::findOrFail($templateId);

            // Sample data để preview
            $sampleData = [
                'current_date'      => now()->format('d/m/Y'),
                'contract_number'   => 'HĐ001/2025',
                'transaction_value' => '1,000,000,000 VNĐ',
                'parties_Bên A'     => 'Nguyễn Văn A - CCCD: 123456789',
                'parties_Bên B'     => 'Công ty TNHH ABC - MST: 0123456789',
                'assets_list'       => '1. Nhà ở tại 123 Đường ABC, Quận 1, TP.HCM',
            ];

            // Xử lý template với sample data
            $content = $template->content;
            foreach ($sampleData as $key => $value) {
                $content = str_replace('{{' . $key . '}}', $value, $content);
            }

            return response()->json([
                'success'       => true,
                'content'       => $content,
                'template_name' => $template->name,
                'contract_type' => $template->contractType->name,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error'   => 'Không thể tải preview template',
            ], 500);
        }
    }

    /**
     * Lấy thông tin template (AJAX)
     */
    public function getTemplateInfo(Request $request, $dossierId): JsonResponse
    {
        // Verify user access to dossier
        Dossier::where('id', $dossierId)
            ->where('created_by', auth()->id())
            ->firstOrFail();

        $templateId = $request->get('template_id');

        try {
            $template = ContractTemplate::with('contractType')
                ->findOrFail($templateId);

            return response()->json([
                'id'                => $template->id,
                'name'              => $template->name,
                'contract_type'     => $template->contractType->name,
                'template_settings' => $template->template_settings,
                'default_clauses'   => $template->default_clauses,
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Template not found'], 404);
        }
    }

    /**
     * Cập nhật hồ sơ
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $dossier = Dossier::where('id', $id)
            ->where('created_by', auth()->id())
            ->firstOrFail();

        if (! $dossier->canBeUpdated()) {
            return back()->with('error', 'Hồ sơ này không thể chỉnh sửa.');
        }

        $validated = $request->validate([
            'name'        => [
                'required',
                'string',
                'max:255',
                Rule::unique('dossiers')->where(function ($query) use ($dossier) {
                    return $query->where('created_by', auth()->id())
                        ->where('id', '!=', $dossier->id);
                }),
            ],
            'description' => 'nullable|string|max:1000',
        ]);

        try {
            $dossier->update($validated);

            return redirect()
                ->route('admin.dossiers.show', $dossier->id)
                ->with('success', 'Hồ sơ đã được cập nhật thành công.');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra khi cập nhật hồ sơ: ' . $e->getMessage());
        }
    }

    /**
     * Hiển thị form chỉnh sửa
     */
    public function edit($id): View
    {
        $dossier = Dossier::where('id', $id)
            ->where('created_by', auth()->id())
            ->firstOrFail();

        if (! $dossier->canBeUpdated()) {
            return view('admin.dossiers.show');
        }

        return view('admin.dossiers.edit', compact('dossier'));
    }

    /**
     * Xóa hồ sơ
     */
    public function destroy($id): RedirectResponse
    {
        $dossier = Dossier::where('id', $id)
            ->where('created_by', auth()->id())
            ->firstOrFail();

        if (! $dossier->canBeCancelled()) {
            return back()->with('error', 'Hồ sơ này không thể xóa.');
        }

        try {
            $dossierName = $dossier->name;
            $dossier->delete();

            return redirect()
                ->route('admin.dossiers.index')
                ->with('success', "Hồ sơ '{$dossierName}' đã được xóa thành công.");

        } catch (\Exception $e) {
            return back()->with('error', 'Có lỗi xảy ra khi xóa hồ sơ: ' . $e->getMessage());
        }
    }

    /**
     * Cập nhật trạng thái hồ sơ
     */
    public function updateStatus(Request $request, $id): JsonResponse
    {
        $dossier = Dossier::where('id', $id)
            ->where('created_by', auth()->id())
            ->firstOrFail();

        $validated = $request->validate([
            'status' => 'required|in:draft,processing,completed,cancelled',
        ]);

        try {
            $dossier->update(['status' => $validated['status']]);

            return response()->json([
                'success'      => true,
                'message'      => 'Trạng thái hồ sơ đã được cập nhật.',
                'status'       => $validated['status'],
                'status_label' => $dossier->status_label,
                'status_color' => $dossier->status_color,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage(),
            ], 500);
        }
    }
}
