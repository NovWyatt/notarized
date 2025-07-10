{{-- resources/views/assets/show.blade.php --}}
@extends('layouts.app2')
@section('content')
    <div class="container-fluid p-3">
        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-md-8">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('properties.index') }}">Tài sản</a></li>
                        <li class="breadcrumb-item active">{{ $asset->asset_name ?: 'Tài sản #' . $asset->id }}</li>
                    </ol>
                </nav>
                <h3 class="mb-0">{{ $asset->asset_name ?: 'Tài sản #' . $asset->id }}</h3>
                <div class="d-flex align-items-center mt-2">
                    <span class="badge bg-primary me-2">{{ $typeLabel }}</span>
                    {{-- <span class="text-muted">Được tạo {{ $asset->created_at->format('d/m/Y H:i') }}</span> --}}
                </div>
            </div>
            <div class="col-md-4 text-end">
                <div class="btn-group" role="group">
                    @if($canEdit)
                        <a href="{{ route('properties.edit', $asset) }}" class="btn btn-warning">
                            <i class="bi bi-pencil me-2"></i>Chỉnh sửa
                        </a>
                    @endif
                    {{-- <a href="{{ route('properties.clone', $asset) }}" class="btn btn-info">
                        <i class="bi bi-files me-2"></i>Sao chép
                    </a> --}}
                    @if($canDelete)
                        {{-- <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                            <i class="bi bi-trash me-2"></i>Xóa
                        </button> --}}
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
                                <td class="fw-bold">{{ $asset->asset_name ?: 'Chưa đặt tên' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Loại tài sản:</td>
                                <td><span class="badge bg-primary">{{ $typeLabel }}</span></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Giá trị ước tính:</td>
                                <td class="fw-bold text-success fs-5">{{ $formattedValue }}</td>
                            </tr>
                            @if($asset->notes)
                                <tr>
                                    <td class="text-muted">Ghi chú:</td>
                                    <td>{{ $asset->notes }}</td>
                                </tr>
                            @endif
                            <tr>
                                <td class="text-muted">Ngày tạo:</td>
                                <td>{{ $asset->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Cập nhật:</td>
                                <td>{{ $asset->updated_at->format('d/m/Y H:i') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <!-- Detail Sections -->
                @foreach($detailSections as $sectionKey => $section)
                    <div class="card mb-3">
                        <div class="card-header">
                            <h5 class="mb-0">{{ $section['title'] }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @foreach($section['data'] as $label => $value)
                                    @if($value)
                                        <div class="col-md-6 mb-2">
                                            <strong class="text-muted">{{ $label }}:</strong>
                                            <div>{{ $value }}</div>
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
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Action History (if you want to add this feature) -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Lịch sử thay đổi</h5>
                    </div>
                    <div class="card-body">
                        <div class="timeline">
                            <div class="timeline-item">
                                <div class="timeline-marker bg-primary"></div>
                                <div class="timeline-content">
                                    <h6 class="mb-1">Tài sản được tạo</h6>
                                    <p class="mb-0 text-muted">{{ $asset->created_at->format('d/m/Y H:i') }}</p>
                                </div>
                            </div>
                            @if($asset->updated_at != $asset->created_at)
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-warning"></div>
                                    <div class="timeline-content">
                                        <h6 class="mb-1">Cập nhật thông tin</h6>
                                        <p class="mb-0 text-muted">{{ $asset->updated_at->format('d/m/Y H:i') }}</p>
                                    </div>
                                </div>
                            @endif
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
                            <p>Bạn có chắc chắn muốn xóa tài sản <strong>{{ $asset->asset_name ?: 'Tài sản #' . $asset->id }}</strong>?</p>
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
            left: 10px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #dee2e6;
        }

        .timeline-item {
            position: relative;
            padding-bottom: 20px;
        }

        .timeline-marker {
            position: absolute;
            left: -25px;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            border: 3px solid #fff;
            box-shadow: 0 0 0 2px #dee2e6;
        }

        .timeline-content {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            border-left: 3px solid #007bff;
        }
    </style>
@endsection
