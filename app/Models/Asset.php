<?php

// app/Models/Asset.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Asset extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_type',
        'asset_name',
        'estimated_value',
        'notes'
    ];

    protected $casts = [
        'estimated_value' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    protected $hidden = [];

    // Relationships
    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class);
    }

    public function landPlots(): HasMany
    {
        return $this->hasMany(LandPlot::class);
    }

    public function house(): HasOne
    {
        return $this->hasOne(House::class);
    }

    public function apartment(): HasOne
    {
        return $this->hasOne(Apartment::class);
    }

    public function vehicle(): HasOne
    {
        return $this->hasOne(Vehicle::class);
    }

    // Asset type constants
    const TYPE_REAL_ESTATE_HOUSE = 'real_estate_house';
    const TYPE_REAL_ESTATE_APARTMENT = 'real_estate_apartment';
    const TYPE_REAL_ESTATE_LAND_ONLY = 'real_estate_land_only';
    const TYPE_MOVABLE_PROPERTY_CAR = 'movable_property_car';
    const TYPE_MOVABLE_PROPERTY_MOTORCYCLE = 'movable_property_motorcycle';

    public static function getAssetTypes(): array
    {
        return [
            self::TYPE_REAL_ESTATE_HOUSE,
            self::TYPE_REAL_ESTATE_APARTMENT,
            self::TYPE_REAL_ESTATE_LAND_ONLY,
            self::TYPE_MOVABLE_PROPERTY_CAR,
            self::TYPE_MOVABLE_PROPERTY_MOTORCYCLE
        ];
    }
}
