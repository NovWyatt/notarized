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
        'certificate_type_id',
        'issuing_authority_id',
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

    public function certificateType(): BelongsTo
    {
        return $this->belongsTo(CertificateType::class);
    }

    public function issuingAuthority(): BelongsTo
    {
        return $this->belongsTo(IssuingAuthority::class);
    }
}
