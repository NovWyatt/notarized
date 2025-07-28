@extends('layouts.app')

@section('content')
<div class="container-fluid p-3">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Quản Lý Hồ Sơ</h1>
            <p class="text-muted">Quản lý tất cả hồ sơ và hợp đồng của bạn</p>
        </div>
        <div>
            <a href="{{ route('admin.dossiers.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Tạo Hồ Sơ Mới
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <div class="text-primary">
                        <i class="fas fa-folder fa-2x mb-2"></i>
                    </div>
                    <h4 class="mb-1">{{ $stats['total'] }}</h4>
                    <small class="text-muted">Tổng hồ sơ</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <div class="text-warning">
                        <i class="fas fa-edit fa-2x mb-2"></i>
                    </div>
                    <h4 class="mb-1">{{ $stats['draft'] }}</h4>
                    <small class="text-muted">Đang soạn</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <div class="text-info">
                        <i class="fas fa-cogs fa-2x mb-2"></i>
                    </div>
                    <h4 class="mb-1">{{ $stats['processing'] }}</h4>
                    <small class="text-muted">Đang xử lý</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <div class="text-success">
                        <i class="fas fa-check-circle fa-2x mb-2"></i>
                    </div>
                    <h4 class="mb-1">{{ $stats['completed'] }}</h4>
                    <small class="text-muted">Hoàn thành</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.dossiers.index') }}">
                <div class="row align-items-end">
                    <div class="col-md-4">
                        <label class="form-label">Tìm kiếm</label>
                        <input type="text" name="search" class="form-control"
                               placeholder="Tên hồ sơ, mô tả..."
                               value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Trạng thái</label>
                        <select name="status" class="form-select">
                            <option value="">Tất cả trạng thái</option>
                            <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Nháp</option>
                            <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>Đang xử lý</option>
                            <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Hoàn thành</option>
                            <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Sắp xếp</label>
                        <select name="sort" class="form-select">
                            <option value="latest">Mới nhất</option>
                            <option value="oldest">Cũ nhất</option>
                            <option value="name">Theo tên A-Z</option>
                            <option value="name_desc">Theo tên Z-A</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search me-1"></i>Lọc
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Dossiers List -->
    <div class="card shadow-sm">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="fas fa-list me-2"></i>Danh Sách Hồ Sơ
                @if($dossiers->total() > 0)
                    <span class="badge bg-primary ms-2">{{ $dossiers->total() }}</span>
                @endif
            </h5>
        </div>
        <div class="card-body p-0">
            @if($dossiers->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Tên Hồ Sơ</th>
                                <th>Trạng thái</th>
                                <th>Số HĐ</th>
                                <th>Ngày tạo</th>
                                <th>Cập nhật</th>
                                <th width="150">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($dossiers as $dossier)
                                <tr>
                                    <td>
                                        <div>
                                            <a href="{{ route('admin.dossiers.show', $dossier) }}"
                                               class="fw-medium text-decoration-none">
                                                {{ $dossier->name }}
                                            </a>
                                            @if($dossier->description)
                                                <small class="text-muted d-block">{{ Str::limit($dossier->description, 60) }}</small>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $dossier->status_color }}">
                                            {{ $dossier->status_label }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $dossier->contracts_count }}</span>
                                    </td>
                                    <td>
                                        <span class="text-muted">{{ $dossier->created_at->format('d/m/Y') }}</span>
                                        <small class="text-muted d-block">{{ $dossier->created_at->format('H:i') }}</small>
                                    </td>
                                    <td>
                                        <span class="text-muted">{{ $dossier->updated_at->format('d/m/Y') }}</span>
                                        <small class="text-muted d-block">{{ $dossier->updated_at->diffForHumans() }}</small>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('admin.dossiers.show', $dossier) }}"
                                               class="btn btn-outline-primary" title="Xem chi tiết">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if($dossier->canBeUpdated())
                                                <a href="{{ route('admin.dossiers.edit', $dossier) }}"
                                                   class="btn btn-outline-secondary" title="Chỉnh sửa">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            @endif
                                            @if($dossier->canBeCancelled())
                                                <button type="button" class="btn btn-outline-danger"
                                                        onclick="deleteDossier({{ $dossier->id }})" title="Xóa">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($dossiers->hasPages())
                    <div class="card-footer">
                        {{ $dossiers->withQueryString()->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-5">
                    <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Chưa có hồ sơ nào</h5>
                    <p class="text-muted">Tạo hồ sơ đầu tiên để bắt đầu quản lý hợp đồng</p>
                    <a href="{{ route('admin.dossiers.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Tạo Hồ Sơ Đầu Tiên
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
function deleteDossier(dossierId) {
    if (!confirm('Bạn có chắc chắn muốn xóa hồ sơ này? Thao tác này không thể hoàn tác!')) {
        return;
    }

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/admin/dossiers/${dossierId}`;

    const csrfToken = document.createElement('input');
    csrfToken.type = 'hidden';
    csrfToken.name = '_token';
    csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    const methodField = document.createElement('input');
    methodField.type = 'hidden';
    methodField.name = '_method';
    methodField.value = 'DELETE';

    form.appendChild(csrfToken);
    form.appendChild(methodField);
    document.body.appendChild(form);
    form.submit();
}
</script>
@endsection
