<?php

// app/Enums/CertificateTypeEnum.php (Compatible with PHP 8.0+)
namespace App\Enums;

class CertificateTypeEnum
{
    const LAND_USE_CERTIFICATE = 'land_use_certificate';
    const APARTMENT_OWNERSHIP_CERTIFICATE = 'apartment_ownership_certificate';
    const LAND_HOUSE_OWNERSHIP_CERTIFICATE = 'land_house_ownership_certificate';
    const HOUSE_OWNERSHIP_CERTIFICATE = 'house_ownership_certificate';
    const LAND_USE_RIGHT_CERTIFICATE = 'land_use_right_certificate';
    const BL735265 = 'bl735265';

    private static array $labels = [
        self::LAND_USE_CERTIFICATE => 'Giấy chứng nhận quyền sử dụng đất',
        self::APARTMENT_OWNERSHIP_CERTIFICATE => 'Giấy chứng nhận quyền sử dụng căn hộ',
        self::LAND_HOUSE_OWNERSHIP_CERTIFICATE => 'Giấy chứng nhận quyền sử dụng đất quyền sở hữu nhà ở và tài sản gắn liền với đất',
        self::HOUSE_OWNERSHIP_CERTIFICATE => 'Giấy chứng nhận quyền sử dụng nhà ở',
        self::LAND_USE_RIGHT_CERTIFICATE => 'Giấy chứng nhận quyền sử dụng đất',
        self::BL735265 => 'BL735265',
    ];

    public static function label(string $type): string
    {
        return self::$labels[$type] ?? $type;
    }

    public static function options(): array
    {
        return self::$labels;
    }

    public static function all(): array
    {
        return [
            self::LAND_USE_CERTIFICATE,
            self::APARTMENT_OWNERSHIP_CERTIFICATE,
            self::LAND_HOUSE_OWNERSHIP_CERTIFICATE,
            self::HOUSE_OWNERSHIP_CERTIFICATE,
            self::LAND_USE_RIGHT_CERTIFICATE,
            self::BL735265,
        ];
    }
}
