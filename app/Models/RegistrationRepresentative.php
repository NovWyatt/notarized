<?php

// app/Models/RegistrationRepresentative.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegistrationRepresentative extends Model
{
    use HasFactory;

    protected $fillable = [
        'representable_type',
        'representable_id',
        'representative_id',
        'position',
        'legal_basis'
    ];

    protected $casts = [
        'representable_type' => 'string'
    ];
}
