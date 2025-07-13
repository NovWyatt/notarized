{{-- resources/views/properties/create.blade.php --}}
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
                                <label for="asset_type" class="form-label">
                                    Loại tài sản <span class="text-danger">*</span>
                                </label>
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

    <script src="{{ asset('js/assets-common.js') }}?v={{ time() }}"></script>
    <script>
        // Set configuration for create page
        AssetManager.config.routes.getFields = '{{ route('properties.get-fields') }}';
        AssetManager.config.routes.store = '{{ route('properties.store') }}';
    </script>
    <script src="{{ asset('js/assets-form.js') }}?v={{ time() }}"></script>
    <script src="{{ asset('js/assets-date.js') }}?v={{ time() }}"></script>
    <script>
        // Initialize form for create mode
        document.addEventListener('DOMContentLoaded', function() {
            AssetManager.form.init('create');
        });
    </script>
@endsection
