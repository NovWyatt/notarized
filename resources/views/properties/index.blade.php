{{-- resources/views/properties/index.blade.php --}}
@extends('layouts.app2')
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
                    <i class="bi bi-plus-circle me-2"></i>Thêm Tài sản Mới
                </a>
                <button class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#exportModal">
                    <i class="bi bi-download me-2"></i>Xuất Excel
                </button>
            </div>
        </div>

        <!-- Filters & Search -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('properties.index') }}" class="row g-3">
                    <div class="col-md-4">
                        <label for="search" class="form-label">Tìm kiếm</label>
                        <input type="text" class="form-control" id="search" name="search" value="{{ $searchTerm }}"
                            placeholder="Tên tài sản, ghi chú...">
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
                    <div class="col-md-3">
                        <label for="sort" class="form-label">Sắp xếp theo</label>
                        <select class="form-select" id="sort" name="sort">
                            <option value="created_at" {{ $sortField === 'created_at' ? 'selected' : '' }}>Ngày tạo</option>
                            <option value="asset_name" {{ $sortField === 'asset_name' ? 'selected' : '' }}>Tên tài sản
                            </option>
                            <option value="estimated_value" {{ $sortField === 'estimated_value' ? 'selected' : '' }}>Giá trị
                            </option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-outline-primary me-2">
                            <i class="fas fa-search"></i> Lọc
                        </button>
                        <a href="{{ route('properties.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times"></i>
                        </a>
                    </div>
                </form>
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
                                    <th>
                                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'asset_name', 'direction' => $sortDirection === 'asc' ? 'desc' : 'asc']) }}"
                                            class="text-decoration-none text-dark">
                                            Tên Tài sản
                                            @if ($sortField === 'asset_name')
                                                <i class="bi bi-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th>Loại</th>
                                    <th>Địa chỉ/Thông tin</th>
                                    <th>
                                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'estimated_value', 'direction' => $sortDirection === 'asc' ? 'desc' : 'asc']) }}"
                                            class="text-decoration-none text-dark">
                                            Giá trị ước tính
                                            @if ($sortField === 'estimated_value')
                                                <i class="bi bi-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th>Ngày tạo</th>
                                    <th width="120">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($assets as $asset)
                                    <tr>
                                        <td>
                                            <input type="checkbox" class="asset-checkbox" value="{{ $asset->id }}">
                                        </td>
                                        <td>
                                            <div class="fw-bold">{{ $asset->asset_name ?: 'Tài sản #' . $asset->id }}</div>
                                            @if ($asset->notes)
                                                <small class="text-muted">{{ Str::limit($asset->notes, 50) }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-primary">{{ $asset->type_label }}</span>
                                        </td>
                                        <td>
                                            <div>{{ $asset->primary_address }}</div>
                                            @if ($asset->summary_info !== 'Không có thông tin chi tiết')
                                                <small class="text-muted">{{ $asset->summary_info }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="fw-bold text-success">{{ $asset->formatted_value }}</span>
                                        </td>
                                        <td>
                                            <small>{{ $asset->created_at->format('d/m/Y H:i') }}</small>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="{{ route('properties.show', $asset) }}"
                                                    class="btn btn-outline-info" title="Xem chi tiết">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('properties.edit', $asset) }}"
                                                    class="btn btn-outline-warning" title="Sửa">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                {{-- <button type="button" class="btn btn-outline-danger" title="Xóa"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#deleteModal{{ $asset->id }}">
                                                    <i class="fas fa-trash"></i>
                                                </button> --}}
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Delete Modal -->
                                    <div class="modal fade" id="deleteModal{{ $asset->id }}" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Xác nhận xóa</h5>
                                                    <button type="button" class="btn-close"
                                                        data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    Bạn có chắc chắn muốn xóa tài sản
                                                    <strong>{{ $asset->asset_name ?: 'Tài sản #' . $asset->id }}</strong>?
                                                    <br><small class="text-muted">Hành động này không thể hoàn tác.</small>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Hủy</button>
                                                    <form method="POST"
                                                        action="{{ route('properties.destroy', $asset) }}"
                                                        class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger">Xóa</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
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
    </div>

    <script>
        // Handle select all checkboxes
        document.getElementById('select-all').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.asset-checkbox');
            checkboxes.forEach(checkbox => checkbox.checked = this.checked);
            toggleBulkDelete();
        });

        document.getElementById('select-all-header').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.asset-checkbox');
            checkboxes.forEach(checkbox => checkbox.checked = this.checked);
            toggleBulkDelete();
        });

        // Handle individual checkboxes
        document.querySelectorAll('.asset-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', toggleBulkDelete);
        });

        function toggleBulkDelete() {
            const checkedBoxes = document.querySelectorAll('.asset-checkbox:checked');
            const bulkDeleteBtn = document.getElementById('bulk-delete');

            if (checkedBoxes.length > 0) {
                bulkDeleteBtn.style.display = 'block';
            } else {
                bulkDeleteBtn.style.display = 'none';
            }
        }

        // Handle bulk delete
        document.getElementById('bulk-delete').addEventListener('click', function() {
            const checkedBoxes = document.querySelectorAll('.asset-checkbox:checked');
            if (checkedBoxes.length === 0) return;

            if (confirm(`Bạn có chắc chắn muốn xóa ${checkedBoxes.length} tài sản đã chọn?`)) {
                const assetIds = Array.from(checkedBoxes).map(cb => cb.value);

                fetch('{{ route('properties.bulk-delete') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            asset_ids: assetIds
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert('Có lỗi xảy ra: ' + data.message);
                        }
                    });
            }
        });
    </script>
@endsection
