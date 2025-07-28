@extends('layouts.app')

@section('content')
<div class="container-fluid p-3">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.dossiers.index') }}">Hồ sơ</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.dossiers.show', $dossier) }}">{{ $dossier->name }}</a></li>
            <li class="breadcrumb-item active">Chỉnh sửa</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Chỉnh Sửa Hồ Sơ</h1>
            <p class="text-muted">Cập nhật thông tin hồ sơ: <strong>{{ $dossier->name }}</strong></p>
        </div>
        <div>
            <a href="{{ route('admin.dossiers.show', $dossier) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Quay lại
            </a>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-edit me-2"></i>Thông Tin Hồ Sơ
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.dossiers.update', $dossier) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Tên hồ sơ -->
                        <div class="mb-3">
                            <label for="name" class="form-label">
                                Tên Hồ Sơ <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                   class="form-control @error('name') is-invalid @enderror"
                                   id="name"
                                   name="name"
                                   value="{{ old('name', $dossier->name) }}"
                                   required
                                   autofocus>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Mô tả -->
                        <div class="mb-4">
                            <label for="description" class="form-label">Mô Tả</label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                      id="description"
                                      name="description"
                                      rows="4">{{ old('description', $dossier->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Actions -->
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.dossiers.show', $dossier) }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>Hủy
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Cập Nhật
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar with info -->
        <div class="col-lg-4">
            <div class="card bg-light">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-info-circle me-2"></i>Thông Tin Hồ Sơ
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted d-block">Trạng thái hiện tại</small>
                        <span class="badge bg-{{ $dossier->status_color }} fs-6">{{ $dossier->status_label }}</span>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block">Người tạo</small>
                        <div class="fw-medium">{{ $dossier->creator->name }}</div>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block">Ngày tạo</small>
                        <div class="fw-medium">{{ $dossier->created_at->format('d/m/Y H:i') }}</div>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block">Cập nhật cuối</small>
                        <div class="fw-medium">{{ $dossier->updated_at->format('d/m/Y H:i') }}</div>
                    </div>

                    <div class="mb-0">
                        <small class="text-muted d-block">Số hợp đồng</small>
                        <div class="fw-medium">{{ $dossier->contracts()->count() }} hợp đồng</div>
                    </div>
                </div>
            </div>

            <!-- Warning card if needed -->
            @if($dossier->contracts()->count() > 0)
                <div class="card border-warning mt-3">
                    <div class="card-header bg-warning bg-opacity-10">
                        <h6 class="card-title mb-0 text-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>Lưu Ý
                        </h6>
                    </div>
                    <div class="card-body">
                        <p class="small text-muted mb-0">
                            Hồ sơ này đã có {{ $dossier->contracts()->count() }} hợp đồng.
                            Việc thay đổi tên hồ sơ có thể ảnh hưởng đến việc tra cứu sau này.
                        </p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
