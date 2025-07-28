@extends('layouts.app')

@section('content')
    <div class="container-fluid p-3">
        <div class="row">
            <div class="col-12">
                <!-- Header -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h1 class="h3 mb-0 text-gray-800">{{ $contractType->name }}</h1>
                        <p class="text-muted">
                            <i class="fas fa-calendar mr-1"></i>Tạo: {{ $contractType->created_at->format('d/m/Y H:i') }}
                            @if ($contractType->updated_at != $contractType->created_at)
                                <span class="ml-3">
                                    <i class="fas fa-edit mr-1"></i>Cập nhật:
                                    {{ $contractType->updated_at->format('d/m/Y H:i') }}
                                </span>
                            @endif
                        </p>
                    </div>
                    <div>
                        <a href="{{ route('admin.contract-types.templates', $contractType) }}" class="btn btn-info">
                            <i class="fas fa-file-alt mr-2"></i>Xem templates
                        </a>
                        <a href="{{ route('admin.contract-types.edit', $contractType) }}" class="btn btn-primary">
                            <i class="fas fa-edit mr-2"></i>Chỉnh sửa
                        </a>
                        <a href="{{ route('admin.contract-types.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left mr-2"></i>Quay lại
                        </a>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-8">
                        <!-- Contract Type Information -->
                        <div class="card shadow mb-4">
                            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                                <h6 class="m-0 font-weight-bold text-primary">Thông tin loại hợp đồng</h6>
                                <div>
                                    <span class="badge badge-{{ $contractType->is_active ? 'success' : 'secondary' }}">
                                        {{ $contractType->is_active ? 'Hoạt động' : 'Tạm dừng' }}
                                    </span>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6 class="font-weight-bold">Tên loại hợp đồng:</h6>
                                        <p class="text-muted">{{ $contractType->name }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="font-weight-bold">Thứ tự sắp xếp:</h6>
                                        <p class="text-muted">{{ $contractType->sort_order }}</p>
                                    </div>
                                </div>
                                @if ($contractType->description)
                                    <div class="row">
                                        <div class="col-12">
                                            <h6 class="font-weight-bold">Mô tả:</h6>
                                            <p class="text-muted">{{ $contractType->description }}</p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Templates List -->
                        <div class="card shadow mb-4">
                            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                                <h6 class="m-0 font-weight-bold text-primary">
                                    Templates ({{ $contractType->templates->count() }})
                                </h6>
                                <div>
                                    <a href="{{ route('admin.contract-templates.create') }}?contract_type_id={{ $contractType->id }}"
                                        class="btn btn-sm btn-primary">
                                        <i class="fas fa-plus mr-1"></i>Thêm template
                                    </a>
                                    <a href="{{ route('admin.contract-types.templates', $contractType) }}"
                                        class="btn btn-sm btn-outline-info">
                                        <i class="fas fa-list mr-1"></i>Xem tất cả
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
                                @if ($contractType->templates->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table table-hover mb-0">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th>Tên template</th>
                                                    <th width="120">Hợp đồng</th>
                                                    <th width="100">Trạng thái</th>
                                                    <th width="80">Thứ tự</th>
                                                    <th width="150">Thao tác</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($contractType->templates->take(10) as $template)
                                                    <tr>
                                                        <td>
                                                            <div>
                                                                <a href="{{ route('admin.contract-templates.show', $template) }}"
                                                                    class="font-weight-bold text-decoration-none">
                                                                    {{ $template->name }}
                                                                </a>
                                                                <div class="small text-muted">
                                                                    Tạo: {{ $template->created_at->format('d/m/Y H:i') }}
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td class="text-center">
                                                            @if ($template->contracts->count() > 0)
                                                                <span
                                                                    class="badge badge-success">{{ $template->contracts->count() }}</span>
                                                            @else
                                                                <span class="text-muted">0</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <span
                                                                class="badge badge-{{ $template->is_active ? 'success' : 'secondary' }}">
                                                                {{ $template->is_active ? 'Hoạt động' : 'Tắt' }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <span
                                                                class="badge badge-secondary">{{ $template->sort_order }}</span>
                                                        </td>
                                                        <td>
                                                            <div class="btn-group" role="group">
                                                                <a href="{{ route('admin.contract-templates.edit', $template) }}"
                                                                    class="btn btn-sm btn-outline-primary"
                                                                    title="Chỉnh sửa">
                                                                    <i class="fas fa-edit"></i>
                                                                </a>
                                                                <a href="{{ route('admin.contract-templates.show', $template) }}"
                                                                    class="btn btn-sm btn-outline-secondary"
                                                                    title="Chi tiết">
                                                                    <i class="fas fa-info"></i>
                                                                </a>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    @if ($contractType->templates->count() > 10)
                                        <div class="text-center mt-3">
                                            <a href="{{ route('admin.contract-types.templates', $contractType) }}"
                                                class="btn btn-sm btn-outline-primary">
                                                Xem tất cả {{ $contractType->templates->count() }} templates
                                            </a>
                                        </div>
                                    @endif
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

                        <!-- Usage History -->
                        @if ($contractType->templates->flatMap->contracts->count() > 0)
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Hợp đồng gần đây</h6>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Template</th>
                                                    <th>Số hợp đồng</th>
                                                    <th>Ngày ký</th>
                                                    <th>Giá trị</th>
                                                    <th>Trạng thái</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($contractType->templates->flatMap->contracts->sortByDesc('created_at')->take(10) as $contract)
                                                    <tr>
                                                        <td>
                                                            <a href="{{ route('admin.contract-templates.show', $contract->template) }}"
                                                                class="text-decoration-none small">
                                                                {{ $contract->template->name }}
                                                            </a>
                                                        </td>
                                                        <td>
                                                            <a href="#" class="text-decoration-none">
                                                                {{ $contract->contract_number ?? 'Chưa có số' }}
                                                            </a>
                                                        </td>
                                                        <td>{{ $contract->contract_date->format('d/m/Y') }}</td>
                                                        <td>{{ $contract->formatted_transaction_value }}</td>
                                                        <td>
                                                            <span class="badge badge-{{ $contract->status_color }}">
                                                                {{ $contract->status_label }}
                                                            </span>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="col-lg-4">
                        <!-- Quick Actions -->
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-success">
                                    <i class="fas fa-bolt mr-2"></i>Thao tác nhanh
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="d-grid gap-2">
                                    <a href="{{ route('admin.contract-types.edit', $contractType) }}"
                                        class="btn btn-primary btn-sm">
                                        <i class="fas fa-edit mr-2"></i>Chỉnh sửa loại hợp đồng
                                    </a>
                                    <a href="{{ route('admin.contract-templates.create') }}?contract_type_id={{ $contractType->id }}"
                                        class="btn btn-success btn-sm">
                                        <i class="fas fa-plus mr-2"></i>Thêm template mới
                                    </a>
                                    <a href="{{ route('admin.contract-types.templates', $contractType) }}"
                                        class="btn btn-info btn-sm">
                                        <i class="fas fa-list mr-2"></i>Quản lý templates
                                    </a>
                                    <button type="button" class="btn btn-outline-warning btn-sm" id="toggleStatusBtn">
                                        <i class="fas fa-{{ $contractType->is_active ? 'pause' : 'play' }} mr-2"></i>
                                        {{ $contractType->is_active ? 'Tạm dừng' : 'Kích hoạt' }}
                                    </button>
                                    @if ($contractType->canBeDeleted())
                                        <button type="button" class="btn btn-outline-danger btn-sm" id="deleteBtn">
                                            <i class="fas fa-trash mr-2"></i>Xóa loại hợp đồng
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Statistics -->
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-warning">
                                    <i class="fas fa-chart-bar mr-2"></i>Thống kê
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row text-center mb-3">
                                    <div class="col-6">
                                        <div class="border-right">
                                            <h4 class="font-weight-bold text-primary">
                                                {{ $contractType->templates->count() }}</h4>
                                            <div class="small text-muted">Templates</div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <h4 class="font-weight-bold text-success">
                                            {{ $contractType->templates->where('is_active', true)->count() }}
                                        </h4>
                                        <div class="small text-muted">Hoạt động</div>
                                    </div>
                                </div>
                                <div class="row text-center mb-3">
                                    <div class="col-6">
                                        <div class="border-right">
                                            <h4 class="font-weight-bold text-info">
                                                {{ $contractType->templates->flatMap->contracts->count() }}
                                            </h4>
                                            <div class="small text-muted">Hợp đồng</div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <h4 class="font-weight-bold text-warning">
                                            {{ $contractType->templates->flatMap->contracts->where('status', 'completed')->count() }}
                                        </h4>
                                        <div class="small text-muted">Hoàn thành</div>
                                    </div>
                                </div>
                                <hr class="my-3">
                                <div class="small text-muted">
                                    <div class="d-flex justify-content-between">
                                        <span>Tạo:</span>
                                        <span>{{ $contractType->created_at->format('d/m/Y') }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span>Cập nhật:</span>
                                        <span>{{ $contractType->updated_at->format('d/m/Y') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Template Usage Chart -->
                        @if ($contractType->templates->count() > 0)
                            <div class="card shadow">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-info">
                                        <i class="fas fa-chart-pie mr-2"></i>Sử dụng templates
                                    </h6>
                                </div>
                                <div class="card-body">
                                    @foreach ($contractType->templates->sortByDesc(function ($template) {
                return $template->contracts->count();
            })->take(5) as $template)
                                        <div class="mb-3">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <small
                                                    class="font-weight-bold">{{ Str::limit($template->name, 20) }}</small>
                                                <small class="text-muted">{{ $template->contracts->count() }}</small>
                                            </div>
                                            <div class="progress" style="height: 8px;">
                                                @php
                                                    $totalContracts = $contractType->templates->flatMap->contracts->count();
                                                    $percentage =
                                                        $totalContracts > 0
                                                            ? ($template->contracts->count() / $totalContracts) * 100
                                                            : 0;
                                                @endphp
                                                <div class="progress-bar" style="width: {{ $percentage }}%"
                                                    title="{{ $template->name }}: {{ $template->contracts->count() }} hợp đồng">
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                    @if ($contractType->templates->count() == 0)
                                        <div class="text-center text-muted">
                                            <i class="fas fa-chart-pie fa-2x mb-2"></i>
                                            <p class="small">Chưa có dữ liệu sử dụng</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            // Toggle status
            $('#toggleStatusBtn').click(function() {
                const typeId = {{ $contractType->id }};

                $.post(`/admin/contract-types/${typeId}/toggle-status`, {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    })
                    .done(function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            setTimeout(() => location.reload(), 1000);
                        } else {
                            toastr.error(response.message);
                        }
                    })
                    .fail(function() {
                        toastr.error('Có lỗi xảy ra khi cập nhật trạng thái');
                    });
            });

            // Duplicate contract type
            $('#duplicateBtn').click(function() {
                const typeId = {{ $contractType->id }};

                Swal.fire({
                    title: 'Sao chép loại hợp đồng',
                    text: 'Tạo bản sao của loại hợp đồng này cùng tất cả templates?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Sao chép',
                    cancelButtonText: 'Hủy'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Show loading
                        Swal.fire({
                            title: 'Đang sao chép...',
                            text: 'Vui lòng đợi',
                            allowOutsideClick: false,
                            showConfirmButton: false,
                            willOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        $.post(`/admin/contract-types/${typeId}/duplicate`, {
                                _token: $('meta[name="csrf-token"]').attr('content')
                            })
                            .done(function(response) {
                                Swal.close();
                                toastr.success('Đã sao chép thành công');
                                // Redirect to edit page of new contract type
                                window.location.href = response.redirect ||
                                    '/admin/contract-types';
                            })
                            .fail(function() {
                                Swal.close();
                                toastr.error('Có lỗi xảy ra khi sao chép loại hợp đồng');
                            });
                    }
                });
            });

            // Delete contract type
            $('#deleteBtn').click(function() {
                const typeId = {{ $contractType->id }};
                const typeName = '{{ $contractType->name }}';

                Swal.fire({
                    title: 'Xác nhận xóa',
                    text: `Bạn có chắc chắn muốn xóa loại hợp đồng "${typeName}"?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Xóa',
                    cancelButtonText: 'Hủy'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/admin/contract-types/${typeId}`,
                            type: 'DELETE',
                            data: {
                                _token: $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                if (response.success) {
                                    toastr.success(response.message);
                                    window.location.href = '/admin/contract-types';
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
        });
    </script>
@endsection
