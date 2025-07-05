<?php

// app/Models/Organization.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Organization extends Model
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
}
