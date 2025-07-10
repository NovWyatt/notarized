{{-- resources/views/properties/create.blade.php --}}
@extends('layouts.app2')
@section('content')
    <div class="container-fluid p-3">
        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-md-6">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('properties.index') }}">Tài sản</a></li>
                        <li class="breadcrumb-item active">Thêm mới</li>
                    </ol>
                </nav>
                <h3 class="mb-0">Thêm Tài sản Mới</h3>
                <p class="text-muted">Điền thông tin để tạo tài sản mới trong hệ thống</p>
            </div>
            <div class="col-md-6 text-end">
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

        <!-- Form -->
        <form method="POST" action="{{ route('properties.store') }}" id="assetForm">
            @csrf

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
                                            {{ old('asset_type') === $value ? 'selected' : '' }}>
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
                                    value="{{ old('asset_name') }}" placeholder="Nhập tên tài sản">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="estimated_value" class="form-label">Giá trị ước tính (₫)</label>
                                <input type="number" class="form-control" id="estimated_value" name="estimated_value"
                                    value="{{ old('estimated_value') }}" placeholder="0" min="0" step="1000">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="mb-3">
                                <label for="notes" class="form-label">Ghi chú</label>
                                <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Thêm ghi chú về tài sản...">{{ old('notes') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dynamic Fields Container -->
            <div id="dynamic-fields"></div>

            <!-- Submit Buttons -->
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-2"></i>Tạo Tài sản
                            </button>
                            <button type="button" class="btn btn-outline-secondary ms-2" onclick="resetForm()">
                                <i class="bi bi-arrow-clockwise me-2"></i>Đặt lại
                            </button>
                        </div>
                        <div class="col-md-6 text-end">
                            <a href="{{ route('properties.index') }}" class="btn btn-outline-danger">
                                <i class="bi bi-x-circle me-2"></i>Hủy
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        // Dynamic field templates
        const fieldTemplates = {
            certificate: `
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
                                                <option value="{{ $value }}" {{ old('certificate_type') === $value ? 'selected' : '' }}>
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
                                               value="{{ old('issue_number') }}" placeholder="Nhập số phát hành">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="book_number" class="form-label">Số vào sổ</label>
                                        <input type="text" class="form-control" id="book_number" name="book_number"
                                               value="{{ old('book_number') }}" placeholder="Nhập số vào sổ">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="issue_date" class="form-label">Ngày cấp</label>
                                        <input type="date" class="form-control" id="issue_date" name="issue_date">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `,

            landPlot: `
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
                                               value="{{ old('plot_number') }}" placeholder="Nhập số thửa">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="map_sheet_number" class="form-label">Tờ bản đồ số</label>
                                        <input type="text" class="form-control" id="map_sheet_number" name="map_sheet_number"
                                               value="{{ old('map_sheet_number') }}" placeholder="Nhập tờ bản đồ">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="area" class="form-label">Diện tích (m²)</label>
                                        <input type="number" class="form-control" id="area" name="area"
                                               value="{{ old('area') }}" placeholder="0" min="0" step="0.01">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="house_number" class="form-label">Số nhà</label>
                                        <input type="text" class="form-control" id="house_number" name="house_number"
                                               value="{{ old('house_number') }}" placeholder="Nhập số nhà">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="street_name" class="form-label">Tên đường</label>
                                        <input type="text" class="form-control" id="street_name" name="street_name"
                                            value="{{ old('street_name') }}" placeholder="Nhập tên đường">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="mb-3">
                                        <label for="province" class="form-label">Tỉnh/Thành</label>
                                        <!-- Input text mới -->
                                        <input type="text" class="form-control" id="province" name="province"
                                        placeholder="Nhập tỉnh/thành">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="mb-3">
                                        <label for="district" class="form-label">Quận/Huyện</label>
                                        <input type="text" class="form-control" id="district" name="district"
                                        placeholder="Nhập quận/huyện">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="mb-3">
                                        <label for="ward" class="form-label">Phường/Xã</label>
                                        <input type="text" class="form-control" id="ward" name="ward"
                                        placeholder="Nhập phường xã">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="usage_form" class="form-label">Hình thức sử dụng</label>
                                        <input type="text" class="form-control" id="usage_form" name="usage_form"
                                            value="{{ old('usage_form') }}" placeholder="Nhập hình thức sử dụng">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="usage_purpose" class="form-label">Mục đích sử dụng</label>
                                        <input type="text" class="form-control" id="usage_purpose" name="usage_purpose"
                                            value="{{ old('usage_purpose') }}" placeholder="Nhập mục đích sử dụng">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="land_use_term" class="form-label">Thời hạn sử dụng</label>
                                        <input type="date" class="form-control" id="land_use_term" name="land_use_term"
                                            value="{{ old('land_use_term') }}">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="usage_origin" class="form-label">Nguồn gốc sử dụng</label>
                                        <input type="text" class="form-control" id="usage_origin" name="usage_origin"
                                            value="{{ old('usage_origin') }}" placeholder="Nhập nguồn gốc sử dụng">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="land_notes" class="form-label">Ghi chú về đất</label>
                                        <textarea class="form-control" id="land_notes" name="land_notes" rows="2"
                                                placeholder="Ghi chú về thửa đất...">{{ old('land_notes') }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `,

            house: `
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
                                            value="{{ old('house_type') }}" placeholder="Nhập loại nhà">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="construction_area" class="form-label">Diện tích xây dựng (m²)</label>
                                        <input type="number" class="form-control" id="construction_area" name="construction_area"
                                            value="{{ old('construction_area') }}" placeholder="0" min="0" step="0.01">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="floor_area" class="form-label">Diện tích sàn (m²)</label>
                                        <input type="number" class="form-control" id="floor_area" name="floor_area"
                                            value="{{ old('floor_area') }}" placeholder="0" min="0" step="0.01">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="number_of_floors" class="form-label">Số tầng</label>
                                        <input type="number" class="form-control" id="number_of_floors" name="number_of_floors"
                                            value="{{ old('number_of_floors') }}" placeholder="1" min="1" max="100">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="ownership_form" class="form-label">Hình thức sở hữu</label>
                                        <input type="text" class="form-control" id="ownership_form" name="ownership_form"
                                            value="{{ old('ownership_form') }}" placeholder="Nhập hình thức sở hữu">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="grade_level" class="form-label">Cấp (Hạng)</label>
                                        <input type="text" class="form-control" id="grade_level" name="grade_level"
                                            value="{{ old('grade_level') }}" placeholder="Nhập cấp/hạng">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="ownership_term" class="form-label">Thời hạn sở hữu</label>
                                        <input type="date" class="form-control" id="ownership_term" name="ownership_term"
                                            value="{{ old('ownership_term') }}">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="structure" class="form-label">Kết cấu</label>
                                        <input type="text" class="form-control" id="structure" name="structure"
                                            value="{{ old('structure') }}" placeholder="Nhập kết cấu">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="house_notes" class="form-label">Ghi chú về nhà</label>
                                        <textarea class="form-control" id="house_notes" name="house_notes" rows="2"
                                                  placeholder="Ghi chú về nhà ở...">{{ old('house_notes') }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `,

            apartment: `
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Thông tin Căn Hộ</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="apartment_number" class="form-label">Căn hộ số</label>
                                        <input type="text" class="form-control" id="apartment_number" name="apartment_number"
                                               value="{{ old('apartment_number') }}" placeholder="Nhập số căn hộ">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="apartment_floor" class="form-label">Căn hộ thuộc tầng</label>
                                        <input type="number" class="form-control" id="apartment_floor" name="apartment_floor"
                                               value="{{ old('apartment_floor') }}" placeholder="1" min="1" max="200">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="building_floors" class="form-label">Số tầng nhà chung cư</label>
                                        <input type="number" class="form-control" id="building_floors" name="building_floors"
                                               value="{{ old('building_floors') }}" placeholder="1" min="1" max="200">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="apartment_construction_area" class="form-label">Diện tích xây dựng (m²)</label>
                                        <input type="number" class="form-control" id="apartment_construction_area" name="apartment_construction_area"
                                               value="{{ old('apartment_construction_area') }}" placeholder="0" min="0" step="0.01">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="apartment_floor_area" class="form-label">Diện tích sàn (m²)</label>
                                        <input type="number" class="form-control" id="apartment_floor_area" name="apartment_floor_area"
                                               value="{{ old('apartment_floor_area') }}" placeholder="0" min="0" step="0.01">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="apartment_ownership_form" class="form-label">Hình thức sở hữu</label>
                                        <input type="text" class="form-control" id="apartment_ownership_form" name="apartment_ownership_form"
                                               value="{{ old('apartment_ownership_form') }}" placeholder="Nhập hình thức sở hữu">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="apartment_ownership_term" class="form-label">Thời hạn sở hữu</label>
                                        <input type="date" class="form-control" id="apartment_ownership_term" name="apartment_ownership_term"
                                               value="{{ old('apartment_ownership_term') }}">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="apartment_structure" class="form-label">Kết cấu</label>
                                        <input type="text" class="form-control" id="apartment_structure" name="apartment_structure"
                                               value="{{ old('apartment_structure') }}" placeholder="Nhập kết cấu">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="apartment_notes" class="form-label">Ghi chú về căn hộ</label>
                                        <textarea class="form-control" id="apartment_notes" name="apartment_notes" rows="2"
                                                  placeholder="Ghi chú về căn hộ...">{{ old('apartment_notes') }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `,

            vehicle: `
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Thông tin Phương Tiện</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="registration_number" class="form-label">Giấy đăng ký số</label>
                                        <input type="text" class="form-control" id="registration_number" name="registration_number"
                                               value="{{ old('registration_number') }}" placeholder="Nhập số đăng ký">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="license_plate" class="form-label">Biển kiểm soát</label>
                                        <input type="text" class="form-control" id="license_plate" name="license_plate"
                                               value="{{ old('license_plate') }}" placeholder="Nhập biển số">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="brand" class="form-label">Nhãn hiệu</label>
                                        <input type="text" class="form-control" id="brand" name="brand"
                                               value="{{ old('brand') }}" placeholder="Nhập nhãn hiệu">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="vehicle_type" class="form-label">Loại xe</label>
                                        <input type="text" class="form-control" id="vehicle_type" name="vehicle_type"
                                               value="{{ old('vehicle_type') }}" placeholder="Nhập loại xe">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="color" class="form-label">Màu sơn</label>
                                        <input type="text" class="form-control" id="color" name="color"
                                               value="{{ old('color') }}" placeholder="Nhập màu sơn">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="engine_number" class="form-label">Số máy</label>
                                        <input type="text" class="form-control" id="engine_number" name="engine_number"
                                               value="{{ old('engine_number') }}" placeholder="Nhập số máy">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="chassis_number" class="form-label">Số khung</label>
                                        <input type="text" class="form-control" id="chassis_number" name="chassis_number"
                                               value="{{ old('chassis_number') }}" placeholder="Nhập số khung">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="vehicle_issue_date" class="form-label">Ngày cấp</label>
                                        <input type="date" class="form-control" id="vehicle_issue_date" name="vehicle_issue_date"
                                               value="{{ old('vehicle_issue_date') }}">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="issuing_authority" class="form-label">Nơi cấp</label>
                                        <input type="text" class="form-control" id="issuing_authority" name="issuing_authority"
                                               value="{{ old('issuing_authority') }}" placeholder="Nhập nơi cấp">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="mb-3">
                                        <label for="payload" class="form-label">Trọng tải (tấn)</label>
                                        <input type="number" class="form-control" id="payload" name="payload"
                                               value="{{ old('payload') }}" placeholder="0" min="0" step="0.01">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="mb-3">
                                        <label for="engine_capacity" class="form-label">Dung tích (L)</label>
                                        <input type="number" class="form-control" id="engine_capacity" name="engine_capacity"
                                               value="{{ old('engine_capacity') }}" placeholder="0" min="0" step="0.01">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="mb-3">
                                        <label for="seating_capacity" class="form-label">Số chỗ ngồi</label>
                                        <input type="number" class="form-control" id="seating_capacity" name="seating_capacity"
                                               value="{{ old('seating_capacity') }}" placeholder="0" min="1" max="100">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="mb-3">
                                        <label for="type_number" class="form-label">Số loại</label>
                                        <input type="text" class="form-control" id="type_number" name="type_number"
                                               value="{{ old('type_number') }}" placeholder="Nhập số loại">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label for="vehicle_notes" class="form-label">Ghi chú về phương tiện</label>
                                        <textarea class="form-control" id="vehicle_notes" name="vehicle_notes" rows="2"
                                                  placeholder="Ghi chú về phương tiện...">{{ old('vehicle_notes') }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `
        };

        document.getElementById('asset_type').addEventListener('change', function() {
            const assetType = this.value;
            const dynamicFields = document.getElementById('dynamic-fields');

            console.log('Asset type changed to:', assetType);

            if (!assetType) {
                dynamicFields.innerHTML = '';
                return;
            }

            // Show loading
            dynamicFields.innerHTML =
                '<div class="text-center py-3"><div class="spinner-border text-primary" role="status"></div></div>';

            // Get CSRF token từ nhiều nguồn
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ||
                document.querySelector('input[name="_token"]')?.value || '';

            console.log('CSRF Token:', csrfToken ? 'Found' : 'Not found');

            // Tạo URL với debug
            const baseUrl = '{{ route('properties.get-fields') }}';
            const url = `${baseUrl}?asset_type=${encodeURIComponent(assetType)}`;

            console.log('Request URL:', url);

            const requestOptions = {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            };

            // Chỉ thêm CSRF nếu có
            if (csrfToken) {
                requestOptions.headers['X-CSRF-TOKEN'] = csrfToken;
            }

            console.log('Request options:', requestOptions);

            fetch(url, requestOptions)
                .then(response => {
                    console.log('Response received:', {
                        status: response.status,
                        statusText: response.statusText,
                        ok: response.ok,
                        headers: Object.fromEntries(response.headers.entries())
                    });

                    // Lấy text trước để debug
                    return response.text().then(text => {
                        console.log('Raw response text:', text);

                        // Kiểm tra xem có phải JSON không
                        try {
                            const jsonData = JSON.parse(text);
                            console.log('Parsed JSON:', jsonData);
                            return {
                                ok: response.ok,
                                status: response.status,
                                data: jsonData,
                                contentType: response.headers.get('content-type')
                            };
                        } catch (parseError) {
                            console.error('JSON Parse Error:', parseError);
                            console.log('Response was not JSON. First 500 chars:', text.substring(0,
                                500));
                            throw new Error(
                                `Invalid JSON response. Status: ${response.status}. Content: ${text.substring(0, 100)}...`
                            );
                        }
                    });
                })
                .then(result => {
                    console.log('Processed result:', result);

                    if (!result.ok) {
                        throw new Error(`HTTP ${result.status}: ${result.data.message || 'Unknown error'}`);
                    }

                    const data = result.data;

                    // Kiểm tra có lỗi trong response không
                    if (data.error) {
                        throw new Error(data.message || data.error);
                    }

                    let fieldsHtml = '';

                    // Add certificate fields
                    if (data.certificate_fields) {
                        console.log('Adding certificate fields');
                        fieldsHtml += fieldTemplates.certificate;
                    }

                    // Add land plot fields
                    if (data.land_plot_fields) {
                        console.log('Adding land plot fields');
                        fieldsHtml += fieldTemplates.landPlot;
                    }

                    // Add house fields
                    if (data.house_fields) {
                        console.log('Adding house fields');
                        fieldsHtml += fieldTemplates.house;
                    }

                    // Add apartment fields
                    if (data.apartment_fields) {
                        console.log('Adding apartment fields');
                        fieldsHtml += fieldTemplates.apartment;
                    }

                    // Add vehicle fields
                    if (data.vehicle_fields) {
                        console.log('Adding vehicle fields');
                        fieldsHtml += fieldTemplates.vehicle;
                    }

                    console.log('Final HTML length:', fieldsHtml.length);
                    dynamicFields.innerHTML = fieldsHtml;
                })
                .catch(error => {
                    console.error('Complete error details:', error);

                    let errorMessage = 'Có lỗi xảy ra khi tải form.';
                    let technicalDetails = error.message;

                    if (error.message.includes('NetworkError') || error.message.includes('Failed to fetch')) {
                        errorMessage = 'Lỗi kết nối mạng. Vui lòng kiểm tra kết nối internet.';
                    } else if (error.message.includes('404')) {
                        errorMessage = 'Không tìm thấy endpoint. Vui lòng kiểm tra route.';
                    } else if (error.message.includes('500')) {
                        errorMessage = 'Lỗi server. Vui lòng kiểm tra logs.';
                    } else if (error.message.includes('JSON')) {
                        errorMessage = 'Server trả về dữ liệu không đúng định dạng.';
                    }

                    dynamicFields.innerHTML = `
                <div class="alert alert-danger">
                    <h6><i class="bi bi-exclamation-triangle me-2"></i>Lỗi Debug</h6>
                    <p class="mb-2"><strong>${errorMessage}</strong></p>
                    <details>
                        <summary>Chi tiết kỹ thuật (click để xem)</summary>
                        <pre class="mt-2 small text-muted">${technicalDetails}</pre>
                        <hr>
                        <p class="small mb-1"><strong>URL:</strong> ${url}</p>
                        <p class="small mb-1"><strong>CSRF Token:</strong> ${csrfToken ? 'Có' : 'Không có'}</p>
                        <p class="small mb-0"><strong>Thời gian:</strong> ${new Date().toLocaleString()}</p>
                    </details>
                    <button type="button" class="btn btn-sm btn-outline-danger mt-2" onclick="document.getElementById('asset_type').dispatchEvent(new Event('change'))">
                        <i class="bi bi-arrow-clockwise me-1"></i>Thử lại
                    </button>
                </div>
            `;
                });
        });
    </script>
    <script src="{{ asset('js/assets-date.js') }}?v={{ time() }}"></script>
@endsection
