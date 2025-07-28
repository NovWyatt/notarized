@extends('layouts.app')

@section('content')
<div class="container-fluid p-3">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.dossiers.index') }}">Hồ sơ</a></li>
            <li class="breadcrumb-item active">Tạo mới</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Tạo Hồ Sơ Mới</h1>
            <p class="text-muted">Tạo hồ sơ để quản lý các hợp đồng liên quan</p>
        </div>
        <div>
            <a href="{{ route('admin.dossiers.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Quay lại
            </a>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-folder-plus me-2"></i>Thông Tin Hồ Sơ
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.dossiers.store') }}" method="POST">
                        @csrf

                        <!-- Tên hồ sơ -->
                        <div class="mb-3">
                            <label for="name" class="form-label">
                                Tên Hồ Sơ <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                   class="form-control @error('name') is-invalid @enderror"
                                   id="name"
                                   name="name"
                                   value="{{ old('name') }}"
                                   required
                                   autofocus
                                   placeholder="Nhập tên hồ sơ...">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                Tên hồ sơ nên ngắn gọn và mô tả rõ mục đích (VD: "Mua bán nhà 123 Đường ABC")
                            </div>
                        </div>

                        <!-- Mô tả -->
                        <div class="mb-4">
                            <label for="description" class="form-label">Mô Tả</label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                      id="description"
                                      name="description"
                                      rows="4"
                                      placeholder="Mô tả chi tiết về hồ sơ này...">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                Mô tả thêm về hồ sơ, các bên liên quan, mục đích sử dụng... (tùy chọn)
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.dossiers.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>Hủy
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Tạo Hồ Sơ
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar with tips -->
        <div class="col-lg-4">
            <div class="card bg-light">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-lightbulb me-2"></i>Gợi Ý
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6 class="text-primary">💡 Đặt tên hồ sơ</h6>
                        <ul class="small text-muted mb-0">
                            <li>Sử dụng tên ngắn gọn, dễ hiểu</li>
                            <li>Bao gồm loại giao dịch và địa chỉ/tài sản</li>
                            <li>VD: "Mua bán nhà 123 Nguyễn Trãi"</li>
                        </ul>
                    </div>

                    <div class="mb-3">
                        <h6 class="text-success">📁 Sau khi tạo hồ sơ</h6>
                        <ul class="small text-muted mb-0">
                            <li>Chọn template hợp đồng phù hợp</li>
                            <li>Thêm đương sự và tài sản</li>
                            <li>Tạo và xuất hợp đồng</li>
                        </ul>
                    </div>

                    <div>
                        <h6 class="text-info">🔧 Chuẩn bị trước</h6>
                        <ul class="small text-muted mb-0">
                            <li>
                                <a href="{{ route('litigants.create') }}" target="_blank" class="text-decoration-none">
                                    Tạo đương sự
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('properties.create') }}" target="_blank" class="text-decoration-none">
                                    Thêm tài sản
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.contract-templates.index') }}" target="_blank" class="text-decoration-none">
                                    Xem templates
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Auto-focus vào tên hồ sơ
document.getElementById('name').focus();

// Auto-suggest tên hồ sơ dựa vào ngày
document.addEventListener('DOMContentLoaded', function() {
    const nameInput = document.getElementById('name');
    const today = new Date();
    const dateStr = today.getDate().toString().padStart(2, '0') + '/' +
                   (today.getMonth() + 1).toString().padStart(2, '0') + '/' +
                   today.getFullYear();

    nameInput.placeholder = `VD: Hồ sơ giao dịch ${dateStr}`;
});
</script>
@endsection
