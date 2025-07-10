<?php

// app/Models/House.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class House extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_id',
        'house_type',
        'construction_area',
        'floor_area',
        'ownership_form',
        'grade_level',
        'number_of_floors',
        'ownership_term',
        'structure',
        'notes'
    ];

    protected $casts = [
        'construction_area' => 'decimal:2',
        'floor_area' => 'decimal:2',
        'number_of_floors' => 'integer',
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
