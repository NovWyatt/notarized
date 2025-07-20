@extends('layouts.app2')

@section('content')
    <div class="container-fluid p-3">
        <div class="row">
            <div class="col-12">
                <!-- Header -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h1 class="h3 mb-0 text-gray-800">Quản lý loại hợp đồng</h1>
                        <p class="text-muted">Tạo và quản lý các loại hợp đồng trong hệ thống</p>
                    </div>
                    <div>
                        <a href="{{ route('admin.contract-types.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus mr-2"></i>Thêm loại hợp đồng
                        </a>
                    </div>
                </div>

                <!-- Filters -->
                <div class="card shadow-sm mb-4">
                    <div class="card-body py-3">
                        <form method="GET" action="{{ route('admin.contract-types.index') }}" class="row g-3">
                            <div class="col-md-6">
                                <input type="text" name="search" class="form-control"
                                    placeholder="Tìm kiếm theo tên hoặc mô tả..." value="{{ request('search') }}">
                            </div>
                            <div class="col-md-3">
                                <select name="is_active" class="form-control">
                                    <option value="">Tất cả trạng thái</option>
                                    <option value="1" {{ request('is_active') == '1' ? 'selected' : '' }}>Đang hoạt
                                        động</option>
                                    <option value="0" {{ request('is_active') == '0' ? 'selected' : '' }}>Đã tắt
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-outline-primary">
                                        <i class="fas fa-search"></i> Tìm kiếm
                                    </button>
                                    <a href="{{ route('admin.contract-types.index') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-undo"></i> Xóa bộ lọc
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card border-left-primary shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                            Tổng số loại hợp đồng
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $contractTypes->total() }}
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-file-contract fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-left-success shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                            Đang hoạt động
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            {{ $contractTypes->where('is_active', true)->count() }}
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-left-info shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                            Có template
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            {{ $contractTypes->where('templates_count', '>', 0)->count() }}
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-file-alt fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-left-warning shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                            Tổng template
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            {{ $contractTypes->sum('templates_count') }}
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-layer-group fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bulk Actions -->
                <div class="card shadow-sm mb-3">
                    <div class="card-body py-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="bulk-actions" style="display: none;">
                                <span class="selected-count">0</span> mục đã chọn:
                                <button type="button" class="btn btn-sm btn-success bulk-activate">
                                    <i class="fas fa-check"></i> Kích hoạt
                                </button>
                                <button type="button" class="btn btn-sm btn-warning bulk-deactivate">
                                    <i class="fas fa-pause"></i> Vô hiệu hóa
                                </button>
                                <button type="button" class="btn btn-sm btn-danger bulk-delete">
                                    <i class="fas fa-trash"></i> Xóa
                                </button>
                            </div>
                            <div class="ml-auto">
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="sortable-toggle">
                                    <i class="fas fa-sort"></i> Sắp xếp
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contract Types Table -->
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Danh sách loại hợp đồng</h6>
                    </div>
                    <div class="card-body p-0">
                        @if ($contractTypes->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover mb-0" id="contractTypesTable">
                                    <thead class="bg-light">
                                        <tr>
                                            <th width="50">
                                                <input type="checkbox" id="selectAll" class="form-check-input">
                                            </th>
                                            <th>Tên loại hợp đồng</th>
                                            <th>Mô tả</th>
                                            <th width="120">Templates</th>
                                            <th width="100">Trạng thái</th>
                                            <th width="80">Thứ tự</th>
                                            <th width="150">Thao tác</th>
                                        </tr>
                                    </thead>
                                    <tbody id="sortable-tbody">
                                        @foreach ($contractTypes as $contractType)
                                            <tr data-id="{{ $contractType->id }}">
                                                <td>
                                                    <input type="checkbox" class="form-check-input select-item"
                                                        value="{{ $contractType->id }}">
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <i class="fas fa-grip-vertical text-muted mr-2 sort-handle"
                                                            style="cursor: move;"></i>
                                                        <div>
                                                            <a href="{{ route('admin.contract-types.show', $contractType) }}"
                                                                class="font-weight-bold text-decoration-none">
                                                                {{ $contractType->name }}
                                                            </a>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="text-muted">
                                                        {{ Str::limit($contractType->description, 100) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="text-center">
                                                        <a href="{{ route('admin.contract-types.templates', $contractType) }}"
                                                            class="btn btn-sm btn-outline-primary">
                                                            {{ $contractType->templates_count }}
                                                            <small
                                                                class="text-success">({{ $contractType->active_templates_count }}
                                                                hoạt động)</small>
                                                        </a>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input status-toggle"
                                                            id="status{{ $contractType->id }}"
                                                            {{ $contractType->is_active ? 'checked' : '' }}
                                                            data-id="{{ $contractType->id }}">
                                                        <label class="custom-control-label"
                                                            for="status{{ $contractType->id }}"></label>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span
                                                        class="badge badge-secondary">{{ $contractType->sort_order }}</span>
                                                </td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <a href="{{ route('admin.contract-types.edit', $contractType) }}"
                                                            class="btn btn-sm btn-outline-primary" title="Chỉnh sửa">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <button type="button"
                                                            class="btn btn-sm btn-outline-secondary duplicate-btn"
                                                            data-id="{{ $contractType->id }}" title="Sao chép">
                                                            <i class="fas fa-copy"></i>
                                                        </button>
                                                        <button type="button"
                                                            class="btn btn-sm btn-outline-danger delete-btn"
                                                            data-id="{{ $contractType->id }}"
                                                            data-name="{{ $contractType->name }}" title="Xóa">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-file-contract fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">Chưa có loại hợp đồng nào</h5>
                                <p class="text-muted">Hãy tạo loại hợp đồng đầu tiên để bắt đầu.</p>
                                <a href="{{ route('admin.contract-types.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus mr-2"></i>Thêm loại hợp đồng
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Pagination -->
                @if ($contractTypes->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $contractTypes->appends(request()->query())->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script>
        $(document).ready(function() {
            // Select all functionality
            $('#selectAll').change(function() {
                $('.select-item').prop('checked', this.checked);
                updateBulkActions();
            });

            $('.select-item').change(function() {
                updateBulkActions();
            });

            function updateBulkActions() {
                const selected = $('.select-item:checked').length;
                const total = $('.select-item').length;

                $('#selectAll').prop('indeterminate', selected > 0 && selected < total);
                $('#selectAll').prop('checked', selected === total && total > 0);

                if (selected > 0) {
                    $('.bulk-actions').show();
                    $('.selected-count').text(selected);
                } else {
                    $('.bulk-actions').hide();
                }
            }

            // Status toggle
            $('.status-toggle').change(function() {
                const id = $(this).data('id');
                const isActive = $(this).is(':checked');

                $.post(`/admin/contract-types/${id}/toggle-status`, {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    })
                    .done(function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                        } else {
                            toastr.error(response.message);
                            // Revert checkbox state
                            $(this).prop('checked', !isActive);
                        }
                    })
                    .fail(function() {
                        toastr.error('Có lỗi xảy ra khi cập nhật trạng thái');
                        $(this).prop('checked', !isActive);
                    });
            });

            // Delete functionality
            $('.delete-btn').click(function() {
                const id = $(this).data('id');
                const name = $(this).data('name');

                Swal.fire({
                    title: 'Xác nhận xóa',
                    text: `Bạn có chắc chắn muốn xóa loại hợp đồng "${name}"?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Xóa',
                    cancelButtonText: 'Hủy'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/admin/contract-types/${id}`,
                            type: 'DELETE',
                            data: {
                                _token: $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                if (response.success) {
                                    toastr.success(response.message);
                                    location.reload();
                                } else {
                                    toastr.error(response.message);
                                }
                            },
                            error: function() {
                                toastr.error('Có lỗi xảy ra khi xóa loại hợp đồng');
                            }
                        });
                    }
                });
            });

            // Duplicate functionality
            $('.duplicate-btn').click(function() {
                const id = $(this).data('id');

                $.post(`/admin/contract-types/${id}/duplicate`, {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    })
                    .done(function(response) {
                        toastr.success('Đã sao chép thành công');
                        location.reload();
                    })
                    .fail(function() {
                        toastr.error('Có lỗi xảy ra khi sao chép');
                    });
            });

            // Bulk actions
            $('.bulk-activate, .bulk-deactivate, .bulk-delete').click(function() {
                const action = $(this).hasClass('bulk-activate') ? 'activate' :
                    $(this).hasClass('bulk-deactivate') ? 'deactivate' : 'delete';
                const selectedIds = $('.select-item:checked').map(function() {
                    return $(this).val();
                }).get();

                if (selectedIds.length === 0) {
                    toastr.warning('Vui lòng chọn ít nhất một mục');
                    return;
                }

                let confirmText = '';
                if (action === 'activate') confirmText = 'kích hoạt';
                else if (action === 'deactivate') confirmText = 'vô hiệu hóa';
                else confirmText = 'xóa';

                Swal.fire({
                    title: 'Xác nhận',
                    text: `Bạn có chắc chắn muốn ${confirmText} ${selectedIds.length} mục đã chọn?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Xác nhận',
                    cancelButtonText: 'Hủy'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.post('/admin/contract-types/bulk-action', {
                                _token: $('meta[name="csrf-token"]').attr('content'),
                                action: action,
                                ids: selectedIds
                            })
                            .done(function(response) {
                                if (response.success) {
                                    toastr.success(response.message);
                                    location.reload();
                                } else {
                                    toastr.error(response.message);
                                }
                            })
                            .fail(function() {
                                toastr.error('Có lỗi xảy ra khi thực hiện thao tác');
                            });
                    }
                });
            });

            // Sortable functionality
            let sortable = null;
            $('#sortable-toggle').click(function() {
                if (sortable) {
                    sortable.destroy();
                    sortable = null;
                    $(this).removeClass('btn-primary').addClass('btn-outline-secondary')
                        .html('<i class="fas fa-sort"></i> Sắp xếp');
                    $('.sort-handle').hide();
                } else {
                    sortable = Sortable.create(document.getElementById('sortable-tbody'), {
                        handle: '.sort-handle',
                        animation: 150,
                        onEnd: function(evt) {
                            const orders = [];
                            $('#sortable-tbody tr').each(function(index) {
                                orders.push({
                                    id: $(this).data('id'),
                                    sort_order: index + 1
                                });
                            });

                            $.post('/admin/contract-types/update-order', {
                                    _token: $('meta[name="csrf-token"]').attr('content'),
                                    orders: orders
                                })
                                .done(function(response) {
                                    if (response.success) {
                                        toastr.success(response.message);
                                    } else {
                                        toastr.error(response.message);
                                    }
                                })
                                .fail(function() {
                                    toastr.error('Có lỗi xảy ra khi cập nhật thứ tự');
                                });
                        }
                    });
                    $(this).removeClass('btn-outline-secondary').addClass('btn-primary')
                        .html('<i class="fas fa-check"></i> Hoàn thành');
                    $('.sort-handle').show();
                }
            });

            // Initially hide sort handles
            $('.sort-handle').hide();
        });
    </script>
@endsection
