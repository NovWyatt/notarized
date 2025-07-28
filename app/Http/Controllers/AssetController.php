<?php

// app/Http/Controllers/AssetController.php
namespace App\Http\Controllers;

use App\Enums\AssetTypeEnum;
use App\Models\Apartment;
use App\Models\Asset;
use App\Models\Certificate;
use App\Models\CertificateType;
use App\Models\House;
use App\Models\IssuingAuthority;
use App\Models\LandPlot;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AssetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View | JsonResponse
    {
        $query = Asset::with([
            'certificates.certificateType',
            'certificates.issuingAuthority', // Thêm relationship với issuing authority
            'landPlots',
            'house',
            'apartment',
            'vehicle.issuingAuthority',
            'creator:id,name,email,department',
            'updater:id,name,email,department',
        ]);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('notes', 'like', "%{$search}%")
                    ->orWhereHas('landPlots', function ($landQuery) use ($search) {
                        $landQuery->where('street_name', 'like', "%{$search}%")
                            ->orWhere('house_number', 'like', "%{$search}%")
                            ->orWhere('province', 'like', "%{$search}%")
                            ->orWhere('district', 'like', "%{$search}%")
                            ->orWhere('ward', 'like', "%{$search}%");
                    })
                    ->orWhereHas('vehicle', function ($vehicleQuery) use ($search) {
                        $vehicleQuery->where('license_plate', 'like', "%{$search}%")
                            ->orWhere('brand', 'like', "%{$search}%");
                    })
                    ->orWhereHas('creator', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Filter by asset type
        if ($request->filled('asset_type')) {
            $query->where('asset_type', $request->get('asset_type'));
        }

        // Filter by creator (my assets)
        if ($request->filled('my_assets') && $request->my_assets == '1') {
            $query->where('created_by', auth()->id());
        }

        // Filter by creator
        if ($request->filled('created_by')) {
            $query->where('created_by', $request->get('created_by'));
        }

        // Sort functionality
        $sortField     = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');

        $allowedSortFields = ['asset_type', 'created_at', 'updated_at'];
        if (in_array($sortField, $allowedSortFields)) {
            $query->orderBy($sortField, $sortDirection);
        }

        $assets = $query->paginate(15)->withQueryString();

        // Add formatted data for each asset
        $assets->getCollection()->transform(function ($asset) {
            $asset->type_label      = AssetTypeEnum::label($asset->asset_type);
            $asset->primary_address = $this->getPrimaryAddress($asset);
            $asset->summary_info    = $this->getAssetSummary($asset);
            $asset->display_name    = $this->getAssetDisplayName($asset);
            $asset->creator_name    = $asset->creator_name;
            $asset->updater_name    = $asset->updater_name;
            $asset->can_edit        = $this->canEditAsset($asset);
            $asset->can_delete      = $this->canDeleteAsset($asset);
            return $asset;
        });

        $data = [
            'assets'          => $assets,
            'assetTypes'      => AssetTypeEnum::options(),
            'users'           => $this->getActiveUsers(),
            'searchTerm'      => $request->get('search'),
            'selectedType'    => $request->get('asset_type'),
            'selectedCreator' => $request->get('created_by'),
            'myAssets'        => $request->get('my_assets'),
            'sortField'       => $sortField,
            'sortDirection'   => $sortDirection,
        ];

        if ($request->expectsJson()) {
            return response()->json($data);
        }

        return view('properties.index', $data);
    }

    private function getActiveUsers(): array
    {
        return User::orderBy('name')
            ->pluck('name', 'id')
            ->toArray();
    }

    public function myAssets(Request $request): View | JsonResponse
    {
        $request->merge(['my_assets' => '1']);
        return $this->index($request);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $data = [
            'assetTypes'         => AssetTypeEnum::options(),
            'certificateTypes'   => CertificateType::active()->get()->pluck('name', 'id'),
            'issuingAuthorities' => IssuingAuthority::active()->get()->pluck('name', 'id'),
        ];

        return view('properties.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse | JsonResponse
    {
        // Debug: Log all incoming data
        Log::info('Asset store request data:', [
            'all_data'                         => $request->all(),
            'certificate_type_id'              => $request->certificate_type_id,
            'certificate_issuing_authority_id' => $request->certificate_issuing_authority_id,
            'issue_number'                     => $request->issue_number,
            'book_number'                      => $request->book_number,
            'issue_date'                       => $request->issue_date,
        ]);

        $validator = $this->validateAssetData($request);

        if ($validator->fails()) {
            Log::warning('Asset store validation failed:', [
                'errors' => $validator->errors()->toArray(),
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'errors'  => $validator->errors(),
                ], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();

            // Create main asset
            $asset = Asset::create($validator->validated());

            Log::info('Asset created successfully:', [
                'asset_id'   => $asset->id,
                'asset_type' => $asset->asset_type,
            ]);

            // Create related data based on asset type
            $this->createRelatedData($asset, $request);

            // Generate and update asset name after all related data is created
            $asset->load(['certificates', 'landPlots', 'vehicle']);
            $asset->generateAndUpdateName();

            // Debug: Check what was actually created
            if (AssetTypeEnum::isRealEstate($asset->asset_type)) {
                $certificate = $asset->certificates()->first();
                Log::info('Certificate created:', [
                    'certificate_exists' => $certificate ? true : false,
                    'certificate_data'   => $certificate ? $certificate->toArray() : null,
                ]);
            }

            Log::info('Asset name generated:', [
                'asset_id'       => $asset->id,
                'generated_name' => $asset->name,
            ]);

            DB::commit();

            $message = 'Tài sản đã được tạo thành công!';

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'asset'   => $asset->load($this->getEagerLoadRelations($asset->asset_type)),
                ]);
            }

            return redirect()->route('properties.show', $asset)
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Asset store error:', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);

            $errorMessage = 'Có lỗi xảy ra khi tạo tài sản: ' . $e->getMessage();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage,
                ], 500);
            }

            return back()->withErrors(['error' => $errorMessage])->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Asset $property): View | JsonResponse
    {
        // Load relations với null check
        $relations = [];
        if (! empty($property->asset_type)) {
            $relations = $this->getEagerLoadRelations($property->asset_type);
        }

        // Thêm thông tin user vào relations
        $relations[] = 'creator:id,name,email';
        $relations[] = 'updater:id,name,email';

        $property->load($relations);

        $data = [
            'asset'          => $property,
            'typeLabel'      => AssetTypeEnum::label($property->asset_type ?? ''),
            'displayName'    => $this->getAssetDisplayName($property),
            'detailSections' => $this->getDetailSections($property),
            'userInfo'       => $this->getUserInfo($property),
            'canEdit'        => $this->canEditAsset($property),
            'canDelete'      => $this->canDeleteAsset($property),
        ];

        if (request()->expectsJson()) {
            return response()->json($data);
        }

        return view('properties.show', $data);
    }

    private function getUserInfo(Asset $asset): array
    {
        return [
            'creator' => [
                'name'       => $asset->creator ? $asset->creator->name : 'Hệ thống',
                'email'      => $asset->creator ? $asset->creator->email : null,
                'created_at' => $asset->created_at->format('d/m/Y H:i:s'),
            ],
            'updater' => [
                'name'       => $asset->updater ? $asset->updater->name : 'Hệ thống',
                'email'      => $asset->updater ? $asset->updater->email : null,
                'updated_at' => $asset->updated_at->format('d/m/Y H:i:s'),
            ],
        ];
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Asset $property): View
    {
        // Load relations với null check
        $relations = [];
        if (! empty($property->asset_type)) {
            $relations = $this->getEagerLoadRelations($property->asset_type);
        }

        $property->load($relations);

        $data = [
            'asset'              => $property,
            'assetTypes'         => AssetTypeEnum::options(),
            'certificateTypes'   => CertificateType::active()->get()->pluck('name', 'id'),
            'issuingAuthorities' => IssuingAuthority::active()->get()->pluck('name', 'id'),
            'isEditing'          => true,
        ];

        return view('properties.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Asset $property): RedirectResponse | JsonResponse
    {
        $validator = $this->validateAssetData($request, $property->id);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'errors'  => $validator->errors(),
                ], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();

            // Update main asset
            $property->update($validator->validated());

            // Update related data
            $this->updateRelatedData($property, $request);

            // Regenerate asset name after updating related data
            $property->refreshName();

            DB::commit();

            $message = 'Tài sản đã được cập nhật thành công!';

            if ($request->expectsJson()) {
                // Load relations với null check
                $relations = [];
                if (! empty($property->asset_type)) {
                    $relations = $this->getEagerLoadRelations($property->asset_type);
                }

                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'asset'   => $property->fresh($relations),
                ]);
            }

            return redirect()->route('properties.show', $property)
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();

            $errorMessage = 'Có lỗi xảy ra khi cập nhật tài sản: ' . $e->getMessage();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage,
                ], 500);
            }

            return back()->withErrors(['error' => $errorMessage])->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Asset $asset): RedirectResponse | JsonResponse
    {
        try {
            $assetName = $this->getAssetDisplayName($asset);
            $asset->delete();

            $message = "Tài sản '{$assetName}' đã được xóa thành công!";

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                ]);
            }

            return redirect()->route('properties.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            $errorMessage = 'Có lỗi xảy ra khi xóa tài sản: ' . $e->getMessage();

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage,
                ], 500);
            }

            return back()->withErrors(['error' => $errorMessage]);
        }
    }

    /**
     * Get asset form fields based on type (AJAX endpoint)
     */
    public function getAssetFields(Request $request): JsonResponse
    {
        // Log để debug
        Log::info('getAssetFields called', [
            'asset_type' => $request->get('asset_type'),
            'all_params' => $request->all(),
            'headers'    => $request->headers->all(),
        ]);

        try {
            $assetType = $request->get('asset_type');

            // Kiểm tra asset type có tồn tại không
            if (empty($assetType)) {
                Log::warning('Asset type is empty');
                return response()->json([
                    'error' => 'Asset type is required',
                ], 400);
            }

            // Kiểm tra asset type có hợp lệ không
            if (! in_array($assetType, AssetTypeEnum::all())) {
                Log::warning('Invalid asset type', ['asset_type' => $assetType]);
                return response()->json([
                    'error'       => 'Invalid asset type',
                    'provided'    => $assetType,
                    'valid_types' => AssetTypeEnum::all(),
                ], 400);
            }

            // Tạo response data
            $responseData = [
                'success'             => true,
                'asset_type'          => $assetType,
                'certificate_fields'  => AssetTypeEnum::isRealEstate($assetType),
                'land_plot_fields'    => AssetTypeEnum::isRealEstate($assetType),
                'house_fields'        => AssetTypeEnum::hasHouseInfo($assetType),
                'apartment_fields'    => AssetTypeEnum::hasApartmentInfo($assetType),
                'vehicle_fields'      => AssetTypeEnum::hasVehicleInfo($assetType),
                'certificate_types'   => CertificateType::active()->get()->map(function ($type) {
                    return ['id' => $type->id, 'name' => $type->name];
                }),
                'issuing_authorities' => IssuingAuthority::active()->get()->map(function ($authority) {
                    return ['id' => $authority->id, 'name' => $authority->name];
                }),
                'debug_info'          => [
                    'timestamp'       => now()->toISOString(),
                    'is_real_estate'  => AssetTypeEnum::isRealEstate($assetType),
                    'php_version'     => PHP_VERSION,
                    'laravel_version' => app()->version(),
                ],
            ];

            Log::info('getAssetFields success', $responseData);

            return response()->json($responseData);

        } catch (\Throwable $e) {
            Log::error('getAssetFields error', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
                'trace'   => $e->getTraceAsString(),
            ]);

            return response()->json([
                'error'   => 'Internal server error',
                'message' => $e->getMessage(),
                'debug'   => config('app.debug') ? [
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ] : null,
            ], 500);
        }
    }

    /**
     * Search issuing authorities for autocomplete
     */
    public function searchIssuingAuthorities(Request $request): JsonResponse
    {
        $query = $request->get('q', '');

        if (strlen($query) < 1) {
            $authorities = IssuingAuthority::active()
                ->orderBy('name')
                ->limit(10)
                ->get();
        } else {
            $authorities = IssuingAuthority::active()
                ->where('name', 'like', "%{$query}%")
                ->orderBy('name')
                ->limit(10)
                ->get();
        }

        return response()->json([
            'success' => true,
            'data'    => $authorities->map(function ($authority) {
                return [
                    'id'   => $authority->id,
                    'text' => $authority->name,
                    'name' => $authority->name,
                ];
            }),
        ]);
    }

    /**
     * Bulk delete assets
     */
    public function bulkDelete(Request $request): JsonResponse
    {
        $request->validate([
            'asset_ids'   => 'required|array',
            'asset_ids.*' => 'exists:assets,id',
        ]);

        try {
            $deletedCount = Asset::whereIn('id', $request->asset_ids)->delete();

            return response()->json([
                'success' => true,
                'message' => "Đã xóa {$deletedCount} tài sản thành công!",
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xóa tài sản: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Export assets to Excel/CSV
     */
    public function export(Request $request): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        // Implementation for export functionality
        // This would typically use Laravel Excel or similar package
        return response()->download('path/to/exported/file.xlsx');
    }

    /**
     * Clone an existing asset
     */
    public function cloneAsset(Asset $asset): RedirectResponse
    {
        try {
            DB::beginTransaction();

            // Clone main asset
            $newAsset = $asset->replicate();
            $newAsset->save();

            // Clone related data
            foreach ($asset->certificates as $certificate) {
                $newCertificate           = $certificate->replicate();
                $newCertificate->asset_id = $newAsset->id;
                $newCertificate->save();
            }

            foreach ($asset->landPlots as $landPlot) {
                $newLandPlot           = $landPlot->replicate();
                $newLandPlot->asset_id = $newAsset->id;
                $newLandPlot->save();
            }

            if ($asset->house) {
                $newHouse           = $asset->house->replicate();
                $newHouse->asset_id = $newAsset->id;
                $newHouse->save();
            }

            if ($asset->apartment) {
                $newApartment           = $asset->apartment->replicate();
                $newApartment->asset_id = $newAsset->id;
                $newApartment->save();
            }

            if ($asset->vehicle) {
                $newVehicle           = $asset->vehicle->replicate();
                $newVehicle->asset_id = $newAsset->id;
                $newVehicle->save();
            }

            DB::commit();

            return redirect()->route('properties.edit', $newAsset)
                ->with('success', 'Tài sản đã được sao chép thành công! Vui lòng kiểm tra và cập nhật thông tin.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Có lỗi xảy ra khi sao chép tài sản: ' . $e->getMessage()]);
        }
    }

    // ================== PRIVATE HELPER METHODS ==================

    /**
     * Validate asset data
     */
    private function validateAssetData(Request $request, ?int $assetId = null): \Illuminate\Validation\Validator
    {
        $rules = [
            'asset_type' => ['required', Rule::in(AssetTypeEnum::all())],
            'notes'      => 'nullable|string',
        ];

        // Add rules for certificates
        if (AssetTypeEnum::isRealEstate($request->asset_type)) {
            $rules = array_merge($rules, $this->getCertificateValidationRules());
            $rules = array_merge($rules, $this->getLandPlotValidationRules());
        }

        // Add specific rules based on asset type
        if (AssetTypeEnum::hasHouseInfo($request->asset_type)) {
            $rules = array_merge($rules, $this->getHouseValidationRules());
        }

        if (AssetTypeEnum::hasApartmentInfo($request->asset_type)) {
            $rules = array_merge($rules, $this->getApartmentValidationRules());
        }

        if (AssetTypeEnum::hasVehicleInfo($request->asset_type)) {
            $rules = array_merge($rules, $this->getVehicleValidationRules());
        }

        return Validator::make($request->all(), $rules);
    }

    /**
     * Get certificate validation rules
     */
    private function getCertificateValidationRules(): array
    {
        return [
            'certificate_type_id'              => 'nullable|exists:certificate_types,id',
            'certificate_issuing_authority_id' => 'nullable|exists:issuing_authorities,id', // Thêm validation cho nơi cấp
            'issue_number'                     => 'nullable|string|max:50',
            'book_number'                      => 'nullable|string|max:50',
            'issue_date'                       => 'nullable|date',
        ];
    }

    /**
     * Get land plot validation rules
     */
    private function getLandPlotValidationRules(): array
    {
        return [
            'plot_number'      => 'nullable|string|max:50',
            'map_sheet_number' => 'nullable|string|max:50',
            'house_number'     => 'nullable|string|max:20',
            'street_name'      => 'nullable|string|max:255',
            'province'         => 'nullable|string|max:100',
            'district'         => 'nullable|string|max:100',
            'ward'             => 'nullable|string|max:100',
            'area'             => 'nullable|numeric|min:0',
            'usage_form'       => 'nullable|string|max:255',
            'usage_purpose'    => 'nullable|string|max:255',
            'land_use_term'    => 'nullable|date',
            'usage_origin'     => 'nullable|string|max:255',
            'land_notes'       => 'nullable|string',
        ];
    }

    /**
     * Get house validation rules
     */
    private function getHouseValidationRules(): array
    {
        return [
            'house_type'        => 'nullable|string|max:255',
            'construction_area' => 'nullable|numeric|min:0',
            'floor_area'        => 'nullable|numeric|min:0',
            'ownership_form'    => 'nullable|string|max:255',
            'grade_level'       => 'nullable|string|max:50',
            'number_of_floors'  => 'nullable|integer|min:1|max:100',
            'ownership_term'    => 'nullable|date',
            'structure'         => 'nullable|string|max:255',
            'house_notes'       => 'nullable|string',
        ];
    }

    /**
     * Get apartment validation rules
     */
    private function getApartmentValidationRules(): array
    {
        return [
            'apartment_number'            => 'nullable|string|max:50',
            'apartment_floor'             => 'nullable|integer|min:1|max:200',
            'building_floors'             => 'nullable|integer|min:1|max:200',
            'apartment_construction_area' => 'nullable|numeric|min:0',
            'apartment_floor_area'        => 'nullable|numeric|min:0',
            'apartment_ownership_form'    => 'nullable|string|max:255',
            'apartment_ownership_term'    => 'nullable|date',
            'apartment_structure'         => 'nullable|string|max:255',
            'apartment_notes'             => 'nullable|string',
        ];
    }

    /**
     * Get vehicle validation rules
     */
    private function getVehicleValidationRules(): array
    {
        return [
            'registration_number'  => 'nullable|string|max:50',
            'issuing_authority_id' => 'nullable|exists:issuing_authorities,id',
            'vehicle_issue_date'   => 'nullable|date',
            'license_plate'        => 'nullable|string|max:20',
            'brand'                => 'nullable|string|max:100',
            'vehicle_type'         => 'nullable|string|max:100',
            'color'                => 'nullable|string|max:50',
            'payload'              => 'nullable|numeric|min:0',
            'engine_number'        => 'nullable|string|max:50',
            'chassis_number'       => 'nullable|string|max:50',
            'type_number'          => 'nullable|string|max:50',
            'engine_capacity'      => 'nullable|numeric|min:0',
            'seating_capacity'     => 'nullable|integer|min:1|max:100',
            'vehicle_notes'        => 'nullable|string',
        ];
    }

    /**
     * Create related data for new asset
     */
    private function createRelatedData(Asset $asset, Request $request): void
    {
        // Create certificate if real estate (sửa logic: tạo khi có bất kỳ field nào của certificate)
        if (
            AssetTypeEnum::isRealEstate($asset->asset_type) && (
                $request->filled('certificate_type_id') ||
                $request->filled('certificate_issuing_authority_id') ||
                $request->filled('issue_number') ||
                $request->filled('book_number') ||
                $request->filled('issue_date')
            )
        ) {
            Certificate::create([
                'asset_id'             => $asset->id,
                'certificate_type_id'  => $request->certificate_type_id,
                'issuing_authority_id' => $request->certificate_issuing_authority_id, // FIX: Đổi tên field
                'issue_number'         => $request->issue_number,
                'book_number'          => $request->book_number,
                'issue_date'           => $request->issue_date,
            ]);
        }

        // Create land plot if real estate
        if (AssetTypeEnum::isRealEstate($asset->asset_type)) {
            LandPlot::create([
                'asset_id'         => $asset->id,
                'plot_number'      => $request->plot_number,
                'map_sheet_number' => $request->map_sheet_number,
                'house_number'     => $request->house_number,
                'street_name'      => $request->street_name,
                'province'         => $request->province,
                'district'         => $request->district,
                'ward'             => $request->ward,
                'area'             => $request->area,
                'usage_form'       => $request->usage_form,
                'usage_purpose'    => $request->usage_purpose,
                'land_use_term'    => $request->land_use_term,
                'usage_origin'     => $request->usage_origin,
                'notes'            => $request->land_notes,
            ]);
        }

        // Create house info
        if (AssetTypeEnum::hasHouseInfo($asset->asset_type)) {
            House::create([
                'asset_id'          => $asset->id,
                'house_type'        => $request->house_type,
                'construction_area' => $request->construction_area,
                'floor_area'        => $request->floor_area,
                'ownership_form'    => $request->ownership_form,
                'grade_level'       => $request->grade_level,
                'number_of_floors'  => $request->number_of_floors,
                'ownership_term'    => $request->ownership_term,
                'structure'         => $request->structure,
                'notes'             => $request->house_notes,
            ]);
        }

        // Create apartment info
        if (AssetTypeEnum::hasApartmentInfo($asset->asset_type)) {
            Apartment::create([
                'asset_id'          => $asset->id,
                'apartment_number'  => $request->apartment_number,
                'apartment_floor'   => $request->apartment_floor,
                'building_floors'   => $request->building_floors,
                'construction_area' => $request->apartment_construction_area,
                'floor_area'        => $request->apartment_floor_area,
                'ownership_form'    => $request->apartment_ownership_form,
                'ownership_term'    => $request->apartment_ownership_term,
                'structure'         => $request->apartment_structure,
                'notes'             => $request->apartment_notes,
            ]);
        }

        // Create vehicle info
        if (AssetTypeEnum::hasVehicleInfo($asset->asset_type)) {
            Vehicle::create([
                'asset_id'             => $asset->id,
                'registration_number'  => $request->registration_number,
                'issuing_authority_id' => $request->issuing_authority_id,
                'issue_date'           => $request->vehicle_issue_date,
                'license_plate'        => $request->license_plate,
                'brand'                => $request->brand,
                'vehicle_type'         => $request->vehicle_type,
                'color'                => $request->color,
                'payload'              => $request->payload,
                'engine_number'        => $request->engine_number,
                'chassis_number'       => $request->chassis_number,
                'type_number'          => $request->type_number,
                'engine_capacity'      => $request->engine_capacity,
                'seating_capacity'     => $request->seating_capacity,
                'notes'                => $request->vehicle_notes,
            ]);
        }
    }

    /**
     * Update related data for existing asset
     */
    private function updateRelatedData(Asset $asset, Request $request): void
    {
        // Update or create certificate (sửa logic: tạo/cập nhật khi có bất kỳ field nào)
        if (AssetTypeEnum::isRealEstate($asset->asset_type)) {
            // Kiểm tra xem có dữ liệu certificate nào không
            $hasCertificateData = $request->filled('certificate_type_id') ||
            $request->filled('certificate_issuing_authority_id') ||
            $request->filled('issue_number') ||
            $request->filled('book_number') ||
            $request->filled('issue_date');

            if ($hasCertificateData) {
                $asset->certificates()->updateOrCreate(
                    ['asset_id' => $asset->id],
                    [
                        'certificate_type_id'  => $request->certificate_type_id,
                        'issuing_authority_id' => $request->certificate_issuing_authority_id, // FIX: Đổi tên field
                        'issue_number'         => $request->issue_number,
                        'book_number'          => $request->book_number,
                        'issue_date'           => $request->issue_date,
                    ]
                );
            }

            // Update land plot
            $asset->landPlots()->updateOrCreate(
                ['asset_id' => $asset->id],
                [
                    'plot_number'      => $request->plot_number,
                    'map_sheet_number' => $request->map_sheet_number,
                    'house_number'     => $request->house_number,
                    'street_name'      => $request->street_name,
                    'province'         => $request->province,
                    'district'         => $request->district,
                    'ward'             => $request->ward,
                    'area'             => $request->area,
                    'usage_form'       => $request->usage_form,
                    'usage_purpose'    => $request->usage_purpose,
                    'land_use_term'    => $request->land_use_term,
                    'usage_origin'     => $request->usage_origin,
                    'notes'            => $request->land_notes,
                ]
            );
        }

        // Update house info
        if (AssetTypeEnum::hasHouseInfo($asset->asset_type)) {
            $asset->house()->updateOrCreate(
                ['asset_id' => $asset->id],
                [
                    'house_type'        => $request->house_type,
                    'construction_area' => $request->construction_area,
                    'floor_area'        => $request->floor_area,
                    'ownership_form'    => $request->ownership_form,
                    'grade_level'       => $request->grade_level,
                    'number_of_floors'  => $request->number_of_floors,
                    'ownership_term'    => $request->ownership_term,
                    'structure'         => $request->structure,
                    'notes'             => $request->house_notes,
                ]
            );
        }

        // Update apartment info
        if (AssetTypeEnum::hasApartmentInfo($asset->asset_type)) {
            $asset->apartment()->updateOrCreate(
                ['asset_id' => $asset->id],
                [
                    'apartment_number'  => $request->apartment_number,
                    'apartment_floor'   => $request->apartment_floor,
                    'building_floors'   => $request->building_floors,
                    'construction_area' => $request->apartment_construction_area,
                    'floor_area'        => $request->apartment_floor_area,
                    'ownership_form'    => $request->apartment_ownership_form,
                    'ownership_term'    => $request->apartment_ownership_term,
                    'structure'         => $request->apartment_structure,
                    'notes'             => $request->apartment_notes,
                ]
            );
        }

        // Update vehicle info
        if (AssetTypeEnum::hasVehicleInfo($asset->asset_type)) {
            $asset->vehicle()->updateOrCreate(
                ['asset_id' => $asset->id],
                [
                    'registration_number'  => $request->registration_number,
                    'issuing_authority_id' => $request->issuing_authority_id,
                    'issue_date'           => $request->vehicle_issue_date,
                    'license_plate'        => $request->license_plate,
                    'brand'                => $request->brand,
                    'vehicle_type'         => $request->vehicle_type,
                    'color'                => $request->color,
                    'payload'              => $request->payload,
                    'engine_number'        => $request->engine_number,
                    'chassis_number'       => $request->chassis_number,
                    'type_number'          => $request->type_number,
                    'engine_capacity'      => $request->engine_capacity,
                    'seating_capacity'     => $request->seating_capacity,
                    'notes'                => $request->vehicle_notes,
                ]
            );
        }
    }

    /**
     * Get eager load relations based on asset type
     */
    private function getEagerLoadRelations(?string $assetType): array
    {
        // Thêm null check
        if (empty($assetType)) {
            return [];
        }

        $relations = [];

        if (AssetTypeEnum::isRealEstate($assetType)) {
            $relations[] = 'certificates.certificateType';
            $relations[] = 'certificates.issuingAuthority'; // Thêm relationship với issuing authority
            $relations[] = 'landPlots';
        }

        if (AssetTypeEnum::hasHouseInfo($assetType)) {
            $relations[] = 'house';
        }

        if (AssetTypeEnum::hasApartmentInfo($assetType)) {
            $relations[] = 'apartment';
        }

        if (AssetTypeEnum::hasVehicleInfo($assetType)) {
            $relations[] = 'vehicle.issuingAuthority';
        }

        return $relations;
    }

    /**
     * Get asset display name
     */
    private function getAssetDisplayName(Asset $asset): string
    {
        // Tạo tên hiển thị dựa trên loại tài sản và thông tin có sẵn
        $typeLabel = AssetTypeEnum::label($asset->asset_type);

        if ($asset->landPlots->isNotEmpty()) {
            $landPlot = $asset->landPlots->first();
            if ($landPlot->house_number && $landPlot->street_name) {
                return "{$typeLabel} - {$landPlot->house_number} {$landPlot->street_name}";
            }
            if ($landPlot->street_name) {
                return "{$typeLabel} - {$landPlot->street_name}";
            }
        }

        if ($asset->vehicle && $asset->vehicle->license_plate) {
            return "{$typeLabel} - {$asset->vehicle->license_plate}";
        }

        if ($asset->apartment && $asset->apartment->apartment_number) {
            return "{$typeLabel} - Căn hộ {$asset->apartment->apartment_number}";
        }

        return "{$typeLabel} #{$asset->id}";
    }

    /**
     * Get primary address for asset
     */
    private function getPrimaryAddress(Asset $asset): string
    {
        $landPlot = $asset->landPlots->first();

        if (! $landPlot) {
            return 'Chưa có địa chỉ';
        }

        $address = [];
        if ($landPlot->house_number) {
            $address[] = $landPlot->house_number;
        }

        if ($landPlot->street_name) {
            $address[] = $landPlot->street_name;
        }

        if ($landPlot->ward) {
            $address[] = $landPlot->ward;
        }

        if ($landPlot->district) {
            $address[] = $landPlot->district;
        }

        if ($landPlot->province) {
            $address[] = $landPlot->province;
        }

        return implode(', ', $address) ?: 'Chưa có địa chỉ';
    }

    /**
     * Get asset summary information
     */
    private function getAssetSummary(Asset $asset): string
    {
        $summary = [];

        if ($asset->house) {
            if ($asset->house->construction_area) {
                $summary[] = "Diện tích: {$asset->house->construction_area}m²";
            }
            if ($asset->house->number_of_floors) {
                $summary[] = "Số tầng: {$asset->house->number_of_floors}";
            }
        }

        if ($asset->apartment) {
            if ($asset->apartment->apartment_number) {
                $summary[] = "Căn hộ số: {$asset->apartment->apartment_number}";
            }
            if ($asset->apartment->apartment_floor) {
                $summary[] = "Tầng: {$asset->apartment->apartment_floor}";
            }
        }

        if ($asset->vehicle) {
            if ($asset->vehicle->license_plate) {
                $summary[] = "Biển số: {$asset->vehicle->license_plate}";
            }
            if ($asset->vehicle->brand) {
                $summary[] = "Hãng: {$asset->vehicle->brand}";
            }
        }

        return implode(' | ', $summary) ?: 'Không có thông tin chi tiết';
    }

    /**
     * Get detail sections for show view
     */
    private function getDetailSections(Asset $asset): array
    {
        $sections = [];

        // Certificate section
        if ($asset->certificates->isNotEmpty()) {
            $certificate             = $asset->certificates->first();
            $sections['certificate'] = [
                'title' => 'Thông tin Giấy Chứng Nhận',
                'data'  => [
                    'Loại giấy chứng nhận' => $certificate->certificateType ? $certificate->certificateType->name : 'Chưa xác định',
                    'Nơi cấp'              => $certificate->issuingAuthority ? $certificate->issuingAuthority->name : 'Chưa xác định', // Thêm nơi cấp
                    'Số phát hành'         => $certificate->issue_number,
                    'Số vào sổ'            => $certificate->book_number,
                    'Ngày cấp'             => $certificate->issue_date?->format('d/m/Y'),
                ],
            ];
        }

        // Land plot section
        if ($asset->landPlots->isNotEmpty()) {
            $landPlot              = $asset->landPlots->first();
            $sections['land_plot'] = [
                'title' => 'Thông tin Thửa Đất',
                'data'  => [
                    'Thửa đất số'       => $landPlot->plot_number,
                    'Tờ bản đồ số'      => $landPlot->map_sheet_number,
                    'Số nhà'            => $landPlot->house_number,
                    'Tên đường'         => $landPlot->street_name,
                    'Phường/Xã'         => $landPlot->ward,
                    'Quận/Huyện'        => $landPlot->district,
                    'Tỉnh/Thành'        => $landPlot->province,
                    'Diện tích'         => $landPlot->area ? $landPlot->area . ' m²' : null,
                    'Hình thức sử dụng' => $landPlot->usage_form,
                    'Mục đích sử dụng'  => $landPlot->usage_purpose,
                    'Thời hạn sử dụng'  => $landPlot->land_use_term?->format('d/m/Y'),
                    'Nguồn gốc sử dụng' => $landPlot->usage_origin,
                ],
            ];
        }

        // House section
        if ($asset->house) {
            $house             = $asset->house;
            $sections['house'] = [
                'title' => 'Thông tin Nhà Ở',
                'data'  => [
                    'Loại nhà ở'         => $house->house_type,
                    'Diện tích xây dựng' => $house->construction_area ? $house->construction_area . ' m²' : null,
                    'Diện tích sàn'      => $house->floor_area ? $house->floor_area . ' m²' : null,
                    'Hình thức sở hữu'   => $house->ownership_form,
                    'Cấp (Hạng)'         => $house->grade_level,
                    'Số tầng'            => $house->number_of_floors,
                    'Thời hạn sở hữu'    => $house->ownership_term?->format('d/m/Y'),
                    'Kết cấu'            => $house->structure,
                ],
            ];
        }

        // Apartment section
        if ($asset->apartment) {
            $apartment             = $asset->apartment;
            $sections['apartment'] = [
                'title' => 'Thông tin Căn Hộ',
                'data'  => [
                    'Căn hộ số'            => $apartment->apartment_number,
                    'Căn hộ thuộc tầng'    => $apartment->apartment_floor,
                    'Số tầng nhà chung cư' => $apartment->building_floors,
                    'Diện tích xây dựng'   => $apartment->construction_area ? $apartment->construction_area . ' m²' : null,
                    'Diện tích sàn'        => $apartment->floor_area ? $apartment->floor_area . ' m²' : null,
                    'Hình thức sở hữu'     => $apartment->ownership_form,
                    'Thời hạn sở hữu'      => $apartment->ownership_term?->format('d/m/Y'),
                    'Kết cấu'              => $apartment->structure,
                ],
            ];
        }

        // Vehicle section
        if ($asset->vehicle) {
            $vehicle             = $asset->vehicle;
            $sections['vehicle'] = [
                'title' => 'Thông tin Phương Tiện',
                'data'  => [
                    'Giấy đăng ký số' => $vehicle->registration_number,
                    'Nơi cấp'         => $vehicle->issuingAuthority ? $vehicle->issuingAuthority->name : 'Chưa xác định',
                    'Ngày cấp'        => $vehicle->issue_date?->format('d/m/Y'),
                    'Biển kiểm soát'  => $vehicle->license_plate,
                    'Nhãn hiệu'       => $vehicle->brand,
                    'Loại xe'         => $vehicle->vehicle_type,
                    'Màu sơn'         => $vehicle->color,
                    'Trọng tải'       => $vehicle->payload ? $vehicle->payload . ' tấn' : null,
                    'Số máy'          => $vehicle->engine_number,
                    'Số khung'        => $vehicle->chassis_number,
                    'Số loại'         => $vehicle->type_number,
                    'Dung tích'       => $vehicle->engine_capacity ? $vehicle->engine_capacity . ' L' : null,
                    'Số chỗ ngồi'     => $vehicle->seating_capacity,
                ],
            ];
        }

        return $sections;
    }

    /**
     * Check if user can edit asset
     */
    private function canEditAsset(Asset $asset): bool
    {
        // Admin có thể edit tất cả
        if (auth()->user()->hasRole('admin')) {
            return true;
        }

        // User chỉ có thể edit tài sản do mình tạo
        return $asset->created_by === auth()->id();
    }

    /**
     * Check if user can delete asset
     */
    private function canDeleteAsset(Asset $asset): bool
    {
        // Admin có thể delete tất cả
        if (auth()->user()->hasRole('admin')) {
            return true;
        }

        // User chỉ có thể delete tài sản do mình tạo
        return $asset->created_by === auth()->id();
    }

    /**
     * Get asset statistics for dashboard
     */
    public function getStatistics(): JsonResponse
    {
        $userId = auth()->id();

        $stats = [
            'total_assets'      => Asset::count(),
            'my_assets'         => Asset::where('created_by', $userId)->count(),
            'real_estate_count' => Asset::whereIn('asset_type', [
                'real_estate_house',
                'real_estate_apartment',
                'real_estate_land_only',
            ])->count(),
            'vehicle_count'     => Asset::whereIn('asset_type', [
                'movable_property_car',
                'movable_property_motorcycle',
            ])->count(),
            'my_real_estate'    => Asset::where('created_by', $userId)
                ->whereIn('asset_type', [
                    'real_estate_house',
                    'real_estate_apartment',
                    'real_estate_land_only',
                ])->count(),
            'my_vehicles'       => Asset::where('created_by', $userId)
                ->whereIn('asset_type', [
                    'movable_property_car',
                    'movable_property_motorcycle',
                ])->count(),
            'assets_by_type'    => Asset::selectRaw('asset_type, COUNT(*) as count')
                ->groupBy('asset_type')
                ->get()
                ->mapWithKeys(function ($item) {
                    return [AssetTypeEnum::label($item->asset_type) => $item->count];
                }),
            'recent_assets'     => Asset::latest()
                ->with(['creator:id,name'])
                ->limit(5)
                ->get()
                ->map(function ($asset) {
                    return [
                        'id'         => $asset->id,
                        'name'       => $this->getAssetDisplayName($asset),
                        'type'       => AssetTypeEnum::label($asset->asset_type),
                        'creator'    => $asset->creator ? $asset->creator->name : 'Hệ thống',
                        'created_at' => $asset->created_at->format('d/m/Y H:i'),
                    ];
                }),
            'top_contributors'  => User::withCount('createdAssets')
                ->having('created_assets_count', '>', 0)
                ->orderBy('created_assets_count', 'desc')
                ->limit(5)
                ->get()
                ->map(function ($user) {
                    return [
                        'name'  => $user->name,
                        'count' => $user->created_assets_count,
                    ];
                }),
        ];

        return response()->json($stats);
    }

    /**
     * Search assets (for autocomplete/typeahead)
     */
    public function search(Request $request): JsonResponse
    {
        $query = $request->get('q', '');

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $assets = Asset::where('notes', 'like', "%{$query}%")
            ->orWhereHas('landPlots', function ($q) use ($query) {
                $q->where('street_name', 'like', "%{$query}%")
                    ->orWhere('house_number', 'like', "%{$query}%");
            })
            ->orWhereHas('vehicle', function ($q) use ($query) {
                $q->where('license_plate', 'like', "%{$query}%")
                    ->orWhere('brand', 'like', "%{$query}%");
            })
            ->limit(10)
            ->with($this->getBasicRelations())
            ->get()
            ->map(function ($asset) {
                return [
                    'id'   => $asset->id,
                    'text' => $this->getAssetDisplayName($asset),
                    'type' => AssetTypeEnum::label($asset->asset_type),
                    'url'  => route('properties.show', $asset),
                ];
            });

        return response()->json($assets);
    }

    /**
     * Get basic relations for loading
     */
    private function getBasicRelations(): array
    {
        return [
            'landPlots',
            'vehicle',
            'apartment',
            'house',
        ];
    }

    /**
     * API Search assets for autocomplete
     */
    public function apiSearch(Request $request): JsonResponse
    {
        $query = $request->get('q', '');

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $assets = Asset::with(['landPlots', 'vehicle', 'apartment', 'house'])
            ->where(function ($q) use ($query) {
                // Tìm theo notes/description
                $q->where('notes', 'like', "%{$query}%");

                // Tìm theo địa chỉ land plot
                $q->orWhereHas('landPlots', function ($subQ) use ($query) {
                    $subQ->where('street_name', 'like', "%{$query}%")
                        ->orWhere('house_number', 'like', "%{$query}%")
                        ->orWhere('address', 'like', "%{$query}%");
                });

                // Tìm theo thông tin xe
                $q->orWhereHas('vehicle', function ($subQ) use ($query) {
                    $subQ->where('license_plate', 'like', "%{$query}%")
                        ->orWhere('brand', 'like', "%{$query}%")
                        ->orWhere('model', 'like', "%{$query}%");
                });

                // Tìm theo thông tin căn hộ
                $q->orWhereHas('apartment', function ($subQ) use ($query) {
                    $subQ->where('apartment_number', 'like', "%{$query}%")
                        ->orWhere('building_name', 'like', "%{$query}%")
                        ->orWhere('address', 'like', "%{$query}%");
                });

                // Tìm theo thông tin nhà
                $q->orWhereHas('house', function ($subQ) use ($query) {
                    $subQ->where('house_number', 'like', "%{$query}%")
                        ->orWhere('street_name', 'like', "%{$query}%")
                        ->orWhere('address', 'like', "%{$query}%");
                });
            })
            ->limit(20)
            ->get()
            ->map(function ($asset) {
                $displayName = $this->getAssetDisplayName($asset);

                return [
                    'id'         => $asset->id,
                    'text'       => $displayName,
                    'asset_type' => $asset->asset_type,
                    'notes'      => $asset->notes,
                ];
            });

        return response()->json($assets);
    }
}
