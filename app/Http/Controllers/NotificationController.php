<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\SessionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    protected $sessionService;

    public function __construct(SessionService $sessionService)
    {
        $this->sessionService = $sessionService;
    }

    public function checkLogoutNotification(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['status' => 'not_authenticated']);
        }

        $sessionId = session()->getId();
        $notification = $this->sessionService->getLogoutNotification($sessionId);

        if ($notification) {
            return response()->json([
                'status' => 'logout_required',
                'notification' => $notification
            ]);
        }

        // Kiểm tra xem session có còn active không
        $isActive = $this->sessionService->checkSessionStatus($sessionId);

        if (!$isActive) {
            return response()->json(['status' => 'session_expired']);
        }

        return response()->json(['status' => 'active']);
    }

    public function forceLogout(Request $request)
    {
        $sessionId = session()->getId();
        $this->sessionService->logout($sessionId);

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['status' => 'logged_out']);
    }
}
