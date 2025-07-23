@extends('layouts.app')

@section('content')
    <div class="container-fluid p-3">
        <div class="row">
            <div class="col-12">
                <!-- Header -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h1 class="h3 mb-0 text-gray-800">Quản lý template hợp đồng</h1>
                        <p class="text-muted">Tạo và quản lý các template cho từng loại hợp đồng</p>
                    </div>
                    <div>
                        <a href="{{ route('admin.contract-templates.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus mr-2"></i>Thêm template
                        </a>
                        <button type="button" class="btn btn-outline-secondary" data-toggle="modal"
                            data-target="#importModal">
                            <i class="fas fa-upload mr-2"></i>Import
                        </button>
                    </div>
                </div>

                <!-- Filters -->
                <div class="card shadow-sm mb-4">
                    <div class="card-body py-3">
                        <form method="GET" action="{{ route('admin.contract-templates.index') }}" class="row g-3">
                            <div class="col-md-4">
                                <input type="text" name="search" class="form-control" placeholder="Tìm kiếm template..."
                                    value="{{ request('search') }}">
                            </div>
                            <div class="col-md-3">
                                <select name="contract_type_id" class="form-control">
                                    <option value="">Tất cả loại hợp đồng</option>
                                    @foreach ($contractTypes as $type)
                                        <option value="{{ $type->id }}"
                                            {{ request('contract_type_id') == $type->id ? 'selected' : '' }}>
                                            {{ $type->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="is_active" class="form-control">
                                    <option value="">Tất cả trạng thái</option>
                                    <option value="1" {{ request('is_active') == '1' ? 'selected' : '' }}>Hoạt động
                                    </option>
                                    <option value="0" {{ request('is_active') == '0' ? 'selected' : '' }}>Tắt</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-outline-primary">
                                        <i class="fas fa-search"></i> Tìm kiếm
                                    </button>
                                    <a href="{{ route('admin.contract-templates.index') }}"
                                        class="btn btn-outline-secondary">
                                        <i class="fas fa-undo"></i> Reset
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
                                            Tổng template
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
                                            Loại hợp đồng
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            {{ $contractTypes->count() }}
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

                <!-- Templates Table -->
                <div class="card shadow">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">Danh sách template</h6>
                        <div>
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="sortable-toggle">
                                <i class="fas fa-sort"></i> Sắp xếp
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        @if ($templates->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover mb-0" id="templatesTable">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Template</th>
                                            <th>Loại hợp đồng</th>
                                            <th width="120">Hợp đồng</th>
                                            <th width="100">Trạng thái</th>
                                            <th width="80">Thứ tự</th>
                                            <th width="200">Thao tác</th>
                                        </tr>
                                    </thead>
                                    <tbody id="sortable-tbody">
                                        @foreach ($templates as $template)
                                            <tr data-id="{{ $template->id }}">
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <i class="fas fa-grip-vertical text-muted mr-2 sort-handle"
                                                            style="cursor: move; display: none;"></i>
                                                        <div>
                                                            <a href="{{ route('admin.contract-templates.show', $template) }}"
                                                                class="font-weight-bold text-decoration-none">
                                                                {{ $template->name }}
                                                            </a>
                                                            <div class="small text-muted">
                                                                Tạo: {{ $template->created_at->format('d/m/Y H:i') }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge badge-outline-primary">
                                                        {{ $template->contractType->name }}
                                                    </span>
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
                                                                <button class="dropdown-item duplicate-btn"
                                                                    data-id="{{ $template->id }}">
                                                                    <i class="fas fa-copy mr-2"></i>Sao chép
                                                                </button>
                                                                <a class="dropdown-item"
                                                                    href="{{ route('admin.contract-templates.export', $template) }}?format=html">
                                                                    <i class="fas fa-download mr-2"></i>Xuất HTML
                                                                </a>
                                                                <a class="dropdown-item"
                                                                    href="{{ route('admin.contract-templates.export', $template) }}?format=txt">
                                                                    <i class="fas fa-file-text mr-2"></i>Xuất Text
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
                                <p class="text-muted">Hãy tạo template đầu tiên để bắt đầu.</p>
                                <a href="{{ route('admin.contract-templates.create') }}" class="btn btn-primary">
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

    <!-- Import Modal -->
    <div class="modal fade" id="importModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Import Template</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form action="{{ route('admin.contract-templates.import') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="import_name">Tên template</label>
                            <input type="text" class="form-control" id="import_name" name="name" required>
                        </div>
                        <div class="form-group">
                            <label for="import_contract_type_id">Loại hợp đồng</label>
                            <select class="form-control" id="import_contract_type_id" name="contract_type_id" required>
                                <option value="">Chọn loại hợp đồng</option>
                                @foreach ($contractTypes as $type)
                                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="template_file">File template</label>
                            <input type="file" class="form-control-file" id="template_file" name="template_file"
                                accept=".html,.txt" required>
                            <small class="form-text text-muted">Chỉ hỗ trợ file HTML hoặc TXT, tối đa 2MB</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-primary">Import</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script>
        $(document).ready(function() {
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
        });
    </script>
@endsection
