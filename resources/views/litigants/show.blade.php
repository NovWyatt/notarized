@extends('layouts.app2')
@section('content')
    <div class="container-fluid p-3">
        <!-- Header Section -->
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h1 class="h3 mb-0">Chi tiết Đương sự</h1>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="#" class="text-decoration-none">Trang chủ</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('litigants.index') }}"
                                        class="text-decoration-none">Đương sự</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Chi tiết</li>
                            </ol>
                        </nav>
                    </div>
                    <div>
                        <a href="{{ route('litigants.edit', $litigant) }}" class="btn btn-warning me-2">
                            <i class="fas fa-edit me-2"></i>Chỉnh sửa
                        </a>
                        <a href="{{ route('litigants.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Quay lại
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Basic Information Card -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-info-circle me-2"></i>Thông tin cơ bản
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="info-item mb-3">
                            <label class="form-label fw-bold">Họ và tên:</label>
                            <p class="mb-0">{{ $litigant->full_name }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-item mb-3">
                            <label class="form-label fw-bold">Loại:</label>
                            <p class="mb-0">
                                @switch($litigant->type)
                                    @case('individual')
                                        <span class="badge bg-primary">Cá nhân</span>
                                    @break

                                    @case('organization')
                                        <span class="badge bg-success">Tổ chức</span>
                                    @break

                                    @case('credit_institution')
                                        <span class="badge bg-info">Tổ chức tín dụng</span>
                                    @break
                                @endswitch
                            </p>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="info-item mb-3">
                            <label class="form-label fw-bold">Người tạo:</label>
                            <p class="mb-0">{{ $litigant->user?->name ?? 'N/A' }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-item mb-3">
                            <label class="form-label fw-bold">Ngày tạo:</label>
                            <p class="mb-0">{{ $litigant->created_at?->format('d/m/Y H:i') ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
                @if ($litigant->notes)
                    <div class="row">
                        <div class="col-12">
                            <div class="info-item mb-3">
                                <label class="form-label fw-bold">Ghi chú:</label>
                                <p class="mb-0">{{ $litigant->notes }}</p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        @if ($litigant->type == 'individual')
            @php
                $individual = $litigant->individualLitigant;
                $marriageInfo = $litigant->marriageInformation;
                $permanentAddress = $litigant->addresses->where('address_type', 'permanent')->first();
                $temporaryAddress = $litigant->addresses->where('address_type', 'temporary')->first();
            @endphp

            <!-- Individual Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-user me-2"></i>Thông tin cá nhân
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="info-item mb-3">
                                <label class="form-label fw-bold">Ngày sinh:</label>
                                <p class="mb-0">
                                    {{ $individual?->birth_date ? \Carbon\Carbon::parse($individual->birth_date)->format('d/m/Y') : 'Chưa có thông tin' }}
                                </p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-item mb-3">
                                <label class="form-label fw-bold">Giới tính:</label>
                                <p class="mb-0">
                                    @if ($individual?->gender)
                                        @if ($individual->gender == 'male')
                                            Nam
                                        @elseif($individual->gender == 'female')
                                            Nữ
                                        @endif
                                    @else
                                        Chưa có thông tin
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-item mb-3">
                                <label class="form-label fw-bold">Quốc tịch:</label>
                                <p class="mb-0">{{ $individual?->nationality ?? 'Chưa có thông tin' }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="info-item mb-3">
                                <label class="form-label fw-bold">Số điện thoại:</label>
                                <p class="mb-0">{{ $individual?->phone_number ?? 'Chưa có thông tin' }}</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-item mb-3">
                                <label class="form-label fw-bold">Email:</label>
                                <p class="mb-0">{{ $individual?->email ?? 'Chưa có thông tin' }}</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-item mb-3">
                                <label class="form-label fw-bold">Trạng thái:</label>
                                <p class="mb-0">
                                    @if ($individual?->status)
                                        @switch($individual->status)
                                            @case('alive')
                                                <span class="badge bg-success">Còn sống</span>
                                            @break

                                            @case('deceased')
                                                <span class="badge bg-danger">Đã mất</span>
                                            @break

                                            @case('civil_incapacitated')
                                                <span class="badge bg-warning text-dark">Mất năng lực hành vi dân sự</span>
                                            @break
                                        @endswitch
                                    @else
                                        Chưa có thông tin
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-item mb-3">
                                <label class="form-label fw-bold">Tình trạng hôn nhân:</label>
                                <p class="mb-0">
                                    @if ($individual?->marital_status)
                                        @switch($individual->marital_status)
                                            @case('single')
                                                Độc thân
                                            @break

                                            @case('married')
                                                Đã kết hôn
                                            @break

                                            @case('divorced')
                                                Đã ly hôn
                                            @break
                                        @endswitch
                                    @else
                                        Chưa có thông tin
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item mb-3">
                                <label class="form-label fw-bold">Số giấy chứng nhận kết hôn:</label>
                                <p class="mb-0">{{ $individual?->marriage_certificate_number ?? 'Chưa có thông tin' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Identity Documents -->
            @if ($individual && $individual->identityDocuments && $individual->identityDocuments->count() > 0)
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-id-card me-2"></i>Giấy tờ tùy thân
                        </h5>
                    </div>
                    <div class="card-body">
                        @foreach ($individual->identityDocuments as $document)
                            <div class="identity-document-item border rounded p-3 mb-3">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="info-item mb-3">
                                            <label class="form-label fw-bold">Loại giấy tờ:</label>
                                            <p class="mb-0">
                                                @switch($document->document_type)
                                                    @case('cccd')
                                                        Căn cước công dân (12 số)
                                                    @break

                                                    @case('cmnd')
                                                        Chứng minh nhân dân (9 số)
                                                    @break

                                                    @case('passport')
                                                        Hộ chiếu
                                                    @break

                                                    @case('officer_id')
                                                        Chứng minh sĩ quan
                                                    @break

                                                    @case('student_card')
                                                        Thẻ học sinh
                                                    @break

                                                    @default
                                                        {{ $document->document_type }}
                                                @endswitch
                                            </p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-item mb-3">
                                            <label class="form-label fw-bold">Số giấy tờ:</label>
                                            <p class="mb-0">{{ $document->document_number ?? 'Chưa có thông tin' }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="info-item mb-3">
                                            <label class="form-label fw-bold">Ngày cấp:</label>
                                            <p class="mb-0">
                                                {{ $document->issue_date ? \Carbon\Carbon::parse($document->issue_date)->format('d/m/Y') : 'Chưa có thông tin' }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-item mb-3">
                                            <label class="form-label fw-bold">Nơi cấp:</label>
                                            <p class="mb-0">{{ $document->issued_by ?? 'Chưa có thông tin' }}</p>
                                        </div>
                                    </div>
                                </div>
                                @if ($document->document_type == 'student_card')
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="info-item mb-3">
                                                <label class="form-label fw-bold">Tên trường:</label>
                                                <p class="mb-0">{{ $document->school_name ?? 'Chưa có thông tin' }}</p>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-item mb-3">
                                                <label class="form-label fw-bold">Niên khóa:</label>
                                                <p class="mb-0">{{ $document->academic_year ?? 'Chưa có thông tin' }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Addresses -->
            @if ($permanentAddress || $temporaryAddress)
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-map-marker-alt me-2"></i>Địa chỉ
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="accordion" id="addressAccordion">
                            @if ($permanentAddress)
                                <!-- Permanent Address -->
                                <div class="accordion-item">
                                    <h4 class="accordion-header" id="permanentAddressHeader">
                                        <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                            data-bs-target="#permanentAddress" aria-expanded="true"
                                            aria-controls="permanentAddress">
                                            <i class="fas fa-home me-2"></i>Địa chỉ thường trú
                                        </button>
                                    </h4>
                                    <div id="permanentAddress" class="accordion-collapse collapse show"
                                        aria-labelledby="permanentAddressHeader" data-bs-parent="#addressAccordion">
                                        <div class="accordion-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="info-item mb-3">
                                                        <label class="form-label fw-bold">Địa chỉ:</label>
                                                        <p class="mb-0">
                                                            {{ $permanentAddress->street_address ?? 'Chưa có thông tin' }}
                                                        </p>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="info-item mb-3">
                                                        <label class="form-label fw-bold">Tỉnh/Thành phố:</label>
                                                        <p class="mb-0">
                                                            {{ $permanentAddress->province ?? 'Chưa có thông tin' }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="info-item mb-3">
                                                        <label class="form-label fw-bold">Quận/Huyện:</label>
                                                        <p class="mb-0">
                                                            {{ $permanentAddress->district ?? 'Chưa có thông tin' }}</p>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="info-item mb-3">
                                                        <label class="form-label fw-bold">Phường/Xã:</label>
                                                        <p class="mb-0">
                                                            {{ $permanentAddress->ward ?? 'Chưa có thông tin' }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if ($temporaryAddress)
                                <!-- Temporary Address -->
                                <div class="accordion-item">
                                    <h4 class="accordion-header" id="temporaryAddressHeader">
                                        <button class="accordion-button collapsed" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#temporaryAddress"
                                            aria-expanded="false" aria-controls="temporaryAddress">
                                            <i class="fas fa-map-pin me-2"></i>Địa chỉ tạm trú
                                        </button>
                                    </h4>
                                    <div id="temporaryAddress" class="accordion-collapse collapse"
                                        aria-labelledby="temporaryAddressHeader" data-bs-parent="#addressAccordion">
                                        <div class="accordion-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="info-item mb-3">
                                                        <label class="form-label fw-bold">Địa chỉ:</label>
                                                        <p class="mb-0">
                                                            {{ $temporaryAddress->street_address ?? 'Chưa có thông tin' }}
                                                        </p>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="info-item mb-3">
                                                        <label class="form-label fw-bold">Tỉnh/Thành phố:</label>
                                                        <p class="mb-0">
                                                            {{ $temporaryAddress->province ?? 'Chưa có thông tin' }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="info-item mb-3">
                                                        <label class="form-label fw-bold">Quận/Huyện:</label>
                                                        <p class="mb-0">
                                                            {{ $temporaryAddress->district ?? 'Chưa có thông tin' }}</p>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="info-item mb-3">
                                                        <label class="form-label fw-bold">Phường/Xã:</label>
                                                        <p class="mb-0">
                                                            {{ $temporaryAddress->ward ?? 'Chưa có thông tin' }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            <!-- Marriage Information -->
            @if ($marriageInfo)
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-heart me-2"></i>Thông tin kết hôn
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="info-item mb-3">
                                    <label class="form-label fw-bold">Cùng hộ khẩu:</label>
                                    <p class="mb-0">
                                        @if ($marriageInfo->same_household)
                                            <span class="badge bg-success">Có</span>
                                        @else
                                            <span class="badge bg-secondary">Không</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-item mb-3">
                                    <label class="form-label fw-bold">Đã ly hôn:</label>
                                    <p class="mb-0">
                                        @if ($marriageInfo->is_divorced)
                                            <span class="badge bg-danger">Có</span>
                                        @else
                                            <span class="badge bg-success">Không</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-item mb-3">
                                    <label class="form-label fw-bold">Vợ/Chồng:</label>
                                    <p class="mb-0">{{ $marriageInfo->spouse?->full_name ?? 'Chưa có thông tin' }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="info-item mb-3">
                                    <label class="form-label fw-bold">Số đăng ký kết hôn:</label>
                                    <p class="mb-0">
                                        {{ $marriageInfo->marriage_registration_number ?? 'Chưa có thông tin' }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item mb-3">
                                    <label class="form-label fw-bold">Ngày cấp:</label>
                                    <p class="mb-0">
                                        {{ $marriageInfo->issue_date ? \Carbon\Carbon::parse($marriageInfo->issue_date)->format('d/m/Y') : 'Chưa có thông tin' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="info-item mb-3">
                                    <label class="form-label fw-bold">Nơi cấp:</label>
                                    <p class="mb-0">{{ $marriageInfo->issued_by ?? 'Chưa có thông tin' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @elseif($litigant->type == 'organization')
            @php
                $organization = $litigant->organization;
                $orgAdditionalInfo = $organization?->additionalInfo;
                $orgRepresentative = $organization?->registrationRepresentatives->first();
                $headquartersAddress = $litigant->addresses->where('address_type', 'headquarters')->first();
            @endphp

            <!-- Organization Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-building me-2"></i>Thông tin tổ chức
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-item mb-3">
                                <label class="form-label fw-bold">Loại hình kinh doanh:</label>
                                <p class="mb-0">{{ $organization?->business_type ?? 'Chưa có thông tin' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item mb-3">
                                <label class="form-label fw-bold">Số điện thoại:</label>
                                <p class="mb-0">{{ $organization?->phone_number ?? 'Chưa có thông tin' }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="info-item mb-3">
                                <label class="form-label fw-bold">Loại tổ chức:</label>
                                <p class="mb-0">
                                    @if ($organization?->organization_type)
                                        @switch($organization->organization_type)
                                            @case('headquarters')
                                                Trụ sở chính
                                            @break

                                            @case('branch')
                                                Chi nhánh
                                            @break

                                            @case('transaction_office')
                                                Phòng giao dịch
                                            @break
                                        @endswitch
                                    @else
                                        Chưa có thông tin
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-item mb-3">
                                <label class="form-label fw-bold">Loại giấy phép:</label>
                                <p class="mb-0">{{ $organization?->license_type ?? 'Chưa có thông tin' }}</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-item mb-3">
                                <label class="form-label fw-bold">Số giấy phép:</label>
                                <p class="mb-0">{{ $organization?->license_number ?? 'Chưa có thông tin' }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="info-item mb-3">
                                <label class="form-label fw-bold">Ngày đăng ký kinh doanh:</label>
                                <p class="mb-0">
                                    {{ $organization?->business_registration_date ? \Carbon\Carbon::parse($organization->business_registration_date)->format('d/m/Y') : 'Chưa có thông tin' }}
                                </p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-item mb-3">
                                <label class="form-label fw-bold">Cơ quan cấp phép:</label>
                                <p class="mb-0">{{ $organization?->issuing_authority ?? 'Chưa có thông tin' }}</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-item mb-3">
                                <label class="form-label fw-bold">Người đại diện:</label>
                                <p class="mb-0">{{ $organization?->representative?->full_name ?? 'Chưa có thông tin' }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="info-item mb-3">
                                <label class="form-label fw-bold">Chức vụ đại diện:</label>
                                <p class="mb-0">{{ $organization?->representative_position ?? 'Chưa có thông tin' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Organization Address -->
            @if ($headquartersAddress)
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-map-marker-alt me-2"></i>Địa chỉ trụ sở
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="info-item mb-3">
                                    <label class="form-label fw-bold">Địa chỉ:</label>
                                    <p class="mb-0">{{ $headquartersAddress->street_address ?? 'Chưa có thông tin' }}
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item mb-3">
                                    <label class="form-label fw-bold">Tỉnh/Thành phố:</label>
                                    <p class="mb-0">{{ $headquartersAddress->province ?? 'Chưa có thông tin' }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="info-item mb-3">
                                    <label class="form-label fw-bold">Quận/Huyện:</label>
                                    <p class="mb-0">{{ $headquartersAddress->district ?? 'Chưa có thông tin' }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item mb-3">
                                    <label class="form-label fw-bold">Phường/Xã:</label>
                                    <p class="mb-0">{{ $headquartersAddress->ward ?? 'Chưa có thông tin' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Organization Additional Information -->
            @if ($orgAdditionalInfo)
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-info me-2"></i>Thông tin bổ sung
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="info-item mb-3">
                                    <label class="form-label fw-bold">Tên cũ:</label>
                                    <p class="mb-0">{{ $orgAdditionalInfo->former_name ?? 'Chưa có thông tin' }}</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-item mb-3">
                                    <label class="form-label fw-bold">Số tài khoản:</label>
                                    <p class="mb-0">{{ $orgAdditionalInfo->account_number ?? 'Chưa có thông tin' }}</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-item mb-3">
                                    <label class="form-label fw-bold">Số fax:</label>
                                    <p class="mb-0">{{ $orgAdditionalInfo->fax ?? 'Chưa có thông tin' }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="info-item mb-3">
                                    <label class="form-label fw-bold">Email:</label>
                                    <p class="mb-0">{{ $orgAdditionalInfo->email ?? 'Chưa có thông tin' }}</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-item mb-3">
                                    <label class="form-label fw-bold">Website:</label>
                                    <p class="mb-0">
                                        @if ($orgAdditionalInfo->website)
                                            <a href="{{ $orgAdditionalInfo->website }}" target="_blank"
                                                class="text-decoration-none">
                                                {{ $orgAdditionalInfo->website }}
                                                <i class="fas fa-external-link-alt ms-1"></i>
                                            </a>
                                        @else
                                            Chưa có thông tin
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-item mb-3">
                                    <label class="form-label fw-bold">Số đăng ký thay đổi:</label>
                                    <p class="mb-0">
                                        {{ $orgAdditionalInfo->change_registration_number ?? 'Chưa có thông tin' }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="info-item mb-3">
                                    <label class="form-label fw-bold">Ngày đăng ký thay đổi:</label>
                                    <p class="mb-0">
                                        {{ $orgAdditionalInfo->change_registration_date ? \Carbon\Carbon::parse($orgAdditionalInfo->change_registration_date)->format('d/m/Y') : 'Chưa có thông tin' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Organization Registration Representative -->
            @if ($orgRepresentative)
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-user-tie me-2"></i>Đại diện đăng ký
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="info-item mb-3">
                                    <label class="form-label fw-bold">Người đại diện:</label>
                                    <p class="mb-0">
                                        {{ $orgRepresentative->representative?->full_name ?? 'Chưa có thông tin' }}</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-item mb-3">
                                    <label class="form-label fw-bold">Chức vụ:</label>
                                    <p class="mb-0">{{ $orgRepresentative->position ?? 'Chưa có thông tin' }}</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-item mb-3">
                                    <label class="form-label fw-bold">Căn cứ pháp lý:</label>
                                    <p class="mb-0">{{ $orgRepresentative->legal_basis ?? 'Chưa có thông tin' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @elseif($litigant->type == 'credit_institution')
            @php
                $creditInstitution = $litigant->creditInstitution;
                $ciAdditionalInfo = $creditInstitution?->additionalInfo;
                $ciRepresentative = $creditInstitution?->registrationRepresentatives->first();
                $ciHeadquartersAddress = $litigant->addresses->where('address_type', 'headquarters')->first();
            @endphp

            <!-- Credit Institution Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-university me-2"></i>Thông tin tổ chức tín dụng
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-item mb-3">
                                <label class="form-label fw-bold">Loại hình kinh doanh:</label>
                                <p class="mb-0">{{ $creditInstitution?->business_type ?? 'Chưa có thông tin' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item mb-3">
                                <label class="form-label fw-bold">Số điện thoại:</label>
                                <p class="mb-0">{{ $creditInstitution?->phone_number ?? 'Chưa có thông tin' }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="info-item mb-3">
                                <label class="form-label fw-bold">Loại tổ chức:</label>
                                <p class="mb-0">
                                    @if ($creditInstitution?->organization_type)
                                        @switch($creditInstitution->organization_type)
                                            @case('headquarters')
                                                Trụ sở chính
                                            @break

                                            @case('branch')
                                                Chi nhánh
                                            @break

                                            @case('transaction_office')
                                                Phòng giao dịch
                                            @break
                                        @endswitch
                                    @else
                                        Chưa có thông tin
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-item mb-3">
                                <label class="form-label fw-bold">Loại giấy phép:</label>
                                <p class="mb-0">{{ $creditInstitution?->license_type ?? 'Chưa có thông tin' }}</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-item mb-3">
                                <label class="form-label fw-bold">Số giấy phép:</label>
                                <p class="mb-0">{{ $creditInstitution?->license_number ?? 'Chưa có thông tin' }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="info-item mb-3">
                                <label class="form-label fw-bold">Ngày đăng ký kinh doanh:</label>
                                <p class="mb-0">
                                    {{ $creditInstitution?->business_registration_date ? \Carbon\Carbon::parse($creditInstitution->business_registration_date)->format('d/m/Y') : 'Chưa có thông tin' }}
                                </p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-item mb-3">
                                <label class="form-label fw-bold">Cơ quan cấp phép:</label>
                                <p class="mb-0">{{ $creditInstitution?->issuing_authority ?? 'Chưa có thông tin' }}</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-item mb-3">
                                <label class="form-label fw-bold">Người đại diện:</label>
                                <p class="mb-0">
                                    {{ $creditInstitution?->representative?->full_name ?? 'Chưa có thông tin' }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="info-item mb-3">
                                <label class="form-label fw-bold">Chức vụ đại diện:</label>
                                <p class="mb-0">
                                    {{ $creditInstitution?->representative_position ?? 'Chưa có thông tin' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Credit Institution Address -->
            @if ($ciHeadquartersAddress)
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-map-marker-alt me-2"></i>Địa chỉ trụ sở
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="info-item mb-3">
                                    <label class="form-label fw-bold">Địa chỉ:</label>
                                    <p class="mb-0">{{ $ciHeadquartersAddress->street_address ?? 'Chưa có thông tin' }}
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item mb-3">
                                    <label class="form-label fw-bold">Tỉnh/Thành phố:</label>
                                    <p class="mb-0">{{ $ciHeadquartersAddress->province ?? 'Chưa có thông tin' }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="info-item mb-3">
                                    <label class="form-label fw-bold">Quận/Huyện:</label>
                                    <p class="mb-0">{{ $ciHeadquartersAddress->district ?? 'Chưa có thông tin' }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item mb-3">
                                    <label class="form-label fw-bold">Phường/Xã:</label>
                                    <p class="mb-0">{{ $ciHeadquartersAddress->ward ?? 'Chưa có thông tin' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Credit Institution Additional Information -->
            @if ($ciAdditionalInfo)
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-info me-2"></i>Thông tin bổ sung
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="info-item mb-3">
                                    <label class="form-label fw-bold">Tên cũ:</label>
                                    <p class="mb-0">{{ $ciAdditionalInfo->former_name ?? 'Chưa có thông tin' }}</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-item mb-3">
                                    <label class="form-label fw-bold">Số tài khoản:</label>
                                    <p class="mb-0">{{ $ciAdditionalInfo->account_number ?? 'Chưa có thông tin' }}</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-item mb-3">
                                    <label class="form-label fw-bold">Số fax:</label>
                                    <p class="mb-0">{{ $ciAdditionalInfo->fax ?? 'Chưa có thông tin' }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="info-item mb-3">
                                    <label class="form-label fw-bold">Email:</label>
                                    <p class="mb-0">{{ $ciAdditionalInfo->email ?? 'Chưa có thông tin' }}</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-item mb-3">
                                    <label class="form-label fw-bold">Website:</label>
                                    <p class="mb-0">
                                        @if ($ciAdditionalInfo->website)
                                            <a href="{{ $ciAdditionalInfo->website }}" target="_blank"
                                                class="text-decoration-none">
                                                {{ $ciAdditionalInfo->website }}
                                                <i class="fas fa-external-link-alt ms-1"></i>
                                            </a>
                                        @else
                                            Chưa có thông tin
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-item mb-3">
                                    <label class="form-label fw-bold">Số đăng ký thay đổi:</label>
                                    <p class="mb-0">
                                        {{ $ciAdditionalInfo->change_registration_number ?? 'Chưa có thông tin' }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="info-item mb-3">
                                    <label class="form-label fw-bold">Ngày đăng ký thay đổi:</label>
                                    <p class="mb-0">
                                        {{ $ciAdditionalInfo->change_registration_date ? \Carbon\Carbon::parse($ciAdditionalInfo->change_registration_date)->format('d/m/Y') : 'Chưa có thông tin' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Credit Institution Registration Representative -->
            @if ($ciRepresentative)
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-user-tie me-2"></i>Đại diện đăng ký
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="info-item mb-3">
                                    <label class="form-label fw-bold">Người đại diện:</label>
                                    <p class="mb-0">
                                        {{ $ciRepresentative->representative?->full_name ?? 'Chưa có thông tin' }}</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-item mb-3">
                                    <label class="form-label fw-bold">Chức vụ:</label>
                                    <p class="mb-0">{{ $ciRepresentative->position ?? 'Chưa có thông tin' }}</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-item mb-3">
                                    <label class="form-label fw-bold">Căn cứ pháp lý:</label>
                                    <p class="mb-0">{{ $ciRepresentative->legal_basis ?? 'Chưa có thông tin' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endif

        <!-- Action Buttons -->
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <a href="{{ route('litigants.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Quay lại danh sách
                    </a>
                    <div>
                        <a href="{{ route('litigants.edit', $litigant) }}" class="btn btn-warning me-2">
                            <i class="fas fa-edit me-2"></i>Chỉnh sửa
                        </a>
                        <button type="button" class="btn btn-danger" onclick="confirmDelete()">
                            <i class="fas fa-trash me-2"></i>Xóa
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteModalLabel">Xác nhận xóa</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                    </div>
                    <div class="modal-body">
                        <p>Bạn có chắc chắn muốn xóa đương sự <strong>{{ $litigant->full_name }}</strong> không?</p>
                        <p class="text-danger"><small>Hành động này không thể hoàn tác!</small></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <form action="{{ route('litigants.destroy', $litigant) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Xóa</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/litigant-common.js') }}"></script>
    <script src="{{ asset('js/litigant-show.js') }}"></script>
@endsection
