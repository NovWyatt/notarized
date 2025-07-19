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
                        <div class="col-md-6">
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
                        <div class="col-md-6">
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

    <!-- Create Certificate Type Modal -->
    <div class="modal fade" id="createCertificateTypeModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tạo tên gọi GCNSQH tài sản</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="createCertificateTypeForm">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="new_certificate_name" class="form-label">Tên gọi GCNSQH tài sản<span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="new_certificate_name" name="name" required
                                placeholder="Nhập tên tên gọi GCNSQH tài sản...">
                        </div>
                        <div class="mb-3">
                            <label for="new_certificate_description" class="form-label">Ghi chú</label>
                            <textarea class="form-control" id="new_certificate_description" name="description" rows="2"
                                placeholder="Ghi chú tên gọi GCNSQH tài sản..."></textarea>
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
                            <label for="new_authority_name" class="form-label">Tên cơ quan <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="new_authority_name" name="name" required
                                placeholder="Nhập tên cơ quan...">
                        </div>
                        <div class="mb-3">
                            <label for="new_authority_description" class="form-label">Ghi Chú</label>
                            <textarea class="form-control" id="new_authority_description" name="description" rows="2"
                                placeholder="Ghi chú..."></textarea>
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
        // Set configuration for create page
        AssetManager.config.routes.getFields = '{{ route('properties.get-fields') }}';
        AssetManager.config.routes.store = '{{ route('properties.store') }}';
        AssetManager.config.routes.createCertificateType = '{{ route('certificate-types.store') }}';
        AssetManager.config.routes.createIssuingAuthority = '{{ route('issuing-authorities.store') }}';
        AssetManager.config.routes.searchCertificateTypes = '{{ route('certificate-types.search') }}';
        AssetManager.config.routes.searchIssuingAuthorities = '{{ route('issuing-authorities.search') }}';
    </script>
    <script src="{{ asset('js/assets-form.js') }}?v={{ time() }}"></script>
    <script src="{{ asset('js/assets-search.js') }}?v={{ time() }}"></script>
    <script src="{{ asset('js/assets-date.js') }}?v={{ time() }}"></script>
    <script>
        // Initialize form for create mode
        document.addEventListener('DOMContentLoaded', function() {
            AssetManager.form.init('create');
        });
    </script>
@endsection
