<!-- resources/views/admin/logs/show.blade.php -->
@extends('layouts.app2')

@section('content')
    <div class="container-fluid p-3">
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Session Information</h3>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <tr>
                                <th>User</th>
                                <td>{{ $session->user->name }} ({{ $session->user->email }})</td>
                            </tr>
                            <tr>
                                <th>Session ID</th>
                                <td><code>{{ $session->session_id }}</code></td>
                            </tr>
                            <tr>
                                <th>IP Address</th>
                                <td>{{ $session->ip_address }}</td>
                            </tr>
                            <tr>
                                <th>Location</th>
                                <td>{{ $session->location }}</td>
                            </tr>
                            <tr>
                                <th>Login Time</th>
                                <td>{{ $session->login_at->format('d/m/Y H:i:s') }}</td>
                            </tr>
                            <tr>
                                <th>Last Activity</th>
                                <td>
                                    @if ($session->last_activity)
                                        {{ $session->last_activity->format('d/m/Y H:i:s') }}
                                        ({{ $session->last_activity->diffForHumans() }})
                                    @else
                                        <span class="text-muted">Unknown</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Logout Time</th>
                                <td>
                                    @if ($session->logout_at)
                                        {{ $session->logout_at->format('d/m/Y H:i:s') }}
                                    @else
                                        <span class="text-muted">Still active</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Status</th>
                                <td>
                                    @if ($session->is_active)
                                        <span class=" badge-success">Active</span>
                                    @else
                                        <span class=" badge-danger">Inactive</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Device Information</h3>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <tr>
                                <th>Browser</th>
                                <td>{{ $session->browser }}</td>
                            </tr>
                            <tr>
                                <th>Device</th>
                                <td>{{ $session->device }}</td>
                            </tr>
                            <tr>
                                <th>Platform</th>
                                <td>{{ $session->platform }}</td>
                            </tr>
                            <tr>
                                <th>User Agent</th>
                                <td><small>{{ $session->user_agent }}</small></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Actions</h3>
                    </div>
                    <div class="card-body">
                        <a href="{{ route('admin.logs.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>

                        @if ($session->is_active)
                            <form method="POST" action="{{ route('admin.logs.force-logout', $session->id) }}"
                                style="display: inline-block;">
                                @csrf
                                <button type="submit" class="btn btn-danger"
                                    onclick="return confirm('Are you sure you want to force logout this session?')">
                                    <i class="fas fa-sign-out-alt"></i> Force Logout
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
