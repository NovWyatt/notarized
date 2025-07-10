<?php

// app/Models/Apartment.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Apartment extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_id',
        'apartment_number',
        'apartment_floor',
        'building_floors',
        'construction_area',
        'floor_area',
        'ownership_form',
        'ownership_term',
        'structure',
        'notes'
    ];

    protected $casts = [
        'apartment_floor' => 'integer',
        'building_floors' => 'integer',
        'construction_area' => 'decimal:2',
        'floor_area' => 'decimal:2',
        'ownership_term' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    protected $hidden = [];

    // Relationships
    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }
}
