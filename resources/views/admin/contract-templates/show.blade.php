@extends('layouts.app')

@section('content')
    <div class="container-fluid p-3">
        <div class="row">
            <div class="col-12">
                <!-- Header -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h1 class="h3 mb-0 text-gray-800">{{ $contractTemplate->name }}</h1>
                        <p class="text-muted">
                            <i class="fas fa-file-contract mr-1"></i>{{ $contractTemplate->contractType->name }}
                            <span class="ml-3">
                                <i class="fas fa-calendar mr-1"></i>Tạo:
                                {{ $contractTemplate->created_at->format('d/m/Y H:i') }}
                            </span>
                            @if ($contractTemplate->updated_at != $contractTemplate->created_at)
                                <span class="ml-3">
                                    <i class="fas fa-edit mr-1"></i>Cập nhật:
                                    {{ $contractTemplate->updated_at->format('d/m/Y H:i') }}
                                </span>
                            @endif
                        </p>
                    </div>
                    <div>
                        <a href="{{ route('admin.contract-templates.preview', $contractTemplate) }}" class="btn btn-info"
                            target="_blank">
                            <i class="fas fa-eye mr-2"></i>Xem trước
                        </a>
                        <a href="{{ route('admin.contract-templates.edit', $contractTemplate) }}" class="btn btn-primary">
                            <i class="fas fa-edit mr-2"></i>Chỉnh sửa
                        </a>
                        <a href="{{ route('admin.contract-templates.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left mr-2"></i>Quay lại
                        </a>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-8">
                        <!-- Template Information -->
                        <div class="card shadow mb-4">
                            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                                <h6 class="m-0 font-weight-bold text-primary">Thông tin template</h6>
                                <div>
                                    <span class="badge badge-{{ $contractTemplate->is_active ? 'success' : 'secondary' }}">
                                        {{ $contractTemplate->is_active ? 'Hoạt động' : 'Tạm dừng' }}
                                    </span>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6 class="font-weight-bold">Tên template:</h6>
                                        <p class="text-muted">{{ $contractTemplate->name }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="font-weight-bold">Loại hợp đồng:</h6>
                                        <p class="text-muted">
                                            <a href="{{ route('admin.contract-types.show', $contractTemplate->contractType) }}"
                                                class="text-decoration-none">
                                                {{ $contractTemplate->contractType->name }}
                                            </a>
                                        </p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6 class="font-weight-bold">Thứ tự sắp xếp:</h6>
                                        <p class="text-muted">{{ $contractTemplate->sort_order }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="font-weight-bold">Số hợp đồng đã tạo:</h6>
                                        <p class="text-muted">
                                            <span class="badge badge-info">{{ $contractTemplate->contracts_count }}</span>
                                            hợp đồng
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Template Settings -->
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Cài đặt template</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6 class="font-weight-bold mb-3">Hiển thị các thành phần:</h6>
                                        <ul class="list-unstyled">
                                            <li class="mb-2">
                                                <i
                                                    class="fas fa-{{ $contractTemplate->shouldShow('parties') ? 'check text-success' : 'times text-danger' }} mr-2"></i>
                                                Các bên tham gia
                                            </li>
                                            <li class="mb-2">
                                                <i
                                                    class="fas fa-{{ $contractTemplate->shouldShow('assets') ? 'check text-success' : 'times text-danger' }} mr-2"></i>
                                                Tài sản
                                            </li>
                                            <li class="mb-2">
                                                <i
                                                    class="fas fa-{{ $contractTemplate->shouldShow('transaction_value') ? 'check text-success' : 'times text-danger' }} mr-2"></i>
                                                Giá trị giao dịch
                                            </li>
                                            <li class="mb-2">
                                                <i
                                                    class="fas fa-{{ $contractTemplate->shouldShow('clauses') ? 'check text-success' : 'times text-danger' }} mr-2"></i>
                                                Điều khoản
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="font-weight-bold mb-3">Cài đặt khác:</h6>
                                        <ul class="list-unstyled">
                                            <li class="mb-2">
                                                <i
                                                    class="fas fa-{{ $contractTemplate->shouldShow('testimonial') ? 'check text-success' : 'times text-danger' }} mr-2"></i>
                                                Lời chứng
                                            </li>
                                            <li class="mb-2">
                                                <i
                                                    class="fas fa-{{ $contractTemplate->shouldShow('signatures') ? 'check text-success' : 'times text-danger' }} mr-2"></i>
                                                Chữ ký
                                            </li>
                                            <li class="mb-2">
                                                <i
                                                    class="fas fa-{{ $contractTemplate->shouldShow('notary_info') ? 'check text-success' : 'times text-danger' }} mr-2"></i>
                                                Thông tin công chứng
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6 class="font-weight-bold">Yêu cầu tối thiểu:</h6>
                                        <p class="text-muted mb-0">
                                            <i class="fas fa-users mr-2"></i>Số bên:
                                            {{ $contractTemplate->getRequiredPartiesMin() }}
                                        </p>
                                        <p class="text-muted">
                                            <i class="fas fa-home mr-2"></i>Số tài sản:
                                            {{ $contractTemplate->getRequiredAssetsMin() }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Template Content Preview -->
                        <div class="card shadow mb-4">
                            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                                <h6 class="m-0 font-weight-bold text-primary">Nội dung template</h6>
                                <div>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" id="toggleRawContent">
                                        <i class="fas fa-code mr-1"></i>Xem mã HTML
                                    </button>
                                    <a href="{{ route('admin.contract-templates.export', $contractTemplate) }}?format=html"
                                        class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-download mr-1"></i>Tải xuống
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
                                <div id="renderedContent"
                                    style="max-height: 500px; overflow-y: auto; border: 1px solid #e3e6f0; padding: 15px; border-radius: 5px;">
                                    {!! $contractTemplate->generateContent() !!}
                                </div>
                                <div id="rawContent" style="display: none;">
                                    <pre class="bg-light p-3 rounded" style="max-height: 500px; overflow-y: auto;"><code>{{ $contractTemplate->content }}</code></pre>
                                </div>
                            </div>
                        </div>

                        <!-- Default Clauses -->
                        @if (!empty($contractTemplate->default_clauses))
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Điều khoản mặc định</h6>
                                </div>
                                <div class="card-body">
                                    @foreach ($contractTemplate->getProcessedDefaultClauses() as $index => $clause)
                                        <div class="clause-item mb-3 p-3 border rounded">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <h6 class="font-weight-bold text-primary mb-0">
                                                    {{ $clause['title'] ?? 'Điều khoản ' . ($index + 1) }}
                                                </h6>
                                                <div>
                                                    @if (isset($clause['is_required']) && $clause['is_required'])
                                                        <span class="badge badge-warning">Bắt buộc</span>
                                                    @endif
                                                    @if (isset($clause['order']))
                                                        <span class="badge badge-secondary">Thứ tự:
                                                            {{ $clause['order'] }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="text-muted">
                                                {{ $clause['content'] ?? '' }}
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Recent Contracts -->
                        @if ($contractTemplate->contracts->count() > 0)
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Hợp đồng gần đây</h6>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Số hợp đồng</th>
                                                    <th>Ngày ký</th>
                                                    <th>Giá trị</th>
                                                    <th>Trạng thái</th>
                                                    <th>Thao tác</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($contractTemplate->contracts as $contract)
                                                    <tr>
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
                                                        <td>
                                                            <a href="#" class="btn btn-sm btn-outline-primary">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    @if ($contractTemplate->contracts_count > 10)
                                        <div class="text-center mt-3">
                                            <a href="#" class="btn btn-sm btn-outline-primary">
                                                Xem tất cả {{ $contractTemplate->contracts_count }} hợp đồng
                                            </a>
                                        </div>
                                    @endif
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
                                    <a href="{{ route('admin.contract-templates.edit', $contractTemplate) }}"
                                        class="btn btn-primary btn-sm">
                                        <i class="fas fa-edit mr-2"></i>Chỉnh sửa template
                                    </a>
                                    <a href="{{ route('admin.contract-templates.preview', $contractTemplate) }}"
                                        class="btn btn-info btn-sm" target="_blank">
                                        <i class="fas fa-eye mr-2"></i>Xem trước
                                    </a>
                                    <button type="button" class="btn btn-outline-secondary btn-sm" id="duplicateBtn">
                                        <i class="fas fa-copy mr-2"></i>Sao chép template
                                    </button>
                                    <div class="dropdown">
                                        <button class="btn btn-outline-primary btn-sm dropdown-toggle w-100"
                                            type="button" data-toggle="dropdown">
                                            <i class="fas fa-download mr-2"></i>Xuất template
                                        </button>
                                        <div class="dropdown-menu w-100">
                                            <a class="dropdown-item"
                                                href="{{ route('admin.contract-templates.export', $contractTemplate) }}?format=html">
                                                <i class="fas fa-code mr-2"></i>Xuất HTML
                                            </a>
                                            <a class="dropdown-item"
                                                href="{{ route('admin.contract-templates.export', $contractTemplate) }}?format=txt">
                                                <i class="fas fa-file-text mr-2"></i>Xuất Text
                                            </a>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-outline-warning btn-sm" id="toggleStatusBtn">
                                        <i class="fas fa-{{ $contractTemplate->is_active ? 'pause' : 'play' }} mr-2"></i>
                                        {{ $contractTemplate->is_active ? 'Tạm dừng' : 'Kích hoạt' }}
                                    </button>
                                    @if ($contractTemplate->canBeDeleted())
                                        <button type="button" class="btn btn-outline-danger btn-sm" id="deleteBtn">
                                            <i class="fas fa-trash mr-2"></i>Xóa template
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Template Info -->
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-info">
                                    <i class="fas fa-info-circle mr-2"></i>Thông tin văn phòng
                                </h6>
                            </div>
                            <div class="card-body">
                                @foreach ($contractTemplate->template_info as $key => $value)
                                    @if ($value)
                                        <div class="mb-2">
                                            <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong>
                                            <div class="text-muted small">{{ $value }}</div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>

                        <!-- Statistics -->
                        <div class="card shadow">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-warning">
                                    <i class="fas fa-chart-bar mr-2"></i>Thống kê
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row text-center">
                                    <div class="col-6">
                                        <div class="border-right">
                                            <h4 class="font-weight-bold text-primary">
                                                {{ $contractTemplate->contracts_count }}</h4>
                                            <div class="small text-muted">Hợp đồng</div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <h4 class="font-weight-bold text-success">
                                            {{ $contractTemplate->contracts->where('status', 'completed')->count() }}
                                        </h4>
                                        <div class="small text-muted">Hoàn thành</div>
                                    </div>
                                </div>
                                <hr class="my-3">
                                <div class="small text-muted">
                                    <div class="d-flex justify-content-between">
                                        <span>Tạo:</span>
                                        <span>{{ $contractTemplate->created_at->format('d/m/Y') }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span>Cập nhật:</span>
                                        <span>{{ $contractTemplate->updated_at->format('d/m/Y') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // Toggle between rendered and raw content
            $('#toggleRawContent').click(function() {
                const $rendered = $('#renderedContent');
                const $raw = $('#rawContent');
                const $btn = $(this);

                if ($raw.is(':visible')) {
                    $raw.hide();
                    $rendered.show();
                    $btn.html('<i class="fas fa-code mr-1"></i>Xem mã HTML');
                } else {
                    $rendered.hide();
                    $raw.show();
                    $btn.html('<i class="fas fa-eye mr-1"></i>Xem kết xuất');
                }
            });

            // Toggle status
            $('#toggleStatusBtn').click(function() {
                const templateId = {{ $contractTemplate->id }};
                const isActive = {{ $contractTemplate->is_active ? 'true' : 'false' }};

                $.post(`/admin/contract-templates/${templateId}/toggle-status`, {
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

            // Duplicate template
            $('#duplicateBtn').click(function() {
                const templateId = {{ $contractTemplate->id }};

                Swal.fire({
                    title: 'Sao chép template',
                    text: 'Tạo bản sao của template này?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Sao chép',
                    cancelButtonText: 'Hủy'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.post(`/admin/contract-templates/${templateId}/duplicate`, {
                                _token: $('meta[name="csrf-token"]').attr('content')
                            })
                            .done(function(response) {
                                toastr.success('Đã sao chép thành công');
                                // Redirect to edit page of new template
                                window.location.href = response.redirect ||
                                    '/admin/contract-templates';
                            })
                            .fail(function() {
                                toastr.error('Có lỗi xảy ra khi sao chép template');
                            });
                    }
                });
            });

            // Delete template
            $('#deleteBtn').click(function() {
                const templateId = {{ $contractTemplate->id }};
                const templateName = '{{ $contractTemplate->name }}';

                Swal.fire({
                    title: 'Xác nhận xóa',
                    text: `Bạn có chắc chắn muốn xóa template "${templateName}"?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Xóa',
                    cancelButtonText: 'Hủy'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/admin/contract-templates/${templateId}`,
                            type: 'DELETE',
                            data: {
                                _token: $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                if (response.success) {
                                    toastr.success(response.message);
                                    window.location.href = '/admin/contract-templates';
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
        });
    </script>
@endsection
