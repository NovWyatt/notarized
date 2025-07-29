@extends('layouts.app')

@section('content')
<div class="container-fluid p-3">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Chỉnh sửa User: {{ $user->name }}</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('users.update', $user) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label for="name" class="form-label">Tên <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                    @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="department" class="form-label">Phòng ban <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('department') is-invalid @enderror" id="department" name="department" value="{{ old('department', $user->department) }}" required>
                                    @error('department')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="role_id" class="form-label">Role <span class="text-danger">*</span></label>
                                    <select class="form-select @error('role_id') is-invalid @enderror" id="role_id" name="role_id" required>
                                        <option value="">Chọn role</option>
                                        @foreach($roles as $role)
                                        <option value="{{ $role->id }}" {{ old('role_id', $user->roles->first()->id ?? '') == $role->id ? 'selected' : '' }}>
                                            {{ ucfirst($role->name) }}
                                            {{ $role->id == 1 ? ' (Admin)' : '' }}
                                            {{ $role->id == 2 ? ' (Chuyên Viên)' : '' }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('role_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="password" class="form-label">Mật khẩu mới</label>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password">
                                    @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Để trống nếu không muốn thay đổi mật khẩu. Tối thiểu 8 ký tự nếu nhập.</small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="password_confirmation" class="form-label">Xác nhận mật khẩu mới</label>
                                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            <strong>Thông tin hiện tại:</strong>
                            <ul class="mb-0 mt-2">
                                <li>Role hiện tại: <strong>{{ $user->roles->first()->name ?? 'Chưa có' }}</strong></li>
                                <li>Is Admin: <strong>{{ $user->is_admin ? 'Có' : 'Không' }}</strong></li>
                                <li>Ngày tạo: <strong>{{ $user->created_at->format('d/m/Y H:i') }}</strong></li>
                            </ul>
                        </div>

                        <div class="form-group d-flex justify-content-between">
                            <a href="{{ route('users.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Quay lại
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Cập nhật User
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
