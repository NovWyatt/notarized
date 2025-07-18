{{-- resources/views/properties/edit.blade.php --}}
@extends('layouts.app2')

@push('styles')
    <style>
        .spinner-border-sm {
            width: 1rem;
            height: 1rem;
        }

        .form-control:focus,
        .form-select:focus {
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
        }

        .card-header h5 {
            color: #495057;
        }

        .required {
            color: #dc3545;
        }

        .auto-save-indicator {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            background: rgba(25, 135, 84, 0.9);
            color: white;
            padding: 8px 16px;
            border-radius: 4px;
            font-size: 0.875rem;
        }

        .search-dropdown {
            position: relative;
        }

        .search-results {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border: 1px solid #ced4da;
            border-top: none;
            border-radius: 0 0 0.375rem 0.375rem;
            max-height: 200px;
            overflow-y: auto;
            z-index: 1000;
            display: none;
        }

        .search-result-item {
            padding: 0.5rem 0.75rem;
            cursor: pointer;
            border-bottom: 1px solid #f8f9fa;
        }

        .search-result-item:hover {
            background-color: #f8f9fa;
        }

        .search-result-item.selected {
            background-color: #e3f2fd;
        }

        .input-group-append {
            margin-left: -1px;
        }

        .btn-create-item {
            border-top-left-radius: 0;
            border-bottom-left-radius: 0;
        }

        .search-input {
            border-top-right-radius: 0;
            border-bottom-right-radius: 0;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid p-3">
        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-md-6">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('properties.index') }}">Tài sản</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('properties.show', $asset) }}">{{ $asset->display_name }}</a></li>
                        <li class="breadcrumb-item active">Chỉnh sửa</li>
                    </ol>
                </nav>
                <h3 class="mb-0">Chỉnh sửa Tài sản</h3>
                <p class="text-muted">Cập nhật thông tin tài sản trong hệ thống</p>
            </div>
            <div class="col-md-6 text-end">
                <a href="{{ route('properties.show', $asset) }}" class="btn btn-outline-secondary me-2">
                    <i class="bi bi-eye me-2"></i>Xem chi tiết
                </a>
                <a href="{{ route('properties.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Quay lại danh sách
                </a>
            </div>
        </div>

        <!-- Error Messages -->
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <h6><i class="bi bi-exclamation-triangle me-2"></i>Có lỗi xảy ra:</h6>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Success Message -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Form -->
        <form method="POST" action="{{ route('properties.update', $asset) }}" id="assetForm">
            @csrf
            @method('PUT')

            <!-- Basic Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Thông tin cơ bản</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="asset_type" class="form-label">
                                    Loại tài sản <span class="text-danger">*</span>
                                </label>
                                <select class="form-select" id="asset_type" name="asset_type" required>
                                    <option value="">Chọn loại tài sản</option>
                                    @foreach ($assetTypes as $value => $label)
                                        <option value="{{ $value }}"
                                            {{ old('asset_type', $asset->asset_type) === $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="notes" class="form-label">Ghi chú</label>
                                <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Thêm ghi chú về tài sản...">{{ old('notes', $asset->notes) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dynamic Fields Container -->
            <div id="dynamic-fields">
                @php
                    $certificate = $asset->certificates->first();
                    $landPlot = $asset->landPlots->first();
                    $house = $asset->house;
                    $apartment = $asset->apartment;
                    $vehicle = $asset->vehicle;
                @endphp

                <!-- Certificate Fields -->
                @if ($certificate || (old('certificate_type_id') && \App\Enums\AssetTypeEnum::isRealEstate($asset->asset_type)))
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Thông tin Giấy Chứng Nhận</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="certificate_type_id" class="form-label">Loại giấy chứng nhận</label>
                                        <div class="search-dropdown">
                                            <div class="input-group">
                                                <input type="text" class="form-control search-input" id="certificate_type_search"
                                                       placeholder="Tìm kiếm loại chứng chỉ..." autocomplete="off"
                                                       value="{{ $certificate && $certificate->certificateType ? $certificate->certificateType->name : old('certificate_type_search') }}">
                                                <button type="button" class="btn btn-outline-primary btn-create-item"
                                                        onclick="AssetManager.search.showCreateCertificateTypeModal()">
                                                    <i class="bi bi-plus"></i>
                                                </button>
                                            </div>
                                            <input type="hidden" id="certificate_type_id" name="certificate_type_id"
                                                   value="{{ old('certificate_type_id', $certificate?->certificate_type_id) }}">
                                            <div class="search-results" id="certificate_type_results"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="issue_number" class="form-label">Số phát hành</label>
                                        <input type="text" class="form-control" id="issue_number" name="issue_number"
                                            value="{{ old('issue_number', $certificate?->issue_number) }}"
                                            placeholder="Nhập số phát hành">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="book_number" class="form-label">Số vào sổ</label>
                                        <input type="text" class="form-control" id="book_number" name="book_number"
                                            value="{{ old('book_number', $certificate?->book_number) }}"
                                            placeholder="Nhập số vào sổ">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="issue_date" class="form-label">Ngày cấp</label>
                                        <input type="date" class="form-control" id="issue_date" name="issue_date"
                                            value="{{ old('issue_date', $certificate?->issue_date?->format('Y-m-d')) }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Land Plot Fields -->
                @if ($landPlot || (old('plot_number') && \App\Enums\AssetTypeEnum::isRealEstate($asset->asset_type)))
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Thông tin Thửa Đất</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="plot_number" class="form-label">Thửa đất số</label>
                                        <input type="text" class="form-control" id="plot_number" name="plot_number"
                                            value="{{ old('plot_number', $landPlot?->plot_number) }}"
                                            placeholder="Nhập số thửa">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="map_sheet_number" class="form-label">Tờ bản đồ số</label>
                                        <input type="text" class="form-control" id="map_sheet_number"
                                            name="map_sheet_number"
                                            value="{{ old('map_sheet_number', $landPlot?->map_sheet_number) }}"
                                            placeholder="Nhập tờ bản đồ">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="area" class="form-label">Diện tích (m²)</label>
                                        <input type="number" class="form-control" id="area" name="area"
                                            value="{{ old('area', $landPlot?->area) }}" placeholder="0" min="0"
                                            step="0.01">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="house_number" class="form-label">Số nhà</label>
                                        <input type="text" class="form-control" id="house_number" name="house_number"
                                            value="{{ old('house_number', $landPlot?->house_number) }}"
                                            placeholder="Nhập số nhà">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="street_name" class="form-label">Tên đường</label>
                                        <input type="text" class="form-control" id="street_name" name="street_name"
                                            value="{{ old('street_name', $landPlot?->street_name) }}"
                                            placeholder="Nhập tên đường">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="mb-3">
                                        <label for="province" class="form-label">Tỉnh/Thành</label>
                                        <input type="text" class="form-control" id="province" name="province"
                                            value="{{ old('province', $landPlot?->province) }}"
                                            placeholder="Nhập tỉnh/thành">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="mb-3">
                                        <label for="district" class="form-label">Quận/Huyện</label>
                                        <input type="text" class="form-control" id="district" name="district"
                                            value="{{ old('district', $landPlot?->district) }}"
                                            placeholder="Nhập quận/huyện">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="mb-3">
                                        <label for="ward" class="form-label">Phường/Xã</label>
                                        <input type="text" class="form-control" id="ward" name="ward"
                                            value="{{ old('ward', $landPlot?->ward) }}" placeholder="Nhập phường xã">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="usage_form" class="form-label">Hình thức sử dụng</label>
                                        <input type="text" class="form-control" id="usage_form" name="usage_form"
                                            value="{{ old('usage_form', $landPlot?->usage_form) }}"
                                            placeholder="Nhập hình thức sử dụng">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="usage_purpose" class="form-label">Mục đích sử dụng</label>
                                        <input type="text" class="form-control" id="usage_purpose"
                                            name="usage_purpose"
                                            value="{{ old('usage_purpose', $landPlot?->usage_purpose) }}"
                                            placeholder="Nhập mục đích sử dụng">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="land_use_term" class="form-label">Thời hạn sử dụng</label>
                                        <input type="date" class="form-control" id="land_use_term"
                                            name="land_use_term"
                                            value="{{ old('land_use_term', $landPlot?->land_use_term?->format('Y-m-d')) }}">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="usage_origin" class="form-label">Nguồn gốc sử dụng</label>
                                        <input type="text" class="form-control" id="usage_origin" name="usage_origin"
                                            value="{{ old('usage_origin', $landPlot?->usage_origin) }}"
                                            placeholder="Nhập nguồn gốc sử dụng">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="land_notes" class="form-label">Ghi chú về đất</label>
                                        <textarea class="form-control" id="land_notes" name="land_notes" rows="2"
                                            placeholder="Ghi chú về thửa đất...">{{ old('land_notes', $landPlot?->notes) }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- House Fields -->
                @if ($house || (old('house_type') && \App\Enums\AssetTypeEnum::hasHouseInfo($asset->asset_type)))
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Thông tin Nhà Ở</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="house_type" class="form-label">Loại nhà ở</label>
                                        <input type="text" class="form-control" id="house_type" name="house_type"
                                            value="{{ old('house_type', $house?->house_type) }}"
                                            placeholder="Nhập loại nhà">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="construction_area" class="form-label">Diện tích xây dựng (m²)</label>
                                        <input type="number" class="form-control" id="construction_area"
                                            name="construction_area"
                                            value="{{ old('construction_area', $house?->construction_area) }}"
                                            placeholder="0" min="0" step="0.01">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="floor_area" class="form-label">Diện tích sàn (m²)</label>
                                        <input type="number" class="form-control" id="floor_area" name="floor_area"
                                            value="{{ old('floor_area', $house?->floor_area) }}" placeholder="0"
                                            min="0" step="0.01">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="number_of_floors" class="form-label">Số tầng</label>
                                        <input type="number" class="form-control" id="number_of_floors"
                                            name="number_of_floors"
                                            value="{{ old('number_of_floors', $house?->number_of_floors) }}"
                                            placeholder="1" min="1" max="100">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="ownership_form" class="form-label">Hình thức sở hữu</label>
                                        <input type="text" class="form-control" id="ownership_form"
                                            name="ownership_form"
                                            value="{{ old('ownership_form', $house?->ownership_form) }}"
                                            placeholder="Nhập hình thức sở hữu">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="grade_level" class="form-label">Cấp (Hạng)</label>
                                        <input type="text" class="form-control" id="grade_level" name="grade_level"
                                            value="{{ old('grade_level', $house?->grade_level) }}"
                                            placeholder="Nhập cấp/hạng">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="ownership_term" class="form-label">Thời hạn sở hữu</label>
                                        <input type="date" class="form-control" id="ownership_term"
                                            name="ownership_term"
                                            value="{{ old('ownership_term', $house?->ownership_term?->format('Y-m-d')) }}">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="structure" class="form-label">Kết cấu</label>
                                        <input type="text" class="form-control" id="structure" name="structure"
                                            value="{{ old('structure', $house?->structure) }}"
                                            placeholder="Nhập kết cấu">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="house_notes" class="form-label">Ghi chú về nhà</label>
                                        <textarea class="form-control" id="house_notes" name="house_notes" rows="2"
                                            placeholder="Ghi chú về nhà ở...">{{ old('house_notes', $house?->notes) }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Apartment Fields -->
                @if ($apartment || (old('apartment_number') && \App\Enums\AssetTypeEnum::hasApartmentInfo($asset->asset_type)))
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Thông tin Căn Hộ</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="apartment_number" class="form-label">Căn hộ số</label>
                                        <input type="text" class="form-control" id="apartment_number"
                                            name="apartment_number"
                                            value="{{ old('apartment_number', $apartment?->apartment_number) }}"
                                            placeholder="Nhập số căn hộ">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="apartment_floor" class="form-label">Căn hộ thuộc tầng</label>
                                        <input type="number" class="form-control" id="apartment_floor"
                                            name="apartment_floor"
                                            value="{{ old('apartment_floor', $apartment?->apartment_floor) }}"
                                            placeholder="1" min="1" max="200">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="building_floors" class="form-label">Số tầng nhà chung cư</label>
                                        <input type="number" class="form-control" id="building_floors"
                                            name="building_floors"
                                            value="{{ old('building_floors', $apartment?->building_floors) }}"
                                            placeholder="1" min="1" max="200">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="apartment_construction_area" class="form-label">Diện tích xây dựng
                                            (m²)</label>
                                        <input type="number" class="form-control" id="apartment_construction_area"
                                            name="apartment_construction_area"
                                            value="{{ old('apartment_construction_area', $apartment?->construction_area) }}"
                                            placeholder="0" min="0" step="0.01">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="apartment_floor_area" class="form-label">Diện tích sàn (m²)</label>
                                        <input type="number" class="form-control" id="apartment_floor_area"
                                            name="apartment_floor_area"
                                            value="{{ old('apartment_floor_area', $apartment?->floor_area) }}"
                                            placeholder="0" min="0" step="0.01">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="apartment_ownership_form" class="form-label">Hình thức sở hữu</label>
                                        <input type="text" class="form-control" id="apartment_ownership_form"
                                            name="apartment_ownership_form"
                                            value="{{ old('apartment_ownership_form', $apartment?->ownership_form) }}"
                                            placeholder="Nhập hình thức sở hữu">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="apartment_ownership_term" class="form-label">Thời hạn sở hữu</label>
                                        <input type="date" class="form-control" id="apartment_ownership_term"
                                            name="apartment_ownership_term"
                                            value="{{ old('apartment_ownership_term', $apartment?->ownership_term?->format('Y-m-d')) }}">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="apartment_structure" class="form-label">Kết cấu</label>
                                        <input type="text" class="form-control" id="apartment_structure"
                                            name="apartment_structure"
                                            value="{{ old('apartment_structure', $apartment?->structure) }}"
                                            placeholder="Nhập kết cấu">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="apartment_notes" class="form-label">Ghi chú về căn hộ</label>
                                        <textarea class="form-control" id="apartment_notes" name="apartment_notes" rows="2"
                                            placeholder="Ghi chú về căn hộ...">{{ old('apartment_notes', $apartment?->notes) }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Vehicle Fields -->
                @if ($vehicle || (old('registration_number') && \App\Enums\AssetTypeEnum::hasVehicleInfo($asset->asset_type)))
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Thông tin Phương Tiện</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="registration_number" class="form-label">Giấy đăng ký số</label>
                                        <input type="text" class="form-control" id="registration_number"
                                            name="registration_number"
                                            value="{{ old('registration_number', $vehicle?->registration_number) }}"
                                            placeholder="Nhập số đăng ký">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="license_plate" class="form-label">Biển kiểm soát</label>
                                        <input type="text" class="form-control" id="license_plate"
                                            name="license_plate"
                                            value="{{ old('license_plate', $vehicle?->license_plate) }}"
                                            placeholder="Nhập biển số">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="brand" class="form-label">Nhãn hiệu</label>
                                        <input type="text" class="form-control" id="brand" name="brand"
                                            value="{{ old('brand', $vehicle?->brand) }}" placeholder="Nhập nhãn hiệu">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="vehicle_type" class="form-label">Loại xe</label>
                                        <input type="text" class="form-control" id="vehicle_type" name="vehicle_type"
                                            value="{{ old('vehicle_type', $vehicle?->vehicle_type) }}"
                                            placeholder="Nhập loại xe">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="color" class="form-label">Màu sơn</label>
                                        <input type="text" class="form-control" id="color" name="color"
                                            value="{{ old('color', $vehicle?->color) }}" placeholder="Nhập màu sơn">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="engine_number" class="form-label">Số máy</label>
                                        <input type="text" class="form-control" id="engine_number"
                                            name="engine_number"
                                            value="{{ old('engine_number', $vehicle?->engine_number) }}"
                                            placeholder="Nhập số máy">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="chassis_number" class="form-label">Số khung</label>
                                        <input type="text" class="form-control" id="chassis_number"
                                            name="chassis_number"
                                            value="{{ old('chassis_number', $vehicle?->chassis_number) }}"
                                            placeholder="Nhập số khung">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="vehicle_issue_date" class="form-label">Ngày cấp</label>
                                        <input type="date" class="form-control" id="vehicle_issue_date"
                                            name="vehicle_issue_date"
                                            value="{{ old('vehicle_issue_date', $vehicle?->issue_date?->format('Y-m-d')) }}">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="issuing_authority_id" class="form-label">Nơi cấp</label>
                                        <div class="search-dropdown">
                                            <div class="input-group">
                                                <input type="text" class="form-control search-input" id="issuing_authority_search"
                                                       placeholder="Tìm kiếm cơ quan cấp phát..." autocomplete="off"
                                                       value="{{ $vehicle && $vehicle->issuingAuthority ? $vehicle->issuingAuthority->name : old('issuing_authority_search') }}">
                                                <button type="button" class="btn btn-outline-primary btn-create-item"
                                                        onclick="AssetManager.search.showCreateIssuingAuthorityModal()">
                                                    <i class="bi bi-plus"></i>
                                                </button>
                                            </div>
                                            <input type="hidden" id="issuing_authority_id" name="issuing_authority_id"
                                                   value="{{ old('issuing_authority_id', $vehicle?->issuing_authority_id) }}">
                                            <div class="search-results" id="issuing_authority_results"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="mb-3">
                                        <label for="payload" class="form-label">Trọng tải (tấn)</label>
                                        <input type="number" class="form-control" id="payload" name="payload"
                                            value="{{ old('payload', $vehicle?->payload) }}" placeholder="0"
                                            min="0" step="0.01">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="mb-3">
                                        <label for="engine_capacity" class="form-label">Dung tích (L)</label>
                                        <input type="number" class="form-control" id="engine_capacity"
                                            name="engine_capacity"
                                            value="{{ old('engine_capacity', $vehicle?->engine_capacity) }}"
                                            placeholder="0" min="0" step="0.01">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="mb-3">
                                        <label for="seating_capacity" class="form-label">Số chỗ ngồi</label>
                                        <input type="number" class="form-control" id="seating_capacity"
                                            name="seating_capacity"
                                            value="{{ old('seating_capacity', $vehicle?->seating_capacity) }}"
                                            placeholder="0" min="1" max="100">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="mb-3">
                                        <label for="type_number" class="form-label">Số loại</label>
                                        <input type="text" class="form-control" id="type_number" name="type_number"
                                            value="{{ old('type_number', $vehicle?->type_number) }}"
                                            placeholder="Nhập số loại">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label for="vehicle_notes" class="form-label">Ghi chú về phương tiện</label>
                                        <textarea class="form-control" id="vehicle_notes" name="vehicle_notes" rows="2"
                                            placeholder="Ghi chú về phương tiện...">{{ old('vehicle_notes', $vehicle?->notes) }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Submit Buttons -->
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-2"></i>Cập nhật Tài sản
                            </button>
                            <button type="button" class="btn btn-outline-secondary ms-2" onclick="resetForm()">
                                <i class="bi bi-arrow-clockwise me-2"></i>Đặt lại
                            </button>
                        </div>
                        <div class="col-md-6 text-end">
                            <a href="{{ route('properties.show', $asset) }}" class="btn btn-outline-info me-2">
                                <i class="bi bi-eye me-2"></i>Xem chi tiết
                            </a>
                            <a href="{{ route('properties.index') }}" class="btn btn-outline-danger">
                                <i class="bi bi-x-circle me-2"></i>Hủy
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Create Certificate Type Modal -->
    <div class="modal fade" id="createCertificateTypeModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tạo loại chứng chỉ mới</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="createCertificateTypeForm">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="new_certificate_name" class="form-label">Tên loại chứng chỉ <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="new_certificate_name" name="name" required
                                   placeholder="Nhập tên loại chứng chỉ...">
                        </div>
                        <div class="mb-3">
                            <label for="new_certificate_description" class="form-label">Mô tả</label>
                            <textarea class="form-control" id="new_certificate_description" name="description" rows="2"
                                      placeholder="Mô tả về loại chứng chỉ..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-2"></i>Tạo
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Create Issuing Authority Modal -->
    <div class="modal fade" id="createIssuingAuthorityModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tạo cơ quan cấp phát mới</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="createIssuingAuthorityForm">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="new_authority_name" class="form-label">Tên cơ quan <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="new_authority_name" name="name" required
                                   placeholder="Nhập tên cơ quan...">
                        </div>
                        <div class="mb-3">
                            <label for="new_authority_address" class="form-label">Địa chỉ</label>
                            <textarea class="form-control" id="new_authority_address" name="address" rows="2"
                                      placeholder="Địa chỉ cơ quan..."></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="new_authority_phone" class="form-label">Điện thoại</label>
                                    <input type="text" class="form-control" id="new_authority_phone" name="phone"
                                           placeholder="Số điện thoại...">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="new_authority_email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="new_authority_email" name="email"
                                           placeholder="Email...">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-2"></i>Tạo
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/assets-common.js') }}?v={{ time() }}"></script>
    <script>
        // Set configuration for edit page
        AssetManager.config.routes.getFields = '{{ route('properties.get-fields') }}';
        AssetManager.config.routes.update = '{{ route('properties.update', $asset) }}';
        AssetManager.config.routes.createCertificateType = '{{ route('certificate-types.store') }}';
        AssetManager.config.routes.createIssuingAuthority = '{{ route('issuing-authorities.store') }}';
        AssetManager.config.routes.searchCertificateTypes = '{{ route('certificate-types.search') }}';
        AssetManager.config.routes.searchIssuingAuthorities = '{{ route('issuing-authorities.search') }}';
    </script>
    <script src="{{ asset('js/assets-form.js') }}?v={{ time() }}"></script>
    <script src="{{ asset('js/assets-search.js') }}?v={{ time() }}"></script>
    <script src="{{ asset('js/assets-date.js') }}?v={{ time() }}"></script>
    <script>
        // Initialize form for edit mode
        document.addEventListener('DOMContentLoaded', function() {
            AssetManager.form.init('edit');
        });
    </script>
@endsection
