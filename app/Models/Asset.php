<?php

// app/Models/Asset.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Auth;

class Asset extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_type',
        'name',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $hidden = [];

    // Boot method để tự động gán user và generate name
    protected static function boot()
    {
        parent::boot();

        // Tự động gán created_by khi tạo mới
        static::creating(function ($model) {
            if (Auth::check() && ! $model->created_by) {
                $model->created_by = Auth::id();
            }
            if (Auth::check() && ! $model->updated_by) {
                $model->updated_by = Auth::id();
            }
        });

        // Tự động gán updated_by khi cập nhật
        static::updating(function ($model) {
            if (Auth::check()) {
                $model->updated_by = Auth::id();
            }
        });

        // Generate name sau khi tạo asset và các related records
        static::created(function ($asset) {
            // Tạm thời skip việc generate name ngay khi created
            // vì các related records chưa được tạo
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
    const TYPE_REAL_ESTATE_HOUSE           = 'real_estate_house';
    const TYPE_REAL_ESTATE_APARTMENT       = 'real_estate_apartment';
    const TYPE_REAL_ESTATE_LAND_ONLY       = 'real_estate_land_only';
    const TYPE_MOVABLE_PROPERTY_CAR        = 'movable_property_car';
    const TYPE_MOVABLE_PROPERTY_MOTORCYCLE = 'movable_property_motorcycle';

    public static function getAssetTypes(): array
    {
        return [
            self::TYPE_REAL_ESTATE_HOUSE,
            self::TYPE_REAL_ESTATE_APARTMENT,
            self::TYPE_REAL_ESTATE_LAND_ONLY,
            self::TYPE_MOVABLE_PROPERTY_CAR,
            self::TYPE_MOVABLE_PROPERTY_MOTORCYCLE,
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

    public function contracts()
    {
        return $this->belongsToMany(Contract::class, 'contract_asset')
            ->withPivot('notes')
            ->withTimestamps();
    }

    public function getDisplayNameAttribute(): string
    {
        return $this->name;
    }

    public function isUsedInContracts(): bool
    {
        return $this->contracts()->exists();
    }

    /**
     * Generate and update asset name based on asset type and related data
     */
    public function generateAndUpdateName(): void
    {
        $name = $this->generateAssetName();

        if ($name && $this->name !== $name) {
            // Update name without triggering events
            $this->updateQuietly(['name' => $name]);
        }
    }

    /**
     * Generate asset name based on asset type and related data
     */
    public function generateAssetName(): string
    {
        switch ($this->asset_type) {
            case self::TYPE_MOVABLE_PROPERTY_CAR:
                return $this->generateVehicleName('xe ô tô');

            case self::TYPE_MOVABLE_PROPERTY_MOTORCYCLE:
                return $this->generateVehicleName('xe mô tô');

            case self::TYPE_REAL_ESTATE_LAND_ONLY:
                return $this->generateRealEstateHouseName();

            case self::TYPE_REAL_ESTATE_APARTMENT:
            case self::TYPE_REAL_ESTATE_HOUSE:
                return $this->generateRealEstateName();

            default:
                return "Tài sản #{$this->id}";
        }
    }

    /**
     * Generate name for vehicle assets
     * Format: 'xe mô tô (registration_number) (license_plate)'
     */
    private function generateVehicleName(string $vehicleType): string
    {
        $vehicle = $this->vehicle;

        if (!$vehicle) {
            return "{$vehicleType} #{$this->id}";
        }

        $parts = [$vehicleType];

        if ($vehicle->registration_number) {
            $parts[] = "({$vehicle->registration_number})";
        }

        if ($vehicle->license_plate) {
            $parts[] = "({$vehicle->license_plate})";
        }

        // Nếu không có thông tin gì thì dùng ID
        if (count($parts) === 1) {
            $parts[] = "#{$this->id}";
        }

        return implode(' ', $parts);
    }

    /**
     * Generate name for real estate house
     * Format: 'QSDĐ (issue_number) ST (plot_number) TBĐ (map_sheet_number)'
     */
    private function generateRealEstateHouseName(): string
    {
        $certificate = $this->certificates->first();
        $landPlot = $this->landPlots->first();

        $parts = ['QSDĐ'];

        // Add certificate issue number
        if ($certificate && $certificate->issue_number) {
            $parts[] = "({$certificate->issue_number})";
        }

        // Add plot number
        if ($landPlot && $landPlot->plot_number) {
            $parts[] = "ST ({$landPlot->plot_number})";
        }

        // Add map sheet number
        if ($landPlot && $landPlot->map_sheet_number) {
            $parts[] = "TBĐ ({$landPlot->map_sheet_number})";
        }

        // Nếu không có thông tin chi tiết thì thêm ID
        if (count($parts) === 1) {
            $parts[] = "#{$this->id}";
        }

        return implode(' ', $parts);
    }

    /**
     * Generate name for real estate apartment and land only
     * Format: 'QSDĐ TSGLVĐ (issue_number) (book_number) ST (plot_number) TBĐ (map_sheet_number)'
     */
    private function generateRealEstateName(): string
    {
        $certificate = $this->certificates->first();
        $landPlot = $this->landPlots->first();

        $parts = ['QSDĐ TSGLVĐ'];

        // Add certificate info
        if ($certificate) {
            if ($certificate->issue_number) {
                $parts[] = "({$certificate->issue_number})";
            }
            if ($certificate->book_number) {
                $parts[] = "({$certificate->book_number})";
            }
        }

        // Add land plot info
        if ($landPlot) {
            if ($landPlot->plot_number) {
                $parts[] = "ST ({$landPlot->plot_number})";
            }
            if ($landPlot->map_sheet_number) {
                $parts[] = "TBĐ ({$landPlot->map_sheet_number})";
            }
        }

        // Nếu không có thông tin chi tiết thì thêm ID
        if (count($parts) === 1) {
            $parts[] = "#{$this->id}";
        }

        return implode(' ', $parts);
    }

    /**
     * Refresh and regenerate name (useful when related data changes)
     */
    public function refreshName(): void
    {
        // Load fresh relations
        $this->load(['certificates', 'landPlots', 'vehicle']);

        // Generate new name
        $this->generateAndUpdateName();
    }
}
