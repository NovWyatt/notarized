@extends('layouts.app')

@section('content')
    <div class="container-fluid p-3">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title">Danh sách User</h4>
                        <a href="{{ route('users.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Thêm User
                        </a>
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Tên</th>
                                        <th>Phòng ban</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Trạng thái</th>
                                        <th>Ngày tạo</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($users as $user)
                                        <tr>
                                            <td>{{ $loop->iteration + ($users->currentPage() - 1) * $users->perPage() }}</td>
                                            <td>
                                                <strong>{{ $user->name }}</strong>
                                                @if($user->is_admin)
                                                    <span class="badge bg-danger ms-1">Admin</span>
                                                @endif
                                            </td>
                                            <td>{{ $user->department ?? '-' }}</td>
                                            <td>{{ $user->email }}</td>
                                            <td>
                                                @foreach($user->roles as $role)
                                                    <span class="badge bg-{{ $role->name == 'admin' ? 'danger' : 'info' }}">
                                                        {{ ucfirst($role->name) }}
                                                    </span>
                                                @endforeach
                                            </td>
                                            <td>
                                                <span class="badge bg-success">Hoạt động</span>
                                            </td>
                                            <td>{{ $user->created_at->format('d/m/Y H:i') }}</td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('users.show', $user) }}" class="btn btn-info btn-sm"
                                                        title="Xem chi tiết">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('users.edit', $user) }}" class="btn btn-warning btn-sm"
                                                        title="Chỉnh sửa">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    @if(auth()->id() !== $user->id)
                                                        <form action="{{ route('users.destroy', $user) }}" method="POST"
                                                            class="d-inline"
                                                            onsubmit="return confirm('Bạn có chắc chắn muốn xóa user này?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger btn-sm" title="Xóa">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center">Không có dữ liệu</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center mt-3">
                            {{ $users->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
