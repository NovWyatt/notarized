<?php

// app/Enums/AssetTypeEnum.php (Compatible with PHP 8.0+)
namespace App\Enums;

class AssetTypeEnum
{
    const REAL_ESTATE_HOUSE = 'real_estate_house';
    const REAL_ESTATE_APARTMENT = 'real_estate_apartment';
    const REAL_ESTATE_LAND_ONLY = 'real_estate_land_only';
    const MOVABLE_PROPERTY_CAR = 'movable_property_car';
    const MOVABLE_PROPERTY_MOTORCYCLE = 'movable_property_motorcycle';

    private static array $labels = [
        self::REAL_ESTATE_HOUSE => 'Bất động sản / Đất có tài sản gắn liền / Nhà',
        self::REAL_ESTATE_APARTMENT => 'Bất động sản / Đất có tài sản gắn liền / Căn hộ',
        self::REAL_ESTATE_LAND_ONLY => 'Bất động sản / Đất không có tài sản gắn liền trên đất',
        self::MOVABLE_PROPERTY_CAR => 'Động sản / Phương tiện GT đường bộ / Ô tô',
        self::MOVABLE_PROPERTY_MOTORCYCLE => 'Động sản / Phương tiện GT đường bộ / Mô tô - xe máy',
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
            self::REAL_ESTATE_HOUSE,
            self::REAL_ESTATE_APARTMENT,
            self::REAL_ESTATE_LAND_ONLY,
            self::MOVABLE_PROPERTY_CAR,
            self::MOVABLE_PROPERTY_MOTORCYCLE,
        ];
    }

    public static function isRealEstate(string $type): bool
    {
        return in_array($type, [
            self::REAL_ESTATE_HOUSE,
            self::REAL_ESTATE_APARTMENT,
            self::REAL_ESTATE_LAND_ONLY
        ]);
    }

    public static function isMovableProperty(string $type): bool
    {
        return in_array($type, [
            self::MOVABLE_PROPERTY_CAR,
            self::MOVABLE_PROPERTY_MOTORCYCLE
        ]);
    }

    public static function hasHouseInfo(string $type): bool
    {
        return $type === self::REAL_ESTATE_HOUSE;
    }

    public static function hasApartmentInfo(string $type): bool
    {
        return $type === self::REAL_ESTATE_APARTMENT;
    }

    public static function hasVehicleInfo(string $type): bool
    {
        return in_array($type, [
            self::MOVABLE_PROPERTY_CAR,
            self::MOVABLE_PROPERTY_MOTORCYCLE
        ]);
    }
}
