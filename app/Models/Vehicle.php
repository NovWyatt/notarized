<?php

// app/Models/Vehicle.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_id',
        'registration_number',
        'issuing_authority_id',
        'issue_date',
        'license_plate',
        'brand',
        'vehicle_type',
        'color',
        'payload',
        'engine_number',
        'chassis_number',
        'type_number',
        'engine_capacity',
        'seating_capacity',
        'notes'
    ];

    protected $casts = [
        'issue_date' => 'date',
        'payload' => 'decimal:2',
        'engine_capacity' => 'decimal:2',
        'seating_capacity' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    protected $hidden = [];

    // Relationships
    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function issuingAuthority(): BelongsTo
    {
        return $this->belongsTo(IssuingAuthority::class);
    }
}
