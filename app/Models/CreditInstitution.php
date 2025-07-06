<?php

// app/Models/CreditInstitution.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CreditInstitution extends Model
{
    use HasFactory;

    protected $fillable = [
        'litigant_id',
        'business_type',
        'phone_number',
        'organization_type',
        'license_type',
        'license_number',
        'business_registration_date',
        'issuing_authority',
        'representative_id',
        'representative_position'
    ];

    protected $casts = [
        'business_registration_date' => 'date',
        'organization_type' => 'string'
    ];

    protected $hidden = [
        'license_number'
    ];

    const TYPE_HEADQUARTERS = 'headquarters';
    const TYPE_BRANCH = 'branch';
    const TYPE_TRANSACTION_OFFICE = 'transaction_office';

    const TYPES = [
        self::TYPE_HEADQUARTERS => 'Headquarters',
        self::TYPE_BRANCH => 'Branch',
        self::TYPE_TRANSACTION_OFFICE => 'Transaction Office'
    ];

    // Relationships
    public function litigant()
    {
        return $this->belongsTo(Litigant::class);
    }

    public function representative()
    {
        return $this->belongsTo(Litigant::class, 'representative_id');
    }

    public function additionalInfo()
    {
        return $this->hasOne(CreditInstitutionAdditionalInfo::class);
    }

    public function registrationRepresentatives()
    {
        return $this->morphMany(RegistrationRepresentative::class, 'representable');
    }
}
