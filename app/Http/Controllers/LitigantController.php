<?php

// app/Http/Controllers/LitigantController.php
namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\CreditInstitution;
use App\Models\CreditInstitutionAdditionalInfo;
use App\Models\IndividualLitigant;
use App\Models\Litigant;
use App\Models\MarriageInformation;
use App\Models\Organization;
use App\Models\OrganizationAdditionalInfo;
use App\Models\RegistrationRepresentative;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LitigantController extends Controller
{
    /**
     * Display a listing of the litigants.
     */
    public function index(Request $request)
    {
        $query = Litigant::with('user');

        // Search functionality
        if ($request->filled('search')) {
            $query->where('full_name', 'like', '%' . $request->search . '%');
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by department
        if ($request->filled('department')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('department', $request->department);
            });
        }

        $litigants = $query->orderBy('updated_at', 'desc')->paginate(15);

        // Get departments for filter
        $departments = User::whereNotNull('department')
            ->distinct()
            ->pluck('department');

        // Get statistics
        $stats = [
            'individual'         => Litigant::where('type', 'individual')->count(),
            'organization'       => Litigant::where('type', 'organization')->count(),
            'credit_institution' => Litigant::where('type', 'credit_institution')->count(),
        ];

        return view('litigants.index', compact('litigants', 'departments', 'stats'));
    }

    /**
     * Show the form for creating a new litigant.
     */
    public function create()
    {
        $litigantTypes      = Litigant::TYPES;
        $availableLitigants = Litigant::select('id', 'full_name', 'type')->get();

        return view('litigants.create', compact('litigantTypes', 'availableLitigants'));
    }

    /**
     * Store a newly created litigant.
     */
    public function store(Request $request)
    {
        $validated = $this->validateLitigant($request);

        DB::transaction(function () use ($validated) {
            // Tạo Litigant chính
            $litigant = Litigant::create([
                'full_name' => $validated['full_name'],
                'type'      => $validated['type'],
                'user_id'   => Auth::id(),
                'notes'     => $validated['notes'] ?? null,
            ]);

            // Xử lý theo loại đương sự
            switch ($validated['type']) {
                case Litigant::TYPE_INDIVIDUAL:
                    $this->storeIndividual($litigant, $validated);
                    break;
                case Litigant::TYPE_ORGANIZATION:
                    $this->storeOrganization($litigant, $validated);
                    break;
                case Litigant::TYPE_CREDIT_INSTITUTION:
                    $this->storeCreditInstitution($litigant, $validated);
                    break;
            }
        });

        return redirect()->route('litigants.index')
            ->with('success', 'Litigant created successfully.');
    }

    /**
     * Display the specified litigant.
     */
    public function show(Litigant $litigant)
    {
        $litigant->load(['user', 'addresses']);

        switch ($litigant->type) {
            case Litigant::TYPE_INDIVIDUAL:
                $litigant->load(['individualLitigant', 'marriageInformation']);
                break;
            case Litigant::TYPE_ORGANIZATION:
                $litigant->load(['organization.additionalInfo', 'organization.registrationRepresentatives']);
                break;
            case Litigant::TYPE_CREDIT_INSTITUTION:
                $litigant->load(['creditInstitution.additionalInfo', 'creditInstitution.registrationRepresentatives']);
                break;
        }

        return view('litigants.show', compact('litigant'));
    }

    /**
     * Show the form for editing the specified litigant.
     */
    public function edit(Litigant $litigant)
    {
        $litigantTypes      = Litigant::TYPES;
        $availableLitigants = Litigant::where('id', '!=', $litigant->id)
            ->select('id', 'full_name', 'type')
            ->get();

        $litigant->load(['addresses']);

        switch ($litigant->type) {
            case Litigant::TYPE_INDIVIDUAL:
                $litigant->load(['individualLitigant', 'marriageInformation']);
                break;
            case Litigant::TYPE_ORGANIZATION:
                $litigant->load(['organization.additionalInfo', 'organization.registrationRepresentatives']);
                break;
            case Litigant::TYPE_CREDIT_INSTITUTION:
                $litigant->load(['creditInstitution.additionalInfo', 'creditInstitution.registrationRepresentatives']);
                break;
        }

        return view('litigants.edit', compact('litigant', 'litigantTypes', 'availableLitigants'));
    }

    /**
     * Update the specified litigant.
     */
    public function update(Request $request, Litigant $litigant)
    {
        $validated = $this->validateLitigant($request);

        DB::transaction(function () use ($litigant, $validated) {
            // Cập nhật Litigant chính
            $litigant->update([
                'full_name' => $validated['full_name'],
                'type'      => $validated['type'],
                'user_id'   => Auth::id(), // Cập nhật người sửa
                'notes'     => $validated['notes'] ?? null,
            ]);

            // Xóa dữ liệu cũ nếu thay đổi loại
            $this->cleanupOldData($litigant, $validated['type']);

            // Xử lý theo loại đương sự
            switch ($validated['type']) {
                case Litigant::TYPE_INDIVIDUAL:
                    $this->updateIndividual($litigant, $validated);
                    break;
                case Litigant::TYPE_ORGANIZATION:
                    $this->updateOrganization($litigant, $validated);
                    break;
                case Litigant::TYPE_CREDIT_INSTITUTION:
                    $this->updateCreditInstitution($litigant, $validated);
                    break;
            }
        });

        return redirect()->route('litigants.index')
            ->with('success', 'Litigant updated successfully.');
    }

    /**
     * Remove the specified litigant.
     */
    public function destroy(Litigant $litigant)
    {
        $litigant->delete();

        return redirect()->route('litigants.index')
            ->with('success', 'Litigant deleted successfully.');
    }

    /**
     * Validate litigant data
     */
    private function validateLitigant(Request $request)
    {
        $rules = [
            'full_name' => 'required|string|max:255',
            'type'      => 'required|in:' . implode(',', array_keys(Litigant::TYPES)),
            'notes'     => 'nullable|string',
        ];

        // Validation theo loại
        switch ($request->type) {
            case Litigant::TYPE_INDIVIDUAL:
                $rules = array_merge($rules, $this->getIndividualValidationRules());
                break;
            case Litigant::TYPE_ORGANIZATION:
                $rules = array_merge($rules, $this->getOrganizationValidationRules());
                break;
            case Litigant::TYPE_CREDIT_INSTITUTION:
                $rules = array_merge($rules, $this->getCreditInstitutionValidationRules());
                break;
        }

        return $request->validate($rules);
    }

    /**
     * Individual validation rules
     */
    private function getIndividualValidationRules()
    {
        return [
            'birth_date'                     => 'nullable|date',
            'gender'                         => 'nullable|in:' . implode(',', array_keys(IndividualLitigant::GENDERS)),
            'nationality'                    => 'nullable|string|max:255',
            'phone_number'                   => 'nullable|string|max:20',
            'email'                          => 'nullable|email|max:255',
            'status'                         => 'nullable|in:' . implode(',', array_keys(IndividualLitigant::STATUSES)),
            'marital_status'                 => 'nullable|in:' . implode(',', array_keys(IndividualLitigant::MARITAL_STATUSES)),
            'marriage_certificate_number'    => 'nullable|string|max:255',
            'marriage_certificate_date'      => 'nullable|date',
            'marriage_certificate_issued_by' => 'nullable|string|max:255',
            'marriage_notes'                 => 'nullable|string',

            // giấy tờ tùy thân
            'identity_documents' => 'nullable|array',
            'identity_documents.*.document_type' => 'nullable|in:cccd,cmnd,passport,officer_id,student_card',
            'identity_documents.*.document_number' => 'nullable|string|max:255',
            'identity_documents.*.issue_date' => 'nullable|date',
            'identity_documents.*.issued_by' => 'nullable|string|max:255',
            'identity_documents.*.school_name' => 'nullable|string|max:255',
            'identity_documents.*.academic_year' => 'nullable|string|max:255',

            // Địa chỉ thường trú
            'permanent_street_address'       => 'nullable|string|max:255',
            'permanent_province'             => 'nullable|string|max:255',
            'permanent_district'             => 'nullable|string|max:255',
            'permanent_ward'                 => 'nullable|string|max:255',

            // Địa chỉ tạm trú
            'temporary_street_address'       => 'nullable|string|max:255',
            'temporary_province'             => 'nullable|string|max:255',
            'temporary_district'             => 'nullable|string|max:255',
            'temporary_ward'                 => 'nullable|string|max:255',

            // Thông tin kết hôn
            'same_household'                 => 'nullable|boolean',
            'spouse_id'                      => 'nullable|exists:litigants,id',
            'marriage_registration_number'   => 'nullable|string|max:255',
            'marriage_issue_date'            => 'nullable|date',
            'marriage_issued_by'             => 'nullable|string|max:255',
            'is_divorced'                    => 'nullable|boolean',
        ];
    }

    /**
     * Organization validation rules
     */
    private function getOrganizationValidationRules()
    {
        return [
            'business_type'                  => 'nullable|string|max:255',
            'org_phone_number'               => 'nullable|string|max:20',
            'organization_type'              => 'nullable|in:' . implode(',', array_keys(Organization::TYPES)),
            'license_type'                   => 'nullable|string|max:255',
            'license_number'                 => 'nullable|string|max:255',
            'business_registration_date'     => 'nullable|date',
            'issuing_authority'              => 'nullable|string|max:255',
            'representative_id'              => 'nullable|exists:litigants,id',
            'representative_position'        => 'nullable|string|max:255',

            // Địa chỉ trụ sở
            'headquarters_street_address'    => 'nullable|string|max:255',
            'headquarters_province'          => 'nullable|string|max:255',
            'headquarters_district'          => 'nullable|string|max:255',
            'headquarters_ward'              => 'nullable|string|max:255',

            // Thông tin bổ sung
            'former_name'                    => 'nullable|string|max:255',
            'account_number'                 => 'nullable|string|max:255',
            'fax'                            => 'nullable|string|max:255',
            'org_email'                      => 'nullable|email|max:255',
            'website'                        => 'nullable|url|max:255',
            'change_registration_number'     => 'nullable|integer',
            'change_registration_date'       => 'nullable|date',

            // Đại diện đăng ký
            'registration_representative_id' => 'nullable|exists:litigants,id',
            'registration_position'          => 'nullable|string|max:255',
            'legal_basis'                    => 'nullable|string',
        ];
    }

    /**
     * Credit Institution validation rules
     */
    private function getCreditInstitutionValidationRules()
    {
        return [
            'ci_business_type'                  => 'nullable|string|max:255',
            'ci_phone_number'                   => 'nullable|string|max:20',
            'ci_organization_type'              => 'nullable|in:' . implode(',', array_keys(CreditInstitution::TYPES)),
            'ci_license_type'                   => 'nullable|string|max:255',
            'ci_license_number'                 => 'nullable|string|max:255',
            'ci_business_registration_date'     => 'nullable|date',
            'ci_issuing_authority'              => 'nullable|string|max:255',
            'ci_representative_id'              => 'nullable|exists:litigants,id',
            'ci_representative_position'        => 'nullable|string|max:255',

            // Địa chỉ trụ sở
            'ci_headquarters_street_address'    => 'nullable|string|max:255',
            'ci_headquarters_province'          => 'nullable|string|max:255',
            'ci_headquarters_district'          => 'nullable|string|max:255',
            'ci_headquarters_ward'              => 'nullable|string|max:255',

            // Thông tin bổ sung
            'ci_former_name'                    => 'nullable|string|max:255',
            'ci_account_number'                 => 'nullable|string|max:255',
            'ci_fax'                            => 'nullable|string|max:255',
            'ci_email'                          => 'nullable|email|max:255',
            'ci_website'                        => 'nullable|url|max:255',
            'ci_change_registration_number'     => 'nullable|integer',
            'ci_change_registration_date'       => 'nullable|date',

            // Đại diện đăng ký
            'ci_registration_representative_id' => 'nullable|exists:litigants,id',
            'ci_registration_position'          => 'nullable|string|max:255',
            'ci_legal_basis'                    => 'nullable|string',
        ];
    }

    /**
     * Store individual litigant data
     */
    private function storeIndividual(Litigant $litigant, array $data)
    {
        // Tạo individual litigant
        IndividualLitigant::create([
            'litigant_id'                    => $litigant->id,
            'birth_date'                     => $data['birth_date'] ?? null,
            'gender'                         => $data['gender'] ?? null,
            'nationality'                    => $data['nationality'] ?? null,
            'phone_number'                   => $data['phone_number'] ?? null,
            'email'                          => $data['email'] ?? null,
            'status'                         => $data['status'] ?? 'alive',
            'marital_status'                 => $data['marital_status'] ?? 'single',
            'marriage_certificate_number'    => $data['marriage_certificate_number'] ?? null,
            'marriage_certificate_date'      => $data['marriage_certificate_date'] ?? null,
            'marriage_certificate_issued_by' => $data['marriage_certificate_issued_by'] ?? null,
            'marriage_notes'                 => $data['marriage_notes'] ?? null,
        ]);

        // Tạo địa chỉ thường trú
        if (! empty($data['permanent_street_address'])) {
            Address::create([
                'addressable_type' => Litigant::class,
                'addressable_id'   => $litigant->id,
                'address_type'     => Address::TYPE_PERMANENT,
                'street_address'   => $data['permanent_street_address'],
                'province'         => $data['permanent_province'] ?? null,
                'district'         => $data['permanent_district'] ?? null,
                'ward'             => $data['permanent_ward'] ?? null,
            ]);
        }

        // Tạo địa chỉ tạm trú
        if (! empty($data['temporary_street_address'])) {
            Address::create([
                'addressable_type' => Litigant::class,
                'addressable_id'   => $litigant->id,
                'address_type'     => Address::TYPE_TEMPORARY,
                'street_address'   => $data['temporary_street_address'],
                'province'         => $data['temporary_province'] ?? null,
                'district'         => $data['temporary_district'] ?? null,
                'ward'             => $data['temporary_ward'] ?? null,
            ]);
        }

        // Tạo thông tin kết hôn
        if (! empty($data['spouse_id']) || ! empty($data['marriage_registration_number'])) {
            MarriageInformation::create([
                'litigant_id'                  => $litigant->id,
                'same_household'               => $data['same_household'] ?? false,
                'spouse_id'                    => $data['spouse_id'] ?? null,
                'marriage_registration_number' => $data['marriage_registration_number'] ?? null,
                'issue_date'                   => $data['marriage_issue_date'] ?? null,
                'issued_by'                    => $data['marriage_issued_by'] ?? null,
                'is_divorced'                  => $data['is_divorced'] ?? false,
            ]);
        }
    }

    /**
     * Store organization litigant data
     */
    private function storeOrganization(Litigant $litigant, array $data)
    {
        // Tạo organization
        $organization = Organization::create([
            'litigant_id'                => $litigant->id,
            'business_type'              => $data['business_type'] ?? null,
            'phone_number'               => $data['org_phone_number'] ?? null,
            'organization_type'          => $data['organization_type'] ?? null,
            'license_type'               => $data['license_type'] ?? null,
            'license_number'             => $data['license_number'] ?? null,
            'business_registration_date' => $data['business_registration_date'] ?? null,
            'issuing_authority'          => $data['issuing_authority'] ?? null,
            'representative_id'          => $data['representative_id'] ?? null,
            'representative_position'    => $data['representative_position'] ?? null,
        ]);

        // Tạo địa chỉ trụ sở
        if (! empty($data['headquarters_street_address'])) {
            Address::create([
                'addressable_type' => Litigant::class,
                'addressable_id'   => $litigant->id,
                'address_type'     => Address::TYPE_HEADQUARTERS,
                'street_address'   => $data['headquarters_street_address'],
                'province'         => $data['headquarters_province'] ?? null,
                'district'         => $data['headquarters_district'] ?? null,
                'ward'             => $data['headquarters_ward'] ?? null,
            ]);
        }

        // Tạo thông tin bổ sung
        OrganizationAdditionalInfo::create([
            'organization_id'            => $organization->id,
            'former_name'                => $data['former_name'] ?? null,
            'account_number'             => $data['account_number'] ?? null,
            'fax'                        => $data['fax'] ?? null,
            'email'                      => $data['org_email'] ?? null,
            'website'                    => $data['website'] ?? null,
            'change_registration_number' => $data['change_registration_number'] ?? null,
            'change_registration_date'   => $data['change_registration_date'] ?? null,
        ]);

        // Tạo đại diện đăng ký
        if (! empty($data['registration_representative_id'])) {
            RegistrationRepresentative::create([
                'representable_type' => Organization::class,
                'representable_id'   => $organization->id,
                'representative_id'  => $data['registration_representative_id'],
                'position'           => $data['registration_position'] ?? null,
                'legal_basis'        => $data['legal_basis'] ?? null,
            ]);
        }
    }

    /**
     * Store credit institution litigant data
     */
    private function storeCreditInstitution(Litigant $litigant, array $data)
    {
        // Tạo credit institution
        $creditInstitution = CreditInstitution::create([
            'litigant_id'                => $litigant->id,
            'business_type'              => $data['ci_business_type'] ?? null,
            'phone_number'               => $data['ci_phone_number'] ?? null,
            'organization_type'          => $data['ci_organization_type'] ?? null,
            'license_type'               => $data['ci_license_type'] ?? null,
            'license_number'             => $data['ci_license_number'] ?? null,
            'business_registration_date' => $data['ci_business_registration_date'] ?? null,
            'issuing_authority'          => $data['ci_issuing_authority'] ?? null,
            'representative_id'          => $data['ci_representative_id'] ?? null,
            'representative_position'    => $data['ci_representative_position'] ?? null,
        ]);

        // Tạo địa chỉ trụ sở
        if (! empty($data['ci_headquarters_street_address'])) {
            Address::create([
                'addressable_type' => Litigant::class,
                'addressable_id'   => $litigant->id,
                'address_type'     => Address::TYPE_HEADQUARTERS,
                'street_address'   => $data['ci_headquarters_street_address'],
                'province'         => $data['ci_headquarters_province'] ?? null,
                'district'         => $data['ci_headquarters_district'] ?? null,
                'ward'             => $data['ci_headquarters_ward'] ?? null,
            ]);
        }

        // Tạo thông tin bổ sung
        CreditInstitutionAdditionalInfo::create([
            'credit_institution_id'      => $creditInstitution->id,
            'former_name'                => $data['ci_former_name'] ?? null,
            'account_number'             => $data['ci_account_number'] ?? null,
            'fax'                        => $data['ci_fax'] ?? null,
            'email'                      => $data['ci_email'] ?? null,
            'website'                    => $data['ci_website'] ?? null,
            'change_registration_number' => $data['ci_change_registration_number'] ?? null,
            'change_registration_date'   => $data['ci_change_registration_date'] ?? null,
        ]);

        // Tạo đại diện đăng ký
        if (! empty($data['ci_registration_representative_id'])) {
            RegistrationRepresentative::create([
                'representable_type' => CreditInstitution::class,
                'representable_id'   => $creditInstitution->id,
                'representative_id'  => $data['ci_registration_representative_id'],
                'position'           => $data['ci_registration_position'] ?? null,
                'legal_basis'        => $data['ci_legal_basis'] ?? null,
            ]);
        }
    }

    /**
     * Update individual litigant data
     */
    private function updateIndividual(Litigant $litigant, array $data)
    {
        // Cập nhật hoặc tạo individual litigant
        $litigant->individualLitigant()->updateOrCreate(
            ['litigant_id' => $litigant->id],
            [
                'birth_date'                     => $data['birth_date'] ?? null,
                'gender'                         => $data['gender'] ?? null,
                'nationality'                    => $data['nationality'] ?? null,
                'phone_number'                   => $data['phone_number'] ?? null,
                'email'                          => $data['email'] ?? null,
                'status'                         => $data['status'] ?? 'alive',
                'marital_status'                 => $data['marital_status'] ?? 'single',
                'marriage_certificate_number'    => $data['marriage_certificate_number'] ?? null,
                'marriage_certificate_date'      => $data['marriage_certificate_date'] ?? null,
                'marriage_certificate_issued_by' => $data['marriage_certificate_issued_by'] ?? null,
                'marriage_notes'                 => $data['marriage_notes'] ?? null,
            ]
        );

        // Cập nhật địa chỉ
        $this->updateAddresses($litigant, $data, 'individual');

        // Cập nhật thông tin kết hôn
        if (! empty($data['spouse_id']) || ! empty($data['marriage_registration_number'])) {
            $litigant->marriageInformation()->updateOrCreate(
                ['litigant_id' => $litigant->id],
                [
                    'same_household'               => $data['same_household'] ?? false,
                    'spouse_id'                    => $data['spouse_id'] ?? null,
                    'marriage_registration_number' => $data['marriage_registration_number'] ?? null,
                    'issue_date'                   => $data['marriage_issue_date'] ?? null,
                    'issued_by'                    => $data['marriage_issued_by'] ?? null,
                    'is_divorced'                  => $data['is_divorced'] ?? false,
                ]
            );
        }
    }

    /**
     * Update organization litigant data
     */
    private function updateOrganization(Litigant $litigant, array $data)
    {
        // Cập nhật hoặc tạo organization
        $organization = $litigant->organization()->updateOrCreate(
            ['litigant_id' => $litigant->id],
            [
                'business_type'              => $data['business_type'] ?? null,
                'phone_number'               => $data['org_phone_number'] ?? null,
                'organization_type'          => $data['organization_type'] ?? null,
                'license_type'               => $data['license_type'] ?? null,
                'license_number'             => $data['license_number'] ?? null,
                'business_registration_date' => $data['business_registration_date'] ?? null,
                'issuing_authority'          => $data['issuing_authority'] ?? null,
                'representative_id'          => $data['representative_id'] ?? null,
                'representative_position'    => $data['representative_position'] ?? null,
            ]
        );

        // Cập nhật địa chỉ
        $this->updateAddresses($litigant, $data, 'organization');

        // Cập nhật thông tin bổ sung
        $organization->additionalInfo()->updateOrCreate(
            ['organization_id' => $organization->id],
            [
                'former_name'                => $data['former_name'] ?? null,
                'account_number'             => $data['account_number'] ?? null,
                'fax'                        => $data['fax'] ?? null,
                'email'                      => $data['org_email'] ?? null,
                'website'                    => $data['website'] ?? null,
                'change_registration_number' => $data['change_registration_number'] ?? null,
                'change_registration_date'   => $data['change_registration_date'] ?? null,
            ]
        );

        // Cập nhật đại diện đăng ký
        if (! empty($data['registration_representative_id'])) {
            $organization->registrationRepresentatives()->updateOrCreate(
                [
                    'representable_type' => Organization::class,
                    'representable_id'   => $organization->id,
                ],
                [
                    'representative_id' => $data['registration_representative_id'],
                    'position'          => $data['registration_position'] ?? null,
                    'legal_basis'       => $data['legal_basis'] ?? null,
                ]
            );
        }
    }

    /**
     * Update credit institution litigant data
     */
    private function updateCreditInstitution(Litigant $litigant, array $data)
    {
        // Cập nhật hoặc tạo credit institution
        $creditInstitution = $litigant->creditInstitution()->updateOrCreate(
            ['litigant_id' => $litigant->id],
            [
                'business_type'              => $data['ci_business_type'] ?? null,
                'phone_number'               => $data['ci_phone_number'] ?? null,
                'organization_type'          => $data['ci_organization_type'] ?? null,
                'license_type'               => $data['ci_license_type'] ?? null,
                'license_number'             => $data['ci_license_number'] ?? null,
                'business_registration_date' => $data['ci_business_registration_date'] ?? null,
                'issuing_authority'          => $data['ci_issuing_authority'] ?? null,
                'representative_id'          => $data['ci_representative_id'] ?? null,
                'representative_position'    => $data['ci_representative_position'] ?? null,
            ]
        );

        // Cập nhật địa chỉ
        $this->updateAddresses($litigant, $data, 'credit_institution');

        // Cập nhật thông tin bổ sung
        $creditInstitution->additionalInfo()->updateOrCreate(
            ['credit_institution_id' => $creditInstitution->id],
            [
                'former_name'                => $data['ci_former_name'] ?? null,
                'account_number'             => $data['ci_account_number'] ?? null,
                'fax'                        => $data['ci_fax'] ?? null,
                'email'                      => $data['ci_email'] ?? null,
                'website'                    => $data['ci_website'] ?? null,
                'change_registration_number' => $data['ci_change_registration_number'] ?? null,
                'change_registration_date'   => $data['ci_change_registration_date'] ?? null,
            ]
        );

        // Cập nhật đại diện đăng ký
        if (! empty($data['ci_registration_representative_id'])) {
            $creditInstitution->registrationRepresentatives()->updateOrCreate(
                [
                    'representable_type' => CreditInstitution::class,
                    'representable_id'   => $creditInstitution->id,
                ],
                [
                    'representative_id' => $data['ci_registration_representative_id'],
                    'position'          => $data['ci_registration_position'] ?? null,
                    'legal_basis'       => $data['ci_legal_basis'] ?? null,
                ]
            );
        }
    }

    /**
     * Update addresses for litigant
     */
    private function updateAddresses(Litigant $litigant, array $data, string $type)
    {
        switch ($type) {
            case 'individual':
                // Cập nhật địa chỉ thường trú
                if (! empty($data['permanent_street_address'])) {
                    $litigant->addresses()->updateOrCreate(
                        ['address_type' => Address::TYPE_PERMANENT],
                        [
                            'street_address' => $data['permanent_street_address'],
                            'province'       => $data['permanent_province'] ?? null,
                            'district'       => $data['permanent_district'] ?? null,
                            'ward'           => $data['permanent_ward'] ?? null,
                        ]
                    );
                }

                // Cập nhật địa chỉ tạm trú
                if (! empty($data['temporary_street_address'])) {
                    $litigant->addresses()->updateOrCreate(
                        ['address_type' => Address::TYPE_TEMPORARY],
                        [
                            'street_address' => $data['temporary_street_address'],
                            'province'       => $data['temporary_province'] ?? null,
                            'district'       => $data['temporary_district'] ?? null,
                            'ward'           => $data['temporary_ward'] ?? null,
                        ]
                    );
                }
                break;

            case 'organization':
                // Cập nhật địa chỉ trụ sở
                if (! empty($data['headquarters_street_address'])) {
                    $litigant->addresses()->updateOrCreate(
                        ['address_type' => Address::TYPE_HEADQUARTERS],
                        [
                            'street_address' => $data['headquarters_street_address'],
                            'province'       => $data['headquarters_province'] ?? null,
                            'district'       => $data['headquarters_district'] ?? null,
                            'ward'           => $data['headquarters_ward'] ?? null,
                        ]
                    );
                }
                break;

            case 'credit_institution':
                // Cập nhật địa chỉ trụ sở
                if (! empty($data['ci_headquarters_street_address'])) {
                    $litigant->addresses()->updateOrCreate(
                        ['address_type' => Address::TYPE_HEADQUARTERS],
                        [
                            'street_address' => $data['ci_headquarters_street_address'],
                            'province'       => $data['ci_headquarters_province'] ?? null,
                            'district'       => $data['ci_headquarters_district'] ?? null,
                            'ward'           => $data['ci_headquarters_ward'] ?? null,
                        ]
                    );
                }
                break;
        }
    }

    /**
     * Clean up old data when litigant type changes
     */
    private function cleanupOldData(Litigant $litigant, string $newType)
    {
        $currentType = $litigant->getOriginal('type');

        if ($currentType !== $newType) {
            // Xóa dữ liệu cũ theo loại
            switch ($currentType) {
                case Litigant::TYPE_INDIVIDUAL:
                    $litigant->individualLitigant()->delete();
                    $litigant->marriageInformation()->delete();
                    break;
                case Litigant::TYPE_ORGANIZATION:
                    if ($litigant->organization) {
                        $litigant->organization->additionalInfo()->delete();
                        $litigant->organization->registrationRepresentatives()->delete();
                        $litigant->organization()->delete();
                    }
                    break;
                case Litigant::TYPE_CREDIT_INSTITUTION:
                    if ($litigant->creditInstitution) {
                        $litigant->creditInstitution->additionalInfo()->delete();
                        $litigant->creditInstitution->registrationRepresentatives()->delete();
                        $litigant->creditInstitution()->delete();
                    }
                    break;
            }

            // Xóa tất cả địa chỉ cũ
            $litigant->addresses()->delete();
        }
    }
}
