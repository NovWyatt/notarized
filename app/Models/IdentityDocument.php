<?php

// app/Models/IdentityDocument.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IdentityDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'individual_litigant_id',
        'document_type',
        'document_number',
        'issue_date',
        'issued_by',
        'school_name',
        'academic_year'
    ];

    protected $casts = [
        'issue_date' => 'date',
        'document_type' => 'string'
    ];

    const TYPE_CCCD = 'cccd';
    const TYPE_CMND = 'cmnd';
    const TYPE_PASSPORT = 'passport';
    const TYPE_OFFICER_ID = 'officer_id';
    const TYPE_STUDENT_CARD = 'student_card';

    const TYPES = [
        self::TYPE_CCCD => 'Căn cước công dân (12 số)',
        self::TYPE_CMND => 'Chứng minh nhân dân (9 số)',
        self::TYPE_PASSPORT => 'Hộ chiếu',
        self::TYPE_OFFICER_ID => 'Chứng minh sĩ quan',
        self::TYPE_STUDENT_CARD => 'Thẻ học sinh'
    ];

    // Relationships
    public function individualLitigant()
    {
        return $this->belongsTo(IndividualLitigant::class);
    }
}
