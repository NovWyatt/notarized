<?php

namespace App\Services;

use App\Models\UserSession;
use App\Services\LocationService;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Cache;
use Jenssegers\Agent\Agent;

class SessionService
{
    protected $locationService;

    public function __construct(LocationService $locationService)
    {
        $this->locationService = $locationService;
    }

    public function createSession($user)
    {
        $agent = new Agent();
        $sessionId = Session::getId();
        $ip = Request::ip();

        // Lấy location với service mới
        $location = $this->locationService->getLocationFromIP($ip);

        // Lấy thông tin thiết bị hiện tại
        $currentDevice = $agent->device() ?: ($agent->isDesktop() ? 'Desktop' : 'Mobile');

        // Đăng xuất tất cả session khác của user và gửi thông báo
        $this->logoutOtherSessionsWithNotification($user->id, $sessionId, $currentDevice, $location);

        // Tạo session mới
        $session = UserSession::create([
            'user_id' => $user->id,
            'session_id' => $sessionId,
            'ip_address' => $ip,
            'user_agent' => Request::userAgent(),
            'browser' => $agent->browser() . ' ' . $agent->version($agent->browser()),
            'device' => $currentDevice,
            'platform' => $agent->platform() . ' ' . $agent->version($agent->platform()),
            'location' => $location,
            'login_at' => now(),
            'last_activity' => now(),
            'is_active' => true
        ]);

        return $session;
    }

    public function logoutOtherSessionsWithNotification($userId, $currentSessionId, $newDevice, $newLocation)
    {
        // Lấy các session đang hoạt động
        $activeSessions = UserSession::where('user_id', $userId)
            ->where('session_id', '!=', $currentSessionId)
            ->where('is_active', true)
            ->get();

        foreach ($activeSessions as $session) {
            // Tạo thông báo cho session sắp bị đăng xuất
            $this->createLogoutNotification($session->session_id, $newDevice, $newLocation);

            // Đánh dấu session là inactive
            $session->update([
                'logout_at' => now(),
                'is_active' => false
            ]);
        }
    }

    public function createLogoutNotification($sessionId, $newDevice, $newLocation)
    {
        $notification = [
            'type' => 'force_logout',
            'message' => "Tài khoản của bạn đã đăng nhập từ {$newDevice} tại {$newLocation}. Bạn sẽ được đăng xuất sau 5 giây.",
            'new_device' => $newDevice,
            'new_location' => $newLocation,
            'countdown' => 5,
            'created_at' => now()->timestamp
        ];

        // Lưu thông báo vào cache với key là session_id
        Cache::put("logout_notification_{$sessionId}", $notification, 300); // 5 phút
    }

    public function getLogoutNotification($sessionId)
    {
        return Cache::get("logout_notification_{$sessionId}");
    }

    public function clearLogoutNotification($sessionId)
    {
        Cache::forget("logout_notification_{$sessionId}");
    }

    public function updateActivity($sessionId)
    {
        UserSession::where('session_id', $sessionId)
            ->where('is_active', true)
            ->update(['last_activity' => now()]);
    }

    public function logout($sessionId)
    {
        UserSession::where('session_id', $sessionId)
            ->update([
                'logout_at' => now(),
                'is_active' => false
            ]);

        $this->clearLogoutNotification($sessionId);
    }

    public function checkSessionStatus($sessionId)
    {
        $session = UserSession::where('session_id', $sessionId)->first();
        return $session ? $session->is_active : false;
    }
}
