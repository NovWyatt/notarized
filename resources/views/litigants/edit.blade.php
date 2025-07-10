@extends('layouts.app2')
@section('content')
    <div class="container-fluid p-3">
        <!-- Header Section -->
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h1 class="h3 mb-0">Chỉnh sửa đương sự</h1>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="#" class="text-decoration-none">Trang chủ</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('litigants.index') }}"
                                        class="text-decoration-none">Đương sự</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Chỉnh sửa</li>
                            </ol>
                        </nav>
                    </div>
                    <div>
                        <a href="{{ route('litigants.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Quay lại
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Error Messages -->
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>Please fix the following errors:</strong>
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Main Form -->
        <form action="{{ route('litigants.update', $litigant) }}" method="POST" id="litigantForm">
            @csrf
            @method('PUT')

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
                            <div class="form-floating mb-3">
                                <select class="form-select @error('type') is-invalid @enderror" id="type"
                                    name="type" required onchange="toggleSections()">
                                    <option value="">Chọn loại</option>
                                    <option value="individual"
                                        {{ old('type', $litigant->type) == 'individual' ? 'selected' : '' }}>
                                        Cá Nhân</option>
                                    <option value="organization"
                                        {{ old('type', $litigant->type) == 'organization' ? 'selected' : '' }}>
                                        Tổ chức</option>
                                    <option value="credit_institution"
                                        {{ old('type', $litigant->type) == 'credit_institution' ? 'selected' : '' }}>Tổ chức
                                        tín dụng
                                    </option>
                                </select>
                                <label for="type">Loại <span class="text-danger">*</span></label>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control @error('full_name') is-invalid @enderror"
                                    id="full_name" name="full_name" placeholder="Full Name"
                                    value="{{ old('full_name', $litigant->full_name) }}" required>
                                <label for="full_name">Tên đương sự <span class="text-danger">*</span></label>
                                @error('full_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="form-floating mb-3">
                                <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" placeholder="Notes"
                                    style="height: 100px">{{ old('notes', $litigant->notes) }}</textarea>
                                <label for="notes">Ghi chú</label>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Individual Section -->
            <div id="individualSection" class="{{ $litigant->type != 'individual' ? 'd-none' : '' }}">
                @php
                    $individual = $litigant->individualLitigant;
                    $marriageInfo = $litigant->marriageInformation;
                    $permanentAddress = $litigant->addresses->where('address_type', 'permanent')->first();
                    $temporaryAddress = $litigant->addresses->where('address_type', 'temporary')->first();
                @endphp

                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-user me-2"></i>Thông tin cá nhân
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control @error('birth_date') is-invalid @enderror"
                                        id="birth_date" name="birth_date_display" placeholder="ddmmyyyy" maxlength="10"
                                        value="{{ old('birth_date_display', $individual?->birth_date ? $individual->birth_date->format('d/m/Y') : '') }}">
                                    <label for="birth_date">Ngày sinh (dd/mm/yyyy)</label>
                                    <!-- Hidden input để gửi data đúng format cho server -->
                                    <input type="hidden" id="birth_date_formatted" name="birth_date"
                                        value="{{ old('birth_date', $individual?->birth_date ? $individual->birth_date->format('Y-m-d') : '') }}">
                                    @error('birth_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="invalid-feedback" id="birth_date_error"></div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating mb-3">
                                    <select class="form-select @error('gender') is-invalid @enderror" id="gender"
                                        name="gender">
                                        <option value="">Giới tính</option>
                                        <option value="male"
                                            {{ old('gender', $individual?->gender) == 'male' ? 'selected' : '' }}>Nam
                                        </option>
                                        <option value="female"
                                            {{ old('gender', $individual?->gender) == 'female' ? 'selected' : '' }}>Nữ
                                        </option>
                                    </select>
                                    <label for="gender">Chọn giới tính</label>
                                    @error('gender')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control @error('nationality') is-invalid @enderror"
                                        id="nationality" name="nationality" placeholder="Nationality"
                                        value="{{ old('nationality', $individual?->nationality) }}">
                                    <label for="nationality">Quốc tịch</label>
                                    @error('nationality')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-floating mb-3">
                                    <input type="tel" class="form-control @error('phone_number') is-invalid @enderror"
                                        id="phone_number" name="phone_number" placeholder="Phone Number"
                                        value="{{ old('phone_number', $individual?->phone_number) }}">
                                    <label for="phone_number">Số điện thoại</label>
                                    @error('phone_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating mb-3">
                                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                                        id="email" name="email" placeholder="Email"
                                        value="{{ old('email', $individual?->email) }}">
                                    <label for="email">Email</label>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating mb-3">
                                    <select class="form-select @error('status') is-invalid @enderror" id="status"
                                        name="status">
                                        <option value="alive"
                                            {{ old('status', $individual?->status) == 'alive' ? 'selected' : '' }}>Sống
                                        </option>
                                        <option value="deceased"
                                            {{ old('status', $individual?->status) == 'deceased' ? 'selected' : '' }}>
                                            Đã mất</option>
                                        <option value="civil_incapacitated"
                                            {{ old('status', $individual?->status) == 'civil_incapacitated' ? 'selected' : '' }}>
                                            Dân sự không có năng lực </option>
                                    </select>
                                    <label for="status">Tình trạng</label>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-floating mb-3">
                                    <select class="form-select @error('marital_status') is-invalid @enderror"
                                        id="marital_status" name="marital_status">
                                        <option value="single"
                                            {{ old('marital_status', $individual?->marital_status) == 'single' ? 'selected' : '' }}>
                                            Độc thân</option>
                                        <option value="married"
                                            {{ old('marital_status', $individual?->marital_status) == 'married' ? 'selected' : '' }}>
                                            Kết hôn</option>
                                        <option value="divorced"
                                            {{ old('marital_status', $individual?->marital_status) == 'divorced' ? 'selected' : '' }}>
                                            Đã lý hôn</option>
                                    </select>
                                    <label for="marital_status">Tình trạng hôn nhân</label>
                                    @error('marital_status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            {{-- <div class="col-md-4">
                                <div class="form-floating mb-3">
                                    <input type="text"
                                        class="form-control @error('marriage_certificate_number') is-invalid @enderror"
                                        id="marriage_certificate_number" name="marriage_certificate_number"
                                        placeholder="Marriage Certificate Number"
                                        value="{{ old('marriage_certificate_number', $individual?->marriage_certificate_number) }}">
                                    <label for="marriage_certificate_number">Số giấy chứng nhận kết hôn</label>
                                    @error('marriage_certificate_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating mb-3">
                                    <input type="date"
                                        class="form-control @error('marriage_certificate_date') is-invalid @enderror"
                                        id="marriage_certificate_date" name="marriage_certificate_date"
                                        value="{{ old('marriage_certificate_date', $individual?->marriage_certificate_date) }}">
                                    <label for="marriage_certificate_date">Ngày cấp giấy chứng nhận kết hôn</label>
                                    @error('marriage_certificate_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div> --}}
                        </div>
                    </div>
                </div>

                <!-- Identity Documents Section -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-id-card me-2"></i>Giấy tờ tùy thân
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <strong>Ghi chú:</strong> Bạn có thể chọn nhiều loại giấy tờ
                                </div>
                            </div>
                        </div>

                        <div id="identityDocumentsContainer">
                            @if ($individual && $individual->identityDocuments && $individual->identityDocuments->count() > 0)
                                @foreach ($individual->identityDocuments as $index => $document)
                                    <!-- Existing document -->
                                    <div class="identity-document-item border rounded p-3 mb-3">
                                        <div class="row align-items-center">
                                            <div class="col-md-11">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-floating mb-3">
                                                            <select class="form-select document-type-select"
                                                                name="identity_documents[{{ $index }}][document_type]"
                                                                onchange="toggleDocumentFields(this, {{ $index }})">
                                                                <option value="">Chọn loại giấy tờ</option>
                                                                <option value="cccd"
                                                                    {{ old("identity_documents.$index.document_type", $document->document_type) == 'cccd' ? 'selected' : '' }}>
                                                                    Căn cước công dân (12 số)</option>
                                                                <option value="cmnd"
                                                                    {{ old("identity_documents.$index.document_type", $document->document_type) == 'cmnd' ? 'selected' : '' }}>
                                                                    Chứng minh nhân dân (9 số)</option>
                                                                <option value="passport"
                                                                    {{ old("identity_documents.$index.document_type", $document->document_type) == 'passport' ? 'selected' : '' }}>
                                                                    Hộ chiếu</option>
                                                                <option value="officer_id"
                                                                    {{ old("identity_documents.$index.document_type", $document->document_type) == 'officer_id' ? 'selected' : '' }}>
                                                                    Chứng minh sĩ quan</option>
                                                                <option value="student_card"
                                                                    {{ old("identity_documents.$index.document_type", $document->document_type) == 'student_card' ? 'selected' : '' }}>
                                                                    Thẻ học sinh</option>
                                                            </select>
                                                            <label>Loại giấy tờ</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-floating mb-3">
                                                            <input type="text" class="form-control"
                                                                name="identity_documents[{{ $index }}][document_number]"
                                                                placeholder="Document Number"
                                                                value="{{ old("identity_documents.$index.document_number", $document->document_number) }}">
                                                            <label>Số giấy tờ</label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-floating mb-3">
                                                            <input type="text" class="form-control"
                                                                id="issue_date_{{ $index }}"
                                                                name="identity_documents[{{ $index }}][issue_date_display]"
                                                                placeholder="ddmmyyyy" maxlength="10"
                                                                value="{{ old("identity_documents.$index.issue_date_display", $document->issue_date ? $document->issue_date->format('d/m/Y') : '') }}">
                                                            <label>Ngày cấp (dd/mm/yyyy)</label>
                                                            <!-- Hidden input để gửi data đúng format cho server -->
                                                            <input type="hidden"
                                                                id="issue_date_formatted_{{ $index }}"
                                                                name="identity_documents[{{ $index }}][issue_date]"
                                                                value="{{ old("identity_documents.$index.issue_date", $document->issue_date ? $document->issue_date->format('Y-m-d') : '') }}">
                                                            <div class="invalid-feedback"
                                                                id="issue_date_error_{{ $index }}"></div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-floating mb-3">
                                                            <input type="text" class="form-control"
                                                                name="identity_documents[{{ $index }}][issued_by]"
                                                                placeholder="Issued By"
                                                                value="{{ old("identity_documents.$index.issued_by", $document->issued_by) }}">
                                                            <label>Cấp bởi</label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- Student Card Specific Fields -->
                                                <div
                                                    class="student-card-fields {{ $document->document_type == 'student_card' ? '' : 'd-none' }}">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-floating mb-3">
                                                                <input type="text" class="form-control"
                                                                    name="identity_documents[{{ $index }}][school_name]"
                                                                    placeholder="School Name"
                                                                    value="{{ old("identity_documents.$index.school_name", $document->school_name) }}">
                                                                <label>Tên trường học</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-floating mb-3">
                                                                <input type="text" class="form-control"
                                                                    name="identity_documents[{{ $index }}][academic_year]"
                                                                    placeholder="Academic Year (e.g., 2023-2024)"
                                                                    value="{{ old("identity_documents.$index.academic_year", $document->academic_year) }}">
                                                                <label>Niên khóa</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-1">
                                                <button type="button"
                                                    class="btn btn-outline-danger btn-sm remove-document-btn {{ $individual->identityDocuments->count() > 1 ? '' : 'd-none' }}"
                                                    onclick="removeDocument(this)">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <!-- Default first document if no existing documents -->
                                <div class="identity-document-item border rounded p-3 mb-3">
                                    <div class="row align-items-center">
                                        <div class="col-md-11">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-floating mb-3">
                                                        <select class="form-select document-type-select"
                                                            name="identity_documents[0][document_type]"
                                                            onchange="toggleDocumentFields(this, 0)">
                                                            <option value="">Select Document Type</option>
                                                            <option value="cccd">Căn cước công dân (12 số)</option>
                                                            <option value="cmnd">Chứng minh nhân dân (9 số)</option>
                                                            <option value="passport">Hộ chiếu</option>
                                                            <option value="officer_id">Chứng minh sĩ quan</option>
                                                            <option value="student_card">Thẻ học sinh</option>
                                                        </select>
                                                        <label>Loại giấy tờ</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-floating mb-3">
                                                        <input type="text" class="form-control"
                                                            name="identity_documents[0][document_number]"
                                                            placeholder="Document Number">
                                                        <label>Số giấy tờ</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-floating mb-3">
                                                        <input type="text" class="form-control" id="issue_date_0"
                                                            name="identity_documents[0][issue_date_display]"
                                                            placeholder="ddmmyyyy" maxlength="10">
                                                        <label>Ngày cấp (dd/mm/yyyy)</label>
                                                        <!-- Hidden input để gửi data đúng format cho server -->
                                                        <input type="hidden" id="issue_date_formatted_0"
                                                            name="identity_documents[0][issue_date]">
                                                        <div class="invalid-feedback" id="issue_date_error_0"></div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-floating mb-3">
                                                        <input type="text" class="form-control"
                                                            name="identity_documents[0][issued_by]"
                                                            placeholder="Issued By">
                                                        <label>Cấp bởi</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Student Card Specific Fields -->
                                            <div class="student-card-fields d-none">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-floating mb-3">
                                                            <input type="text" class="form-control"
                                                                name="identity_documents[0][school_name]"
                                                                placeholder="School Name">
                                                            <label>Tên trường học</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-floating mb-3">
                                                            <input type="text" class="form-control"
                                                                name="identity_documents[0][academic_year]"
                                                                placeholder="Academic Year (e.g., 2023-2024)">
                                                            <label>Niên khóa</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-1">
                                            <button type="button"
                                                class="btn btn-outline-danger btn-sm remove-document-btn d-none"
                                                onclick="removeDocument(this)">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="text-end">
                            <button type="button" class="btn btn-outline-primary" onclick="addDocument()">
                                <i class="fas fa-plus me-2"></i>Thêm giấy tờ
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Addresses Section for Individual -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-map-marker-alt me-2"></i>Địa chỉ
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="accordion" id="addressAccordion">
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
                                                <div class="form-floating mb-3">
                                                    <input type="text" class="form-control"
                                                        id="permanent_street_address" name="permanent_street_address"
                                                        placeholder="Street Address"
                                                        value="{{ old('permanent_street_address', $permanentAddress?->street_address) }}">
                                                    <label for="permanent_street_address">Số nhà, đường</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-floating mb-3">
                                                    <input type="text" class="form-control" id="permanent_province"
                                                        name="permanent_province" placeholder="Province"
                                                        value="{{ old('permanent_province', $permanentAddress?->province) }}">
                                                    <label for="permanent_province">Tỉnh</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-floating mb-3">
                                                    <input type="text" class="form-control" id="permanent_district"
                                                        name="permanent_district" placeholder="District"
                                                        value="{{ old('permanent_district', $permanentAddress?->district) }}">
                                                    <label for="permanent_district">Quận, huyện</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-floating mb-3">
                                                    <input type="text" class="form-control" id="permanent_ward"
                                                        name="permanent_ward" placeholder="Ward"
                                                        value="{{ old('permanent_ward', $permanentAddress?->ward) }}">
                                                    <label for="permanent_ward">Phường</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12">
                                                <button type="button" class="btn btn-outline-info btn-sm"
                                                    onclick="copyPermanentAddress()">
                                                    <i class="fas fa-copy me-2"></i>Sao chép vào địa chỉ tạm trú
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Temporary Address -->
                            <div class="accordion-item">
                                <h4 class="accordion-header" id="temporaryAddressHeader">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#temporaryAddress" aria-expanded="false"
                                        aria-controls="temporaryAddress">
                                        <i class="fas fa-map-pin me-2"></i>Địa chỉ tạm trú
                                    </button>
                                </h4>
                                <div id="temporaryAddress" class="accordion-collapse collapse"
                                    aria-labelledby="temporaryAddressHeader" data-bs-parent="#addressAccordion">
                                    <div class="accordion-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-floating mb-3">
                                                    <input type="text" class="form-control"
                                                        id="temporary_street_address" name="temporary_street_address"
                                                        placeholder="Street Address"
                                                        value="{{ old('temporary_street_address', $temporaryAddress?->street_address) }}">
                                                    <label for="temporary_street_address">Số nhà, đường</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-floating mb-3">
                                                    <input type="text" class="form-control" id="temporary_province"
                                                        name="temporary_province" placeholder="Province"
                                                        value="{{ old('temporary_province', $temporaryAddress?->province) }}">
                                                    <label for="temporary_province">Tỉnh</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-floating mb-3">
                                                    <input type="text" class="form-control" id="temporary_district"
                                                        name="temporary_district" placeholder="District"
                                                        value="{{ old('temporary_district', $temporaryAddress?->district) }}">
                                                    <label for="temporary_district">Quận, Huyện</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-floating mb-3">
                                                    <input type="text" class="form-control" id="temporary_ward"
                                                        name="temporary_ward" placeholder="Ward"
                                                        value="{{ old('temporary_ward', $temporaryAddress?->ward) }}">
                                                    <label for="temporary_ward">Phường</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Marriage Information -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-heart me-2"></i>Thông tin kết hôn
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="same_household"
                                        name="same_household" value="1"
                                        {{ old('same_household', $marriageInfo?->same_household) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="same_household">
                                        Cùng một hộ khẩu
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="is_divorced" name="is_divorced"
                                        value="1"
                                        {{ old('is_divorced', $marriageInfo?->is_divorced) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_divorced">
                                        Đã ly hôn
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <div class="search-container">
                                        <input type="text"
                                            class="form-control @error('spouse_id') is-invalid @enderror"
                                            id="spouse_search" placeholder="Tìm theo tên hoặc số giấy tờ..."
                                            autocomplete="off"
                                            value="{{ old('spouse_search', $marriageInfo?->spouse?->full_name) }}">
                                        <button type="button"
                                            class="clear-selection {{ $marriageInfo?->spouse_id ? '' : 'd-none' }}"
                                            onclick="clearSpouseSelection()">
                                            <i class="fas fa-times"></i>
                                        </button>
                                        <div class="search-results" id="spouseSearchResults"></div>
                                    </div>
                                    <!-- Hidden input để gửi ID -->
                                    <input type="hidden" id="spouse_id" name="spouse_id"
                                        value="{{ old('spouse_id', $marriageInfo?->spouse_id) }}">
                                    @error('spouse_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="marriage_registration_number"
                                        name="marriage_registration_number" placeholder="Marriage Registration Number"
                                        value="{{ old('marriage_registration_number', $marriageInfo?->marriage_registration_number) }}">
                                    <label for="marriage_registration_number">Số đăng ký kết hôn</label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input type="text"
                                        class="form-control @error('marriage_issue_date') is-invalid @enderror"
                                        id="marriage_issue_date" name="marriage_issue_date_display"
                                        placeholder="ddmmyyyy" maxlength="10"
                                        value="{{ old('marriage_issue_date_display', $marriageInfo?->issue_date ? $marriageInfo->issue_date->format('d/m/Y') : '') }}">
                                    <label for="marriage_issue_date">Ngày cấp giấy kết hôn (dd/mm/yyyy)</label>
                                    <!-- Hidden input để gửi data đúng format cho server -->
                                    <input type="hidden" id="marriage_issue_date_formatted" name="marriage_issue_date"
                                        value="{{ old('marriage_issue_date', $marriageInfo?->issue_date ? $marriageInfo->issue_date->format('Y-m-d') : '') }}">
                                    @error('marriage_issue_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="invalid-feedback" id="marriage_issue_date_error"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="marriage_issued_by"
                                        name="marriage_issued_by" placeholder="Marriage Issued By"
                                        value="{{ old('marriage_issued_by', $marriageInfo?->issued_by) }}">
                                    <label for="marriage_issued_by">Nơi cấp giấy kết hôn</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Organization Section -->
            <div id="organizationSection" class="{{ $litigant->type != 'organization' ? 'd-none' : '' }}">
                @php
                    $organization = $litigant->organization;
                    $orgAdditionalInfo = $organization?->additionalInfo;
                    $orgRepresentative = $organization?->registrationRepresentatives->first();
                    $headquartersAddress = $litigant->addresses->where('address_type', 'headquarters')->first();
                @endphp

                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-building me-2"></i>Thông tin tổ chức
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="business_type" name="business_type"
                                        placeholder="Business Type"
                                        value="{{ old('business_type', $organization?->business_type) }}">
                                    <label for="business_type">Loại Hình</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input type="tel" class="form-control" id="org_phone_number"
                                        name="org_phone_number" placeholder="Phone Number"
                                        value="{{ old('org_phone_number', $organization?->phone_number) }}">
                                    <label for="org_phone_number">Số điện thoại</label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-floating mb-3">
                                    <select class="form-select" id="organization_type" name="organization_type">
                                        <option value="">Loại tổ chức</option>
                                        <option value="headquarters"
                                            {{ old('organization_type', $organization?->organization_type) == 'headquarters' ? 'selected' : '' }}>
                                            Trụ sở chính
                                        </option>
                                        <option value="branch"
                                            {{ old('organization_type', $organization?->organization_type) == 'branch' ? 'selected' : '' }}>
                                            Chi nhánh</option>
                                        <option value="transaction_office"
                                            {{ old('organization_type', $organization?->organization_type) == 'transaction_office' ? 'selected' : '' }}>
                                            Phòng giao dịch</option>
                                    </select>
                                    <label for="organization_type">Loại tổ chức</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="license_type" name="license_type"
                                        placeholder="License Type"
                                        value="{{ old('license_type', $organization?->license_type) }}">
                                    <label for="license_type">Loại giấy phép</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="license_number" name="license_number"
                                        placeholder="License Number"
                                        value="{{ old('license_number', $organization?->license_number) }}">
                                    <label for="license_number">Số giấy phép</label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-floating mb-3">
                                    <input type="text"
                                        class="form-control @error('business_registration_date') is-invalid @enderror"
                                        id="business_registration_date" name="business_registration_date_display"
                                        placeholder="ddmmyyyy" maxlength="10"
                                        value="{{ old('business_registration_date_display', $organization?->business_registration_date ? $organization->business_registration_date->format('d/m/Y') : '') }}">
                                    <label for="business_registration_date">Ngày đăng ký kinh doanh (dd/mm/yyyy)</label>
                                    <!-- Hidden input để gửi data đúng format cho server -->
                                    <input type="hidden" id="business_registration_date_formatted"
                                        name="business_registration_date"
                                        value="{{ old('business_registration_date', $organization?->business_registration_date ? $organization->business_registration_date->format('Y-m-d') : '') }}">
                                    @error('business_registration_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="invalid-feedback" id="business_registration_date_error"></div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="issuing_authority"
                                        name="issuing_authority" placeholder="Issuing Authority"
                                        value="{{ old('issuing_authority', $organization?->issuing_authority) }}">
                                    <label for="issuing_authority">Cơ quan phát hành</label>
                                </div>
                            </div>
                            {{-- <div class="col-md-4">
                                <div class="form-floating mb-3">
                                    <select class="form-select" id="representative_id" name="representative_id">
                                        <option value="">Chọn người đại diện </option>
                                        @foreach ($availableLitigants as $availableLitigant)
                                            <option value="{{ $availableLitigant->id }}"
                                                {{ old('representative_id', $organization?->representative_id) == $availableLitigant->id ? 'selected' : '' }}>
                                                {{ $availableLitigant->full_name }}
                                                ({{ ucfirst($availableLitigant->type) }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <label for="representative_id">Tiêu biểu</label>
                                </div>
                            </div> --}}
                        </div>
                        {{-- <div class="row">
                            <div class="col-md-12">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="representative_position"
                                        name="representative_position" placeholder="Representative Position"
                                        value="{{ old('representative_position', $organization?->representative_position) }}">
                                    <label for="representative_position">Chức vụ người đại diện</label>
                                </div>
                            </div>
                        </div> --}}
                    </div>
                </div>

                <!-- Organization Address -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-map-marker-alt me-2"></i>Địa chỉ trụ sở
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="headquarters_street_address"
                                        name="headquarters_street_address" placeholder="Street Address"
                                        value="{{ old('headquarters_street_address', $headquartersAddress?->street_address) }}">
                                    <label for="headquarters_street_address">Số nhà, đường</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="headquarters_province"
                                        name="headquarters_province" placeholder="Province"
                                        value="{{ old('headquarters_province', $headquartersAddress?->province) }}">
                                    <label for="headquarters_province">Tỉnh</label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="headquarters_district"
                                        name="headquarters_district" placeholder="District"
                                        value="{{ old('headquarters_district', $headquartersAddress?->district) }}">
                                    <label for="headquarters_district">Quận, huyện</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="headquarters_ward"
                                        name="headquarters_ward" placeholder="Ward"
                                        value="{{ old('headquarters_ward', $headquartersAddress?->ward) }}">
                                    <label for="headquarters_ward">Phường</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Organization Additional Info -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-info me-2"></i>Thông tin bổ sung
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="former_name" name="former_name"
                                        placeholder="Former Name"
                                        value="{{ old('former_name', $orgAdditionalInfo?->former_name) }}">
                                    <label for="former_name">Tên cũ(Nếu có)</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="account_number" name="account_number"
                                        placeholder="Account Number"
                                        value="{{ old('account_number', $orgAdditionalInfo?->account_number) }}">
                                    <label for="account_number">Số tài khoản</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="fax" name="fax"
                                        placeholder="Fax" value="{{ old('fax', $orgAdditionalInfo?->fax) }}">
                                    <label for="fax">Fax</label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-floating mb-3">
                                    <input type="email" class="form-control" id="org_email" name="org_email"
                                        placeholder="Email" value="{{ old('org_email', $orgAdditionalInfo?->email) }}">
                                    <label for="org_email">Email</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating mb-3">
                                    <input type="url" class="form-control" id="website" name="website"
                                        placeholder="Website" value="{{ old('website', $orgAdditionalInfo?->website) }}">
                                    <label for="website">Website</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating mb-3">
                                    <input type="number" class="form-control" id="change_registration_number"
                                        name="change_registration_number" placeholder="Change Registration Number"
                                        value="{{ old('change_registration_number', $orgAdditionalInfo?->change_registration_number) }}">
                                    <label for="change_registration_number">Thay đổi số đăng ký</label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input type="text"
                                        class="form-control @error('change_registration_date') is-invalid @enderror"
                                        id="change_registration_date" name="change_registration_date_display"
                                        placeholder="ddmmyyyy" maxlength="10"
                                        value="{{ old('change_registration_date_display', $orgAdditionalInfo?->change_registration_date ? $orgAdditionalInfo->change_registration_date->format('d/m/Y') : '') }}">
                                    <label for="change_registration_date">Thay đổi ngày đăng ký (dd/mm/yyyy)</label>
                                    <!-- Hidden input để gửi data đúng format cho server -->
                                    <input type="hidden" id="change_registration_date_formatted"
                                        name="change_registration_date"
                                        value="{{ old('change_registration_date', $orgAdditionalInfo?->change_registration_date ? $orgAdditionalInfo->change_registration_date->format('Y-m-d') : '') }}">
                                    @error('change_registration_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="invalid-feedback" id="change_registration_date_error"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Organization Registration Representative -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-user-tie me-2"></i>Đại diện đăng ký
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-floating mb-3">
                                    <div class="search-container">
                                        <input type="text"
                                            class="form-control @error('registration_representative_id') is-invalid @enderror"
                                            id="registration_representative_search"
                                            placeholder="Tìm theo tên hoặc số giấy tờ..." autocomplete="off"
                                            value="{{ old('registration_representative_search', $orgRepresentative?->representative?->full_name) }}">
                                        <button type="button"
                                            class="clear-selection {{ $orgRepresentative?->representative_id ? '' : 'd-none' }}"
                                            onclick="clearRegistrationRepresentativeSelection()">
                                            <i class="fas fa-times"></i>
                                        </button>
                                        <div class="search-results" id="registrationRepresentativeSearchResults"></div>
                                    </div>
                                    {{-- <label for="registration_representative_search">Đại diện đăng ký (Tìm theo tên hoặc số
                                        giấy tờ)</label> --}}
                                    <!-- Hidden input để gửi ID -->
                                    <input type="hidden" id="registration_representative_id"
                                        name="registration_representative_id"
                                        value="{{ old('registration_representative_id', $orgRepresentative?->representative_id) }}">
                                    @error('registration_representative_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="registration_position"
                                        name="registration_position" placeholder="Position"
                                        value="{{ old('registration_position', $orgRepresentative?->position) }}">
                                    <label for="registration_position">Chức vụ</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating mb-3">
                                    <textarea class="form-control" id="legal_basis" name="legal_basis" placeholder="Legal Basis" style="height: 100px">{{ old('legal_basis', $orgRepresentative?->legal_basis) }}</textarea>
                                    <label for="legal_basis">Cơ sở pháp lý</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Credit Institution Section -->
            <div id="creditInstitutionSection" class="{{ $litigant->type != 'credit_institution' ? 'd-none' : '' }}">
                @php
                    $creditInstitution = $litigant->creditInstitution;
                    $ciAdditionalInfo = $creditInstitution?->additionalInfo;
                    $ciRepresentative = $creditInstitution?->registrationRepresentatives->first();
                    $ciHeadquartersAddress = $litigant->addresses->where('address_type', 'headquarters')->first();
                @endphp

                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-university me-2"></i>Thông tin tổ chức tín dụng
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="ci_business_type"
                                        name="ci_business_type" placeholder="Business Type"
                                        value="{{ old('ci_business_type', $creditInstitution?->business_type) }}">
                                    <label for="ci_business_type">Loại hình</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input type="tel" class="form-control" id="ci_phone_number"
                                        name="ci_phone_number" placeholder="Phone Number"
                                        value="{{ old('ci_phone_number', $creditInstitution?->phone_number) }}">
                                    <label for="ci_phone_number">Số điện thoại</label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-floating mb-3">
                                    <select class="form-select" id="ci_organization_type" name="ci_organization_type">
                                        <option value="">Chọn loại tổ chức</option>
                                        <option value="headquarters"
                                            {{ old('ci_organization_type', $creditInstitution?->organization_type) == 'headquarters' ? 'selected' : '' }}>
                                            Trụ sở chính</option>
                                        <option value="branch"
                                            {{ old('ci_organization_type', $creditInstitution?->organization_type) == 'branch' ? 'selected' : '' }}>
                                            Chi nhánh</option>
                                        <option value="transaction_office"
                                            {{ old('ci_organization_type', $creditInstitution?->organization_type) == 'transaction_office' ? 'selected' : '' }}>
                                            Phòng giao dịch</option>
                                    </select>
                                    <label for="ci_organization_type">Loại tổ chức</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="ci_license_type"
                                        name="ci_license_type" placeholder="License Type"
                                        value="{{ old('ci_license_type', $creditInstitution?->license_type) }}">
                                    <label for="ci_license_type">Loại giấy phép</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="ci_license_number"
                                        name="ci_license_number" placeholder="License Number"
                                        value="{{ old('ci_license_number', $creditInstitution?->license_number) }}">
                                    <label for="ci_license_number">Số giấy phép</label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-floating mb-3">
                                    <input type="text"
                                        class="form-control @error('ci_business_registration_date') is-invalid @enderror"
                                        id="ci_business_registration_date" name="ci_business_registration_date_display"
                                        placeholder="ddmmyyyy" maxlength="10"
                                        value="{{ old('ci_business_registration_date_display', $creditInstitution?->business_registration_date ? $creditInstitution->business_registration_date->format('d/m/Y') : '') }}">
                                    <label for="ci_business_registration_date">Ngày đăng ký kinh doanh (dd/mm/yyyy)</label>
                                    <!-- Hidden input để gửi data đúng format cho server -->
                                    <input type="hidden" id="ci_business_registration_date_formatted"
                                        name="ci_business_registration_date"
                                        value="{{ old('ci_business_registration_date', $creditInstitution?->business_registration_date ? $creditInstitution->business_registration_date->format('Y-m-d') : '') }}">
                                    @error('ci_business_registration_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="invalid-feedback" id="ci_business_registration_date_error"></div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="ci_issuing_authority"
                                        name="ci_issuing_authority" placeholder="Issuing Authority"
                                        value="{{ old('ci_issuing_authority', $creditInstitution?->issuing_authority) }}">
                                    <label for="ci_issuing_authority">Cơ quan pháp hành</label>
                                </div>
                            </div>
                            {{-- <div class="col-md-4">
                                <div class="form-floating mb-3">
                                    <select class="form-select" id="ci_representative_id" name="ci_representative_id">
                                        <option value="">Chọn người đại diện</option>
                                        @foreach ($availableLitigants as $availableLitigant)
                                            <option value="{{ $availableLitigant->id }}"
                                                {{ old('ci_representative_id', $creditInstitution?->representative_id) == $availableLitigant->id ? 'selected' : '' }}>
                                                {{ $availableLitigant->full_name }}
                                                ({{ ucfirst($availableLitigant->type) }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <label for="ci_representative_id">Đại diện</label>
                                </div>
                            </div> --}}
                        </div>
                        {{-- <div class="row">
                            <div class="col-md-12">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="ci_representative_position"
                                        name="ci_representative_position" placeholder="Representative Position"
                                        value="{{ old('ci_representative_position', $creditInstitution?->representative_position) }}">
                                    <label for="ci_representative_position">Chức vụ người đại diện</label>
                                </div>
                            </div>
                        </div> --}}
                    </div>
                </div>

                <!-- Credit Institution Address -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-map-marker-alt me-2"></i>Đại chỉ trụ sở chính
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="ci_headquarters_street_address"
                                        name="ci_headquarters_street_address" placeholder="Street Address"
                                        value="{{ old('ci_headquarters_street_address', $ciHeadquartersAddress?->street_address) }}">
                                    <label for="ci_headquarters_street_address">Số nhà, đường</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="ci_headquarters_province"
                                        name="ci_headquarters_province" placeholder="Province"
                                        value="{{ old('ci_headquarters_province', $ciHeadquartersAddress?->province) }}">
                                    <label for="ci_headquarters_province">Tỉnh</label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="ci_headquarters_district"
                                        name="ci_headquarters_district" placeholder="District"
                                        value="{{ old('ci_headquarters_district', $ciHeadquartersAddress?->district) }}">
                                    <label for="ci_headquarters_district">Quận, huyện</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="ci_headquarters_ward"
                                        name="ci_headquarters_ward" placeholder="Ward"
                                        value="{{ old('ci_headquarters_ward', $ciHeadquartersAddress?->ward) }}">
                                    <label for="ci_headquarters_ward">Phường</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Credit Institution Additional Info -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-info me-2"></i>Thông tin bổ sung
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="ci_former_name" name="ci_former_name"
                                        placeholder="Former Name"
                                        value="{{ old('ci_former_name', $ciAdditionalInfo?->former_name) }}">
                                    <label for="ci_former_name">Tên cũ(Nếu có)</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="ci_account_number"
                                        name="ci_account_number" placeholder="Account Number"
                                        value="{{ old('ci_account_number', $ciAdditionalInfo?->account_number) }}">
                                    <label for="ci_account_number">Số tài khoản</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="ci_fax" name="ci_fax"
                                        placeholder="Fax" value="{{ old('ci_fax', $ciAdditionalInfo?->fax) }}">
                                    <label for="ci_fax">Fax</label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-floating mb-3">
                                    <input type="email" class="form-control" id="ci_email" name="ci_email"
                                        placeholder="Email" value="{{ old('ci_email', $ciAdditionalInfo?->email) }}">
                                    <label for="ci_email">Email</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating mb-3">
                                    <input type="url" class="form-control" id="ci_website" name="ci_website"
                                        placeholder="Website"
                                        value="{{ old('ci_website', $ciAdditionalInfo?->website) }}">
                                    <label for="ci_website">Website</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating mb-3">
                                    <input type="number" class="form-control" id="ci_change_registration_number"
                                        name="ci_change_registration_number" placeholder="Change Registration Number"
                                        value="{{ old('ci_change_registration_number', $ciAdditionalInfo?->change_registration_number) }}">
                                    <label for="ci_change_registration_number">Thay đổi số đăng ký</label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input type="text"
                                        class="form-control @error('ci_change_registration_date') is-invalid @enderror"
                                        id="ci_change_registration_date" name="ci_change_registration_date_display"
                                        placeholder="ddmmyyyy" maxlength="10"
                                        value="{{ old('ci_change_registration_date_display', $ciAdditionalInfo?->change_registration_date ? $ciAdditionalInfo->change_registration_date->format('d/m/Y') : '') }}">
                                    <label for="ci_change_registration_date">Thay đổi ngày đăng ký (dd/mm/yyyy)</label>
                                    <!-- Hidden input để gửi data đúng format cho server -->
                                    <input type="hidden" id="ci_change_registration_date_formatted"
                                        name="ci_change_registration_date"
                                        value="{{ old('ci_change_registration_date', $ciAdditionalInfo?->change_registration_date ? $ciAdditionalInfo->change_registration_date->format('Y-m-d') : '') }}">
                                    @error('ci_change_registration_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="invalid-feedback" id="ci_change_registration_date_error"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Credit Institution Registration Representative -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-user-tie me-2"></i>Đại diện đăng ký
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-floating mb-3">
                                    <div class="search-container">
                                        <input type="text"
                                            class="form-control @error('ci_registration_representative_id') is-invalid @enderror"
                                            id="ci_registration_representative_search"
                                            placeholder="Tìm theo tên hoặc số giấy tờ..." autocomplete="off"
                                            value="{{ old('ci_registration_representative_search', $ciRepresentative?->representative?->full_name) }}">
                                        <button type="button"
                                            class="clear-selection {{ $ciRepresentative?->representative_id ? '' : 'd-none' }}"
                                            onclick="clearCiRegistrationRepresentativeSelection()">
                                            <i class="fas fa-times"></i>
                                        </button>
                                        <div class="search-results" id="ciRegistrationRepresentativeSearchResults"></div>
                                    </div>
                                    {{-- <label for="ci_registration_representative_search">Đại diện đăng ký (Tìm theo tên hoặc
                                        số giấy tờ)</label> --}}
                                    <!-- Hidden input để gửi ID -->
                                    <input type="hidden" id="ci_registration_representative_id"
                                        name="ci_registration_representative_id"
                                        value="{{ old('ci_registration_representative_id', $ciRepresentative?->representative_id) }}">
                                    @error('ci_registration_representative_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="ci_registration_position"
                                        name="ci_registration_position" placeholder="Position"
                                        value="{{ old('ci_registration_position', $ciRepresentative?->position) }}">
                                    <label for="ci_registration_position">Chức vụ</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating mb-3">
                                    <textarea class="form-control" id="ci_legal_basis" name="ci_legal_basis" placeholder="Legal Basis"
                                        style="height: 100px">{{ old('ci_legal_basis', $ciRepresentative?->legal_basis) }}</textarea>
                                    <label for="ci_legal_basis">Cơ sở pháp lý</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Section -->
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('litigants.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Hủy
                        </a>
                        <div>
                            <button type="button" class="btn btn-outline-primary me-2" onclick="validateForm()">
                                <i class="fas fa-check me-2"></i>Kiểm tra
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Cập nhật đương sự
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <script src="{{ asset('js/litigant-common.js') }}?v={{ time() }}"></script>
    <script src="{{ asset('js/litigant-form.js') }}?v={{ time() }}"></script>
    <script src="{{ asset('js/litigant-date.js') }}?v={{ time() }}"></script>
    <script>
        // Override documentCounter for edit form
        documentCounter =
            {{ $individual && $individual->identityDocuments ? $individual->identityDocuments->count() : 1 }};
    </script>
@endsection
