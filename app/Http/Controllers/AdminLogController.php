<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\UserSession;
use App\Models\User;
use Illuminate\Http\Request;

class AdminLogController extends Controller
{
    public function index(Request $request)
    {
        $query = UserSession::with('user')
            ->orderBy('login_at', 'desc');

        // Filter theo user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter theo IP
        if ($request->filled('ip_address')) {
            $query->where('ip_address', 'like', '%' . $request->ip_address . '%');
        }

        // Filter theo status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        // Filter theo date range
        if ($request->filled('date_from')) {
            $query->whereDate('login_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('login_at', '<=', $request->date_to);
        }

        $sessions = $query->paginate(20);
        $users = User::select('id', 'name', 'email')->get();

        return view('logs.index', compact('sessions', 'users'));
    }

    public function show($id)
    {
        $session = UserSession::with('user')->findOrFail($id);
        return view('logs.show', compact('session'));
    }

    public function forceLogout($id)
    {
        $session = UserSession::findOrFail($id);

        if ($session->is_active) {
            $session->update([
                'logout_at' => now(),
                'is_active' => false
            ]);

            return back()->with('success', 'Đã buộc đăng xuất thành công!');
        }

        return back()->with('error', 'Session đã không còn hoạt động!');
    }

    public function analytics()
    {
        $data = [
            'total_sessions' => UserSession::count(),
            'active_sessions' => UserSession::where('is_active', true)->count(),
            'unique_users' => UserSession::distinct('user_id')->count(),
            'top_browsers' => UserSession::select('browser')
                ->selectRaw('COUNT(*) as count')
                ->groupBy('browser')
                ->orderBy('count', 'desc')
                ->limit(10)
                ->get(),
            'top_devices' => UserSession::select('device')
                ->selectRaw('COUNT(*) as count')
                ->groupBy('device')
                ->orderBy('count', 'desc')
                ->limit(10)
                ->get(),
            'recent_logins' => UserSession::with('user')
                ->orderBy('login_at', 'desc')
                ->limit(10)
                ->get()
        ];

        return view('logs.analytics', $data);
    }
}
