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
        'notes'
    ];

    protected $casts = [
        'type' => 'string',
        'deleted_at' => 'datetime'
    ];

    const TYPE_INDIVIDUAL = 'individual';
    const TYPE_ORGANIZATION = 'organization';
    const TYPE_CREDIT_INSTITUTION = 'credit_institution';

    const TYPES = [
        self::TYPE_INDIVIDUAL => 'Individual',
        self::TYPE_ORGANIZATION => 'Organization',
        self::TYPE_CREDIT_INSTITUTION => 'Credit Institution'
    ];
}
