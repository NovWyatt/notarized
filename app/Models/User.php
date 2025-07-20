<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'department',
        'is_admin',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function litigants()
    {
        return $this->hasMany(Litigant::class);
    }
    /**
     * Get assets created by this user
     */
    public function createdAssets()
    {
        return $this->hasMany(Asset::class, 'created_by');
    }

    /**
     * Get assets last updated by this user
     */
    public function updatedAssets()
    {
        return $this->hasMany(Asset::class, 'updated_by');
    }

    /**
     * Get user's asset statistics
     */
    public function getAssetStatsAttribute(): array
    {
        return [
            'total_created'       => $this->createdAssets()->count(),
            'real_estate_created' => $this->createdAssets()
                ->whereIn('asset_type', [
                    'real_estate_house',
                    'real_estate_apartment',
                    'real_estate_land_only',
                ])->count(),
            'vehicles_created'    => $this->createdAssets()
                ->whereIn('asset_type', [
                    'movable_property_car',
                    'movable_property_motorcycle',
                ])->count(),
            'last_created'        => $this->createdAssets()
                ->latest()
                ->first()?->created_at,
        ];
    }

    /**
     * Check if user can manage asset
     */
    public function canManageAsset(Asset $asset): bool
    {
        return $asset->created_by === $this->id;
    }

    /**
     * Scope to get users who have created assets
     */
    public function scopeWithAssets($query)
    {
        return $query->has('createdAssets');
    }

    /**
     * Scope to get users by department
     */
    public function scopeByDepartment($query, $department)
    {
        return $query->where('department', $department);
    }

    /**
     * Get full name with department
     */
    public function getFullNameWithDepartmentAttribute(): string
    {
        $name = $this->name;
        if ($this->department) {
            $name .= ' (' . $this->department . ')';
        }
        return $name;
    }
}
