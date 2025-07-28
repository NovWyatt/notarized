@extends('layouts.app')

@section('content')
    <div class="container-fluid p-3">
        <div class="row">
            <div class="col-12">
                <!-- Header -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h1 class="h3 mb-0 text-gray-800">Chỉnh sửa loại hợp đồng: {{ $contractType->name }}</h1>
                        <p class="text-muted">
                            <i class="fas fa-calendar mr-1"></i>Tạo: {{ $contractType->created_at->format('d/m/Y H:i') }}
                            @if ($contractType->templates->count() > 0)
                                <span class="ml-3">
                                    <i class="fas fa-file-alt mr-1"></i>{{ $contractType->templates->count() }} templates
                                </span>
                            @endif
                        </p>
                    </div>
                    <div>
                        <a href="{{ route('admin.contract-types.show', $contractType) }}" class="btn btn-info">
                            <i class="fas fa-eye mr-2"></i>Xem chi tiết
                        </a>
                        <a href="{{ route('admin.contract-types.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left mr-2"></i>Quay lại
                        </a>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-8">
                        <!-- Main Form -->
                        <div class="card shadow">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Thông tin loại hợp đồng</h6>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('admin.contract-types.update', $contractType) }}" method="POST"
                                    id="contractTypeForm">
                                    @csrf
                                    @method('PUT')

                                    <div class="form-group">
                                        <label for="name" class="form-label required">Tên loại hợp đồng</label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                                            id="name" name="name" value="{{ old('name', $contractType->name) }}"
                                            placeholder="Ví dụ: Hợp đồng mua bán nhà đất" required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">
                                            Tên loại hợp đồng phải duy nhất trong hệ thống
                                        </small>
                                    </div>

                                    <div class="form-group">
                                        <label for="description" class="form-label">Mô tả</label>
                                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                                            rows="4" placeholder="Mô tả chi tiết về loại hợp đồng này...">{{ old('description', $contractType->description) }}</textarea>
                                        @error('description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="sort_order" class="form-label">Thứ tự sắp xếp</label>
                                                <input type="number"
                                                    class="form-control @error('sort_order') is-invalid @enderror"
                                                    id="sort_order" name="sort_order"
                                                    value="{{ old('sort_order', $contractType->sort_order) }}"
                                                    min="0">
                                                @error('sort_order')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <small class="form-text text-muted">
                                                    Số nhỏ hơn sẽ hiển thị trước
                                                </small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label">Trạng thái</label>
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input" id="is_active"
                                                        name="is_active" value="1"
                                                        {{ old('is_active', $contractType->is_active) ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="is_active">
                                                        Kích hoạt loại hợp đồng
                                                    </label>
                                                </div>
                                                <small class="form-text text-muted">
                                                    Chỉ các loại hợp đồng được kích hoạt mới có thể tạo template
                                                </small>
                                                @if ($contractType->templates->count() > 0 && !$contractType->is_active)
                                                    <div class="alert alert-warning mt-2">
                                                        <i class="fas fa-exclamation-triangle mr-2"></i>
                                                        <small>Loại hợp đồng này có {{ $contractType->templates->count() }}
                                                            templates. Việc tắt kích hoạt sẽ ảnh hưởng đến khả năng tạo hợp
                                                            đồng mới.</small>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <hr class="my-4">

                                    <div class="d-flex justify-content-between">
                                        <a href="{{ route('admin.contract-types.show', $contractType) }}"
                                            class="btn btn-secondary">
                                            <i class="fas fa-times mr-2"></i>Hủy
                                        </a>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save mr-2"></i>Cập nhật loại hợp đồng
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <!-- Usage Impact Warning -->
                        @if ($contractType->templates->count() > 0)
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-warning">
                                        <i class="fas fa-exclamation-triangle mr-2"></i>Cảnh báo
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="alert alert-warning mb-3">
                                        <strong>Loại hợp đồng này đang được sử dụng!</strong>
                                    </div>
                                    <ul class="list-unstyled mb-0">
                                        <li class="mb-2">
                                            <i class="fas fa-file-alt text-info mr-2"></i>
                                            <strong>{{ $contractType->templates->count() }}</strong> templates
                                        </li>
                                        <li class="mb-2">
                                            <i class="fas fa-handshake text-success mr-2"></i>
                                            <strong>{{ $contractType->templates->flatMap->contracts->count() }}</strong>
                                            hợp đồng
                                        </li>
                                    </ul>
                                    <hr class="my-3">
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle mr-1"></i>
                                        Hãy cẩn thận khi thay đổi thông tin vì có thể ảnh hưởng đến các templates và hợp
                                        đồng hiện có.
                                    </small>
                                </div>
                            </div>
                        @endif

                        <!-- Current Templates -->
                        @if ($contractType->templates->count() > 0)
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-info">
                                        <i class="fas fa-list mr-2"></i>Templates hiện tại
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="list-group list-group-flush">
                                        @foreach ($contractType->templates->take(5) as $template)
                                            <div class="list-group-item px-0">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <a href="{{ route('admin.contract-templates.show', $template) }}"
                                                            class="text-decoration-none font-weight-bold">
                                                            {{ Str::limit($template->name, 25) }}
                                                        </a>
                                                        <div class="small text-muted">
                                                            {{ $template->contracts->count() }} hợp đồng
                                                        </div>
                                                    </div>
                                                    <span
                                                        class="badge badge-{{ $template->is_active ? 'success' : 'secondary' }}">
                                                        {{ $template->is_active ? 'Hoạt động' : 'Tắt' }}
                                                    </span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    @if ($contractType->templates->count() > 5)
                                        <div class="text-center mt-3">
                                            <a href="{{ route('admin.contract-types.templates', $contractType) }}"
                                                class="btn btn-sm btn-outline-info">
                                                Xem tất cả {{ $contractType->templates->count() }} templates
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <!-- Statistics -->
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-success">
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
                                <div class="row text-center">
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

                        <!-- Quick Actions -->
                        <div class="card shadow">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">
                                    <i class="fas fa-bolt mr-2"></i>Thao tác nhanh
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="d-grid gap-2">
                                    <a href="{{ route('admin.contract-templates.create') }}?contract_type_id={{ $contractType->id }}"
                                        class="btn btn-success btn-sm">
                                        <i class="fas fa-plus mr-2"></i>Thêm template mới
                                    </a>
                                    <a href="{{ route('admin.contract-types.templates', $contractType) }}"
                                        class="btn btn-info btn-sm">
                                        <i class="fas fa-list mr-2"></i>Quản lý templates
                                    </a>
                                    @if ($contractType->canBeDeleted())
                                        <button type="button" class="btn btn-outline-danger btn-sm" id="deleteBtn">
                                            <i class="fas fa-trash mr-2"></i>Xóa loại hợp đồng
                                        </button>
                                    @endif
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
            // Real-time preview
            function updatePreview() {
                const name = $('#name').val();
                const description = $('#description').val();
                const isActive = $('#is_active').is(':checked');
                const sortOrder = $('#sort_order').val();

                // Update any preview elements if needed
                // This is just for demonstration
                console.log('Contract Type updated:', {
                    name,
                    description,
                    isActive,
                    sortOrder
                });
            }

            // Bind events for real-time updates
            $('#name, #description, #sort_order').on('input', updatePreview);
            $('#is_active').on('change', updatePreview);

            // Form validation
            $('#contractTypeForm').on('submit', function(e) {
                let isValid = true;

                // Validate name
                const name = $('#name').val().trim();
                if (!name) {
                    $('#name').addClass('is-invalid');
                    isValid = false;
                } else {
                    $('#name').removeClass('is-invalid');
                }

                if (!isValid) {
                    e.preventDefault();
                    toastr.error('Vui lòng điền đầy đủ thông tin bắt buộc');
                }
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

            // Warn about unsaved changes
            @if ($contractType->templates->count() > 0)
                const originalFormData = $('#contractTypeForm').serialize();
                $(window).on('beforeunload', function() {
                    if ($('#contractTypeForm').serialize() !== originalFormData) {
                        return 'Bạn có thay đổi chưa được lưu. Bạn có chắc chắn muốn rời khỏi trang?';
                    }
                });

                $('#contractTypeForm').on('submit', function() {
                    $(window).off('beforeunload');
                });
            @endif

            // Auto-save draft functionality (optional)
            let saveTimeout;
            $('#name, #description, #sort_order').on('input', function() {
                clearTimeout(saveTimeout);
                saveTimeout = setTimeout(function() {
                    // Auto-save logic can be implemented here
                    console.log('Auto-saving draft...');
                }, 2000);
            });

            // Name validation (check for duplicates)
            $('#name').on('blur', function() {
                const name = $(this).val().trim();
                const currentName = '{{ $contractType->name }}';

                if (name && name !== currentName) {
                    // Check if name exists (you can implement AJAX check here)
                    // For now, just basic validation
                    $(this).removeClass('is-invalid');
                }
            });
        });
    </script>
@endsection
