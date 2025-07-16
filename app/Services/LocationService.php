<?php

// app/Services/LocationService.php - Tạo service riêng cho location
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class LocationService
{
    /**
     * Lấy location từ IP với multiple APIs fallback
     */
    public function getLocationFromIP($ip)
    {
        // Bỏ qua IP localhost/private
        if ($this->isPrivateIP($ip)) {
            return 'Local Network';
        }

        // Check cache trước
        $cacheKey = "location_" . md5($ip);
        $cachedLocation = Cache::get($cacheKey);

        if ($cachedLocation) {
            return $cachedLocation;
        }

        // Thử các API khác nhau
        $location = $this->tryMultipleAPIs($ip);

        // Cache kết quả trong 24 giờ
        if ($location !== 'Unknown') {
            Cache::put($cacheKey, $location, 24 * 60 * 60);
        }

        return $location;
    }

    /**
     * Thử nhiều API khác nhau
     */
    private function tryMultipleAPIs($ip)
    {
        // API 1: ip-api.com (miễn phí, không cần key)
        $location = $this->getFromIpApi($ip);
        if ($location !== 'Unknown') {
            return $location;
        }

        // API 2: ipapi.co (miễn phí, 1000 requests/day)
        $location = $this->getFromIpApiCo($ip);
        if ($location !== 'Unknown') {
            return $location;
        }

        // API 3: ipinfo.io (miễn phí, 50k requests/month)
        $location = $this->getFromIpInfo($ip);
        if ($location !== 'Unknown') {
            return $location;
        }

        // API 4: freeipapi.com (miễn phí, unlimited)
        $location = $this->getFromFreeIpApi($ip);
        if ($location !== 'Unknown') {
            return $location;
        }

        return 'Unknown';
    }

    /**
     * API 1: ip-api.com
     */
    private function getFromIpApi($ip)
    {
        try {
            $response = Http::timeout(5)->get("http://ip-api.com/json/{$ip}");

            if ($response->successful()) {
                $data = $response->json();

                if ($data && $data['status'] === 'success') {
                    $location = [];

                    if (!empty($data['city'])) {
                        $location[] = $data['city'];
                    }
                    if (!empty($data['regionName']) && $data['regionName'] !== $data['city']) {
                        $location[] = $data['regionName'];
                    }
                    if (!empty($data['country'])) {
                        $location[] = $data['country'];
                    }

                    return implode(', ', $location);
                }
            }
        } catch (\Exception $e) {
            Log::warning("ip-api.com failed for IP {$ip}: " . $e->getMessage());
        }

        return 'Unknown';
    }

    /**
     * API 2: ipapi.co
     */
    private function getFromIpApiCo($ip)
    {
        try {
            $response = Http::timeout(5)->get("https://ipapi.co/{$ip}/json/");

            if ($response->successful()) {
                $data = $response->json();

                if ($data && !isset($data['error'])) {
                    $location = [];

                    if (!empty($data['city'])) {
                        $location[] = $data['city'];
                    }
                    if (!empty($data['region']) && $data['region'] !== $data['city']) {
                        $location[] = $data['region'];
                    }
                    if (!empty($data['country_name'])) {
                        $location[] = $data['country_name'];
                    }

                    return implode(', ', $location);
                }
            }
        } catch (\Exception $e) {
            Log::warning("ipapi.co failed for IP {$ip}: " . $e->getMessage());
        }

        return 'Unknown';
    }

    /**
     * API 3: ipinfo.io
     */
    private function getFromIpInfo($ip)
    {
        try {
            $response = Http::timeout(5)->get("https://ipinfo.io/{$ip}/json");

            if ($response->successful()) {
                $data = $response->json();

                if ($data && !isset($data['error'])) {
                    $location = [];

                    if (!empty($data['city'])) {
                        $location[] = $data['city'];
                    }
                    if (!empty($data['region']) && $data['region'] !== $data['city']) {
                        $location[] = $data['region'];
                    }
                    if (!empty($data['country'])) {
                        $location[] = $data['country'];
                    }

                    return implode(', ', $location);
                }
            }
        } catch (\Exception $e) {
            Log::warning("ipinfo.io failed for IP {$ip}: " . $e->getMessage());
        }

        return 'Unknown';
    }

    /**
     * API 4: freeipapi.com
     */
    private function getFromFreeIpApi($ip)
    {
        try {
            $response = Http::timeout(5)->get("https://freeipapi.com/api/json/{$ip}");

            if ($response->successful()) {
                $data = $response->json();

                if ($data && !isset($data['error'])) {
                    $location = [];

                    if (!empty($data['cityName'])) {
                        $location[] = $data['cityName'];
                    }
                    if (!empty($data['regionName']) && $data['regionName'] !== $data['cityName']) {
                        $location[] = $data['regionName'];
                    }
                    if (!empty($data['countryName'])) {
                        $location[] = $data['countryName'];
                    }

                    return implode(', ', $location);
                }
            }
        } catch (\Exception $e) {
            Log::warning("freeipapi.com failed for IP {$ip}: " . $e->getMessage());
        }

        return 'Unknown';
    }

    /**
     * Kiểm tra IP có phải private không
     */
    private function isPrivateIP($ip)
    {
        // Localhost
        if ($ip === '127.0.0.1' || $ip === '::1') {
            return true;
        }

        // Private IP ranges
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
            return true;
        }

        return false;
    }

    /**
     * Lấy location chi tiết với timezone
     */
    public function getDetailedLocation($ip)
    {
        if ($this->isPrivateIP($ip)) {
            return [
                'city' => 'Local',
                'region' => 'Network',
                'country' => 'Local',
                'timezone' => config('app.timezone'),
                'full' => 'Local Network'
            ];
        }

        $cacheKey = "detailed_location_" . md5($ip);
        $cachedLocation = Cache::get($cacheKey);

        if ($cachedLocation) {
            return $cachedLocation;
        }

        try {
            $response = Http::timeout(5)->get("http://ip-api.com/json/{$ip}?fields=status,message,country,countryCode,region,regionName,city,timezone,query");

            if ($response->successful()) {
                $data = $response->json();

                if ($data && $data['status'] === 'success') {
                    $location = [
                        'city' => $data['city'] ?? 'Unknown',
                        'region' => $data['regionName'] ?? 'Unknown',
                        'country' => $data['country'] ?? 'Unknown',
                        'timezone' => $data['timezone'] ?? 'UTC',
                        'full' => implode(', ', array_filter([
                            $data['city'] ?? null,
                            $data['regionName'] ?? null,
                            $data['country'] ?? null
                        ]))
                    ];

                    Cache::put($cacheKey, $location, 24 * 60 * 60);
                    return $location;
                }
            }
        } catch (\Exception $e) {
            Log::warning("Detailed location failed for IP {$ip}: " . $e->getMessage());
        }

        return [
            'city' => 'Unknown',
            'region' => 'Unknown',
            'country' => 'Unknown',
            'timezone' => 'UTC',
            'full' => 'Unknown'
        ];
    }
}
