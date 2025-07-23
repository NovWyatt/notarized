{{-- resources/views/properties/index.blade.php --}}
@extends('layouts.app')

<style>
    .asset-checkbox {
        cursor: pointer;
    }

    .table-hover tbody tr:hover {
        background-color: rgba(0, 0, 0, .05);
    }

    .btn-group-sm .btn {
        padding: 0.25rem 0.5rem;
    }

    .spinner-border-sm {
        width: 1rem;
        height: 1rem;
    }

    .avatar-sm {
        width: 2.5rem;
        height: 2.5rem;
    }

    .avatar-xs {
        width: 1.5rem;
        height: 1.5rem;
    }

    .avatar-title {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        font-weight: 500;
    }

    .bg-soft-primary {
        background-color: rgba(13, 110, 253, 0.1);
        color: #0d6efd;
    }
</style>

@section('content')
    <div class="container-fluid p-3">
        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-md-6">
                <h3 class="mb-0">Quản lý Tài sản</h3>
                <p class="text-muted">Danh sách tất cả tài sản trong hệ thống</p>
            </div>
            <div class="col-md-6 text-end">
                <a href="{{ route('properties.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Thêm Tài sản Mới
                </a>
                {{-- <button class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#exportModal">
                    <i class="bi bi-download me-2"></i>Xuất Excel
                </button> --}}
            </div>
        </div>

        <!-- Filters & Search -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('properties.index') }}" class="row g-3">
                    <div class="col-md-3">
                        <label for="search" class="form-label">Tìm kiếm</label>
                        <input type="text" class="form-control" id="search" name="search" value="{{ $searchTerm }}"
                            placeholder="Địa chỉ, biển số, ghi chú...">
                    </div>
                    <div class="col-md-3">
                        <label for="asset_type" class="form-label">Loại tài sản</label>
                        <select class="form-select" id="asset_type" name="asset_type">
                            <option value="">Tất cả loại</option>
                            @foreach ($assetTypes as $value => $label)
                                <option value="{{ $value }}" {{ $selectedType === $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="created_by" class="form-label">Người tạo</label>
                        <select class="form-select" id="created_by" name="created_by">
                            <option value="">Tất cả</option>
                            @foreach ($users as $userId => $userName)
                                <option value="{{ $userId }}" {{ $selectedCreator == $userId ? 'selected' : '' }}>
                                    {{ $userName }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="my_assets" class="form-label">Bộ lọc</label>
                        <select class="form-select" id="my_assets" name="my_assets">
                            <option value="">Tất cả tài sản</option>
                            <option value="1" {{ $myAssets == '1' ? 'selected' : '' }}>Tài sản của tôi</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-outline-primary me-2">
                                    <i class="fas fa-search"></i>
                                </button>
                                <a href="{{ route('properties.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times"></i>
                                </a>
                            </div>
                        </div>
                </form>
            </div>
        </div>

        <!-- Quick Filter Buttons -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="d-flex gap-2 flex-wrap">
                    <a href="{{ route('properties.index', ['my_assets' => 1]) }}"
                        class="btn btn-outline-primary btn-sm {{ $myAssets == '1' ? 'active' : '' }}">
                        <i class="fas fa-user me-1"></i>Tài sản của tôi
                    </a>
                    <a href="{{ route('properties.index', ['asset_type' => 'real_estate_house']) }}"
                        class="btn btn-outline-success btn-sm {{ $selectedType == 'real_estate_house' ? 'active' : '' }}">
                        <i class="fas fa-home me-1"></i>Nhà ở
                    </a>
                    <a href="{{ route('properties.index', ['asset_type' => 'real_estate_apartment']) }}"
                        class="btn btn-outline-info btn-sm {{ $selectedType == 'real_estate_apartment' ? 'active' : '' }}">
                        <i class="fas fa-building me-1"></i>Căn hộ
                    </a>
                    <a href="{{ route('properties.index', ['asset_type' => 'movable_property_car']) }}"
                        class="btn btn-outline-warning btn-sm {{ $selectedType == 'movable_property_car' ? 'active' : '' }}">
                        <i class="fas fa-car me-1"></i>Ô tô
                    </a>
                </div>
            </div>
        </div>

        <!-- Success/Error Messages -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Assets List -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Danh sách Tài sản ({{ $assets->total() }} kết quả)</h5>
                <div class="btn-group" role="group">
                    <input type="checkbox" class="btn-check" id="select-all">
                    <label class="btn btn-outline-secondary btn-sm" for="select-all">Chọn tất cả</label>
                    <button type="button" class="btn btn-outline-danger btn-sm" id="bulk-delete" style="display: none;">
                        <i class="bi bi-trash me-1"></i>Xóa đã chọn
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                @if ($assets->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="40"><input type="checkbox" id="select-all-header"></th>
                                    <th>Tên Tài sản</th>
                                    <th>
                                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'asset_type', 'direction' => $sortDirection === 'asc' ? 'desc' : 'asc']) }}"
                                            class="text-decoration-none text-dark">
                                            Loại
                                            @if ($sortField === 'asset_type')
                                                <i class="bi bi-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th>Địa chỉ/Thông tin</th>
                                    <th>Người tạo</th>
                                    <th>Văn phòng</th>
                                    <th>
                                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'updated_at', 'direction' => $sortDirection === 'asc' ? 'desc' : 'asc']) }}"
                                            class="text-decoration-none text-dark">
                                            Cập nhật cuối
                                            @if ($sortField === 'updated_at')
                                                <i class="bi bi-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th width="150">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($assets as $asset)
                                    <tr>
                                        <td>
                                            <input type="checkbox" class="asset-checkbox" value="{{ $asset->id }}">
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm me-3">
                                                    @if (str_contains($asset->asset_type, 'real_estate_house'))
                                                        <div class="avatar-title bg-success rounded-circle">
                                                            <i class="fas fa-home text-white"></i>
                                                        </div>
                                                    @elseif(str_contains($asset->asset_type, 'real_estate_apartment'))
                                                        <div class="avatar-title bg-info rounded-circle">
                                                            <i class="fas fa-building text-white"></i>
                                                        </div>
                                                    @elseif(str_contains($asset->asset_type, 'land_only'))
                                                        <div class="avatar-title bg-warning rounded-circle">
                                                            <i class="fas fa-map text-white"></i>
                                                        </div>
                                                    @elseif(str_contains($asset->asset_type, 'car'))
                                                        <div class="avatar-title bg-primary rounded-circle">
                                                            <i class="fas fa-car text-white"></i>
                                                        </div>
                                                    @else
                                                        <div class="avatar-title bg-secondary rounded-circle">
                                                            <i class="fas fa-motorcycle text-white"></i>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div>
                                                    <div class="fw-bold">{{ $asset->display_name }}</div>
                                                    @if ($asset->notes)
                                                        <small
                                                            class="text-muted">{{ Str::limit($asset->notes, 50) }}</small>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @if (str_contains($asset->asset_type, 'real_estate_house'))
                                                <span class="badge bg-success">Nhà ở</span>
                                            @elseif(str_contains($asset->asset_type, 'real_estate_apartment'))
                                                <span class="badge bg-info">Căn hộ</span>
                                            @elseif(str_contains($asset->asset_type, 'land_only'))
                                                <span class="badge bg-warning">Đất trống</span>
                                            @elseif(str_contains($asset->asset_type, 'car'))
                                                <span class="badge bg-primary">Ô tô</span>
                                            @else
                                                <span class="badge bg-secondary">Xe máy</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div>
                                                @if ($asset->primary_address && $asset->primary_address !== 'Chưa có địa chỉ')
                                                    <div class="fw-semibold">{{ Str::limit($asset->primary_address, 40) }}
                                                    </div>
                                                @endif
                                                @if ($asset->summary_info && $asset->summary_info !== 'Không có thông tin chi tiết')
                                                    <small class="text-muted">{{ $asset->summary_info }}</small>
                                                @else
                                                    <small class="text-muted">Chưa có thông tin chi tiết</small>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-xs me-2">
                                                    <div class="avatar-title bg-soft-primary rounded-circle">
                                                        {{ $asset->creator ? substr($asset->creator->name, 0, 1) : 'S' }}
                                                    </div>
                                                </div>
                                                <div>
                                                    <div class="fw-semibold">{{ $asset->creator_name }}</div>
                                                    @if ($asset->creator && $asset->creator->email)
                                                        <small
                                                            class="text-muted">{{ Str::limit($asset->creator->email, 20) }}</small>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark">
                                                {{ $asset->creator && $asset->creator->department ? $asset->creator->department : 'Chưa có' }}
                                            </span>
                                        </td>
                                        <td>
                                            <div>
                                                <small class="text-muted">
                                                    {{ $asset->updated_at->format('d/m/Y') }}
                                                </small>
                                                <br>
                                                <small class="text-muted">
                                                    {{ $asset->updated_at->format('H:i') }}
                                                </small>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="{{ route('properties.show', $asset) }}"
                                                    class="btn btn-outline-info" title="Xem chi tiết">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @if ($asset->can_edit)
                                                    <a href="{{ route('properties.edit', $asset) }}"
                                                        class="btn btn-outline-warning" title="Sửa">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                @endif
                                                @if ($asset->can_delete)
                                                    <button type="button" class="btn btn-outline-danger" title="Xóa"
                                                        onclick="confirmDelete({{ $asset->id }})">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                @endif
                                                <div class="btn-group" role="group">
                                                    <button type="button"
                                                        class="btn btn-outline-secondary dropdown-toggle"
                                                        data-bs-toggle="dropdown" aria-expanded="false" title="Thêm">
                                                        <i class="fas fa-ellipsis-v"></i>
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li><a class="dropdown-item"
                                                                href="{{ route('properties.clone', $asset) }}">
                                                                <i class="fas fa-copy me-2"></i>Sao chép
                                                            </a></li>
                                                        <li>
                                                            <hr class="dropdown-divider">
                                                        </li>
                                                        <li><a class="dropdown-item" href="#">
                                                                <i class="fas fa-download me-2"></i>Xuất PDF
                                                            </a></li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="bi bi-inbox display-1 text-muted"></i>
                        <h5 class="mt-3">Không tìm thấy tài sản nào</h5>
                        <p class="text-muted">Hãy thử điều chỉnh bộ lọc hoặc tạo tài sản mới.</p>
                        <a href="{{ route('properties.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-2"></i>Thêm Tài sản Mới
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Pagination -->
        @if ($assets->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $assets->links() }}
            </div>
        @endif

        <!-- Export Modal -->
        <div class="modal fade" id="exportModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Xuất dữ liệu</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('properties.export') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Định dạng file</label>
                                <select class="form-select" name="format">
                                    <option value="xlsx">Excel (.xlsx)</option>
                                    <option value="csv">CSV (.csv)</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Loại tài sản</label>
                                <select class="form-select" name="asset_type">
                                    <option value="">Tất cả</option>
                                    @foreach ($assetTypes as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                <button type="submit" class="btn btn-primary">Xuất file</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteModalLabel">Xác nhận xóa tài sản</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="text-center">
                            <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                            <h5>Bạn chắc chứ?</h5>
                            <p>Hành động này không thể hoàn tác. Bạn có chắc chắn muốn xóa tài sản này không?</p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <form id="deleteForm" method="POST" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Xóa</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/assets-common.js') }}?v={{ time() }}"></script>
    <script>
        // Set configuration for index page
        AssetManager.config.routes.bulkDelete = '{{ route('properties.bulk-delete') }}';

        function confirmDelete(id) {
            const form = document.getElementById('deleteForm');
            form.action = `/properties/${id}`;

            const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
            modal.show();
        }

        // Auto-submit form when select changes for better UX
        document.addEventListener('DOMContentLoaded', function() {
            const selects = ['asset_type', 'created_by', 'my_assets'];
            selects.forEach(function(selectId) {
                const select = document.getElementById(selectId);
                if (select) {
                    select.addEventListener('change', function() {
                        this.form.submit();
                    });
                }
            });
        });
    </script>
    <script src="{{ asset('js/assets-index.js') }}?v={{ time() }}"></script>
@endsection
