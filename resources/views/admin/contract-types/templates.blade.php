@extends('layouts.app')

@section('content')
    <div class="container-fluid p-3">
        <div class="row">
            <div class="col-12">
                <!-- Header -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h1 class="h3 mb-0 text-gray-800">Templates: {{ $contractType->name }}</h1>
                        <p class="text-muted">
                            <i class="fas fa-file-alt mr-1"></i>{{ $templates->total() }} templates
                            <span class="ml-3">
                                <i
                                    class="fas fa-handshake mr-1"></i>{{ $contractType->templates->flatMap->contracts->count() }}
                                hợp đồng
                            </span>
                        </p>
                    </div>
                    <div>
                        <a href="{{ route('admin.contract-templates.create') }}?contract_type_id={{ $contractType->id }}"
                            class="btn btn-success">
                            <i class="fas fa-plus mr-2"></i>Thêm template
                        </a>
                        <a href="{{ route('admin.contract-types.show', $contractType) }}" class="btn btn-info">
                            <i class="fas fa-eye mr-2"></i>Chi tiết loại HĐ
                        </a>
                        <a href="{{ route('admin.contract-types.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left mr-2"></i>Quay lại
                        </a>
                    </div>
                </div>

                <!-- Contract Type Info -->
                <div class="card shadow-sm mb-4">
                    <div class="card-body py-3">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <div class="d-flex align-items-center">
                                    <div class="mr-3">
                                        <span
                                            class="badge badge-{{ $contractType->is_active ? 'success' : 'secondary' }} badge-lg">
                                            {{ $contractType->is_active ? 'Hoạt động' : 'Tạm dừng' }}
                                        </span>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">{{ $contractType->name }}</h6>
                                        @if ($contractType->description)
                                            <small
                                                class="text-muted">{{ Str::limit($contractType->description, 100) }}</small>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 text-right">
                                <a href="{{ route('admin.contract-types.edit', $contractType) }}"
                                    class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-edit mr-1"></i>Chỉnh sửa
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="card shadow-sm mb-4">
                    <div class="card-body py-3">
                        <form method="GET" action="{{ route('admin.contract-types.templates', $contractType) }}"
                            class="row g-3">
                            <div class="col-md-6">
                                <input type="text" name="search" class="form-control" placeholder="Tìm kiếm template..."
                                    value="{{ request('search') }}">
                            </div>
                            <div class="col-md-2">
                                <select name="is_active" class="form-control">
                                    <option value="">Tất cả trạng thái</option>
                                    <option value="1" {{ request('is_active') == '1' ? 'selected' : '' }}>Hoạt động
                                    </option>
                                    <option value="0" {{ request('is_active') == '0' ? 'selected' : '' }}>Tắt</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="has_contracts" class="form-control">
                                    <option value="">Tất cả</option>
                                    <option value="1" {{ request('has_contracts') == '1' ? 'selected' : '' }}>Có hợp
                                        đồng</option>
                                    <option value="0" {{ request('has_contracts') == '0' ? 'selected' : '' }}>Chưa
                                        dùng</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-outline-primary">
                                        <i class="fas fa-search"></i> Tìm
                                    </button>
                                    <a href="{{ route('admin.contract-types.templates', $contractType) }}"
                                        class="btn btn-outline-secondary">
                                        <i class="fas fa-undo"></i>
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
                                            Tổng templates
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $templates->total() }}</div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-file-alt fa-2x text-gray-300"></i>
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
                                            {{ $templates->where('is_active', true)->count() }}
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
                                            Đã sử dụng
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            {{ $templates->where('contracts_count', '>', 0)->count() }}
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-handshake fa-2x text-gray-300"></i>
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
                                            Tổng hợp đồng
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            {{ $templates->sum('contracts_count') }}
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-file-contract fa-2x text-gray-300"></i>
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
                                <span class="selected-count">0</span> template đã chọn:
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

                <!-- Templates Table -->
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            Danh sách templates ({{ $templates->total() }})
                        </h6>
                    </div>
                    <div class="card-body p-0">
                        @if ($templates->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover mb-0" id="templatesTable">
                                    <thead class="bg-light">
                                        <tr>
                                            <th width="50">
                                                <input type="checkbox" id="selectAll" class="form-check-input">
                                            </th>
                                            <th>Template</th>
                                            <th width="120">Hợp đồng</th>
                                            <th width="100">Trạng thái</th>
                                            <th width="80">Thứ tự</th>
                                            <th width="120">Tạo lúc</th>
                                            <th width="200">Thao tác</th>
                                        </tr>
                                    </thead>
                                    <tbody id="sortable-tbody">
                                        @foreach ($templates as $template)
                                            <tr data-id="{{ $template->id }}">
                                                <td>
                                                    <input type="checkbox" class="form-check-input select-item"
                                                        value="{{ $template->id }}">
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <i class="fas fa-grip-vertical text-muted mr-2 sort-handle"
                                                            style="cursor: move; display: none;"></i>
                                                        <div>
                                                            <a href="{{ route('admin.contract-templates.show', $template) }}"
                                                                class="font-weight-bold text-decoration-none">
                                                                {{ $template->name }}
                                                            </a>
                                                            @if ($template->contracts_count > 0)
                                                                <div class="small text-success">
                                                                    <i class="fas fa-check-circle mr-1"></i>Đã sử dụng
                                                                </div>
                                                            @else
                                                                <div class="small text-muted">
                                                                    <i class="fas fa-clock mr-1"></i>Chưa sử dụng
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    @if ($template->contracts_count > 0)
                                                        <span
                                                            class="badge badge-success">{{ $template->contracts_count }}</span>
                                                    @else
                                                        <span class="text-muted">0</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input status-toggle"
                                                            id="status{{ $template->id }}"
                                                            {{ $template->is_active ? 'checked' : '' }}
                                                            data-id="{{ $template->id }}">
                                                        <label class="custom-control-label"
                                                            for="status{{ $template->id }}"></label>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge badge-secondary">{{ $template->sort_order }}</span>
                                                </td>
                                                <td>
                                                    <small
                                                        class="text-muted">{{ $template->created_at->format('d/m/Y H:i') }}</small>
                                                </td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <a href="{{ route('admin.contract-templates.preview', $template) }}"
                                                            class="btn btn-sm btn-outline-info" title="Xem trước"
                                                            target="_blank">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <a href="{{ route('admin.contract-templates.edit', $template) }}"
                                                            class="btn btn-sm btn-outline-primary" title="Chỉnh sửa">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <div class="btn-group" role="group">
                                                            <button type="button"
                                                                class="btn btn-sm btn-outline-secondary dropdown-toggle"
                                                                data-toggle="dropdown" title="Thêm">
                                                                <i class="fas fa-ellipsis-v"></i>
                                                            </button>
                                                            <div class="dropdown-menu">
                                                                <a class="dropdown-item"
                                                                    href="{{ route('admin.contract-templates.show', $template) }}">
                                                                    <i class="fas fa-info mr-2"></i>Chi tiết
                                                                </a>
                                                                <button class="dropdown-item duplicate-btn"
                                                                    data-id="{{ $template->id }}">
                                                                    <i class="fas fa-copy mr-2"></i>Sao chép
                                                                </button>
                                                                <a class="dropdown-item"
                                                                    href="{{ route('admin.contract-templates.export', $template) }}?format=html">
                                                                    <i class="fas fa-download mr-2"></i>Xuất HTML
                                                                </a>
                                                                <div class="dropdown-divider"></div>
                                                                <button class="dropdown-item text-danger delete-btn"
                                                                    data-id="{{ $template->id }}"
                                                                    data-name="{{ $template->name }}">
                                                                    <i class="fas fa-trash mr-2"></i>Xóa
                                                                </button>
                                                            </div>
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
                                <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">Chưa có template nào</h5>
                                <p class="text-muted">Hãy tạo template đầu tiên cho loại hợp đồng này.</p>
                                <a href="{{ route('admin.contract-templates.create') }}?contract_type_id={{ $contractType->id }}"
                                    class="btn btn-primary">
                                    <i class="fas fa-plus mr-2"></i>Thêm template
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Pagination -->
                @if ($templates->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $templates->appends(request()->query())->links() }}
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

                $.post(`/admin/contract-templates/${id}/toggle-status`, {
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
                    text: `Bạn có chắc chắn muốn xóa template "${name}"?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Xóa',
                    cancelButtonText: 'Hủy'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/admin/contract-templates/${id}`,
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
                                toastr.error('Có lỗi xảy ra khi xóa template');
                            }
                        });
                    }
                });
            });

            // Duplicate functionality
            $('.duplicate-btn').click(function() {
                const id = $(this).data('id');

                $.post(`/admin/contract-templates/${id}/duplicate`, {
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
                    toastr.warning('Vui lòng chọn ít nhất một template');
                    return;
                }

                let confirmText = '';
                if (action === 'activate') confirmText = 'kích hoạt';
                else if (action === 'deactivate') confirmText = 'vô hiệu hóa';
                else confirmText = 'xóa';

                Swal.fire({
                    title: 'Xác nhận',
                    text: `Bạn có chắc chắn muốn ${confirmText} ${selectedIds.length} template đã chọn?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Xác nhận',
                    cancelButtonText: 'Hủy'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Call bulk action API for templates
                        $.post('/admin/contract-templates/bulk-action', {
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

                            $.post('/admin/contract-templates/update-order', {
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

            // Auto-refresh every 30 seconds to show live updates
            setInterval(function() {
                // Only refresh if no user interaction is happening
                if (!$('.modal').hasClass('show') && !$('.dropdown-menu').hasClass('show')) {
                    const currentUrl = window.location.href;
                    const params = new URLSearchParams(window.location.search);

                    // Add a refresh parameter to avoid caching
                    params.set('_refresh', Date.now());

                    // Silent AJAX call to check for updates
                    $.get(currentUrl + '?' + params.toString())
                        .done(function(response) {
                            // Check if there are updates by comparing timestamps
                            // This is a simple implementation - you can make it more sophisticated
                            const newContent = $(response).find('#templatesTable tbody').html();
                            const currentContent = $('#templatesTable tbody').html();

                            if (newContent !== currentContent && !$('.select-item:checked').length) {
                                // Only update if no items are selected to avoid disrupting user actions
                                $('#templatesTable tbody').html(newContent);

                                // Re-bind event handlers
                                bindEventHandlers();
                            }
                        });
                }
            }, 30000); // 30 seconds

            function bindEventHandlers() {
                // Re-bind all event handlers after content update
                $('.status-toggle').off('change').on('change', function() {
                    // Status toggle handler code here
                });

                $('.delete-btn').off('click').on('click', function() {
                    // Delete handler code here
                });

                // Add other handlers as needed
            }
        });
    </script>
@endsection
