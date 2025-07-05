<?php

// app/Models/OrganizationAdditionalInfo.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrganizationAdditionalInfo extends Model
{
    use HasFactory;

    protected $table = 'organization_additional_info';

    protected $fillable = [
        'organization_id',
        'former_name',
        'account_number',
        'fax',
        'email',
        'website',
        'change_registration_number',
        'change_registration_date'
    ];

    protected $casts = [
        'change_registration_date' => 'date',
        'change_registration_number' => 'integer'
    ];

    protected $hidden = [
        'account_number'
    ];
}
