@extends('layouts.app2')
@section('content')
    <div class="container-fluid p-3">
        <!-- Header Section -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0">Quản lý đương sự</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="#" class="text-decoration-none">Trang chủ</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Đương sự</li>
                    </ol>
                </nav>
            </div>
            <div>
                <a href="{{ route('litigants.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Tạo đương sự
                </a>
            </div>
        </div>

        <!-- Alerts Section -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Filter and Search Section -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('litigants.index') }}">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="search" class="form-label">Tìm</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                <input type="text" class="form-control" id="search" name="search"
                                    placeholder="Search by name..." value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label for="type" class="form-label">Loại</label>
                            <select class="form-select" id="type" name="type">
                                <option value="">Tất cả</option>
                                <option value="individual" {{ request('type') == 'individual' ? 'selected' : '' }}>
                                    Cá nhân</option>
                                <option value="organization" {{ request('type') == 'organization' ? 'selected' : '' }}>
                                    Tổ chức</option>
                                <option value="credit_institution"
                                    {{ request('type') == 'credit_institution' ? 'selected' : '' }}>Tổ chức tín dụng
                                </option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="department" class="form-label">Phòng ban</label>
                            <select class="form-select" id="department" name="department">
                                <option value="">Tất cả</option>
                                @foreach ($departments as $dept)
                                    <option value="{{ $dept }}"
                                        {{ request('department') == $dept ? 'selected' : '' }}>
                                        {{ $dept }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i>
                                </button>
                                <a href="{{ route('litigants.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="card-title mb-0">Tổng số đương sự</h5>
                                <h3 class="mb-0">{{ $litigants->total() }}</h3>
                            </div>
                            <div class="fs-1 opacity-75">
                                <i class="fas fa-users"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="card-title mb-0">Cá nhân</h5>
                                <h3 class="mb-0">{{ $stats['individual'] ?? 0 }}</h3>
                            </div>
                            <div class="fs-1 opacity-75">
                                <i class="fas fa-user"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="card-title mb-0">Tổ chức</h5>
                                <h3 class="mb-0">{{ $stats['organization'] ?? 0 }}</h3>
                            </div>
                            <div class="fs-1 opacity-75">
                                <i class="fas fa-building"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="card-title mb-0">Tổ chức tín dụng</h5>
                                <h3 class="mb-0">{{ $stats['credit_institution'] ?? 0 }}</h3>
                            </div>
                            <div class="fs-1 opacity-75">
                                <i class="fas fa-university"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Table -->
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Danh Sách Đương Sự</h5>
                    <small class="text-muted">
                        Showing {{ $litigants->firstItem() }} to {{ $litigants->lastItem() }} of
                        {{ $litigants->total() }} results
                    </small>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th scope="col" class="px-4 py-3">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-hashtag me-2 text-muted"></i>
                                        ID
                                    </div>
                                </th>
                                <th scope="col" class="px-4 py-3">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-user me-2 text-muted"></i>
                                        Tên đương sự
                                    </div>
                                </th>
                                <th scope="col" class="px-4 py-3">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-tag me-2 text-muted"></i>
                                        Loại
                                    </div>
                                </th>
                                <th scope="col" class="px-4 py-3">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-user-tie me-2 text-muted"></i>
                                        Tạo bởi
                                    </div>
                                </th>
                                <th scope="col" class="px-4 py-3">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-building me-2 text-muted"></i>
                                        Phòng ban
                                    </div>
                                </th>
                                <th scope="col" class="px-4 py-3">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-clock me-2 text-muted"></i>
                                        Cập nhật lần cuối
                                    </div>
                                </th>
                                <th scope="col" class="px-4 py-3 text-center">
                                    <i class="fas fa-cog text-muted"></i>
                                    Thao tác
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($litigants as $litigant)
                                <tr>
                                    <td class="px-4 py-3">
                                        <span class="badge bg-light text-dark">{{ $litigant->id }}</span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm me-3">
                                                @if ($litigant->type == 'individual')
                                                    <div class="avatar-title bg-primary rounded-circle">
                                                        <i class="fas fa-user text-white"></i>
                                                    </div>
                                                @elseif($litigant->type == 'organization')
                                                    <div class="avatar-title bg-info rounded-circle">
                                                        <i class="fas fa-building text-white"></i>
                                                    </div>
                                                @else
                                                    <div class="avatar-title bg-warning rounded-circle">
                                                        <i class="fas fa-university text-white"></i>
                                                    </div>
                                                @endif
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $litigant->full_name }}</h6>
                                                @if ($litigant->notes)
                                                    <small
                                                        class="text-muted">{{ Str::limit($litigant->notes, 50) }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        @if ($litigant->type == 'individual')
                                            <span class="badge bg-primary">Cá nhân</span>
                                        @elseif($litigant->type == 'organization')
                                            <span class="badge bg-info">Tổ chức</span>
                                        @else
                                            <span class="badge bg-warning">Tổ chức tín dụng</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-xs me-2">
                                                <div class="avatar-title bg-soft-primary rounded-circle">
                                                    {{ substr($litigant->user->name, 0, 1) }}
                                                </div>
                                            </div>
                                            <div>
                                                <h6 class="mb-0 text-dark">{{ $litigant->user->name }}</h6>
                                                <small class="text-muted">{{ $litigant->user->email }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="badge bg-light text-dark">
                                            {{ $litigant->user->department ?? 'Not Assigned' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div>
                                            <small class="text-muted">
                                                {{ $litigant->updated_at->format(' d/m/Y') }}
                                            </small>
                                            <br>
                                            <small class="text-muted">
                                                {{ $litigant->updated_at->format('h:i A') }}
                                            </small>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('litigants.show', $litigant) }}"
                                                class="btn btn-sm btn-outline-primary" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('litigants.edit', $litigant) }}"
                                                class="btn btn-sm btn-outline-secondary" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-danger" title="Delete"
                                                onclick="confirmDelete({{ $litigant->id }})">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="fas fa-users fa-3x mb-3"></i>
                                            <h5>Không tìm thấy đương sự</h5>
                                            <p>Không có đương sự nào phù hợp với tiêu chí của bạn</p>
                                            <a href="{{ route('litigants.create') }}" class="btn btn-primary">
                                                <i class="fas fa-plus me-2"></i>Tạo đương sự
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if ($litigants->hasPages())
                <div class="card-footer">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted">
                                Showing {{ $litigants->firstItem() }} to {{ $litigants->lastItem() }} of
                                {{ $litigants->total() }} results
                            </small>
                        </div>
                        <div>
                            {{ $litigants->links() }}
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Xác nhận xóa đương sự</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center">
                        <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                        <h5>Bạn chắc chứ?</h5>
                        <p>Hành động này không thể hoàn tác. Bạn có chắc chắn muốn xóa người đương sự này không??</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <form id="deleteForm" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Xóa</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="{{ asset('js/litigant-common.js') }}"></script>
    <script>
        function confirmDelete(id) {
            const form = document.getElementById('deleteForm');
            form.action = `/litigants/${id}`;

            const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
            modal.show();
        }
    </script>
@endsection
