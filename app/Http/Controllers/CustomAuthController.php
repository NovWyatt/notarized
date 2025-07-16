<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\SessionService;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomAuthController extends Controller
{
    use AuthenticatesUsers;

    protected $sessionService;

    public function __construct(SessionService $sessionService)
    {
        $this->sessionService = $sessionService;
    }

    protected function authenticated(Request $request, $user)
    {
        // Tạo session log khi đăng nhập thành công
        $this->sessionService->createSession($user);

        return redirect()->intended($this->redirectPath());
    }

    public function logout(Request $request)
    {
        // Log logout
        $this->sessionService->logout(session()->getId());

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
