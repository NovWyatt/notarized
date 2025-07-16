<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\SessionService;
use Illuminate\Support\Facades\Auth;

class SessionMiddleware
{
    protected $sessionService;

    public function __construct(SessionService $sessionService)
    {
        $this->sessionService = $sessionService;
    }

    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $sessionId = session()->getId();

            // Kiểm tra xem session có bị đăng xuất từ thiết bị khác không
            if (!$this->sessionService->checkSessionStatus($sessionId)) {
                // Nếu session không còn active, đăng xuất user
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')->with('warning', 'Tài khoản của bạn đã đăng nhập từ thiết bị khác.');
            }

            // Cập nhật last activity
            $this->sessionService->updateActivity($sessionId);
        }

        return $next($request);
    }
}
