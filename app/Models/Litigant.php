<?php

// app/Models/Litigant.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Litigant extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'full_name',
        'type',
        'user_id',
        'notes',
    ];

    protected $casts = [
        'type' => 'string',
        'deleted_at' => 'datetime',
    ];

    const TYPE_INDIVIDUAL = 'individual';
    const TYPE_ORGANIZATION = 'organization';
    const TYPE_CREDIT_INSTITUTION = 'credit_institution';

    const TYPES = [
        self::TYPE_INDIVIDUAL => 'Individual',
        self::TYPE_ORGANIZATION => 'Organization',
        self::TYPE_CREDIT_INSTITUTION => 'Credit Institution',
    ];

    // Relationships

    public function identityDocuments()
    {
        return $this->hasManyThrough(
            IdentityDocument::class,
            IndividualLitigant::class,
            'litigant_id',            // Foreign key on IndividualLitigant table
            'individual_litigant_id', // Foreign key on IdentityDocument table
            'id',                     // Local key on Litigant table
            'id'                      // Local key on IndividualLitigant table
        );
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function individualLitigant()
    {
        return $this->hasOne(IndividualLitigant::class);
    }

    public function individual()
    {
        return $this->individualLitigant();
    }

    public function organization()
    {
        return $this->hasOne(Organization::class);
    }

    public function creditInstitution()
    {
        return $this->hasOne(CreditInstitution::class);
    }

    public function addresses()
    {
        return $this->morphMany(Address::class, 'addressable');
    }

    public function marriageInformation()
    {
        return $this->hasOne(MarriageInformation::class);
    }

    public function spouse()
    {
        return $this->hasOneThrough(Litigant::class, MarriageInformation::class, 'litigant_id', 'id', 'id', 'spouse_id');
    }

    public function contractParties()
    {
        return $this->hasMany(ContractParty::class);
    }

    public function contracts()
    {
        return $this->hasManyThrough(Contract::class, ContractParty::class);
    }

    public function getDisplayNameAttribute(): string
    {
        return $this->full_name;
    }

    public function getPrimaryIdentityDocumentAttribute()
    {
        return $this->identityDocuments()->orderBy('created_at', 'desc')->first();
    }

    public function getIdentityInfoAttribute(): string
    {
        $identityDoc = $this->primary_identity_document;
        if ($identityDoc) {
            return strtoupper($identityDoc->document_type) . ': ' . $identityDoc->document_number;
        }
        return '';
    }
}
