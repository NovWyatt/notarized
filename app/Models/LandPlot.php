<?php

// app/Models/LandPlot.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LandPlot extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_id',
        'plot_number',
        'map_sheet_number',
        'house_number',
        'street_name',
        'province',
        'district',
        'ward',
        'area',
        'usage_form',
        'usage_purpose',
        'land_use_term',
        'usage_origin',
        'notes'
    ];

    protected $casts = [
        'area' => 'decimal:2',
        'land_use_term' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    protected $hidden = [];

    // Relationships
    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    // Accessor for full address
    public function getFullAddressAttribute(): string
    {
        $address = collect([
            $this->house_number,
            $this->street_name,
            $this->ward,
            $this->district,
            $this->province
        ])->filter()->implode(', ');

        return $address;
    }
}
