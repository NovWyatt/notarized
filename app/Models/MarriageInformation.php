<?php

// app/Models/MarriageInformation.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarriageInformation extends Model
{
    use HasFactory;

    protected $table = 'marriage_information';

    protected $fillable = [
        'litigant_id',
        'same_household',
        'spouse_id',
        'marriage_registration_number',
        'issue_date',
        'issued_by',
        'is_divorced'
    ];

    protected $casts = [
        'same_household' => 'boolean',
        'is_divorced' => 'boolean',
        'issue_date' => 'date'
    ];

    protected $hidden = [
        'marriage_registration_number'
    ];
}
