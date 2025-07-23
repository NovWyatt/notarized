{{-- resources/views/properties/show.blade.php --}}
@extends('layouts.app')
@section('content')
    <div class="container-fluid p-3">
        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-md-8">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('properties.index') }}">Tài sản</a></li>
                        <li class="breadcrumb-item active">{{ $displayName }}</li>
                    </ol>
                </nav>
                <h3 class="mb-0">{{ $displayName }}</h3>
                <div class="d-flex align-items-center mt-2">
                    <span class="badge bg-primary me-2">{{ $typeLabel }}</span>
                    <span class="text-muted">
                        <i class="fas fa-user me-1"></i>
                        Được tạo bởi {{ $userInfo['creator']['name'] }} - {{ $userInfo['creator']['created_at'] }}
                    </span>
                </div>
            </div>
            <div class="col-md-4 text-end">
                <div class="btn-group" role="group">
                    @if($canEdit)
                        <a href="{{ route('properties.edit', $asset) }}" class="btn btn-warning">
                            <i class="bi bi-pencil me-2"></i>Chỉnh sửa
                        </a>
                    @endif
                    <a href="{{ route('properties.clone', $asset) }}" class="btn btn-info">
                        <i class="bi bi-files me-2"></i>Sao chép
                    </a>
                    @if($canDelete)
                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                            <i class="bi bi-trash me-2"></i>Xóa
                        </button>
                    @endif
                </div>
            </div>
        </div>

        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Main Asset Information -->
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Thông tin chung</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tr>
                                <td class="text-muted">Tên tài sản:</td>
                                <td class="fw-bold">{{ $displayName }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Loại tài sản:</td>
                                <td><span class="badge bg-primary">{{ $typeLabel }}</span></td>
                            </tr>
                            @if($asset->notes)
                                <tr>
                                    <td class="text-muted">Ghi chú:</td>
                                    <td>{{ $asset->notes }}</td>
                                </tr>
                            @endif
                        </table>
                    </div>
                </div>

                <!-- User Information Card -->
                <div class="card mt-3">
                    <div class="card-header">
                        <h5 class="mb-0">Thông tin người dùng</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="d-flex align-items-center mb-2">
                                <div class="avatar-sm me-3">
                                    <div class="avatar-title bg-success rounded-circle">
                                        <i class="fas fa-user-plus text-white"></i>
                                    </div>
                                </div>
                                <div>
                                    <h6 class="mb-0">Người tạo</h6>
                                    <small class="text-muted">{{ $userInfo['creator']['created_at'] }}</small>
                                </div>
                            </div>
                            <div class="ps-4">
                                <div class="fw-bold">{{ $userInfo['creator']['name'] }}</div>
                                @if($userInfo['creator']['email'])
                                    <div class="text-muted small">{{ $userInfo['creator']['email'] }}</div>
                                @endif
                                @if($asset->creator && $asset->creator->department)
                                    <div class="text-muted small">
                                        <i class="fas fa-building me-1"></i>{{ $asset->creator->department }}
                                    </div>
                                @endif
                            </div>
                        </div>

                        @if($userInfo['updater']['name'] !== $userInfo['creator']['name'] || $userInfo['updater']['updated_at'] !== $userInfo['creator']['created_at'])
                            <div class="mb-0">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="avatar-sm me-3">
                                        <div class="avatar-title bg-warning rounded-circle">
                                            <i class="fas fa-user-edit text-white"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">Cập nhật cuối</h6>
                                        <small class="text-muted">{{ $userInfo['updater']['updated_at'] }}</small>
                                    </div>
                                </div>
                                <div class="ps-4">
                                    <div class="fw-bold">{{ $userInfo['updater']['name'] }}</div>
                                    @if($userInfo['updater']['email'])
                                        <div class="text-muted small">{{ $userInfo['updater']['email'] }}</div>
                                    @endif
                                    @if($asset->updater && $asset->updater->department)
                                        <div class="text-muted small">
                                            <i class="fas fa-building me-1"></i>{{ $asset->updater->department }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <!-- Detail Sections -->
                @foreach($detailSections as $sectionKey => $section)
                    <div class="card mb-3">
                        <div class="card-header">
                            <div class="d-flex align-items-center">
                                @if($sectionKey === 'certificate')
                                    <i class="fas fa-certificate text-primary me-2"></i>
                                @elseif($sectionKey === 'land_plot')
                                    <i class="fas fa-map text-success me-2"></i>
                                @elseif($sectionKey === 'house')
                                    <i class="fas fa-home text-info me-2"></i>
                                @elseif($sectionKey === 'apartment')
                                    <i class="fas fa-building text-warning me-2"></i>
                                @elseif($sectionKey === 'vehicle')
                                    <i class="fas fa-car text-secondary me-2"></i>
                                @endif
                                <h5 class="mb-0">{{ $section['title'] }}</h5>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @foreach($section['data'] as $label => $value)
                                    @if($value)
                                        <div class="col-md-6 mb-3">
                                            <div class="border-start border-3 border-primary ps-3">
                                                <strong class="text-muted d-block small">{{ $label }}</strong>
                                                <div class="fw-semibold">{{ $value }}</div>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach

                @if(empty($detailSections))
                    <div class="card">
                        <div class="card-body text-center py-5">
                            <i class="bi bi-info-circle display-1 text-muted"></i>
                            <h5 class="mt-3">Không có thông tin chi tiết</h5>
                            <p class="text-muted">Loại tài sản này chưa có thông tin chi tiết được cấu hình.</p>
                            @if($canEdit)
                                <a href="{{ route('properties.edit', $asset) }}" class="btn btn-primary">
                                    <i class="bi bi-pencil me-2"></i>Thêm thông tin chi tiết
                                </a>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Action History -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-history me-2"></i>Lịch sử thay đổi
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="timeline">
                            <div class="timeline-item">
                                <div class="timeline-marker bg-primary">
                                    <i class="fas fa-plus text-white small"></i>
                                </div>
                                <div class="timeline-content">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="mb-1">Tài sản được tạo</h6>
                                            <p class="mb-1">
                                                <strong>{{ $userInfo['creator']['name'] }}</strong> đã tạo tài sản này
                                            </p>
                                            <small class="text-muted">
                                                <i class="fas fa-clock me-1"></i>{{ $userInfo['creator']['created_at'] }}
                                            </small>
                                        </div>
                                        <span class="badge bg-primary">Tạo mới</span>
                                    </div>
                                </div>
                            </div>

                            @if($userInfo['updater']['updated_at'] !== $userInfo['creator']['created_at'])
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-warning">
                                        <i class="fas fa-edit text-white small"></i>
                                    </div>
                                    <div class="timeline-content">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <h6 class="mb-1">Cập nhật thông tin</h6>
                                                <p class="mb-1">
                                                    <strong>{{ $userInfo['updater']['name'] }}</strong> đã cập nhật thông tin tài sản
                                                </p>
                                                <small class="text-muted">
                                                    <i class="fas fa-clock me-1"></i>{{ $userInfo['updater']['updated_at'] }}
                                                </small>
                                            </div>
                                            <span class="badge bg-warning">Cập nhật</span>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-bolt me-2"></i>Thao tác nhanh
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @if($canEdit)
                                <div class="col-md-3">
                                    <a href="{{ route('properties.edit', $asset) }}" class="btn btn-outline-warning w-100 mb-2">
                                        <i class="fas fa-edit me-2"></i>
                                        <div>
                                            <div class="fw-bold">Chỉnh sửa</div>
                                            <small>Cập nhật thông tin</small>
                                        </div>
                                    </a>
                                </div>
                            @endif

                            <div class="col-md-3">
                                <a href="{{ route('properties.clone', $asset) }}" class="btn btn-outline-info w-100 mb-2">
                                    <i class="fas fa-copy me-2"></i>
                                    <div>
                                        <div class="fw-bold">Sao chép</div>
                                        <small>Tạo bản sao</small>
                                    </div>
                                </a>
                            </div>

                            <div class="col-md-3">
                                <button class="btn btn-outline-success w-100 mb-2" onclick="exportPDF()">
                                    <i class="fas fa-file-pdf me-2"></i>
                                    <div>
                                        <div class="fw-bold">Xuất PDF</div>
                                        <small>Tải về file PDF</small>
                                    </div>
                                </button>
                            </div>

                            <div class="col-md-3">
                                <button class="btn btn-outline-secondary w-100 mb-2" onclick="shareAsset()">
                                    <i class="fas fa-share-alt me-2"></i>
                                    <div>
                                        <div class="fw-bold">Chia sẻ</div>
                                        <small>Chia sẻ liên kết</small>
                                    </div>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        @if($canDelete)
            <div class="modal fade" id="deleteModal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Xác nhận xóa tài sản</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="alert alert-warning">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                <strong>Cảnh báo!</strong> Hành động này sẽ xóa vĩnh viễn tài sản và tất cả dữ liệu liên quan.
                            </div>
                            <p>Bạn có chắc chắn muốn xóa tài sản <strong>{{ $displayName }}</strong>?</p>
                            <p class="text-muted mb-0">Điều này bao gồm:</p>
                            <ul class="text-muted">
                                <li>Thông tin tài sản chính</li>
                                @if($asset->certificates->count() > 0)
                                    <li>{{ $asset->certificates->count() }} giấy chứng nhận</li>
                                @endif
                                @if($asset->landPlots->count() > 0)
                                    <li>{{ $asset->landPlots->count() }} thửa đất</li>
                                @endif
                                @if($asset->house)
                                    <li>Thông tin nhà ở</li>
                                @endif
                                @if($asset->apartment)
                                    <li>Thông tin căn hộ</li>
                                @endif
                                @if($asset->vehicle)
                                    <li>Thông tin phương tiện</li>
                                @endif
                            </ul>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                            <form method="POST" action="{{ route('properties.destroy', $asset) }}" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">
                                    <i class="bi bi-trash me-2"></i>Xóa vĩnh viễn
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <style>
        .timeline {
            position: relative;
            padding-left: 30px;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: 15px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #dee2e6;
        }

        .timeline-item {
            position: relative;
            padding-bottom: 30px;
        }

        .timeline-item:last-child {
            padding-bottom: 0;
        }

        .timeline-marker {
            position: absolute;
            left: -15px;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            border: 3px solid #fff;
            box-shadow: 0 0 0 2px #dee2e6;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .timeline-content {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            border-left: 4px solid #007bff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .avatar-sm {
            width: 2.5rem;
            height: 2.5rem;
        }

        .avatar-title {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.875rem;
        }

        .btn-outline-warning:hover,
        .btn-outline-info:hover,
        .btn-outline-success:hover,
        .btn-outline-secondary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
            transition: all 0.3s ease;
        }
    </style>

    <script>
        function exportPDF() {
            // Implement PDF export functionality
            alert('Chức năng xuất PDF sẽ được triển khai sau');
        }

        function shareAsset() {
            // Copy current URL to clipboard
            navigator.clipboard.writeText(window.location.href).then(() => {
                // Show success message
                const alert = document.createElement('div');
                alert.className = 'alert alert-success position-fixed';
                alert.style.top = '20px';
                alert.style.right = '20px';
                alert.style.zIndex = '9999';
                alert.innerHTML = '<i class="fas fa-check-circle me-2"></i>Đã sao chép liên kết!';
                document.body.appendChild(alert);
                setTimeout(() => alert.remove(), 3000);
            });
        }
    </script>
@endsection
