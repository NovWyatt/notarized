{{-- resources/views/properties/edit.blade.php --}}
@extends('layouts.app2')
@section('content')
    <div class="container-fluid p-3">
        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-md-6">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('properties.index') }}">Tài sản</a></li>
                        <li class="breadcrumb-item"><a
                                href="{{ route('properties.show', $asset) }}">{{ $asset->asset_name ?: 'Tài sản #' . $asset->id }}</a>
                        </li>
                        <li class="breadcrumb-item active">Chỉnh sửa</li>
                    </ol>
                </nav>
                <h3 class="mb-0">Chỉnh sửa Tài sản</h3>
                <p class="text-muted">Cập nhật thông tin tài sản trong hệ thống</p>
            </div>
            <div class="col-md-6 text-end">
                <div class="btn-group" role="group">
                    <a href="{{ route('properties.show', $asset) }}" class="btn btn-outline-info">
                        <i class="bi bi-eye me-2"></i>Xem chi tiết
                    </a>
                    <a href="{{ route('properties.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Quay lại danh sách
                    </a>
                </div>
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
                {{ session('success') }}
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
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="asset_type" class="form-label">Loại tài sản <span
                                        class="text-danger">*</span></label>
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
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="asset_name" class="form-label">Tên tài sản</label>
                                <input type="text" class="form-control" id="asset_name" name="asset_name"
                                    value="{{ old('asset_name', $asset->asset_name) }}" placeholder="Nhập tên tài sản">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="estimated_value" class="form-label">Giá trị ước tính (₫)</label>
                                <input type="number" class="form-control" id="estimated_value" name="estimated_value"
                                    value="{{ old('estimated_value', $asset->estimated_value) }}" placeholder="0"
                                    min="0" step="1000">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="mb-3">
                                <label for="notes" class="form-label">Ghi chú</label>
                                <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Thêm ghi chú về tài sản...">{{ old('notes', $asset->notes) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Certificate Information -->
            @if (
                $asset->certificates->count() > 0 ||
                    in_array($asset->asset_type, ['real_estate_house', 'real_estate_apartment', 'real_estate_land_only']))
                @php $certificate = $asset->certificates->first() @endphp
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Thông tin Giấy Chứng Nhận</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="certificate_type" class="form-label">Loại giấy chứng nhận</label>
                                    <select class="form-select" id="certificate_type" name="certificate_type">
                                        <option value="">Chọn loại</option>
                                        @foreach ($certificateTypes as $value => $label)
                                            <option value="{{ $value }}"
                                                {{ old('certificate_type', $certificate?->certificate_type) === $value ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
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

            <!-- Land Plot Information -->
            @if (
                $asset->landPlots->count() > 0 ||
                    in_array($asset->asset_type, ['real_estate_house', 'real_estate_apartment', 'real_estate_land_only']))
                @php $landPlot = $asset->landPlots->first() @endphp
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
                                        value="{{ old('ward', $landPlot?->ward) }}" placeholder="Nhập phường/xã">
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
                                    <input type="text" class="form-control" id="usage_purpose" name="usage_purpose"
                                        value="{{ old('usage_purpose', $landPlot?->usage_purpose) }}"
                                        placeholder="Nhập mục đích sử dụng">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="land_use_term" class="form-label">Thời hạn sử dụng</label>
                                    <input type="date" class="form-control" id="land_use_term" name="land_use_term"
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

            <!-- House Information -->
            @if ($asset->house || in_array($asset->asset_type, ['real_estate_house']))
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
                                        value="{{ old('house_type', $asset->house?->house_type) }}"
                                        placeholder="Nhập loại nhà">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="construction_area" class="form-label">Diện tích xây dựng (m²)</label>
                                    <input type="number" class="form-control" id="construction_area"
                                        name="construction_area"
                                        value="{{ old('construction_area', $asset->house?->construction_area) }}"
                                        placeholder="0" min="0" step="0.01">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="floor_area" class="form-label">Diện tích sàn (m²)</label>
                                    <input type="number" class="form-control" id="floor_area" name="floor_area"
                                        value="{{ old('floor_area', $asset->house?->floor_area) }}" placeholder="0"
                                        min="0" step="0.01">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="number_of_floors" class="form-label">Số tầng</label>
                                    <input type="number" class="form-control" id="number_of_floors"
                                        name="number_of_floors"
                                        value="{{ old('number_of_floors', $asset->house?->number_of_floors) }}"
                                        placeholder="1" min="1" max="100">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="ownership_form" class="form-label">Hình thức sở hữu</label>
                                    <input type="text" class="form-control" id="ownership_form" name="ownership_form"
                                        value="{{ old('ownership_form', $asset->house?->ownership_form) }}"
                                        placeholder="Nhập hình thức sở hữu">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="grade_level" class="form-label">Cấp (Hạng)</label>
                                    <input type="text" class="form-control" id="grade_level" name="grade_level"
                                        value="{{ old('grade_level', $asset->house?->grade_level) }}"
                                        placeholder="Nhập cấp/hạng">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="ownership_term" class="form-label">Thời hạn sở hữu</label>
                                    <input type="date" class="form-control" id="ownership_term" name="ownership_term"
                                        value="{{ old('ownership_term', $asset->house?->ownership_term?->format('Y-m-d')) }}">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="structure" class="form-label">Kết cấu</label>
                                    <input type="text" class="form-control" id="structure" name="structure"
                                        value="{{ old('structure', $asset->house?->structure) }}"
                                        placeholder="Nhập kết cấu">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="house_notes" class="form-label">Ghi chú về nhà</label>
                                    <textarea class="form-control" id="house_notes" name="house_notes" rows="2"
                                        placeholder="Ghi chú về nhà ở...">{{ old('house_notes', $asset->house?->notes) }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Apartment Information -->
            @if ($asset->apartment || in_array($asset->asset_type, ['real_estate_apartment']))
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
                                        value="{{ old('apartment_number', $asset->apartment?->apartment_number) }}"
                                        placeholder="Nhập số căn hộ">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="apartment_floor" class="form-label">Căn hộ thuộc tầng</label>
                                    <input type="number" class="form-control" id="apartment_floor"
                                        name="apartment_floor"
                                        value="{{ old('apartment_floor', $asset->apartment?->apartment_floor) }}"
                                        placeholder="1" min="1" max="200">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="building_floors" class="form-label">Số tầng nhà chung cư</label>
                                    <input type="number" class="form-control" id="building_floors"
                                        name="building_floors"
                                        value="{{ old('building_floors', $asset->apartment?->building_floors) }}"
                                        placeholder="1" min="1" max="200">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="apartment_construction_area" class="form-label">Diện tích xây dựng
                                        (m²)</label>
                                    <input type="number" class="form-control" id="apartment_construction_area"
                                        name="apartment_construction_area"
                                        value="{{ old('apartment_construction_area', $asset->apartment?->construction_area) }}"
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
                                        value="{{ old('apartment_floor_area', $asset->apartment?->floor_area) }}"
                                        placeholder="0" min="0" step="0.01">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="apartment_ownership_form" class="form-label">Hình thức sở hữu</label>
                                    <input type="text" class="form-control" id="apartment_ownership_form"
                                        name="apartment_ownership_form"
                                        value="{{ old('apartment_ownership_form', $asset->apartment?->ownership_form) }}"
                                        placeholder="Nhập hình thức sở hữu">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="apartment_ownership_term" class="form-label">Thời hạn sở hữu</label>
                                    <input type="date" class="form-control" id="apartment_ownership_term"
                                        name="apartment_ownership_term"
                                        value="{{ old('apartment_ownership_term', $asset->apartment?->ownership_term?->format('Y-m-d')) }}">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="apartment_structure" class="form-label">Kết cấu</label>
                                    <input type="text" class="form-control" id="apartment_structure"
                                        name="apartment_structure"
                                        value="{{ old('apartment_structure', $asset->apartment?->structure) }}"
                                        placeholder="Nhập kết cấu">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="apartment_notes" class="form-label">Ghi chú về căn hộ</label>
                                    <textarea class="form-control" id="apartment_notes" name="apartment_notes" rows="2"
                                        placeholder="Ghi chú về căn hộ...">{{ old('apartment_notes', $asset->apartment?->notes) }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Vehicle Information -->
            @if ($asset->vehicle || in_array($asset->asset_type, ['movable_property_car', 'movable_property_motorcycle']))
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
                                        value="{{ old('registration_number', $asset->vehicle?->registration_number) }}"
                                        placeholder="Nhập số đăng ký">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="license_plate" class="form-label">Biển kiểm soát</label>
                                    <input type="text" class="form-control" id="license_plate" name="license_plate"
                                        value="{{ old('license_plate', $asset->vehicle?->license_plate) }}"
                                        placeholder="Nhập biển số">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="brand" class="form-label">Nhãn hiệu</label>
                                    <input type="text" class="form-control" id="brand" name="brand"
                                        value="{{ old('brand', $asset->vehicle?->brand) }}" placeholder="Nhập nhãn hiệu">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="vehicle_type" class="form-label">Loại xe</label>
                                    <input type="text" class="form-control" id="vehicle_type" name="vehicle_type"
                                        value="{{ old('vehicle_type', $asset->vehicle?->vehicle_type) }}"
                                        placeholder="Nhập loại xe">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="color" class="form-label">Màu sơn</label>
                                    <input type="text" class="form-control" id="color" name="color"
                                        value="{{ old('color', $asset->vehicle?->color) }}" placeholder="Nhập màu sơn">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="engine_number" class="form-label">Số máy</label>
                                    <input type="text" class="form-control" id="engine_number" name="engine_number"
                                        value="{{ old('engine_number', $asset->vehicle?->engine_number) }}"
                                        placeholder="Nhập số máy">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="chassis_number" class="form-label">Số khung</label>
                                    <input type="text" class="form-control" id="chassis_number" name="chassis_number"
                                        value="{{ old('chassis_number', $asset->vehicle?->chassis_number) }}"
                                        placeholder="Nhập số khung">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="vehicle_issue_date" class="form-label">Ngày cấp</label>
                                    <input type="date" class="form-control" id="vehicle_issue_date"
                                        name="vehicle_issue_date"
                                        value="{{ old('vehicle_issue_date', $asset->vehicle?->issue_date?->format('Y-m-d')) }}">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="issuing_authority" class="form-label">Nơi cấp</label>
                                    <input type="text" class="form-control" id="issuing_authority"
                                        name="issuing_authority"
                                        value="{{ old('issuing_authority', $asset->vehicle?->issuing_authority) }}"
                                        placeholder="Nhập nơi cấp">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="mb-3">
                                    <label for="payload" class="form-label">Trọng tải (tấn)</label>
                                    <input type="number" class="form-control" id="payload" name="payload"
                                        value="{{ old('payload', $asset->vehicle?->payload) }}" placeholder="0"
                                        min="0" step="0.01">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="mb-3">
                                    <label for="engine_capacity" class="form-label">Dung tích (L)</label>
                                    <input type="number" class="form-control" id="engine_capacity"
                                        name="engine_capacity"
                                        value="{{ old('engine_capacity', $asset->vehicle?->engine_capacity) }}"
                                        placeholder="0" min="0" step="0.01">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="mb-3">
                                    <label for="seating_capacity" class="form-label">Số chỗ ngồi</label>
                                    <input type="number" class="form-control" id="seating_capacity"
                                        name="seating_capacity"
                                        value="{{ old('seating_capacity', $asset->vehicle?->seating_capacity) }}"
                                        placeholder="0" min="1" max="100">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="mb-3">
                                    <label for="type_number" class="form-label">Số loại</label>
                                    <input type="text" class="form-control" id="type_number" name="type_number"
                                        value="{{ old('type_number', $asset->vehicle?->type_number) }}"
                                        placeholder="Nhập số loại">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="vehicle_notes" class="form-label">Ghi chú về phương tiện</label>
                                    <textarea class="form-control" id="vehicle_notes" name="vehicle_notes" rows="2"
                                        placeholder="Ghi chú về phương tiện...">{{ old('vehicle_notes', $asset->vehicle?->notes) }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Submit Buttons -->
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <button type="submit" class="btn btn-warning">
                                <i class="bi bi-check-circle me-2"></i>Cập nhật Tài sản
                            </button>
                            <button type="button" class="btn btn-outline-secondary ms-2" onclick="resetToOriginal()">
                                <i class="bi bi-arrow-clockwise me-2"></i>Khôi phục
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

        <!-- Changes History -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0 text-muted">
                            <i class="bi bi-clock-history me-2"></i>
                            Lịch sử thay đổi
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <small class="text-muted">Được tạo:</small>
                                <div>{{ $asset->created_at->format('d/m/Y H:i:s') }}</div>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted">Cập nhật lần cuối:</small>
                                <div>{{ $asset->updated_at->format('d/m/Y H:i:s') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // ===== COMPLETE JAVASCRIPT FOR EDIT FORM =====

        document.addEventListener('DOMContentLoaded', function() {

            // ===== DYNAMIC ASSET TYPE HANDLING =====

            let currentAssetType = document.getElementById('asset_type').value;

            // Asset type change handler
            document.getElementById('asset_type').addEventListener('change', function() {
                const newAssetType = this.value;

                if (newAssetType === currentAssetType) {
                    return; // No change, do nothing
                }

                // Warn user about changes
                const confirmed = confirm(
                    'Thay đổi loại tài sản sẽ ẩn/hiện các phần thông tin tương ứng. ' +
                    'Dữ liệu trong các phần không phù hợp sẽ được giữ nguyên nhưng có thể không được lưu. ' +
                    'Bạn có chắc chắn muốn thay đổi?'
                );

                if (!confirmed) {
                    // Revert to previous selection
                    this.value = currentAssetType;
                    return;
                }

                // Update current asset type
                currentAssetType = newAssetType;

                // Update sections
                updateSections(newAssetType);

                // Show notification
                showNotification(newAssetType);
            });

            function updateSections(assetType) {
                // Find all sections by looking for specific titles
                const cards = [...document.querySelectorAll('.card')];
                const sections = {
                    certificate: cards.find(card => card.querySelector('h5')?.textContent.includes(
                        'Giấy Chứng Nhận')),
                    landPlot: cards.find(card => card.querySelector('h5')?.textContent.includes('Thửa Đất')),
                    house: cards.find(card => card.querySelector('h5')?.textContent.includes('Nhà Ở')),
                    apartment: cards.find(card => card.querySelector('h5')?.textContent.includes('Căn Hộ')),
                    vehicle: cards.find(card => card.querySelector('h5')?.textContent.includes('Phương Tiện'))
                };

                console.log('Found sections:', sections);

                // Hide all dynamic sections first
                Object.values(sections).forEach(section => {
                    if (section) {
                        section.style.transition = 'all 0.3s ease';
                        section.style.opacity = '0';
                        section.style.transform = 'translateY(-10px)';
                        setTimeout(() => {
                            section.style.display = 'none';
                        }, 300);
                    }
                });

                // Show appropriate sections after hide animation
                setTimeout(() => {
                    // Real estate sections
                    if (['real_estate_house', 'real_estate_apartment', 'real_estate_land_only'].includes(
                            assetType)) {
                        showSection(sections.certificate);
                        showSection(sections.landPlot);
                    }

                    // House specific
                    if (assetType === 'real_estate_house') {
                        showSection(sections.house);
                    }

                    // Apartment specific
                    if (assetType === 'real_estate_apartment') {
                        showSection(sections.apartment);
                    }

                    // Vehicle sections
                    if (['movable_property_car', 'movable_property_motorcycle'].includes(assetType)) {
                        showSection(sections.vehicle);
                    }
                }, 350);
            }

            function showSection(section) {
                if (section) {
                    section.style.display = 'block';
                    section.style.opacity = '0';
                    section.style.transform = 'translateY(-10px)';

                    // Force reflow
                    section.offsetHeight;

                    // Animate in
                    section.style.transition = 'all 0.4s ease';
                    section.style.opacity = '1';
                    section.style.transform = 'translateY(0)';

                    // Add highlight
                    section.style.boxShadow = '0 0 20px rgba(13, 110, 253, 0.3)';
                    section.style.borderColor = '#0d6efd';

                    setTimeout(() => {
                        section.style.boxShadow = '';
                        section.style.borderColor = '';
                    }, 2000);
                }
            }

            function showNotification(assetType) {
                // Remove existing notification
                const existing = document.getElementById('asset-type-notification');
                if (existing) existing.remove();

                // Get asset type label
                const select = document.getElementById('asset_type');
                const option = select.options[select.selectedIndex];
                const label = option ? option.text : assetType;

                // Create notification
                const notification = document.createElement('div');
                notification.id = 'asset-type-notification';
                notification.className = 'alert alert-info alert-dismissible fade show mt-3';
                notification.innerHTML = `
            <i class="bi bi-info-circle me-2"></i>
            <strong>Đã thay đổi loại tài sản thành "${label}".</strong>
            Các phần thông tin đã được cập nhật.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;

                // Insert after first card
                const firstCard = document.querySelector('.card');
                firstCard.parentNode.insertBefore(notification, firstCard.nextSibling);

                // Auto remove
                setTimeout(() => {
                    if (notification.parentNode) {
                        notification.remove();
                    }
                }, 5000);
            }

            // ===== FORM DATA MANAGEMENT =====

            // Store original form data for reset functionality
            const originalFormData = new FormData(document.getElementById('assetForm'));

            // Store original values in data attributes
            const form = document.getElementById('assetForm');
            const inputs = form.querySelectorAll('input, select, textarea');
            inputs.forEach(input => {
                input.setAttribute('data-original-value', input.value);
            });

            // Store original asset type
            document.getElementById('asset_type').setAttribute('data-original-value', currentAssetType);

            // ===== RESET FUNCTIONALITY =====

            // Enhanced reset function
            window.resetToOriginal = function() {
                if (confirm('Bạn có chắc chắn muốn khôi phục về dữ liệu ban đầu? Tất cả thay đổi sẽ bị mất.')) {
                    const form = document.getElementById('assetForm');
                    const inputs = form.querySelectorAll('input, select, textarea');

                    inputs.forEach(input => {
                        const originalValue = input.getAttribute('data-original-value') || input
                            .defaultValue;
                        if (input.type === 'checkbox' || input.type === 'radio') {
                            input.checked = input.defaultChecked;
                        } else {
                            input.value = originalValue;
                        }
                    });

                    // Reset asset type
                    const originalType = document.getElementById('asset_type').getAttribute(
                        'data-original-value');
                    if (originalType) {
                        document.getElementById('asset_type').value = originalType;
                        currentAssetType = originalType;
                    }

                    // Show all sections (reset to initial state)
                    const cards = [...document.querySelectorAll('.card')];
                    const sections = {
                        certificate: cards.find(card => card.querySelector('h5')?.textContent.includes(
                            'Giấy Chứng Nhận')),
                        landPlot: cards.find(card => card.querySelector('h5')?.textContent.includes(
                            'Thửa Đất')),
                        house: cards.find(card => card.querySelector('h5')?.textContent.includes('Nhà Ở')),
                        apartment: cards.find(card => card.querySelector('h5')?.textContent.includes(
                            'Căn Hộ')),
                        vehicle: cards.find(card => card.querySelector('h5')?.textContent.includes(
                            'Phương Tiện'))
                    };

                    Object.values(sections).forEach(section => {
                        if (section) {
                            section.style.display = 'block';
                            section.style.opacity = '1';
                            section.style.transform = 'translateY(0)';
                            section.style.boxShadow = '';
                            section.style.borderColor = '';
                            section.style.transition = '';
                        }
                    });

                    // Remove notification
                    const notification = document.getElementById('asset-type-notification');
                    if (notification) notification.remove();

                    // Reset form change tracking
                    formChanged = false;
                }
            };

            // ===== FORM CHANGE TRACKING =====

            // Track form changes
            let formChanged = false;
            form.addEventListener('input', function() {
                formChanged = true;
            });

            form.addEventListener('change', function() {
                formChanged = true;
            });

            // Warn user about unsaved changes
            window.addEventListener('beforeunload', function(e) {
                if (formChanged) {
                    e.preventDefault();
                    e.returnValue = 'Bạn có thay đổi chưa được lưu. Bạn có chắc chắn muốn rời khỏi trang?';
                }
            });

            // Don't warn when submitting form
            form.addEventListener('submit', function() {
                formChanged = false;
            });

            // ===== CURRENCY FORMATTING =====

            // Format currency input
            const estimatedValueInput = document.getElementById('estimated_value');
            if (estimatedValueInput) {
                estimatedValueInput.addEventListener('input', function() {
                    let value = this.value.replace(/\D/g, '');
                    if (value) {
                        this.value = value;
                    }
                });
            }

            // ===== FORM VALIDATION =====

            // Highlight required fields that are empty
            function highlightRequiredFields() {
                const requiredFields = document.querySelectorAll('[required]');
                requiredFields.forEach(field => {
                    if (!field.value.trim()) {
                        field.classList.add('is-invalid');
                    } else {
                        field.classList.remove('is-invalid');
                    }
                });
            }

            // Add form validation on submit
            form.addEventListener('submit', function(e) {
                // Validate all visible required fields
                const visibleRequiredFields = document.querySelectorAll(
                    '[required]:not([style*="display: none"])');
                let hasErrors = false;

                visibleRequiredFields.forEach(field => {
                    // Check if field is in a hidden section
                    const parentCard = field.closest('.card');
                    const isInHiddenSection = parentCard && parentCard.style.display === 'none';

                    if (!isInHiddenSection && !field.value.trim()) {
                        field.classList.add('is-invalid');
                        hasErrors = true;
                    } else {
                        field.classList.remove('is-invalid');
                    }
                });

                if (hasErrors) {
                    e.preventDefault();
                    alert('Vui lòng điền đầy đủ các trường bắt buộc.');

                    // Focus on first invalid field
                    const firstInvalid = document.querySelector('.is-invalid');
                    if (firstInvalid) {
                        firstInvalid.focus();
                        firstInvalid.scrollIntoView({
                            behavior: 'smooth',
                            block: 'center'
                        });
                    }
                }
            });

            // Real-time validation
            document.querySelectorAll('[required]').forEach(field => {
                field.addEventListener('blur', function() {
                    const parentCard = this.closest('.card');
                    const isInHiddenSection = parentCard && parentCard.style.display === 'none';

                    if (!isInHiddenSection) {
                        if (!this.value.trim()) {
                            this.classList.add('is-invalid');
                        } else {
                            this.classList.remove('is-invalid');
                        }
                    }
                });

                field.addEventListener('input', function() {
                    if (this.classList.contains('is-invalid') && this.value.trim()) {
                        this.classList.remove('is-invalid');
                    }
                });
            });

            // ===== AUTO-SAVE FUNCTIONALITY (OPTIONAL) =====

            let autoSaveTimeout;
            form.addEventListener('input', function() {
                clearTimeout(autoSaveTimeout);
                autoSaveTimeout = setTimeout(function() {
                    // You can implement auto-save here if needed
                    console.log('Auto-save would be triggered here');
                    // Example: save form data to localStorage or send AJAX request
                }, 30000); // Auto-save after 30 seconds of inactivity
            });

            // ===== FORM FIELD ENHANCEMENTS =====

            // Add loading state to submit button
            const submitButton = document.querySelector('button[type="submit"]');
            if (submitButton) {
                form.addEventListener('submit', function() {
                    submitButton.disabled = true;
                    submitButton.innerHTML =
                        '<span class="spinner-border spinner-border-sm me-2"></span>Đang cập nhật...';

                    // Re-enable button after 5 seconds (fallback)
                    setTimeout(() => {
                        submitButton.disabled = false;
                        submitButton.innerHTML =
                            '<i class="bi bi-check-circle me-2"></i>Cập nhật Tài sản';
                    }, 5000);
                });
            }

            // Add confirmation for dangerous actions
            const cancelLinks = document.querySelectorAll('a[href*="properties"]');
            cancelLinks.forEach(link => {
                if (link.textContent.includes('Hủy') || link.textContent.includes('Quay lại')) {
                    link.addEventListener('click', function(e) {
                        if (formChanged) {
                            const confirmed = confirm(
                                'Bạn có thay đổi chưa được lưu. Bạn có chắc chắn muốn rời khỏi?'
                                );
                            if (!confirmed) {
                                e.preventDefault();
                            }
                        }
                    });
                }
            });

            // ===== ACCESSIBILITY IMPROVEMENTS =====

            // Add keyboard navigation for form sections
            document.addEventListener('keydown', function(e) {
                // Ctrl/Cmd + S to save form
                if ((e.ctrlKey || e.metaKey) && e.key === 's') {
                    e.preventDefault();
                    form.submit();
                }

                // Ctrl/Cmd + R to reset form
                if ((e.ctrlKey || e.metaKey) && e.key === 'r') {
                    e.preventDefault();
                    resetToOriginal();
                }
            });

            // Add focus management for better UX
            const firstFormField = form.querySelector('input, select, textarea');
            if (firstFormField && !firstFormField.value) {
                // Don't auto-focus if field already has value (editing mode)
                setTimeout(() => {
                    firstFormField.focus();
                }, 100);
            }

            // ===== INITIALIZATION COMPLETE =====

            console.log('Edit form JavaScript initialized successfully');
            console.log('Current asset type:', currentAssetType);
            console.log('Form change tracking enabled');
            console.log('Validation enabled');
            console.log('Auto-save enabled (30s delay)');
        });

        // ===== CSS STYLES =====
        const editFormStyles = document.createElement('style');
        editFormStyles.textContent = `
    /* Form validation styles */
    .is-invalid {
        border-color: #dc3545 !important;
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
    }

    .is-invalid:focus {
        border-color: #dc3545 !important;
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
    }

    /* Card animations */
    .card {
        transition: all 0.3s ease;
    }

    /* Notification styles */
    #asset-type-notification {
        animation: slideInDown 0.4s ease;
        border-left: 4px solid #0dcaf0;
        background: linear-gradient(135deg, #d1ecf1 0%, #bee5eb 100%);
        border-radius: 8px;
    }

    @keyframes slideInDown {
        from {
            transform: translateY(-30px);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    /* Form field focus styles */
    .form-control:focus,
    .form-select:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
    }

    /* Button loading state */
    .btn:disabled {
        opacity: 0.7;
        cursor: not-allowed;
    }

    /* Smooth scroll for validation focus */
    html {
        scroll-behavior: smooth;
    }

    /* Card header improvements */
    .card-header h5 {
        font-weight: 500;
        color: #495057;
    }

    .card-header h6 {
        font-weight: 500;
    }

    /* Form label improvements */
    .form-label {
        font-weight: 500;
        color: #495057;
    }

    /* Button group spacing */
    .btn-group .btn {
        border-radius: 0.375rem;
        margin-right: 0.25rem;
    }

    /* Card shadow */
    .card {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        border: 1px solid rgba(0, 0, 0, 0.125);
    }

    /* Highlight effect for changed sections */
    .section-highlight {
        transform: scale(1.01);
        transition: all 0.3s ease;
    }
`;
        document.head.appendChild(editFormStyles);
    </script>
    <style>
        .is-invalid {
            border-color: #dc3545;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
        }

        .card-header h6 {
            font-weight: 500;
        }

        .form-label {
            font-weight: 500;
            color: #495057;
        }

        .btn-group .btn {
            border-radius: 0.375rem;
            margin-right: 0.25rem;
        }

        .card {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            border: 1px solid rgba(0, 0, 0, 0.125);
        }
    </style>
    <script src="{{ asset('js/assets-date.js') }}?v={{ time() }}"></script>
@endsection
