<?php

// app/Models/Asset.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class Asset extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_type',
        'notes',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    protected $hidden = [];

    // Boot method để tự động gán user
    protected static function boot()
    {
        parent::boot();

        // Tự động gán created_by khi tạo mới
        static::creating(function ($model) {
            if (Auth::check() && !$model->created_by) {
                $model->created_by = Auth::id();
            }
            if (Auth::check() && !$model->updated_by) {
                $model->updated_by = Auth::id();
            }
        });

        // Tự động gán updated_by khi cập nhật
        static::updating(function ($model) {
            if (Auth::check()) {
                $model->updated_by = Auth::id();
            }
        });
    }

    // Relationships với các bảng con
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

    // Relationships với User
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
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

    // Scopes for filtering by user
    public function scopeCreatedBy($query, $userId)
    {
        return $query->where('created_by', $userId);
    }

    public function scopeUpdatedBy($query, $userId)
    {
        return $query->where('updated_by', $userId);
    }

    public function scopeOwnedBy($query, $userId)
    {
        return $query->where('created_by', $userId);
    }

    // Helper methods
    public function getCreatorNameAttribute(): string
    {
        return $this->creator ? $this->creator->name : 'Hệ thống';
    }

    public function getUpdaterNameAttribute(): string
    {
        return $this->updater ? $this->updater->name : 'Hệ thống';
    }

    public function isCreatedBy($userId): bool
    {
        return $this->created_by == $userId;
    }

    public function isUpdatedBy($userId): bool
    {
        return $this->updated_by == $userId;
    }
}
