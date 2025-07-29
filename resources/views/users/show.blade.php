@extends('layouts.app')

@section('content')
<div class="container-fluid p-3">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Chi tiết User: {{ $user->name }}</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td class="fw-bold">ID:</td>
                                    <td>{{ $user->id }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Tên:</td>
                                    <td>
                                        {{ $user->name }}
                                        @if($user->is_admin)
                                        <span class="badge bg-danger ms-1">Admin</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Phòng ban:</td>
                                    <td>{{ $user->department ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Role:</td>
                                    <td>
                                        @foreach($user->roles as $role)
                                        <span class="badge bg-{{ $role->name == 'admin' ? 'danger' : 'info' }}">
                                            {{ ucfirst($role->name) }}
                                        </span>
                                        @endforeach
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Is Admin:</td>
                                    <td>
                                        @if($user->is_admin)
                                        <span class="badge bg-success">Có</span>
                                        @else
                                        <span class="badge bg-secondary">Không</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td class="fw-bold">Ngày tạo:</td>
                                    <td>{{ $user->created_at->format('d/m/Y H:i:s') }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Cập nhật lần cuối:</td>
                                    <td>{{ $user->updated_at->format('d/m/Y H:i:s') }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Trạng thái:</td>
                                    <td>
                                        <span class="badge bg-success">Hoạt động</span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Thống kê nhanh (nếu có data liên quan) -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <h5>Thống kê</h5>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="card bg-light">
                                        <div class="card-body text-center">
                                            <h6 class="card-title">Tài sản tạo</h6>
                                            <h4 class="text-primary">{{ $user->createdAssets()->count() }}</h4>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card bg-light">
                                        <div class="card-body text-center">
                                            <h6 class="card-title">Litigants tạo</h6>
                                            <h4 class="text-info">{{ $user->litigants()->count() }}</h4>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card bg-light">
                                        <div class="card-body text-center">
                                            <h6 class="card-title">Lần đăng nhập cuối</h6>
                                            <h6 class="text-muted">{{ $user->updated_at->diffForHumans() }}</h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group d-flex justify-content-between mt-4">
                        <a href="{{ route('users.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Quay lại
                        </a>
                        <div>
                            <a href="{{ route('users.edit', $user) }}" class="btn btn-warning">
                                <i class="fas fa-edit"></i> Chỉnh sửa
                            </a>
                            @if(auth()->id() !== $user->id)
                            <form action="{{ route('users.destroy', $user) }}" method="POST" class="d-inline ms-2" onsubmit="return confirm('Bạn có chắc chắn muốn xóa user này?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-trash"></i> Xóa
                                </button>
                            </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
