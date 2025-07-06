<?php

// app/Models/Address.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    protected $fillable = [
        'addressable_type',
        'addressable_id',
        'address_type',
        'street_address',
        'province',
        'district',
        'ward'
    ];

    protected $casts = [
        'address_type' => 'string'
    ];

    const TYPE_PERMANENT = 'permanent';
    const TYPE_TEMPORARY = 'temporary';
    const TYPE_HEADQUARTERS = 'headquarters';

    const TYPES = [
        self::TYPE_PERMANENT => 'Permanent Address',
        self::TYPE_TEMPORARY => 'Temporary Address',
        self::TYPE_HEADQUARTERS => 'Headquarters Address'
    ];

     // Relationships
    public function addressable()
    {
        return $this->morphTo();
    }
}
