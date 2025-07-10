<?php

// app/Models/Certificate.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Certificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_id',
        'certificate_type',
        'issue_number',
        'book_number',
        'issue_date'
    ];

    protected $casts = [
        'issue_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    protected $hidden = [];

    // Relationships
    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    // Certificate type constants
    const TYPE_LAND_USE_CERTIFICATE = 'land_use_certificate';
    const TYPE_APARTMENT_OWNERSHIP_CERTIFICATE = 'apartment_ownership_certificate';
    const TYPE_LAND_HOUSE_OWNERSHIP_CERTIFICATE = 'land_house_ownership_certificate';
    const TYPE_HOUSE_OWNERSHIP_CERTIFICATE = 'house_ownership_certificate';
    const TYPE_LAND_USE_RIGHT_CERTIFICATE = 'land_use_right_certificate';
    const TYPE_BL735265 = 'bl735265';

    public static function getCertificateTypes(): array
    {
        return [
            self::TYPE_LAND_USE_CERTIFICATE,
            self::TYPE_APARTMENT_OWNERSHIP_CERTIFICATE,
            self::TYPE_LAND_HOUSE_OWNERSHIP_CERTIFICATE,
            self::TYPE_HOUSE_OWNERSHIP_CERTIFICATE,
            self::TYPE_LAND_USE_RIGHT_CERTIFICATE,
            self::TYPE_BL735265
        ];
    }
}
