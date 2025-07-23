<!-- resources/views/admin/logs/index.blade.php -->
@extends('layouts.app')

@section('content')
    <div class="container-fluid p-3">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">User Session Logs</h3>
                        <div class="card-tools">
                            <a href="{{ route('admin.logs.analytics') }}" class="btn btn-info btn-sm">
                                <i class="fas fa-chart-bar"></i> Analytics
                            </a>
                        </div>
                    </div>

                    <!-- Filter Form -->
                    <div class="card-body">
                        <form method="GET" class="row mb-3">
                            <div class="col-md-2">
                                <select name="user_id" class="form-control">
                                    <option value="">All Users</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}"
                                            {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <input type="text" name="ip_address" class="form-control" placeholder="IP Address"
                                    value="{{ request('ip_address') }}">
                            </div>
                            <div class="col-md-2">
                                <select name="status" class="form-control">
                                    <option value="">All Status</option>
                                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active
                                    </option>
                                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>
                                        Inactive</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <input type="date" name="date_from" class="form-control"
                                    value="{{ request('date_from') }}">
                            </div>
                            <div class="col-md-2">
                                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary">Filter</button>
                                <a href="{{ route('admin.logs.index') }}" class="btn btn-secondary">Reset</a>
                            </div>
                        </form>
                    </div>

                    <!-- Sessions Table -->
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover text-nowrap">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>IP Address</th>
                                    <th>Device Info</th>
                                    <th>Location</th>
                                    <th>Login Time</th>
                                    <th>Last Activity</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($sessions as $session)
                                    <tr>
                                        <td>
                                            <div>
                                                <strong>{{ $session->user->name }}</strong><br>
                                                <small class="text-muted">{{ $session->user->email }}</small>
                                            </div>
                                        </td>
                                        <td>{{ $session->ip_address }}</td>
                                        <td>
                                            <div>
                                                <strong>{{ $session->browser }}</strong><br>
                                                <small class="text-muted">{{ $session->device }} -
                                                    {{ $session->platform }}</small>
                                            </div>
                                        </td>
                                        <td>{{ $session->location }}</td>
                                        <td>{{ $session->login_at->format('d/m/Y H:i:s') }}</td>
                                        <td>
                                            @if ($session->last_activity)
                                                {{ $session->last_activity->diffForHumans() }}
                                            @else
                                                <span class="text-muted">Unknown</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($session->is_active)
                                                <span class=" badge-success">Active</span>
                                            @else
                                                <span class=" badge-danger">Inactive</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.logs.show', $session->id) }}"
                                                class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if ($session->is_active)
                                                <form method="POST"
                                                    action="{{ route('admin.logs.force-logout', $session->id) }}"
                                                    style="display: inline-block;">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-danger"
                                                        onclick="return confirm('Are you sure?')">
                                                        <i class="fas fa-sign-out-alt"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">No sessions found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="card-footer clearfix">
                        {{ $sessions->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
