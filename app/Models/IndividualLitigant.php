<?php

// app/Models/IndividualLitigant.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IndividualLitigant extends Model
{
    use HasFactory;

    protected $fillable = [
        'litigant_id',
        'birth_date',
        'gender',
        'nationality',
        'phone_number',
        'email',
        'status',
        'marital_status',
        'marriage_certificate_number',
        'marriage_certificate_date',
        'marriage_certificate_issued_by',
        'marriage_notes',
    ];

    protected $casts = [
        'birth_date'                => 'date',
        'marriage_certificate_date' => 'date',
        'gender'                    => 'string',
        'status'                    => 'string',
        'marital_status'            => 'string',
    ];

    protected $hidden = [
        'marriage_certificate_number',
        'marriage_notes',
    ];

    const GENDER_MALE   = 'male';
    const GENDER_FEMALE = 'female';

    const GENDERS = [
        self::GENDER_MALE   => 'Male',
        self::GENDER_FEMALE => 'Female',
    ];

    const STATUS_ALIVE               = 'alive';
    const STATUS_DECEASED            = 'deceased';
    const STATUS_CIVIL_INCAPACITATED = 'civil_incapacitated';

    const STATUSES = [
        self::STATUS_ALIVE               => 'Alive',
        self::STATUS_DECEASED            => 'Deceased',
        self::STATUS_CIVIL_INCAPACITATED => 'Civil Incapacitated',
    ];

    const MARITAL_SINGLE   = 'single';
    const MARITAL_DIVORCED = 'divorced';
    const MARITAL_MARRIED  = 'married';

    const MARITAL_STATUSES = [
        self::MARITAL_SINGLE   => 'Single',
        self::MARITAL_DIVORCED => 'Divorced',
        self::MARITAL_MARRIED  => 'Married',
    ];

    // Relationships
    public function litigant()
    {
        return $this->belongsTo(Litigant::class);
    }

    public function identityDocuments()
    {
        return $this->hasMany(IdentityDocument::class);
    }
}
