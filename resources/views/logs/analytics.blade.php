<!-- resources/views/admin/logs/analytics.blade.php -->
@extends('layouts.app')
<style>
    p {
        color: #2b2626;
    }

    i {
        color: #2b2626;
    }
</style>
@section('content')
    <div class="container-fluid p-3">
        <!-- Summary Cards -->
        <div class="row">
            <div class="col-lg-3 col-6 p-2">
                <div class="small-box bg-info p-2">
                    <div class="inner">
                        <h3>{{ $total_sessions }}</h3>
                        <p>Total Sessions</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-6 p-2">
                <div class="small-box bg-success p-2">
                    <div class="inner">
                        <h3>{{ $active_sessions }}</h3>
                        <p>Active Sessions</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-user-check"></i>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-6 p-2">
                <div class="small-box bg-warning p-2">
                    <div class="inner">
                        <h3>{{ $unique_users }}</h3>
                        <p>Unique Users</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-user-friends"></i>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-6 p-2">
                <div class="small-box bg-danger p-2">
                    <div class="inner">
                        <h3>{{ $total_sessions - $active_sessions }}</h3>
                        <p>Inactive Sessions</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-user-times"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts -->
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Top Browsers</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="browserChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Top Devices</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="deviceChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Logins -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Recent Logins</h3>
                    </div>
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover text-nowrap">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>IP Address</th>
                                    <th>Device</th>
                                    <th>Login Time</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($recent_logins as $session)
                                    <tr>
                                        <td>{{ $session->user->name }}</td>
                                        <td>{{ $session->ip_address }}</td>
                                        <td>{{ $session->device }} - {{ $session->browser }}</td>
                                        <td>{{ $session->login_at->diffForHumans() }}</td>
                                        <td>
                                            @if ($session->is_active)
                                                <span class=" badge-success">Active</span>
                                            @else
                                                <span class=" badge-danger">Inactive</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Browser Chart
        const browserData = {
            labels: {!! json_encode($top_browsers->pluck('browser')) !!},
            datasets: [{
                data: {!! json_encode($top_browsers->pluck('count')) !!},
                backgroundColor: [
                    '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF',
                    '#FF9F40', '#FF6384', '#C9CBCF', '#4BC0C0', '#FF6384'
                ]
            }]
        };

        new Chart(document.getElementById('browserChart'), {
            type: 'doughnut',
            data: browserData,
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                    }
                }
            }
        });

        // Device Chart
        const deviceData = {
            labels: {!! json_encode($top_devices->pluck('device')) !!},
            datasets: [{
                data: {!! json_encode($top_devices->pluck('count')) !!},
                backgroundColor: [
                    '#36A2EB', '#FF6384', '#FFCE56', '#4BC0C0', '#9966FF',
                    '#FF9F40', '#FF6384', '#C9CBCF', '#4BC0C0', '#FF6384'
                ]
            }]
        };

        new Chart(document.getElementById('deviceChart'), {
            type: 'pie',
            data: deviceData,
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                    }
                }
            }
        });
    </script>
@endsection
