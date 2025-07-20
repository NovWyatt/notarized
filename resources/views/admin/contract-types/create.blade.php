@extends('layouts.app2')

@section('content')
    <div class="container-fluid p-3">
        <div class="row">
            <div class="col-12">
                <!-- Header -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h1 class="h3 mb-0 text-gray-800">Tạo loại hợp đồng mới</h1>
                        <p class="text-muted">Thêm loại hợp đồng mới vào hệ thống</p>
                    </div>
                    <div>
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
                                <form action="{{ route('admin.contract-types.store') }}" method="POST"
                                    id="contractTypeForm">
                                    @csrf

                                    <div class="form-group">
                                        <label for="name" class="form-label required">Tên loại hợp đồng</label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                                            id="name" name="name" value="{{ old('name') }}"
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
                                            rows="4" placeholder="Mô tả chi tiết về loại hợp đồng này...">{{ old('description') }}</textarea>
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
                                                    id="sort_order" name="sort_order" value="{{ old('sort_order', 0) }}"
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
                                                        {{ old('is_active', true) ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="is_active">
                                                        Kích hoạt loại hợp đồng
                                                    </label>
                                                </div>
                                                <small class="form-text text-muted">
                                                    Chỉ các loại hợp đồng được kích hoạt mới có thể tạo template
                                                </small>
                                            </div>
                                        </div>
                                    </div>

                                    <hr class="my-4">

                                    <div class="d-flex justify-content-between">
                                        <a href="{{ route('admin.contract-types.index') }}" class="btn btn-secondary">
                                            <i class="fas fa-times mr-2"></i>Hủy
                                        </a>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save mr-2"></i>Lưu loại hợp đồng
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <!-- Help Card -->
                        <div class="card shadow">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-info">
                                    <i class="fas fa-info-circle mr-2"></i>Hướng dẫn
                                </h6>
                            </div>
                            <div class="card-body">
                                <h6 class="font-weight-bold">Loại hợp đồng là gì?</h6>
                                <p class="text-muted small">
                                    Loại hợp đồng giúp phân loại và tổ chức các template hợp đồng theo từng lĩnh vực khác
                                    nhau.
                                </p>

                                <h6 class="font-weight-bold mt-3">Một số ví dụ:</h6>
                                <ul class="text-muted small">
                                    <li>Hợp đồng mua bán nhà đất</li>
                                    <li>Hợp đồng chuyển nhượng quyền sử dụng đất</li>
                                    <li>Hợp đồng thuê nhà</li>
                                    <li>Hợp đồng vay tài sản</li>
                                    <li>Hợp đồng thế chấp</li>
                                </ul>

                                <h6 class="font-weight-bold mt-3">Lưu ý:</h6>
                                <ul class="text-muted small">
                                    <li>Tên loại hợp đồng phải duy nhất</li>
                                    <li>Chỉ loại hợp đồng được kích hoạt mới có thể tạo template</li>
                                    <li>Không thể xóa loại hợp đồng đã có template</li>
                                </ul>
                            </div>
                        </div>

                        <!-- Quick Actions -->
                        <div class="card shadow mt-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-success">
                                    <i class="fas fa-bolt mr-2"></i>Thao tác nhanh
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="d-grid gap-2">
                                    <button type="button" class="btn btn-outline-primary btn-sm" id="fillSampleData">
                                        <i class="fas fa-magic mr-2"></i>Điền dữ liệu mẫu
                                    </button>
                                    <button type="button" class="btn btn-outline-info btn-sm" id="clearForm">
                                        <i class="fas fa-eraser mr-2"></i>Xóa form
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Preview -->
                        <div class="card shadow mt-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-warning">
                                    <i class="fas fa-eye mr-2"></i>Xem trước
                                </h6>
                            </div>
                            <div class="card-body">
                                <div id="preview-content">
                                    <div class="text-muted text-center">
                                        <i class="fas fa-file-alt fa-2x mb-2"></i>
                                        <p class="small">Nhập thông tin để xem trước</p>
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
            // Real-time preview
            function updatePreview() {
                const name = $('#name').val();
                const description = $('#description').val();
                const isActive = $('#is_active').is(':checked');
                const sortOrder = $('#sort_order').val();

                if (name || description) {
                    const previewHtml = `
                <div class="border rounded p-3">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h6 class="font-weight-bold text-primary mb-0">
                            ${name || 'Tên loại hợp đồng'}
                        </h6>
                        <span class="badge badge-${isActive ? 'success' : 'secondary'}">
                            ${isActive ? 'Hoạt động' : 'Tắt'}
                        </span>
                    </div>
                    ${description ? `<p class="text-muted small mb-2">${description}</p>` : ''}
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted">
                            <i class="fas fa-sort mr-1"></i>Thứ tự: ${sortOrder || 0}
                        </small>
                        <small class="text-muted">
                            <i class="fas fa-file-alt mr-1"></i>0 template
                        </small>
                    </div>
                </div>
            `;
                    $('#preview-content').html(previewHtml);
                } else {
                    $('#preview-content').html(`
                <div class="text-muted text-center">
                    <i class="fas fa-file-alt fa-2x mb-2"></i>
                    <p class="small">Nhập thông tin để xem trước</p>
                </div>
            `);
                }
            }

            // Bind events for real-time preview
            $('#name, #description, #sort_order').on('input', updatePreview);
            $('#is_active').on('change', updatePreview);

            // Fill sample data
            $('#fillSampleData').click(function() {
                $('#name').val('Hợp đồng mua bán nhà đất');
                $('#description').val(
                    'Các hợp đồng liên quan đến việc mua bán, chuyển nhượng quyền sở hữu nhà ở, đất ở và các loại bất động sản khác.'
                    );
                $('#sort_order').val('1');
                $('#is_active').prop('checked', true);
                updatePreview();
            });

            // Clear form
            $('#clearForm').click(function() {
                if (confirm('Bạn có chắc chắn muốn xóa tất cả dữ liệu đã nhập?')) {
                    $('#contractTypeForm')[0].reset();
                    updatePreview();
                }
            });

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

            // Name input validation
            $('#name').on('blur', function() {
                const name = $(this).val().trim();
                if (name) {
                    // Check if name exists (you can implement AJAX check here)
                    // For now, just remove invalid class if name is provided
                    $(this).removeClass('is-invalid');
                }
            });

            // Auto-generate sort order
            $('#name').on('input', function() {
                const sortOrderField = $('#sort_order');
                if (sortOrderField.val() == '0' || sortOrderField.val() == '') {
                    // Auto-generate sort order based on existing types (you can implement AJAX here)
                    // For now, just set to 1
                    if ($(this).val().trim()) {
                        sortOrderField.val('1');
                    }
                }
            });

            // Initialize preview
            updatePreview();
        });
    </script>
@endsection
